# Passkey Authentication Plugin

WebAuthn/Passkey authentication for the October CMS backend. Lets administrators sign in using fingerprint, face recognition, or screen lock instead of a password.

## Features

- **Passwordless login** — "Sign in with Passkey" button on the backend login page
- **Discoverable credentials** — no username required, the passkey identifies the user
- **Multi-device support** — USB security keys, NFC, Bluetooth, hybrid, and platform authenticators
- **Per-user management** — register, name, and delete your own passkeys from **My Account → Passkeys**
- **Admin revocation** — users with `admins.manage` permission can view and revoke (but never add) passkeys of administrators they are allowed to manage
- **Rate limiting** — per-IP limits on login (10 failed verifications / 30 option requests per minute) and per-user limit on wrong passwords when adding a passkey
- **IDOR protection** — user context derived from URL parameters and permissions, not POST data
- **Signature counter validation** — detects cloned authenticators
- **15 languages** — Arabic, Chinese, Dutch, English, French, German, Japanese, Korean, Latvian, Polish, Portuguese, Russian, Spanish, Turkish, Ukrainian

## Requirements

- October CMS 3.1+ or 4.x (one plugin version for both)
- PHP 8.0.2+ (October CMS 4.x itself requires PHP 8.2+)
- HTTPS (required by the WebAuthn specification)
- A WebAuthn-capable browser (all modern browsers)

## Installation

### Via Artisan (recommended)

```bash
php artisan plugin:install IHORCHYSHKALA.Passkey
```

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

1. Go to **My Account → Passkeys** (passkeys can only be added to your own account)
2. Click **Add Passkey**
3. Give it a descriptive name (e.g. "MacBook Pro", "YubiKey") and confirm your current password
4. Click **Create Passkey** and follow the browser prompt
5. The passkey appears in the list immediately

### Removing a Passkey

Click **Remove** next to any passkey in the list. A confirmation dialog prevents accidental deletion.

Administrators with the `admins.manage` permission also see the **Passkeys** tab when editing another administrator and can revoke that user's passkeys (for example, a lost device). The same rank rules as the core Administrators list apply: non-superusers cannot see or touch superusers or higher-ranked administrators. Adding a passkey for someone else is not possible.

## Configuration

By default the WebAuthn Relying Party ID is the host of the backend URL. To pin it (recommended behind proxies or when the backend is reachable under several hosts), set an environment variable:

```
PASSKEY_RP_ID=example.com
```

or override the plugin config in `config/ihorchyshkala/passkey/config.php`:

```php
<?php return ['rp_id' => 'example.com'];
```

The RP ID must equal the backend host or be a registrable parent domain of it. Passkeys are bound to the RP ID, so changing it later invalidates all registered passkeys.

## Version Compatibility

A single release line supports both October CMS 3.1+ (Laravel 9/10) and October CMS 4.x (Laravel 12), PHP 8.0.2+. The same code runs unchanged on both; `composer.json` requires `october/rain >=3.1`.

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
- Passkeys can only be registered for the signed-in administrator's own account (never for another user, never while impersonating); the registration challenge is bound to that user
- Revoking another administrator's passkeys requires `admins.manage` and respects the core superuser/rank restrictions
- Passkey sign-in applies the same ban/suspension (throttle) checks as password sign-in and follows `backend.force_remember` for the "remember me" cookie
- Challenges expire after 120 seconds, are single-use, and login and registration use separate session slots
- Adding a passkey requires the current account password (re-authentication); the registration challenge is only issued after that check, and wrong passwords are limited to 5 per minute per user
- Per-IP rate limiting (Laravel `RateLimiter`, atomic): 30 login option requests and 10 failed login verifications per minute
  - The 30/min limit on login options intentionally counts every request (issuing a challenge is cheap but unauthenticated), not only failures
- The session id is regenerated after a successful passkey sign-in; a successful sign-in clears failed-attempt counters like the password sign-in does
- User verification is required for both registration and authentication
- The returned user handle must match the credential owner; credential IDs are unique across all users
- Signature counters are validated to detect cloned authenticators
- IDOR protection: user targeting uses URL parameters verified against permissions, not client-supplied POST data
- Login and registration failures return a generic message to the browser; details are written to the application log
- Registrations and revocations are written to the application log (real actor — the impersonating admin if any — target user, credential, IP)

> **Behind a proxy / load balancer:** the login rate limits are keyed by client IP (`request()->ip()`). Make sure it resolves to the real client IP — e.g. register Laravel's `TrustProxies` middleware for your proxy addresses, or let the web server restore the client address (nginx `real_ip_header` / `set_real_ip_from`). Otherwise all visitors share the proxy's IP and therefore one limit.

## Database

The plugin creates one table: `ihorchyshkala_passkey_credentials`

| Column | Type | Description |
|--------|------|-------------|
| `id` | int | Primary key |
| `backend_user_id` | int | Foreign key to `backend_users` (cascade delete) |
| `credential_id` | varchar(512) | Base64url-encoded credential ID (unique) |
| `public_key` | text | COSE public key |
| `name` | string | User-given name for the passkey |
| `sign_count` | int | Signature counter |
| `transports` | text | JSON array of supported transports |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

## License

MIT
