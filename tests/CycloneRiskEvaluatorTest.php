<?php

namespace Tests;

use App\Models\WeatherData;
use App\Services\CycloneRiskEvaluator;
use App\Services\OpenMeteoInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class CycloneRiskEvaluatorTest
 *
 * Teste la logique métier de l'évaluateur de risque.
 * L'élément clé ici est le mock de l'OpenMeteoInterface, qui nous permet
 * de tester l'évaluateur en isolation complète, sans dépendre du client API
 * (qu'il soit réel ou simulé).
 */
class CycloneRiskEvaluatorTest extends TestCase
{
    private $meteoClientMock;
    private CycloneRiskEvaluator $evaluator;

    protected function setUp(): void
    {
        // Crée un mock de l'interface OpenMeteoInterface.
        // C'est un objet factice qui se comportera comme nous le dictons.
        $this->meteoClientMock = $this->createMock(OpenMeteoInterface::class);

        // Injecte le mock dans le constructeur de l'évaluateur.
        $this->evaluator = new CycloneRiskEvaluator($this->meteoClientMock);
    }

    public function testEvaluateReturnsHighRisk()
    {
        // 1. Arrange: Définir le comportement du mock.
        // On simule des données météo correspondant à un risque élevé.
        $highRiskData = new WeatherData(
            windSpeed: 150.0,
            seaLevelPressure: 990,
            relativeHumidity: 90
        );

        // On dit au mock de retourner ces données quand sa méthode 'getWeatherData' est appelée.
        $this->meteoClientMock->method('getWeatherData')
             ->willReturn($highRiskData);

        // 2. Act: Appeler la méthode à tester.
        $result = $this->evaluator->evaluate(13.37, -59.5);

        // 3. Assert: Vérifier que le résultat est correct.
        $this->assertEquals('Élevé', $result['risk']);
        $this->assertStringContainsString('Risque cyclonique ÉLEVÉ détecté', $result['message']);
    }

    public function testEvaluateReturnsModerateRisk()
    {
        // Arrange: Simuler des données pour un risque modéré.
        $moderateRiskData = new WeatherData(
            windSpeed: 100.0,
            seaLevelPressure: 1010,
            relativeHumidity: 80
        );
        $this->meteoClientMock->method('getWeatherData')
             ->willReturn($moderateRiskData);

        // Act
        $result = $this->evaluator->evaluate(20.0, -70.0);

        // Assert
        $this->assertEquals('Modéré', $result['risk']);
    }

    public function testEvaluateReturnsLowRisk()
    {
        // Arrange: Simuler des données pour un risque faible.
        $lowRiskData = new WeatherData(
            windSpeed: 30.0,
            seaLevelPressure: 1015,
            relativeHumidity: 60
        );
        $this->meteoClientMock->method('getWeatherData')
             ->willReturn($lowRiskData);

        // Act
        $result = $this->evaluator->evaluate(48.85, 2.35);

        // Assert
        $this->assertEquals('Faible', $result['risk']);
    }

    public function testEvaluateHandlesApiFailure()
    {
        // Arrange: Simuler une défaillance de l'API (le client retourne null).
        $this->meteoClientMock->method('getWeatherData')
             ->willReturn(null);

        // Act
        $result = $this->evaluator->evaluate(0.0, 0.0);

        // Assert
        $this->assertEquals('Erreur', $result['risk']);
        $this->assertEquals('Impossible de récupérer les données météorologiques.', $result['message']);
    }
}
