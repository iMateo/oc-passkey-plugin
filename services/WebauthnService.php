<?php

namespace IHORCHYSHKALA\Passkey\Services;

use Backend;
use Config;
use Session;
use Validator;
use lbuchs\WebAuthn\WebAuthn;
use lbuchs\WebAuthn\Binary\ByteBuffer;
use IHORCHYSHKALA\Passkey\Models\WebauthnCredential;
use Backend\Models\User as BackendUser;

/**
 * WebAuthn ceremonies for backend users.
 *
 * Exception messages thrown from this class are meant for the server log only;
 * callers must translate failures into generic messages for the client.
 */
class WebauthnService
{
    /**
     * Maximum time (seconds) a challenge is valid after creation.
     */
    protected const CHALLENGE_TTL = 120;

    /**
     * Separate session slots so a login ceremony cannot consume (or be satisfied by)
     * a registration challenge and vice versa.
     */
    protected const SESSION_KEY_REGISTER = 'ihorchyshkala_passkey_challenge_register';
    protected const SESSION_KEY_LOGIN = 'ihorchyshkala_passkey_challenge_login';

    protected function getWebAuthn(): WebAuthn
    {
        $rpId = $this->getRpId();
        $rpName = \Backend\Models\BrandSetting::get('app_name', 'October CMS');

        return new WebAuthn($rpName, $rpId, ['none'], true);
    }

    /**
     * RP ID: pinned via plugin config (`rp_id`, env PASSKEY_RP_ID), otherwise the backend host.
     */
    protected function getRpId(): string
    {
        $pinned = Config::get('ihorchyshkala.passkey::rp_id');
        if (is_string($pinned) && trim($pinned) !== '') {
            return strtolower(trim($pinned));
        }

        $backendUrl = Backend::url('/');
        $parsed = parse_url($backendUrl);

        return strtolower($parsed['host'] ?? 'localhost');
    }

    /**
     * Strictly decode base64url (RFC 4648 §5, padding optional) to a binary string.
     * Throws on any character outside the alphabet or an impossible length.
     */
    protected function base64urlDecode(string $data): string
    {
        if (!preg_match('/\A[A-Za-z0-9_-]+={0,2}\z/', $data)) {
            throw new \ApplicationException('Invalid base64url data.');
        }

        $data = rtrim($data, '=');
        $length = strlen($data);

        if ($length === 0 || $length % 4 === 1) {
            throw new \ApplicationException('Invalid base64url data.');
        }

        $base64 = strtr($data, '-_', '+/');
        if ($length % 4 !== 0) {
            $base64 .= str_repeat('=', 4 - $length % 4);
        }

        $decoded = base64_decode($base64, true);
        if ($decoded === false) {
            throw new \ApplicationException('Invalid base64url data.');
        }

        return $decoded;
    }

    /**
     * Encode binary string to base64url (RFC 4648)
     */
    protected function base64urlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * WebAuthn user handle assigned to a backend user at registration:
     * the user id as big-endian bytes, at least 4 bytes long.
     */
    public function getUserHandle(int $userId): string
    {
        $hex = dechex($userId);
        $length = max(8, strlen($hex) + (strlen($hex) % 2));

        return hex2bin(str_pad($hex, $length, '0', STR_PAD_LEFT));
    }

    /**
     * Store the challenge in session with a timestamp
     */
    protected function storeChallenge(WebAuthn $webAuthn, string $sessionKey, ?int $userId = null): void
    {
        Session::put($sessionKey, [
            'hex' => $webAuthn->getChallenge()->getHex(),
            'created_at' => time(),
            'user_id' => $userId,
        ]);
    }

    /**
     * Pull and validate the challenge from session (one-time use + TTL + user binding)
     */
    protected function pullChallenge(string $sessionKey, ?int $expectedUserId = null): ByteBuffer
    {
        $data = Session::pull($sessionKey);

        if (!is_array($data) || empty($data['hex'])) {
            throw new \ApplicationException('Challenge missing from session.');
        }

        if ((time() - ($data['created_at'] ?? 0)) > static::CHALLENGE_TTL) {
            throw new \ApplicationException('Challenge expired.');
        }

        if ($expectedUserId !== null && (int) ($data['user_id'] ?? 0) !== $expectedUserId) {
            throw new \ApplicationException('Challenge was issued for a different user.');
        }

        return ByteBuffer::fromHex($data['hex']);
    }

    /**
     * Generate registration options for a user
     */
    public function createRegistrationOptions(BackendUser $user): array
    {
        $webAuthn = $this->getWebAuthn();

        // Get existing credential IDs to exclude (stored as base64url)
        $existingCredentials = WebauthnCredential::where('backend_user_id', $user->id)
            ->pluck('credential_id')
            ->map(function ($id) {
                return $this->base64urlDecode($id);
            })
            ->all();

        $createArgs = $webAuthn->getCreateArgs(
            $this->getUserHandle((int) $user->id),
            $user->login,
            $user->full_name,
            60,
            'required',   // requireResidentKey for discoverable credentials
            'required',   // requireUserVerification
            null,         // crossPlatformAttachment - allow both
            $existingCredentials
        );

        $this->storeChallenge($webAuthn, static::SESSION_KEY_REGISTER, (int) $user->id);

        return json_decode(json_encode($createArgs), true);
    }

    /**
     * Verify registration response and store credential
     */
    public function verifyRegistration(
        BackendUser $user,
        ?string $clientDataJSON,
        ?string $attestationObject,
        ?string $name = ''
    ): WebauthnCredential {
        // Validate inputs
        $validation = Validator::make(
            compact('clientDataJSON', 'attestationObject', 'name'),
            [
                'clientDataJSON' => 'required|string|min:10',
                'attestationObject' => 'required|string|min:10',
                'name' => 'nullable|string|max:100',
            ]
        );
        if ($validation->fails()) {
            throw new \ApplicationException('Invalid registration data.');
        }

        $webAuthn = $this->getWebAuthn();
        $challenge = $this->pullChallenge(static::SESSION_KEY_REGISTER, (int) $user->id);

        $data = $webAuthn->processCreate(
            $this->base64urlDecode($clientDataJSON),
            $this->base64urlDecode($attestationObject),
            $challenge,
            true,   // requireUserVerification
            true,   // requireUserPresent
            false   // failIfRootMismatch - we use attestation 'none'
        );

        $credentialId = $this->base64urlEncode($data->credentialId);

        // A credential ID must belong to exactly one account
        if (WebauthnCredential::where('credential_id', $credentialId)->exists()) {
            throw new \ApplicationException('Credential ID is already registered.');
        }

        $safeName = mb_substr(trim((string) $name) ?: 'Passkey', 0, 100);

        $credential = new WebauthnCredential();
        $credential->backend_user_id = $user->id;
        $credential->credential_id = $credentialId;
        $credential->public_key = $data->credentialPublicKey;
        $credential->name = $safeName;
        $credential->sign_count = $data->signatureCounter ?? 0;
        $credential->save();

        return $credential;
    }

    /**
     * Generate authentication options (discoverable, no username needed)
     */
    public function createLoginOptions(): array
    {
        $webAuthn = $this->getWebAuthn();

        $getArgs = $webAuthn->getGetArgs(
            [],     // empty = discoverable credentials (resident keys)
            60,     // timeout
            true,   // allowUsb
            true,   // allowNfc
            true,   // allowBle
            true,   // allowHybrid
            true,   // allowInternal
            'required' // requireUserVerification
        );

        $this->storeChallenge($webAuthn, static::SESSION_KEY_LOGIN);

        return json_decode(json_encode($getArgs), true);
    }

    /**
     * Verify authentication response and return the authenticated user
     */
    public function verifyLogin(
        ?string $credentialIdBase64url,
        ?string $clientDataJSON,
        ?string $authenticatorData,
        ?string $signature,
        ?string $userHandleBase64url
    ): BackendUser {
        // Validate required inputs
        $validation = Validator::make(
            [
                'credential_id' => $credentialIdBase64url,
                'client_data_json' => $clientDataJSON,
                'authenticator_data' => $authenticatorData,
                'signature' => $signature,
                'user_handle' => $userHandleBase64url,
            ],
            [
                'credential_id' => 'required|string|min:10|max:512',
                'client_data_json' => 'required|string|min:10',
                'authenticator_data' => 'required|string|min:10',
                'signature' => 'required|string|min:10',
                'user_handle' => 'nullable|string|max:512',
            ]
        );
        if ($validation->fails()) {
            throw new \ApplicationException('Invalid authentication data.');
        }

        $webAuthn = $this->getWebAuthn();
        $challenge = $this->pullChallenge(static::SESSION_KEY_LOGIN);

        // Reject non-canonical encodings so the lookup string is unambiguous
        if ($this->base64urlEncode($this->base64urlDecode($credentialIdBase64url)) !== $credentialIdBase64url) {
            throw new \ApplicationException('Non-canonical credential ID.');
        }

        // Find credential by credential ID (stored as base64url). Compare exactly in PHP,
        // since the DB collation may be case-insensitive.
        $credential = WebauthnCredential::where('credential_id', $credentialIdBase64url)
            ->get()
            ->first(function ($row) use ($credentialIdBase64url) {
                return hash_equals((string) $row->credential_id, $credentialIdBase64url);
            });

        if (!$credential) {
            throw new \ApplicationException('Unknown credential ID.');
        }

        // If the authenticator returned a user handle it must match the one assigned at registration
        if ($userHandleBase64url !== null && $userHandleBase64url !== '') {
            $userHandle = $this->base64urlDecode($userHandleBase64url);
            if (!hash_equals($this->getUserHandle((int) $credential->backend_user_id), $userHandle)) {
                throw new \ApplicationException('User handle does not match credential owner.');
            }
        }

        $webAuthn->processGet(
            $this->base64urlDecode($clientDataJSON),
            $this->base64urlDecode($authenticatorData),
            $this->base64urlDecode($signature),
            $credential->public_key,
            $challenge,
            $credential->sign_count,
            true,  // requireUserVerification
            true   // requireUserPresent
        );

        // Update signature counter
        $credential->sign_count = $webAuthn->getSignatureCounter() ?? $credential->sign_count;
        $credential->save();

        // Return the associated backend user (soft-deleted users are excluded by find())
        $user = BackendUser::find($credential->backend_user_id);

        if (!$user || $user->trashed()) {
            throw new \ApplicationException('Credential owner not found or deleted.');
        }

        // Note: no is_activated check. Backend auth does not require activation
        // (AuthManager::$requireActivation = false) and admins created through the
        // Users form keep the column default (0), so such a check would lock them out.

        return $user;
    }
}
