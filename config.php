<?php

// Fichier de configuration principal de l'application.

return [
    // Configuration des logs
    'LOG_ENABLED' => true,
    'LOG_FILEPATH' => __DIR__ . '/logs/app.log',

    // Configuration des timeouts pour les appels API (en secondes)
    'API_TIMEOUT_CONNECT' => 5,  // Temps maximum pour établir la connexion
    'API_TIMEOUT_TOTAL' => 10, // Temps maximum pour la requête entière

    // Configuration de la vérification SSL (pour le développement local)
    // ATTENTION : Mettre à `false` est un risque de sécurité.
    // La solution correcte est de configurer `curl.cainfo` dans php.ini.
    'API_SSL_VERIFY' => false,
];
