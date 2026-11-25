<?php

return [
    'plugin' => [
        'name'        => '패스키 인증',
        'description' => '백엔드 로그인에 WebAuthn/패스키 인증을 추가합니다.',
    ],
    'tab' => [
        'passkeys' => '패스키',
    ],
    'login' => [
        'divider' => '또는',
        'button'  => '패스키로 로그인',
    ],
    'manage' => [
        'description'      => '패스키를 사용하면 비밀번호 대신 지문, 얼굴 인식 또는 화면 잠금으로 안전하게 로그인할 수 있습니다.',
        'add_button'       => '패스키 추가',
        'name_label'       => '패스키 이름',
        'name_placeholder' => '예: MacBook Pro, iPhone',
        'password_label'   => '현재 비밀번호',
        'register_button'  => '패스키 생성',
        'cancel'           => '취소',
        'col_name'         => '이름',
        'col_created'      => '생성일',
        'col_actions'      => '',
        'delete'           => '삭제',
        'confirm_delete'   => '이 패스키를 삭제하시겠습니까?',
        'no_passkeys'      => '등록된 패스키가 없습니다. 비밀번호 없이 로그인하려면 패스키를 추가하세요.',
        'owner_only'       => '패스키는 계정 소유자만 추가할 수 있습니다(내 계정). 여기에서 기존 패스키를 취소할 수 있습니다.',
    ],
    'messages' => [
        'registered' => '패스키가 성공적으로 등록되었습니다.',
        'deleted'    => '패스키가 삭제되었습니다.',
    ],
    'errors' => [
        'login_failed'        => '패스키 인증에 실패했습니다.',
        'too_many_attempts'   => '시도 횟수가 너무 많습니다. 1분 후에 다시 시도하세요.',
        'registration_failed' => '패스키 등록에 실패했습니다. 다시 시도하세요.',
        'own_account_only'    => '패스키는 본인 계정에만 등록할 수 있습니다.',
        'impersonating'       => '다른 사용자로 가장하는 동안에는 패스키를 등록할 수 없습니다.',
        'access_denied'       => '접근이 거부되었습니다.',
        'not_found'           => '패스키를 찾을 수 없습니다.',
        'wrong_password'      => '현재 비밀번호가 올바르지 않습니다.',
        'unexpected'          => '문제가 발생했습니다. 다시 시도하세요.',
    ],
];
