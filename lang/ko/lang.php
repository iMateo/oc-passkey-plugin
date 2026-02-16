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
        'register_button'  => '패스키 생성',
        'cancel'           => '취소',
        'col_name'         => '이름',
        'col_created'      => '생성일',
        'col_actions'      => '',
        'delete'           => '삭제',
        'confirm_delete'   => '이 패스키를 삭제하시겠습니까?',
        'no_passkeys'      => '등록된 패스키가 없습니다. 비밀번호 없이 로그인하려면 패스키를 추가하세요.',
    ],
    'messages' => [
        'registered' => '패스키가 성공적으로 등록되었습니다.',
        'deleted'    => '패스키가 삭제되었습니다.',
    ],
];
