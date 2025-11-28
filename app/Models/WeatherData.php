<?php

namespace App\Models;

/**
 * Class WeatherData
 *
 * Représente la structure des données météorologiques consolidées, fusionnant
 * les données atmosphériques et marines.
 *
 * Cette classe reçoit les tableaux de données brutes d'Open-Meteo et fournit
 * des méthodes pour accéder facilement à la valeur la plus récente.
 */
class WeatherData
{
    private array $atmosphericData;
    private array $marineData;

    /**
     * Construit l'objet de données météo.
     *
     * @param array $atmosphericData Données brutes de l'API forecast.
     * @param array $marineData Données brutes de l'API marine.
     */
    public function __construct(array $atmosphericData, array $marineData)
    {
        $this->atmosphericData = $atmosphericData['hourly'] ?? [];
        $this->marineData = $marineData['hourly'] ?? [];
    }

    /**
     * Méthode générique pour obtenir la dernière valeur non-nulle d'une série temporelle.
     *
     * @param array $dataArray Le tableau de données horaires (ex: $this->atmosphericData).
     * @param string $key La clé de la variable (ex: 'wind_speed_10m').
     * @return mixed|null La dernière valeur valide ou null si non trouvée.
     */
    private function getMostRecentValue(array $dataArray, string $key)
    {
        if (!isset($dataArray[$key]) || !is_array($dataArray[$key])) {
            return null;
        }
        // Parcourt le tableau à l'envers pour trouver la dernière valeur non-nulle
        $values = array_reverse($dataArray[$key]);
        foreach ($values as $value) {
            if ($value !== null) {
                return $value;
            }
        }
        return null;
    }

    // --- Accesseurs pour les données atmosphériques ---

    public function getWindSpeed(): ?float
    {
        return $this->getMostRecentValue($this->atmosphericData, 'wind_speed_10m');
    }

    public function getSeaLevelPressure(): ?float
    {
        return $this->getMostRecentValue($this->atmosphericData, 'pressure_msl');
    }

    public function getRelativeHumidity(): ?float
    {
        return $this->getMostRecentValue($this->atmosphericData, 'relative_humidity_2m');
    }
    
    public function getTemperature(): ?float
    {
        return $this->getMostRecentValue($this->atmosphericData, 'temperature_2m');
    }

    public function getWindDirection(): ?int
    {
        return $this->getMostRecentValue($this->atmosphericData, 'wind_direction_10m');
    }

    public function getPrecipitation(): ?float
    {
        return $this->getMostRecentValue($this->atmosphericData, 'precipitation');
    }

    // --- Accesseurs pour les données marines ---

    public function getWaveHeight(): ?float
    {
        return $this->getMostRecentValue($this->marineData, 'wave_height');
    }
    
    public function getSeaSurfaceTemperature(): ?float
    {
        return $this->getMostRecentValue($this->marineData, 'sea_surface_temperature');
    }
}
