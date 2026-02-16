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
        'register_button'  => 'Create Passkey',
        'cancel'           => 'Cancel',
        'col_name'         => 'Name',
        'col_created'      => 'Created',
        'col_actions'      => '',
        'delete'           => 'Remove',
        'confirm_delete'   => 'Are you sure you want to remove this passkey?',
        'no_passkeys'      => 'No passkeys registered yet. Add one to enable passwordless login.',
    ],
    'messages' => [
        'registered' => 'Passkey registered successfully.',
        'deleted'    => 'Passkey removed.',
    ],
];
