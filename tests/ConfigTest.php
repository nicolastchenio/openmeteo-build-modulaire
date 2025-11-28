<?php

namespace Tests;

use App\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testGetReturnsValueFromConfigFile()
    {
        // On suppose que config.php existe et contient cette clé
        $this->assertIsInt(Config::get('API_TIMEOUT_TOTAL'));
    }

    public function testGetReturnsDefaultValueForNonExistentKey()
    {
        $defaultValue = 'i_do_not_exist';
        $this->assertEquals($defaultValue, Config::get('NON_EXISTENT_KEY_12345', $defaultValue));
    }

    public function testGetReturnsNullForNonExistentKeyWithNoDefault()
    {
        $this->assertNull(Config::get('NON_EXISTENT_KEY_98765'));
    }
}
