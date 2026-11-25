<?php

return [
    'plugin' => [
        'name'        => 'Passkey autentifikācija',
        'description' => 'Pievieno WebAuthn/Passkey autentifikāciju administrācijas paneļa pieteikšanās lapai.',
    ],
    'tab' => [
        'passkeys' => 'Piekļuves atslēgas',
    ],
    'login' => [
        'divider' => 'vai',
        'button'  => 'Pieteikties ar Passkey',
    ],
    'manage' => [
        'description'      => 'Piekļuves atslēgas ļauj droši pieteikties, izmantojot pirkstu nospiedumu, sejas atpazīšanu vai ekrāna bloķēšanu paroles vietā.',
        'add_button'       => 'Pievienot piekļuves atslēgu',
        'name_label'       => 'Atslēgas nosaukums',
        'name_placeholder' => 'piem. MacBook Pro, iPhone',
        'password_label'   => 'Pašreizējā parole',
        'register_button'  => 'Izveidot piekļuves atslēgu',
        'cancel'           => 'Atcelt',
        'col_name'         => 'Nosaukums',
        'col_created'      => 'Izveidota',
        'col_actions'      => '',
        'delete'           => 'Noņemt',
        'confirm_delete'   => 'Vai tiešām vēlaties noņemt šo piekļuves atslēgu?',
        'no_passkeys'      => 'Nav reģistrētu piekļuves atslēgu. Pievienojiet vienu, lai iespējotu pieteikšanos bez paroles.',
        'owner_only'       => 'Piekļuves atslēgas var pievienot tikai konta īpašnieks (Mans konts). Šeit varat atsaukt esošās piekļuves atslēgas.',
    ],
    'messages' => [
        'registered' => 'Piekļuves atslēga veiksmīgi reģistrēta.',
        'deleted'    => 'Piekļuves atslēga noņemta.',
    ],
    'errors' => [
        'login_failed'        => 'Autentifikācija ar piekļuves atslēgu neizdevās.',
        'too_many_attempts'   => 'Pārāk daudz mēģinājumu. Lūdzu, uzgaidiet minūti un mēģiniet vēlreiz.',
        'registration_failed' => 'Piekļuves atslēgas reģistrācija neizdevās. Lūdzu, mēģiniet vēlreiz.',
        'own_account_only'    => 'Piekļuves atslēgas var reģistrēt tikai savam kontam.',
        'impersonating'       => 'Piekļuves atslēgas nevar reģistrēt, darbojoties cita lietotāja vārdā.',
        'access_denied'       => 'Piekļuve liegta.',
        'not_found'           => 'Piekļuves atslēga nav atrasta.',
        'wrong_password'      => 'Pašreizējā parole ir nepareiza.',
        'unexpected'          => 'Radās kļūda. Lūdzu, mēģiniet vēlreiz.',
    ],
];
