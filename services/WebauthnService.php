<?php

namespace IHORCHYSHKALA\Passkey\Services;

use Backend;
use Session;
use Validator;
use lbuchs\WebAuthn\WebAuthn;
use lbuchs\WebAuthn\Binary\ByteBuffer;
use IHORCHYSHKALA\Passkey\Models\WebauthnCredential;
use Backend\Models\User as BackendUser;

class WebauthnService
{
    /**
     * Maximum time (seconds) a challenge is valid after creation.
     */
    protected const CHALLENGE_TTL = 120;

    protected function getWebAuthn(): WebAuthn
    {
        $rpId = $this->getRpId();
        $rpName = \Backend\Models\BrandSetting::get('app_name', 'October CMS');

        return new WebAuthn($rpName, $rpId, ['none'], true);
    }

    protected function getRpId(): string
    {
        $backendUrl = Backend::url('/');
        $parsed = parse_url($backendUrl);

        return $parsed['host'] ?? 'localhost';
    }

    /**
     * Decode base64url (RFC 4648) to binary string
     */
    protected function base64urlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Encode binary string to base64url (RFC 4648)
     */
    protected function base64urlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Store the challenge in session with a timestamp
     */
    protected function storeChallenge(WebAuthn $webAuthn): void
    {
        Session::put('webauthn_challenge', [
            'hex' => $webAuthn->getChallenge()->getHex(),
            'created_at' => time(),
        ]);
    }

    /**
     * Pull and validate the challenge from session (one-time use + TTL)
     */
    protected function pullChallenge(): ByteBuffer
    {
        $data = Session::pull('webauthn_challenge');

        if (!$data || empty($data['hex'])) {
            throw new \ApplicationException('Session expired. Please try again.');
        }

        if ((time() - ($data['created_at'] ?? 0)) > static::CHALLENGE_TTL) {
            throw new \ApplicationException('Challenge expired. Please try again.');
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
            hex2bin(str_pad(dechex($user->id), 8, '0', STR_PAD_LEFT)),
            $user->login,
            $user->full_name,
            60,
            'required',   // requireResidentKey for discoverable credentials
            'required',   // requireUserVerification
            null,         // crossPlatformAttachment - allow both
            $existingCredentials
        );

        $this->storeChallenge($webAuthn);

        return json_decode(json_encode($createArgs), true);
    }

    /**
     * Verify registration response and store credential
     */
    public function verifyRegistration(
        BackendUser $user,
        string $clientDataJSON,
        string $attestationObject,
        string $name = ''
    ): WebauthnCredential {
        // Validate inputs
        $validation = Validator::make(
            compact('clientDataJSON', 'attestationObject', 'name'),
            [
                'clientDataJSON' => 'required|string|min:10',
                'attestationObject' => 'required|string|min:10',
                'name' => 'string|max:100',
            ]
        );
        if ($validation->fails()) {
            throw new \ApplicationException('Invalid registration data.');
        }

        $webAuthn = $this->getWebAuthn();
        $challenge = $this->pullChallenge();

        $data = $webAuthn->processCreate(
            $this->base64urlDecode($clientDataJSON),
            $this->base64urlDecode($attestationObject),
            $challenge,
            true,   // requireUserVerification
            true,   // requireUserPresent
            false   // failIfRootMismatch - we use attestation 'none'
        );

        $safeName = mb_substr(trim($name) ?: 'Passkey', 0, 100);

        $credential = new WebauthnCredential();
        $credential->backend_user_id = $user->id;
        $credential->credential_id = $this->base64urlEncode($data->credentialId);
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

        $this->storeChallenge($webAuthn);

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
            ],
            [
                'credential_id' => 'required|string|min:10',
                'client_data_json' => 'required|string|min:10',
                'authenticator_data' => 'required|string|min:10',
                'signature' => 'required|string|min:10',
            ]
        );
        if ($validation->fails()) {
            throw new \ApplicationException('Invalid authentication data.');
        }

        $webAuthn = $this->getWebAuthn();
        $challenge = $this->pullChallenge();

        // Find credential by credential ID (stored as base64url)
        $credential = WebauthnCredential::where('credential_id', $credentialIdBase64url)->first();

        if (!$credential) {
            // Use generic message to avoid credential enumeration
            throw new \ApplicationException('Passkey authentication failed.');
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

        // Return the associated backend user
        $user = BackendUser::find($credential->backend_user_id);

        if (!$user || $user->trashed()) {
            throw new \ApplicationException('User account not found or disabled.');
        }

        // Check user is activated (backend doesn't require this by default,
        // but a deactivated admin should not be able to log in via passkey)
        if (!$user->is_activated) {
            throw new \ApplicationException('User account is deactivated.');
        }

        return $user;
    }
}
