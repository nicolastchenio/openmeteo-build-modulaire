<?php

namespace Tests;

use App\Models\WeatherData;
use App\Services\CycloneRiskEvaluator;
use PHPUnit\Framework\TestCase;

/**
 * Class CycloneRiskEvaluatorTest
 *
 * Teste la logique métier de l'évaluateur de risque avec un objet WeatherData pré-rempli.
 * Cela permet de tester la logique de décision en isolation complète.
 */
class CycloneRiskEvaluatorTest extends TestCase
{
    private CycloneRiskEvaluator $evaluator;

    protected function setUp(): void
    {
        $this->evaluator = new CycloneRiskEvaluator();
    }

    /**
     * Crée un objet WeatherData à partir de données simulées.
     * @param array $atmosphericHourly
     * @param array $marineHourly
     * @return WeatherData
     */
    private function createWeatherData(array $atmosphericHourly, array $marineHourly = []): WeatherData
    {
        $atmosphericData = ['hourly' => $atmosphericHourly];
        $marineData = ['hourly' => $marineHourly];
        return new WeatherData($atmosphericData, $marineData);
    }

    public function testEvaluateReturnsHighRisk()
    {
        $weatherData = $this->createWeatherData(
            [
                'wind_speed_10m' => [150.0],
                'pressure_msl' => [990.0],
            ],
            [
                'wave_height' => [8.0] // vagues dangereuses
            ]
        );

        $result = $this->evaluator->evaluate($weatherData);

        $this->assertEquals('Élevé', $result['risk']);
    }

    public function testEvaluateReturnsModerateRisk()
    {
        $weatherData = $this->createWeatherData(
            [
                'wind_speed_10m' => [100.0],
                'pressure_msl' => [1010.0],
            ]
        );

        $result = $this->evaluator->evaluate($weatherData);

        $this->assertEquals('Modéré', $result['risk']);
    }

    public function testEvaluateReturnsLowRisk()
    {
        $weatherData = $this->createWeatherData(
            [
                'wind_speed_10m' => [30.0],
                'pressure_msl' => [1015.0],
            ]
        );

        $result = $this->evaluator->evaluate($weatherData);

        $this->assertEquals('Faible', $result['risk']);
    }

    public function testEvaluateReturnsIndeterminateRiskOnMissingData()
    {
        // Test avec des données de vent manquantes
        $weatherData = $this->createWeatherData(
            [
                'pressure_msl' => [1015.0],
            ]
        );

        $result = $this->evaluator->evaluate($weatherData);

        $this->assertEquals('Indéterminé', $result['risk']);
        $this->assertEquals('Données de vent ou de pression atmosphérique insuffisantes pour une évaluation.', $result['message']);
    }
}
