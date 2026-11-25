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
        'password_label'   => 'Contraseña actual',
        'register_button'  => 'Crear clave de acceso',
        'cancel'           => 'Cancelar',
        'col_name'         => 'Nombre',
        'col_created'      => 'Creada',
        'col_actions'      => '',
        'delete'           => 'Eliminar',
        'confirm_delete'   => '¿Está seguro de que desea eliminar esta clave de acceso?',
        'no_passkeys'      => 'No hay claves de acceso registradas. Añada una para habilitar el inicio de sesión sin contraseña.',
        'owner_only'       => 'Solo el titular de la cuenta puede añadir claves de acceso (Mi cuenta). Aquí puede revocar las claves existentes.',
    ],
    'messages' => [
        'registered' => 'Clave de acceso registrada correctamente.',
        'deleted'    => 'Clave de acceso eliminada.',
    ],
    'errors' => [
        'login_failed'        => 'Error en la autenticación con clave de acceso.',
        'too_many_attempts'   => 'Demasiados intentos. Espere un minuto e inténtelo de nuevo.',
        'registration_failed' => 'No se pudo registrar la clave de acceso. Inténtelo de nuevo.',
        'own_account_only'    => 'Solo puede registrar claves de acceso para su propia cuenta.',
        'impersonating'       => 'No se pueden registrar claves de acceso mientras suplanta a otro usuario.',
        'access_denied'       => 'Acceso denegado.',
        'not_found'           => 'Clave de acceso no encontrada.',
        'wrong_password'      => 'La contraseña actual es incorrecta.',
        'unexpected'          => 'Algo salió mal. Inténtelo de nuevo.',
    ],
];
