<?php

namespace Tests;

use App\Services\Logger;
use App\Config;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    private string $tempLogFile;

    protected function setUp(): void
    {
        // Utiliser un fichier de log temporaire pour les tests
        $this->tempLogFile = sys_get_temp_dir() . '/test_app_log_' . uniqid() . '.log';
        
        // On "triche" un peu en réécrivant la config à la volée pour le test
        // C'est une simplification. Dans une grosse app, on utiliserait une injection de dépendance.
        $GLOBALS['mock_config'] = [
            'LOG_ENABLED' => true,
            'LOG_FILEPATH' => $this->tempLogFile
        ];

        // "Monkey-patch" la classe Config pour utiliser notre mock
        // C'est une technique de test avancée mais utile ici.
        \Closure::bind(function () {
            self::$config = $GLOBALS['mock_config'];
        }, null, Config::class)();
    }

    protected function tearDown(): void
    {
        // Nettoyer le fichier de log temporaire après chaque test
        if (file_exists($this->tempLogFile)) {
            unlink($this->tempLogFile);
        }
        unset($GLOBALS['mock_config']);
        // Réinitialiser la config
        \Closure::bind(function () {
            self::$config = null;
        }, null, Config::class)();
    }

    public function testInfoLogIsWrittenCorrectly()
    {
        $logger = new Logger();
        $message = 'This is an info message.';
        $logger->info($message);

        $this->assertFileExists($this->tempLogFile);

        $logContent = file_get_contents($this->tempLogFile);
        $this->assertStringContainsString('[INFO] ' . $message, $logContent);
        $this->assertMatchesRegularExpression('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $logContent);
    }
    
    public function testErrorLogIsWrittenCorrectly()
    {
        $logger = new Logger();
        $message = 'This is an error message.';
        $logger->error($message);

        $this->assertFileExists($this->tempLogFile);
        
        $logContent = file_get_contents($this->tempLogFile);
        $this->assertStringContainsString('[ERROR] ' . $message, $logContent);
    }

    public function testLogIsDisabled()
    {
        // Surcharger la configuration pour désactiver les logs
        $GLOBALS['mock_config']['LOG_ENABLED'] = false;
         \Closure::bind(function () {
            self::$config = $GLOBALS['mock_config'];
        }, null, Config::class)();

        $logger = new Logger();
        $logger->info('This should not be logged.');

        $this->assertFileDoesNotExist($this->tempLogFile);
    }
}
