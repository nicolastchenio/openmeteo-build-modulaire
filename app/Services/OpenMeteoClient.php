<?php

namespace App\Services;

use App\Exceptions\NetworkException;
use App\Exceptions\InvalidResponseException;

/**
 * Class OpenMeteoClient
 *
 * Client API réel pour Open-Meteo.
 * Ce client effectue de vrais appels HTTP en utilisant cURL et gère les erreurs.
 */
class OpenMeteoClient
{
    private const ATMOSPHERIC_API_URL = 'https://api.open-meteo.com/v1/forecast';
    private const MARINE_API_URL = 'https://marine-api.open-meteo.com/v1/marine';
    private const CURL_TIMEOUT = 10; // en secondes

    /**
     * Exécute une requête cURL et retourne le corps de la réponse décodé.
     *
     * @param string $url L'URL complète à appeler.
     * @return array Le corps de la réponse JSON décodé en tableau PHP.
     * @throws NetworkException Si une erreur cURL survient ou si le code de statut n'est pas 200.
     * @throws InvalidResponseException Si la réponse n'est pas un JSON valide ou est vide.
     */
    private function executeCurlRequest(string $url): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true, // Retourne la réponse au lieu de l'afficher
            CURLOPT_TIMEOUT => self::CURL_TIMEOUT,
            CURLOPT_FAILONERROR => true, // Échoue si le code HTTP est >= 400

            // --- SOLUTION SIMPLE MAIS NON SÉCURISÉE ---
            // Ces deux lignes désactivent la vérification du certificat SSL.
            // À n'utiliser que pour le développement local si vous ne pouvez pas configurer php.ini.
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new NetworkException("Erreur cURL : " . $error_msg);
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new NetworkException("L'API a retourné un code de statut non-200 : " . $httpCode);
        }

        if ($response === false || $response === '') {
            throw new InvalidResponseException("La réponse de l'API était vide.");
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidResponseException("Impossible de décoder la réponse JSON. Erreur : " . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Récupère les données météorologiques atmosphériques.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array
     * @throws NetworkException
     * @throws InvalidResponseException
     */
    public function fetchAtmosphericData(float $latitude, float $longitude): array
    {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'elevation' => 0,
            'forecast_days' => 4,
            'timezone' => 'auto',
            'hourly' => 'temperature_2m,relative_humidity_2m,pressure_msl,wind_speed_10m,wind_direction_10m,precipitation'
        ];

        $url = self::ATMOSPHERIC_API_URL . '?' . http_build_query($params);
        return $this->executeCurlRequest($url);
    }

    /**
     * Récupère les données météorologiques marines.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array
     * @throws NetworkException
     * @throws InvalidResponseException
     */
    public function fetchMarineData(float $latitude, float $longitude): array
    {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'elevation' => 0,
            'forecast_days' => 4,
            'timezone' => 'auto',
            'hourly' => 'sea_surface_temperature,wave_height'
        ];

        $url = self::MARINE_API_URL . '?' . http_build_query($params);
        return $this->executeCurlRequest($url);
    }
}
