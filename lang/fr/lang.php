<?php

return [
    'plugin' => [
        'name'        => 'Authentification Passkey',
        'description' => "Ajoute l'authentification WebAuthn/Passkey à la connexion du panneau d'administration.",
    ],
    'tab' => [
        'passkeys' => "Clés d'accès",
    ],
    'login' => [
        'divider' => 'ou',
        'button'  => 'Se connecter avec Passkey',
    ],
    'manage' => [
        'description'      => "Les clés d'accès vous permettent de vous connecter en toute sécurité avec votre empreinte digitale, votre visage ou le verrouillage de l'écran au lieu d'un mot de passe.",
        'add_button'       => "Ajouter une clé d'accès",
        'name_label'       => 'Nom de la clé',
        'name_placeholder' => 'ex. MacBook Pro, iPhone',
        'register_button'  => "Créer une clé d'accès",
        'cancel'           => 'Annuler',
        'col_name'         => 'Nom',
        'col_created'      => 'Créée',
        'col_actions'      => '',
        'delete'           => 'Supprimer',
        'confirm_delete'   => "Êtes-vous sûr de vouloir supprimer cette clé d'accès ?",
        'no_passkeys'      => "Aucune clé d'accès enregistrée. Ajoutez-en une pour activer la connexion sans mot de passe.",
    ],
    'messages' => [
        'registered' => "Clé d'accès enregistrée avec succès.",
        'deleted'    => "Clé d'accès supprimée.",
    ],
];
