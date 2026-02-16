# Passkey Authentication Plugin

WebAuthn/Passkey authentication for the October CMS backend. Lets administrators sign in using fingerprint, face recognition, or screen lock instead of a password.

## Features

- **Passwordless login** — "Sign in with Passkey" button on the backend login page
- **Discoverable credentials** — no username required, the passkey identifies the user
- **Multi-device support** — USB security keys, NFC, Bluetooth, hybrid, and platform authenticators
- **Per-user management** — register, name, and delete passkeys from the user profile tab
- **Admin management** — users with `admins.manage` permission can manage passkeys for other users
- **Rate limiting** — max 10 authentication attempts per minute per IP
- **IDOR protection** — user context derived from URL parameters and permissions, not POST data
- **Signature counter validation** — detects cloned authenticators
- **15 languages** — Arabic, Chinese, Dutch, English, French, German, Japanese, Korean, Latvian, Polish, Portuguese, Russian, Spanish, Turkish, Ukrainian

## Requirements

- October CMS 3.1+ (plugin v1.x) or October CMS 4.x (plugin v2.x)
- PHP 8.0.2+ (v1.x) or PHP 8.2+ (v2.x)
- HTTPS (required by the WebAuthn specification)
- A WebAuthn-capable browser (all modern browsers)

## Installation

### Via Composer

```bash
composer require ihorchyshkala/passkey-plugin
```

### Via October CMS Marketplace

Search for **Passkey Authentication** in the October CMS plugin marketplace, or install from the admin panel:

```
Settings → Updates & Plugins → Install Plugins → "Passkey Authentication"
```

### Manual Installation

Clone this repository into `plugins/ihorchyshkala/passkey/` and run migrations:

```bash
php artisan october:migrate
```

## Usage

### Signing in with a Passkey

Once a passkey is registered, the login page shows a **"Sign in with Passkey"** button below the standard login form. Click it, authenticate with your device (fingerprint, face, PIN), and you're in — no password needed.

### Registering a Passkey

1. Go to **Settings → Administrators → (your user) → Passkeys** tab, or **My Account → Passkeys**
2. Click **Add Passkey**
3. Give it a descriptive name (e.g. "MacBook Pro", "YubiKey")
4. Click **Create Passkey** and follow the browser prompt
5. The passkey appears in the list immediately

### Removing a Passkey

Click **Remove** next to any passkey in the list. A confirmation dialog prevents accidental deletion.

## Version Compatibility

| Branch | Plugin | October CMS | PHP | Laravel |
|--------|--------|-------------|-----|---------|
| `1.x` | v1.x | v3.1+ | >=8.0.2 | 9 / 10 / 11 |
| `master` | v2.x | v4.x | >=8.2 | 12 |

Composer constraints on `october/rain` ensure the correct version is installed automatically. The namespace `IHORCHYSHKALA\Passkey` is the same on both branches.

## How It Works

The plugin implements the [WebAuthn Level 2](https://www.w3.org/TR/webauthn-2/) specification using the [lbuchs/webauthn](https://github.com/nicoswd/webauthn) PHP library.

**Registration flow:**
1. Backend generates a challenge with user info and excluded credentials
2. Browser calls `navigator.credentials.create()` with the options
3. Authenticator creates a key pair and signs the challenge
4. Backend verifies the attestation and stores the public key

**Authentication flow:**
1. Backend generates a challenge (no user info — discoverable credentials)
2. Browser calls `navigator.credentials.get()`
3. Authenticator signs the challenge with the private key
4. Backend verifies the signature against the stored public key and logs the user in

Challenges are stored in the server session with a 120-second TTL and are consumed on use (one-time).

## Security

- All credentials are stored server-side; private keys never leave the authenticator
- Challenges expire after 120 seconds and are single-use
- Rate limiting prevents brute-force attempts (10/min per IP)
- User verification is required for both registration and authentication
- Signature counters are validated to detect cloned authenticators
- IDOR protection: user targeting uses URL parameters verified against permissions, not client-supplied POST data
- Generic error messages prevent credential enumeration

## Database

The plugin creates one table: `ihorchyshkala_passkey_credentials`

| Column | Type | Description |
|--------|------|-------------|
| `id` | int | Primary key |
| `backend_user_id` | int | Foreign key to `backend_users` (cascade delete) |
| `credential_id` | varchar(512) | Base64url-encoded credential ID (indexed) |
| `public_key` | text | COSE public key |
| `name` | string | User-given name for the passkey |
| `sign_count` | int | Signature counter |
| `transports` | text | JSON array of supported transports |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

## License

MIT
