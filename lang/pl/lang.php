<?php

return [
    'plugin' => [
        'name'        => 'Uwierzytelnianie Passkey',
        'description' => 'Dodaje uwierzytelnianie WebAuthn/Passkey do logowania w panelu administracyjnym.',
    ],
    'tab' => [
        'passkeys' => 'Klucze dostępu',
    ],
    'login' => [
        'divider' => 'lub',
        'button'  => 'Zaloguj się z Passkey',
    ],
    'manage' => [
        'description'      => 'Klucze dostępu umożliwiają bezpieczne logowanie za pomocą odcisku palca, rozpoznawania twarzy lub blokady ekranu zamiast hasła.',
        'add_button'       => 'Dodaj klucz dostępu',
        'name_label'       => 'Nazwa klucza',
        'name_placeholder' => 'np. MacBook Pro, iPhone',
        'register_button'  => 'Utwórz klucz dostępu',
        'cancel'           => 'Anuluj',
        'col_name'         => 'Nazwa',
        'col_created'      => 'Utworzono',
        'col_actions'      => '',
        'delete'           => 'Usuń',
        'confirm_delete'   => 'Czy na pewno chcesz usunąć ten klucz dostępu?',
        'no_passkeys'      => 'Brak zarejestrowanych kluczy dostępu. Dodaj jeden, aby włączyć logowanie bez hasła.',
    ],
    'messages' => [
        'registered' => 'Klucz dostępu zarejestrowany pomyślnie.',
        'deleted'    => 'Klucz dostępu usunięty.',
    ],
];
