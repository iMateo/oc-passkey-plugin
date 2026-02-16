<?php

return [
    'plugin' => [
        'name'        => 'Autenticación Passkey',
        'description' => 'Añade autenticación WebAuthn/Passkey al inicio de sesión del panel de administración.',
    ],
    'tab' => [
        'passkeys' => 'Claves de acceso',
    ],
    'login' => [
        'divider' => 'o',
        'button'  => 'Iniciar sesión con Passkey',
    ],
    'manage' => [
        'description'      => 'Las claves de acceso le permiten iniciar sesión de forma segura con su huella digital, rostro o bloqueo de pantalla en lugar de una contraseña.',
        'add_button'       => 'Añadir clave de acceso',
        'name_label'       => 'Nombre de la clave',
        'name_placeholder' => 'ej. MacBook Pro, iPhone',
        'register_button'  => 'Crear clave de acceso',
        'cancel'           => 'Cancelar',
        'col_name'         => 'Nombre',
        'col_created'      => 'Creada',
        'col_actions'      => '',
        'delete'           => 'Eliminar',
        'confirm_delete'   => '¿Está seguro de que desea eliminar esta clave de acceso?',
        'no_passkeys'      => 'No hay claves de acceso registradas. Añada una para habilitar el inicio de sesión sin contraseña.',
    ],
    'messages' => [
        'registered' => 'Clave de acceso registrada correctamente.',
        'deleted'    => 'Clave de acceso eliminada.',
    ],
];
