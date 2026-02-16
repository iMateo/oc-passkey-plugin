<?php

return [
    'plugin' => [
        'name'        => 'パスキー認証',
        'description' => 'バックエンドログインにWebAuthn/パスキー認証を追加します。',
    ],
    'tab' => [
        'passkeys' => 'パスキー',
    ],
    'login' => [
        'divider' => 'または',
        'button'  => 'パスキーでサインイン',
    ],
    'manage' => [
        'description'      => 'パスキーを使用すると、パスワードの代わりに指紋、顔認証、または画面ロックで安全にサインインできます。',
        'add_button'       => 'パスキーを追加',
        'name_label'       => 'パスキー名',
        'name_placeholder' => '例: MacBook Pro、iPhone',
        'register_button'  => 'パスキーを作成',
        'cancel'           => 'キャンセル',
        'col_name'         => '名前',
        'col_created'      => '作成日',
        'col_actions'      => '',
        'delete'           => '削除',
        'confirm_delete'   => 'このパスキーを削除してもよろしいですか？',
        'no_passkeys'      => 'パスキーはまだ登録されていません。パスワードなしでログインするには、パスキーを追加してください。',
    ],
    'messages' => [
        'registered' => 'パスキーが正常に登録されました。',
        'deleted'    => 'パスキーが削除されました。',
    ],
];
