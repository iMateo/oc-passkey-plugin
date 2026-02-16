<?php

return [
    'plugin' => [
        'name'        => 'Passkey-authenticatie',
        'description' => 'Voegt WebAuthn/Passkey-authenticatie toe aan de backend-aanmelding.',
    ],
    'tab' => [
        'passkeys' => 'Passkeys',
    ],
    'login' => [
        'divider' => 'of',
        'button'  => 'Inloggen met Passkey',
    ],
    'manage' => [
        'description'      => 'Met passkeys kunt u veilig inloggen met uw vingerafdruk, gezicht of schermvergrendeling in plaats van een wachtwoord.',
        'add_button'       => 'Passkey toevoegen',
        'name_label'       => 'Passkey-naam',
        'name_placeholder' => 'bijv. MacBook Pro, iPhone',
        'register_button'  => 'Passkey aanmaken',
        'cancel'           => 'Annuleren',
        'col_name'         => 'Naam',
        'col_created'      => 'Aangemaakt',
        'col_actions'      => '',
        'delete'           => 'Verwijderen',
        'confirm_delete'   => 'Weet u zeker dat u deze passkey wilt verwijderen?',
        'no_passkeys'      => 'Nog geen passkeys geregistreerd. Voeg er een toe om wachtwoordloos inloggen in te schakelen.',
    ],
    'messages' => [
        'registered' => 'Passkey succesvol geregistreerd.',
        'deleted'    => 'Passkey verwijderd.',
    ],
];
