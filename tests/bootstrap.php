<?php

// tests/bootstrap.php

// Ce fichier est exécuté avant que les tests ne commencent.

// On utilise le même autoloader que celui défini dans le point d'entrée du backend
// pour s'assurer que les classes de l'application sont correctement chargées.
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

