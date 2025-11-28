<?php

namespace App\Services;

use App\Config;
use App\Exceptions\NetworkException;
use App\Exceptions\InvalidResponseException;

/**
 * Class OpenMeteoClient
 *
 * Client API réel pour Open-Meteo, avec timeouts et logs configurables.
 */
class OpenMeteoClient
{
    private const ATMOSPHERIC_API_URL = 'https://api.open-meteo.com/v1/forecast';
    private const MARINE_API_URL = 'https://marine-api.open-meteo.com/v1/marine';
    
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

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
        $this->logger->info("Appel API vers : " . $url);
        $startTime = microtime(true);

        $ch = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true, // Échoue si le code HTTP est >= 400
            
            // Timeouts depuis la configuration
            CURLOPT_CONNECTTIMEOUT => Config::get('API_TIMEOUT_CONNECT', 5),
            CURLOPT_TIMEOUT => Config::get('API_TIMEOUT_TOTAL', 10),

            // Gestion SSL depuis la configuration
            CURLOPT_SSL_VERIFYPEER => Config::get('API_SSL_VERIFY', true),
            CURLOPT_SSL_VERIFYHOST => Config::get('API_SSL_VERIFY', true) ? 2 : 0,
        ];
        
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $duration = microtime(true) - $startTime;

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            $this->logger->error(sprintf("Erreur cURL (après %.2fs) : %s", $duration, $error_msg));
            throw new NetworkException("Erreur cURL : " . $error_msg);
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->logger->info(sprintf("Réponse API reçue de %s en %.2fs avec le code %d", $url, $duration, $httpCode));

        if ($httpCode !== 200) {
            $this->logger->error("L'API a retourné un code de statut non-200 : " . $httpCode);
            throw new NetworkException("L'API a retourné un code de statut non-200 : " . $httpCode);
        }

        if ($response === false || $response === '') {
            $this->logger->error("La réponse de l'API était vide.");
            throw new InvalidResponseException("La réponse de l'API était vide.");
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error("Impossible de décoder la réponse JSON. Erreur : " . json_last_error_msg());
            throw new InvalidResponseException("Impossible de décoder la réponse JSON. Erreur : " . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Récupère les données météorologiques atmosphériques.
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
