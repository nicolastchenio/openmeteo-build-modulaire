<?php
// Affiche les erreurs pour le développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autoloader pour les classes de l'application (version robuste)
spl_autoload_register(function ($class) {
    $prefix = 'App\\'; // Corrigé : le backslash est échappé
    // Utilise la constante DIRECTORY_SEPARATOR pour la compatibilité Windows/Linux
    $base_dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR;
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    // Remplace les séparateurs de namespace par les séparateurs de répertoire OS
    $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// --- ROUTEUR ROBUSTE ---
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Route pour l'API
if ($requestPath === '/api/cyclone-risk') {
    try {
        $controller = new App\Controllers\ApiController();
        $controller->handleCycloneRiskRequest();
    } catch (\Throwable $e) {
        // En cas d'erreur dans le contrôleur, on renvoie un JSON propre
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Erreur interne du serveur.', 'details' => $e->getMessage()]);
    }
    exit;
}

// Route pour servir les fichiers statiques du frontend (Logique sans realpath)
$baseFrontendPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'frontend';
// Si la requête est pour la racine, on sert index.html
$requestedFile = $requestPath === '/' ? DIRECTORY_SEPARATOR . 'index.html' : $requestPath;

// Sécurité : Vérifier que le chemin ne contient pas de ".." pour éviter la traversée de répertoire
if (strpos($requestedFile, '..') !== false) {
    http_response_code(400);
    die('Chemin invalide.');
}

$filePath = $baseFrontendPath . $requestedFile;

if (file_exists($filePath) && is_file($filePath)) {
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimeTypes = [
        'html' => 'text/html; charset=utf-8',
        'css' => 'text/css',
        'js' => 'application/javascript',
    ];
    $contentType = $mimeTypes[$extension] ?? 'text/plain';

    header('Content-Type: ' . $contentType);
    readfile($filePath); // Sert le fichier
    exit;
}

// Si aucune route ne correspond, renvoyer une erreur 404
http_response_code(404);
echo "404 Not Found : La ressource '$requestPath' est introuvable.";