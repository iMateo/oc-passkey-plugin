<?php

return [
    'plugin' => [
        'name'        => 'Passkey Kimlik Doğrulama',
        'description' => 'Yönetim paneli girişine WebAuthn/Passkey kimlik doğrulaması ekler.',
    ],
    'tab' => [
        'passkeys' => 'Geçiş anahtarları',
    ],
    'login' => [
        'divider' => 'veya',
        'button'  => 'Passkey ile giriş yap',
    ],
    'manage' => [
        'description'      => 'Geçiş anahtarları, parola yerine parmak izi, yüz tanıma veya ekran kilidi kullanarak güvenli bir şekilde oturum açmanızı sağlar.',
        'add_button'       => 'Geçiş anahtarı ekle',
        'name_label'       => 'Anahtar adı',
        'name_placeholder' => 'örn. MacBook Pro, iPhone',
        'register_button'  => 'Geçiş anahtarı oluştur',
        'cancel'           => 'İptal',
        'col_name'         => 'Ad',
        'col_created'      => 'Oluşturulma',
        'col_actions'      => '',
        'delete'           => 'Kaldır',
        'confirm_delete'   => 'Bu geçiş anahtarını kaldırmak istediğinizden emin misiniz?',
        'no_passkeys'      => 'Henüz geçiş anahtarı kaydedilmedi. Parolasız giriş için bir tane ekleyin.',
    ],
    'messages' => [
        'registered' => 'Geçiş anahtarı başarıyla kaydedildi.',
        'deleted'    => 'Geçiş anahtarı kaldırıldı.',
    ],
];
