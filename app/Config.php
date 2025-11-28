<?php

namespace App;

/**
 * Classe utilitaire statique pour accéder à la configuration.
 * Elle charge le fichier de configuration une seule fois (design pattern Singleton/Registry).
 */
class Config
{
    private static ?array $config = null;

    /**
     * Récupère une valeur de configuration.
     *
     * @param string $key La clé de configuration.
     * @param mixed $default La valeur par défaut si la clé n'est pas trouvée.
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        // Charger la configuration si ce n'est pas déjà fait
        if (self::$config === null) {
            $configPath = __DIR__ . '/../config.php';
            if (!file_exists($configPath)) {
                // En cas d'absence du fichier, on retourne les valeurs par défaut
                // pour éviter de planter l'application.
                return $default;
            }
            self::$config = require $configPath;
        }

        return self::$config[$key] ?? $default;
    }
}
