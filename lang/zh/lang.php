<?php

return [
    'plugin' => [
        'name'        => '通行密钥认证',
        'description' => '为后台登录添加 WebAuthn/Passkey 认证。',
    ],
    'tab' => [
        'passkeys' => '通行密钥',
    ],
    'login' => [
        'divider' => '或',
        'button'  => '使用通行密钥登录',
    ],
    'manage' => [
        'description'      => '通行密钥让您可以使用指纹、面部识别或屏幕锁定安全登录，无需密码。',
        'add_button'       => '添加通行密钥',
        'name_label'       => '密钥名称',
        'name_placeholder' => '例如 MacBook Pro、iPhone',
        'register_button'  => '创建通行密钥',
        'cancel'           => '取消',
        'col_name'         => '名称',
        'col_created'      => '创建时间',
        'col_actions'      => '',
        'delete'           => '删除',
        'confirm_delete'   => '您确定要删除此通行密钥吗？',
        'no_passkeys'      => '尚未注册通行密钥。添加一个以启用无密码登录。',
    ],
    'messages' => [
        'registered' => '通行密钥注册成功。',
        'deleted'    => '通行密钥已删除。',
    ],
];
