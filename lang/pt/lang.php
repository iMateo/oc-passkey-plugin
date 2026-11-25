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
        'password_label'   => 'Senha atual',
        'register_button'  => 'Criar chave de acesso',
        'cancel'           => 'Cancelar',
        'col_name'         => 'Nome',
        'col_created'      => 'Criada',
        'col_actions'      => '',
        'delete'           => 'Remover',
        'confirm_delete'   => 'Tem certeza de que deseja remover esta chave de acesso?',
        'no_passkeys'      => 'Nenhuma chave de acesso registrada. Adicione uma para habilitar o login sem senha.',
        'owner_only'       => 'Apenas o titular da conta pode adicionar chaves de acesso (Minha conta). Aqui você pode revogar as chaves existentes.',
    ],
    'messages' => [
        'registered' => 'Chave de acesso registrada com sucesso.',
        'deleted'    => 'Chave de acesso removida.',
    ],
    'errors' => [
        'login_failed'        => 'Falha na autenticação com chave de acesso.',
        'too_many_attempts'   => 'Muitas tentativas. Aguarde um minuto e tente novamente.',
        'registration_failed' => 'Falha ao registrar a chave de acesso. Tente novamente.',
        'own_account_only'    => 'Chaves de acesso só podem ser registradas para a sua própria conta.',
        'impersonating'       => 'Não é possível registrar chaves de acesso enquanto personifica outro usuário.',
        'access_denied'       => 'Acesso negado.',
        'not_found'           => 'Chave de acesso não encontrada.',
        'wrong_password'      => 'A senha atual está incorreta.',
        'unexpected'          => 'Algo deu errado. Tente novamente.',
    ],
];
