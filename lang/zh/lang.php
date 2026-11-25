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
        'password_label'   => '当前密码',
        'register_button'  => '创建通行密钥',
        'cancel'           => '取消',
        'col_name'         => '名称',
        'col_created'      => '创建时间',
        'col_actions'      => '',
        'delete'           => '删除',
        'confirm_delete'   => '您确定要删除此通行密钥吗？',
        'no_passkeys'      => '尚未注册通行密钥。添加一个以启用无密码登录。',
        'owner_only'       => '通行密钥只能由账户所有者添加（我的账户）。您可以在此撤销现有的通行密钥。',
    ],
    'messages' => [
        'registered' => '通行密钥注册成功。',
        'deleted'    => '通行密钥已删除。',
    ],
    'errors' => [
        'login_failed'        => '通行密钥认证失败。',
        'too_many_attempts'   => '尝试次数过多。请等待一分钟后重试。',
        'registration_failed' => '通行密钥注册失败。请重试。',
        'own_account_only'    => '只能为您自己的账户注册通行密钥。',
        'impersonating'       => '模拟其他用户时无法注册通行密钥。',
        'access_denied'       => '拒绝访问。',
        'not_found'           => '未找到通行密钥。',
        'wrong_password'      => '当前密码不正确。',
        'unexpected'          => '出现错误，请重试。',
    ],
];
