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
        'password_label'   => 'Obecne hasło',
        'register_button'  => 'Utwórz klucz dostępu',
        'cancel'           => 'Anuluj',
        'col_name'         => 'Nazwa',
        'col_created'      => 'Utworzono',
        'col_actions'      => '',
        'delete'           => 'Usuń',
        'confirm_delete'   => 'Czy na pewno chcesz usunąć ten klucz dostępu?',
        'no_passkeys'      => 'Brak zarejestrowanych kluczy dostępu. Dodaj jeden, aby włączyć logowanie bez hasła.',
        'owner_only'       => 'Klucze dostępu może dodawać tylko właściciel konta (Moje konto). Tutaj możesz unieważnić istniejące klucze.',
    ],
    'messages' => [
        'registered' => 'Klucz dostępu zarejestrowany pomyślnie.',
        'deleted'    => 'Klucz dostępu usunięty.',
    ],
    'errors' => [
        'login_failed'        => 'Uwierzytelnianie kluczem dostępu nie powiodło się.',
        'too_many_attempts'   => 'Zbyt wiele prób. Odczekaj minutę i spróbuj ponownie.',
        'registration_failed' => 'Rejestracja klucza dostępu nie powiodła się. Spróbuj ponownie.',
        'own_account_only'    => 'Klucze dostępu można rejestrować tylko dla własnego konta.',
        'impersonating'       => 'Nie można rejestrować kluczy dostępu podczas działania jako inny użytkownik.',
        'access_denied'       => 'Odmowa dostępu.',
        'not_found'           => 'Nie znaleziono klucza dostępu.',
        'wrong_password'      => 'Obecne hasło jest nieprawidłowe.',
        'unexpected'          => 'Coś poszło nie tak. Spróbuj ponownie.',
    ],
];
