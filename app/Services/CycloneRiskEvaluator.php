<?php

namespace App\Services;

use App\Models\WeatherData;
use App\Services\OpenMeteoInterface;

/**
 * Class CycloneRiskEvaluator
 *
 * Contient la logique métier pour évaluer un risque cyclonique
 * à partir de données météorologiques.
 *
 * Il dépend de l'abstraction (OpenMeteoInterface) plutôt que de l'implémentation
 * concrète, ce qui le rend facile à tester et indépendant des détails de l'API.
 */
class CycloneRiskEvaluator
{
    private OpenMeteoInterface $meteoClient;

    public function __construct(OpenMeteoInterface $meteoClient)
    {
        $this->meteoClient = $meteoClient;
    }

    /**
     * Évalue le risque cyclonique pour une localisation donnée.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array Un tableau contenant le niveau de risque et un message.
     */
    public function evaluate(float $latitude, float $longitude): array
    {
        $weatherData = $this->meteoClient->getWeatherData($latitude, $longitude);

        if ($weatherData === null) {
            return [
                'risk' => 'Erreur',
                'message' => 'Impossible de récupérer les données météorologiques.'
            ];
        }

        // Logique de décision très simplifiée pour l'évaluation du risque.
        // Ces seuils sont des exemples et ne reflètent pas une réalité scientifique.
        $windSpeedThreshold = 120; // km/h
        $pressureThreshold = 1000; // hPa

        if ($weatherData->windSpeed >= $windSpeedThreshold && $weatherData->seaLevelPressure <= $pressureThreshold) {
            return [
                'risk' => 'Élevé',
                'message' => sprintf(
                    "Risque cyclonique ÉLEVÉ détecté. Vitesse du vent : %.1f km/h, Pression : %d hPa.",
                    $weatherData->windSpeed,
                    $weatherData->seaLevelPressure
                )
            ];
        }

        if ($weatherData->windSpeed >= 90) {
            return [
                'risk' => 'Modéré',
                'message' => sprintf(
                    "Conditions venteuses notables. Vitesse du vent : %.1f km/h.",
                    $weatherData->windSpeed
                )
            ];
        }

        return [
            'risk' => 'Faible',
            'message' => sprintf(
                "Conditions météorologiques normales. Vitesse du vent : %.1f km/h, Pression : %d hPa.",
                $weatherData->windSpeed,
                $weatherData->seaLevelPressure
            )
        ];
    }
}
