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
        'password_label'   => 'Mevcut parola',
        'register_button'  => 'Geçiş anahtarı oluştur',
        'cancel'           => 'İptal',
        'col_name'         => 'Ad',
        'col_created'      => 'Oluşturulma',
        'col_actions'      => '',
        'delete'           => 'Kaldır',
        'confirm_delete'   => 'Bu geçiş anahtarını kaldırmak istediğinizden emin misiniz?',
        'no_passkeys'      => 'Henüz geçiş anahtarı kaydedilmedi. Parolasız giriş için bir tane ekleyin.',
        'owner_only'       => 'Geçiş anahtarlarını yalnızca hesap sahibi ekleyebilir (Hesabım). Mevcut geçiş anahtarlarını buradan iptal edebilirsiniz.',
    ],
    'messages' => [
        'registered' => 'Geçiş anahtarı başarıyla kaydedildi.',
        'deleted'    => 'Geçiş anahtarı kaldırıldı.',
    ],
    'errors' => [
        'login_failed'        => 'Geçiş anahtarı ile kimlik doğrulama başarısız oldu.',
        'too_many_attempts'   => 'Çok fazla deneme. Lütfen bir dakika bekleyip tekrar deneyin.',
        'registration_failed' => 'Geçiş anahtarı kaydedilemedi. Lütfen tekrar deneyin.',
        'own_account_only'    => 'Geçiş anahtarları yalnızca kendi hesabınız için kaydedilebilir.',
        'impersonating'       => 'Başka bir kullanıcının yerine geçerken geçiş anahtarı kaydedilemez.',
        'access_denied'       => 'Erişim reddedildi.',
        'not_found'           => 'Geçiş anahtarı bulunamadı.',
        'wrong_password'      => 'Mevcut parola yanlış.',
        'unexpected'          => 'Bir şeyler ters gitti. Lütfen tekrar deneyin.',
    ],
];
