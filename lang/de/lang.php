<?php

return [
    'plugin' => [
        'name'        => 'Passkey-Authentifizierung',
        'description' => 'Fügt WebAuthn/Passkey-Authentifizierung zur Backend-Anmeldung hinzu.',
    ],
    'tab' => [
        'passkeys' => 'Passkeys',
    ],
    'login' => [
        'divider' => 'oder',
        'button'  => 'Mit Passkey anmelden',
    ],
    'manage' => [
        'description'      => 'Mit Passkeys können Sie sich sicher per Fingerabdruck, Gesichtserkennung oder Bildschirmsperre anstelle eines Passworts anmelden.',
        'add_button'       => 'Passkey hinzufügen',
        'name_label'       => 'Passkey-Name',
        'name_placeholder' => 'z.B. MacBook Pro, iPhone',
        'register_button'  => 'Passkey erstellen',
        'cancel'           => 'Abbrechen',
        'col_name'         => 'Name',
        'col_created'      => 'Erstellt',
        'col_actions'      => '',
        'delete'           => 'Entfernen',
        'confirm_delete'   => 'Sind Sie sicher, dass Sie diesen Passkey entfernen möchten?',
        'no_passkeys'      => 'Noch keine Passkeys registriert. Fügen Sie einen hinzu, um die passwortlose Anmeldung zu aktivieren.',
    ],
    'messages' => [
        'registered' => 'Passkey erfolgreich registriert.',
        'deleted'    => 'Passkey entfernt.',
    ],
];
