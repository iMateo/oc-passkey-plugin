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
        'password_label'   => 'Mot de passe actuel',
        'register_button'  => "Créer une clé d'accès",
        'cancel'           => 'Annuler',
        'col_name'         => 'Nom',
        'col_created'      => 'Créée',
        'col_actions'      => '',
        'delete'           => 'Supprimer',
        'confirm_delete'   => "Êtes-vous sûr de vouloir supprimer cette clé d'accès ?",
        'no_passkeys'      => "Aucune clé d'accès enregistrée. Ajoutez-en une pour activer la connexion sans mot de passe.",
        'owner_only'       => 'Seul le titulaire du compte peut ajouter des clés d\'accès (Mon compte). Vous pouvez révoquer ici les clés existantes.',
    ],
    'messages' => [
        'registered' => "Clé d'accès enregistrée avec succès.",
        'deleted'    => "Clé d'accès supprimée.",
    ],
    'errors' => [
        'login_failed'        => 'Échec de l\'authentification par clé d\'accès.',
        'too_many_attempts'   => 'Trop de tentatives. Veuillez patienter une minute puis réessayer.',
        'registration_failed' => 'Échec de l\'enregistrement de la clé d\'accès. Veuillez réessayer.',
        'own_account_only'    => 'Vous ne pouvez enregistrer des clés d\'accès que pour votre propre compte.',
        'impersonating'       => 'Impossible d\'enregistrer une clé d\'accès en usurpant l\'identité d\'un autre utilisateur.',
        'access_denied'       => 'Accès refusé.',
        'not_found'           => 'Clé d\'accès introuvable.',
        'wrong_password'      => 'Le mot de passe actuel est incorrect.',
        'unexpected'          => 'Une erreur s\'est produite. Veuillez réessayer.',
    ],
];
