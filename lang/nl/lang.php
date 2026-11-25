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
        'password_label'   => 'Huidig wachtwoord',
        'register_button'  => 'Passkey aanmaken',
        'cancel'           => 'Annuleren',
        'col_name'         => 'Naam',
        'col_created'      => 'Aangemaakt',
        'col_actions'      => '',
        'delete'           => 'Verwijderen',
        'confirm_delete'   => 'Weet u zeker dat u deze passkey wilt verwijderen?',
        'no_passkeys'      => 'Nog geen passkeys geregistreerd. Voeg er een toe om wachtwoordloos inloggen in te schakelen.',
        'owner_only'       => 'Passkeys kunnen alleen door de accounteigenaar worden toegevoegd (Mijn account). Hier kunt u bestaande passkeys intrekken.',
    ],
    'messages' => [
        'registered' => 'Passkey succesvol geregistreerd.',
        'deleted'    => 'Passkey verwijderd.',
    ],
    'errors' => [
        'login_failed'        => 'Aanmelden met passkey mislukt.',
        'too_many_attempts'   => 'Te veel pogingen. Wacht een minuut en probeer het opnieuw.',
        'registration_failed' => 'Registratie van de passkey mislukt. Probeer het opnieuw.',
        'own_account_only'    => 'Passkeys kunnen alleen voor uw eigen account worden geregistreerd.',
        'impersonating'       => 'Passkeys kunnen niet worden geregistreerd terwijl u zich voordoet als een andere gebruiker.',
        'access_denied'       => 'Toegang geweigerd.',
        'not_found'           => 'Passkey niet gevonden.',
        'wrong_password'      => 'Het huidige wachtwoord is onjuist.',
        'unexpected'          => 'Er is iets misgegaan. Probeer het opnieuw.',
    ],
];
