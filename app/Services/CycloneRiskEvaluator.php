<?php

namespace App\Services;

use App\Models\WeatherData;

/**
 * Class CycloneRiskEvaluator
 *
 * Contient la logique métier pour évaluer un risque cyclonique.
 * Cette classe est maintenant découplée de la récupération des données et
 * opère directement sur un objet WeatherData entièrement hydraté.
 */
class CycloneRiskEvaluator
{
    /**
     * Évalue le risque cyclonique à partir d'un objet de données météo.
     *
     * @param WeatherData $weatherData Les données météo consolidées.
     * @return array Un tableau contenant le niveau de risque et un message.
     */
    public function evaluate(WeatherData $weatherData): array
    {
        $windSpeed = $weatherData->getWindSpeed();
        $pressure = $weatherData->getSeaLevelPressure();
        $waveHeight = $weatherData->getWaveHeight();

        // Si les données de base ne sont pas disponibles, on ne peut rien évaluer.
        if ($windSpeed === null || $pressure === null) {
            return [
                'risk' => 'Indéterminé',
                'message' => 'Données de vent ou de pression atmosphérique insuffisantes pour une évaluation.'
            ];
        }

        // Logique de décision améliorée incluant la hauteur des vagues.
        // Ces seuils sont des exemples et ne reflètent pas une réalité scientifique.
        $highWindThreshold = 120; // km/h
        $lowPressureThreshold = 1000; // hPa
        $highWaveThreshold = 6; // mètres

        $riskFactors = [];
        if ($windSpeed >= $highWindThreshold) $riskFactors[] = 'vent extrême';
        if ($pressure <= $lowPressureThreshold) $riskFactors[] = 'basse pression';
        if ($waveHeight !== null && $waveHeight >= $highWaveThreshold) $riskFactors[] = 'vagues dangereuses';

        if (count($riskFactors) >= 2) {
            return [
                'risk' => 'Élevé',
                'message' => sprintf(
                    "Risque cyclonique ÉLEVÉ. Vitesse du vent: %.1f km/h, Pression: %d hPa, Vagues: %.1f m. Facteurs: %s.",
                    $windSpeed,
                    $pressure,
                    $waveHeight ?? 0,
                    implode(', ', $riskFactors)
                )
            ];
        }

        if ($windSpeed >= 90 || ($waveHeight !== null && $waveHeight >= 4)) {
            return [
                'risk' => 'Modéré',
                'message' => sprintf(
                    "Conditions notables. Vitesse du vent: %.1f km/h, Vagues: %.1f m.",
                    $windSpeed,
                    $waveHeight ?? 0
                )
            ];
        }

        return [
            'risk' => 'Faible',
            'message' => sprintf(
                "Conditions météorologiques normales. Vitesse du vent: %.1f km/h, Pression: %d hPa.",
                $windSpeed,
                $pressure
            )
        ];
    }
}
