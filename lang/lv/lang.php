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
        'register_button'  => 'Izveidot piekļuves atslēgu',
        'cancel'           => 'Atcelt',
        'col_name'         => 'Nosaukums',
        'col_created'      => 'Izveidota',
        'col_actions'      => '',
        'delete'           => 'Noņemt',
        'confirm_delete'   => 'Vai tiešām vēlaties noņemt šo piekļuves atslēgu?',
        'no_passkeys'      => 'Nav reģistrētu piekļuves atslēgu. Pievienojiet vienu, lai iespējotu pieteikšanos bez paroles.',
    ],
    'messages' => [
        'registered' => 'Piekļuves atslēga veiksmīgi reģistrēta.',
        'deleted'    => 'Piekļuves atslēga noņemta.',
    ],
];
