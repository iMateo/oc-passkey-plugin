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
        'password_label'   => 'Aktuelles Passwort',
        'register_button'  => 'Passkey erstellen',
        'cancel'           => 'Abbrechen',
        'col_name'         => 'Name',
        'col_created'      => 'Erstellt',
        'col_actions'      => '',
        'delete'           => 'Entfernen',
        'confirm_delete'   => 'Sind Sie sicher, dass Sie diesen Passkey entfernen möchten?',
        'no_passkeys'      => 'Noch keine Passkeys registriert. Fügen Sie einen hinzu, um die passwortlose Anmeldung zu aktivieren.',
        'owner_only'       => 'Passkeys können nur vom Kontoinhaber hinzugefügt werden (Mein Konto). Hier können Sie vorhandene Passkeys widerrufen.',
    ],
    'messages' => [
        'registered' => 'Passkey erfolgreich registriert.',
        'deleted'    => 'Passkey entfernt.',
    ],
    'errors' => [
        'login_failed'        => 'Passkey-Anmeldung fehlgeschlagen.',
        'too_many_attempts'   => 'Zu viele Versuche. Bitte warten Sie eine Minute und versuchen Sie es erneut.',
        'registration_failed' => 'Passkey-Registrierung fehlgeschlagen. Bitte versuchen Sie es erneut.',
        'own_account_only'    => 'Passkeys können nur für das eigene Konto registriert werden.',
        'impersonating'       => 'Während Sie als anderer Benutzer angemeldet sind, können keine Passkeys registriert werden.',
        'access_denied'       => 'Zugriff verweigert.',
        'not_found'           => 'Passkey nicht gefunden.',
        'wrong_password'      => 'Das aktuelle Passwort ist falsch.',
        'unexpected'          => 'Etwas ist schiefgelaufen. Bitte versuchen Sie es erneut.',
    ],
];
