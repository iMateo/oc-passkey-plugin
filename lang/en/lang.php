<?php

return [
    'plugin' => [
        'name'        => 'Passkey Authentication',
        'description' => 'Adds WebAuthn/Passkey authentication to the backend login.',
    ],
    'tab' => [
        'passkeys' => 'Passkeys',
    ],
    'login' => [
        'divider' => 'or',
        'button'  => 'Sign in with Passkey',
    ],
    'manage' => [
        'description'      => 'Passkeys let you sign in securely using your fingerprint, face, or screen lock instead of a password.',
        'add_button'       => 'Add Passkey',
        'name_label'       => 'Passkey Name',
        'name_placeholder' => 'e.g. MacBook Pro, iPhone',
        'password_label'   => 'Current password',
        'register_button'  => 'Create Passkey',
        'cancel'           => 'Cancel',
        'col_name'         => 'Name',
        'col_created'      => 'Created',
        'col_actions'      => '',
        'delete'           => 'Remove',
        'confirm_delete'   => 'Are you sure you want to remove this passkey?',
        'no_passkeys'      => 'No passkeys registered yet. Add one to enable passwordless login.',
        'owner_only'       => 'Passkeys can only be added by the account owner (My Account). You can revoke existing passkeys here.',
    ],
    'messages' => [
        'registered' => 'Passkey registered successfully.',
        'deleted'    => 'Passkey removed.',
    ],
    'errors' => [
        'login_failed'        => 'Passkey authentication failed.',
        'too_many_attempts'   => 'Too many attempts. Please wait a minute and try again.',
        'registration_failed' => 'Passkey registration failed. Please try again.',
        'own_account_only'    => 'Passkeys can only be registered for your own account.',
        'impersonating'       => 'Passkeys cannot be registered while impersonating another user.',
        'access_denied'       => 'Access denied.',
        'not_found'           => 'Passkey not found.',
        'wrong_password'      => 'Current password is incorrect.',
        'unexpected'          => 'Something went wrong. Please try again.',
    ],
];
