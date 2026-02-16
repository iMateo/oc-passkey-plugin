<?php

return [
    'plugin' => [
        'name'        => 'Autenticação Passkey',
        'description' => 'Adiciona autenticação WebAuthn/Passkey ao login do painel administrativo.',
    ],
    'tab' => [
        'passkeys' => 'Chaves de acesso',
    ],
    'login' => [
        'divider' => 'ou',
        'button'  => 'Entrar com Passkey',
    ],
    'manage' => [
        'description'      => 'As chaves de acesso permitem que você faça login com segurança usando sua impressão digital, rosto ou bloqueio de tela em vez de uma senha.',
        'add_button'       => 'Adicionar chave de acesso',
        'name_label'       => 'Nome da chave',
        'name_placeholder' => 'ex. MacBook Pro, iPhone',
        'register_button'  => 'Criar chave de acesso',
        'cancel'           => 'Cancelar',
        'col_name'         => 'Nome',
        'col_created'      => 'Criada',
        'col_actions'      => '',
        'delete'           => 'Remover',
        'confirm_delete'   => 'Tem certeza de que deseja remover esta chave de acesso?',
        'no_passkeys'      => 'Nenhuma chave de acesso registrada. Adicione uma para habilitar o login sem senha.',
    ],
    'messages' => [
        'registered' => 'Chave de acesso registrada com sucesso.',
        'deleted'    => 'Chave de acesso removida.',
    ],
];
