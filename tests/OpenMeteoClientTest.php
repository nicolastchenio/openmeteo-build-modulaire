<?php

namespace Tests;

use App\Models\WeatherData;
use App\Services\OpenMeteoClient;
use PHPUnit\Framework\TestCase;

/**
 * Class OpenMeteoClientTest
 *
 * Teste le client API *simulé*.
 * Ces tests vérifient que le client retourne bien des données factices
 * conformes au contrat, sans faire d'appel réseau.
 */
class OpenMeteoClientTest extends TestCase
{
    public function testClientCanBeInstantiated()
    {
        $client = new OpenMeteoClient();
        $this->assertInstanceOf(OpenMeteoClient::class, $client);
    }

    public function testGetWeatherDataReturnsWeatherDataObject()
    {
        $client = new OpenMeteoClient();
        $data = $client->getWeatherData(45.0, 5.0);

        // Vérifie que la méthode retourne bien une instance de WeatherData
        $this->assertInstanceOf(WeatherData::class, $data);

        // Vérifie que les propriétés de l'objet ne sont pas vides
        $this->assertIsFloat($data->windSpeed);
        $this->assertIsFloat($data->seaLevelPressure);
        $this->assertIsInt($data->relativeHumidity);
    }
}
