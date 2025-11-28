<?php

namespace App\Services;

use App\Config;

/**
 * Classe simple de logging dans un fichier.
 */
class Logger
{
    private bool $isEnabled;
    private string $logFilePath;

    public function __construct()
    {
        $this->isEnabled = Config::get('LOG_ENABLED', false);
        $this->logFilePath = Config::get('LOG_FILEPATH');

        // Créer le répertoire de logs s'il n'existe pas
        if ($this->isEnabled && !file_exists(dirname($this->logFilePath))) {
            mkdir(dirname($this->logFilePath), 0777, true);
        }
    }

    /**
     * Log un message de niveau INFO.
     * @param string $message
     */
    public function info(string $message): void
    {
        $this->log('INFO', $message);
    }

    /**
     * Log un message de niveau WARNING.
     * @param string $message
     */
    public function warning(string $message): void
    {
        $this->log('WARNING', $message);
    }

    /**
     * Log un message de niveau ERROR.
     * @param string $message
     */
    public function error(string $message): void
    {
        $this->log('ERROR', $message);
    }

    /**
     * Écrit le message formaté dans le fichier de log.
     *
     * @param string $level
     * @param string $message
     */
    private function log(string $level, string $message): void
    {
        if (!$this->isEnabled || empty($this->logFilePath)) {
            return;
        }

        // Format du message : [YYYY-MM-DD HH:MM:SS] [LEVEL] Message
        $timestamp = date('Y-m-d H:i:s');
        $formattedMessage = sprintf("[%s] [%s] %s" . PHP_EOL, $timestamp, $level, $message);

        // FILE_APPEND pour ajouter à la fin du fichier, LOCK_EX pour éviter les écritures concurrentes
        file_put_contents($this->logFilePath, $formattedMessage, FILE_APPEND | LOCK_EX);
    }
}
