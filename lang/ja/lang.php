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
        'password_label'   => '現在のパスワード',
        'register_button'  => 'パスキーを作成',
        'cancel'           => 'キャンセル',
        'col_name'         => '名前',
        'col_created'      => '作成日',
        'col_actions'      => '',
        'delete'           => '削除',
        'confirm_delete'   => 'このパスキーを削除してもよろしいですか？',
        'no_passkeys'      => 'パスキーはまだ登録されていません。パスワードなしでログインするには、パスキーを追加してください。',
        'owner_only'       => 'パスキーはアカウント所有者のみが追加できます（マイアカウント）。ここでは既存のパスキーを取り消すことができます。',
    ],
    'messages' => [
        'registered' => 'パスキーが正常に登録されました。',
        'deleted'    => 'パスキーが削除されました。',
    ],
    'errors' => [
        'login_failed'        => 'パスキー認証に失敗しました。',
        'too_many_attempts'   => '試行回数が多すぎます。1分ほど待ってから再試行してください。',
        'registration_failed' => 'パスキーの登録に失敗しました。もう一度お試しください。',
        'own_account_only'    => 'パスキーは自分のアカウントにのみ登録できます。',
        'impersonating'       => '他のユーザーになりすましている間はパスキーを登録できません。',
        'access_denied'       => 'アクセスが拒否されました。',
        'not_found'           => 'パスキーが見つかりません。',
        'wrong_password'      => '現在のパスワードが正しくありません。',
        'unexpected'          => '問題が発生しました。もう一度お試しください。',
    ],
];
