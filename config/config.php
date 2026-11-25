<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WebAuthn Relying Party ID
    |--------------------------------------------------------------------------
    |
    | Pin the RP ID (a registrable domain, e.g. "example.com" or "admin.example.com")
    | instead of deriving it from the backend URL / request host. Passkeys are bound
    | to the RP ID, so changing it invalidates all registered passkeys.
    | Leave empty to keep the default behaviour (host of Backend::url('/')).
    |
    | Override in config/ihorchyshkala/passkey/config.php or via PASSKEY_RP_ID.
    |
    */

    'rp_id' => env('PASSKEY_RP_ID', ''),
];
