<?php

namespace App\Services;

use App\Models\WeatherData;
use App\Services\OpenMeteoInterface;

/**
 * Class OpenMeteoClient
 *
 * Squelette du client API pour Open-Meteo.
 *
 * IMPORTANT : Cette implémentation est une simulation (mock).
 * Elle implémente OpenMeteoInterface mais ne réalise AUCUN appel HTTP réel.
 * Elle retourne des données factices pour permettre au reste de l'application
 * de fonctionner en attendant l'implémentation finale.
 *
 * La future implémentation utilisera cURL pour effectuer les requêtes HTTP.
 */
class OpenMeteoClient implements OpenMeteoInterface
{
    private const API_URL = 'https://api.open-meteo.com/v1/forecast';

    /**
     * Récupère les données météorologiques pour une latitude et une longitude données.
     *
     * @param float $latitude
     * @param float $longitude
     * @return WeatherData|null
     */
    public function getWeatherData(float $latitude, float $longitude): ?WeatherData
    {
        // === SECTION DE SIMULATION (MOCK) ===
        // Aucune requête cURL n'est effectuée ici.
        // Nous retournons un jeu de données factices pour simuler une réponse réussie.
        // La logique réelle de l'appel API sera implémentée plus tard.

        // Logique de simulation : retourne des données différentes si la latitude est "spéciale"
        if ($latitude === 13.37) {
            // Simule des conditions de risque élevé
            return new WeatherData(
                windSpeed: 180.5,
                seaLevelPressure: 980,
                relativeHumidity: 85
            );
        }

        // Simule des conditions normales
        return new WeatherData(
            windSpeed: 25.0,
            seaLevelPressure: 1012,
            relativeHumidity: 65
        );

        // === FUTURE IMPLÉMENTATION AVEC cURL (à ne pas décommenter maintenant) ===
        /*
        $queryParams = http_build_query([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'current' => 'wind_speed_10m,pressure_msl,relative_humidity_2m',
            'wind_speed_unit' => 'kmh'
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL . '?' . $queryParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || $response === false) {
            return null;
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['current'])) {
            return null;
        }

        return new WeatherData(
            windSpeed: $data['current']['wind_speed_10m'],
            seaLevelPressure: $data['current']['pressure_msl'],
            relativeHumidity: $data['current']['relative_humidity_2m']
        );
        */
    }
}
