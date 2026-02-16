# IHORCHYSHKALA.Passkey — Marketplace Publishing Guide

## Step 1. Register as Author on octobercms.com

1. Create an account at [octobercms.com](https://octobercms.com)
2. Go to **Author Registration** from the dashboard
3. Choose Author Code: `IHORCHYSHKALA` (cannot be changed later)
4. Link a **PayPal** account if selling the plugin (not needed for free)

## Step 2. Prepare a Separate Git Repository

The plugin must be in its own Git repository (GitHub or BitBucket).

```bash
mkdir ~/oc-passkey-plugin
cp -R plugins/ihorchyshkala/passkey/* ~/oc-passkey-plugin/
cd ~/oc-passkey-plugin
git init
git add .
git commit -m "Initial release v1.0.1"
git tag -a v1.0.1 -m "Version 1.0.1"
git remote add origin git@github.com:ihorchyshkala/oc-passkey-plugin.git
git push -u origin main --tags
```

Git tags must match versions in `version.yaml`.

## Step 3. Verify composer.json

```json
{
    "name": "ihorchyshkala/passkey-plugin",
    "type": "october-plugin",
    "description": "WebAuthn/Passkey authentication for October CMS backend",
    "require": {
        "composer/installers": "~1.0",
        "lbuchs/webauthn": "^2.2"
    }
}
```

- `name` — must end with `-plugin`
- `type` — must be `october-plugin`
- `composer/installers` — required for correct installation path

## Step 4. Prepare Graphics

| Asset | Size | Format | Notes |
|-------|------|--------|-------|
| Icon | 64x64 px | PNG, transparent | Vector, rounded corners, no artifacts |
| Banner | 837x348 px | PNG | Aesthetic, proper margins |
| Screenshots | 838x630 px | PNG, max 5 | No taskbars, browser frames, 100% zoom, English UI |

Screenshots should show:
- "Sign in with Passkey" button on login page
- Passkey registration in user profile tab
- Registered passkeys table

## Step 5. Write Documentation (Markdown)

Upload via the marketplace web interface. No HTML allowed.

Required sections:
- Features overview
- Requirements (PHP 8.2+, HTTPS, WebAuthn-capable browser)
- Installation instructions
- Usage guide (login, register passkey, delete passkey)
- Security features
- Admin management (permissions)

## Step 6. Create Plugin on Marketplace

1. Log in to octobercms.com
2. **Author Dashboard** > **Plugins** > **Add Plugin**
3. Provide the **Git URL** of the repository
4. Plugin is created in **Draft** status
5. Fill in:
   - Short description (1-2 sentences)
   - Long description (full feature list)
   - Documentation (Markdown)
   - Categories (e.g. Security, Backend) — up to 2
   - Icon (64x64 PNG)
   - Banner (837x348 PNG)
   - Screenshots (up to 5)
   - Pricing (Free or Paid)

## Step 7. Submit for Approval

1. Click **"Submit for approval"** in the plugin sidebar
2. Manual review by October CMS team (usually a few days)
3. Email notification with result

Common rejection reasons:
- Migration errors during installation
- Backend/frontend bugs
- Poor documentation
- Graphics not meeting size/quality requirements
- PSR-2 violations
- Duplicate functionality without clear differentiation

## Step 8. After Approval

- Plugin becomes visible on the marketplace
- Users install via `composer require ihorchyshkala/passkey-plugin` or October CMS admin
- For updates: commit + new Git tag → marketplace pulls automatically
- Can hide/show the plugin without re-approval

## Pricing Model (if paid)

| License | Price | Usage |
|---------|-------|-------|
| Regular | You set (e.g. $15) | 1 site, 1 client |
| Extended | Auto x35 (e.g. $525) | Unlimited sites/clients |

- Author keeps **70%**, October CMS takes **30%**
- Monthly PayPal payouts (first week of each month)

## Pre-Submission Checklist

- [ ] Separate Git repository with version tags
- [ ] `composer.json` with `composer/installers` and `type: october-plugin`
- [ ] `version.yaml` with all versions and migrations
- [ ] `Plugin.php` → `pluginDetails()` with name, description, author, icon
- [ ] Migrations run without errors (`php artisan october:migrate`)
- [ ] Code follows PSR-2
- [ ] Icon 64x64 PNG
- [ ] Banner 837x348 PNG
- [ ] Screenshots 838x630 PNG (up to 5)
- [ ] Documentation in Markdown (English)
- [ ] Tested on clean October CMS 3.4 installation
- [ ] All translations included
