<?php

namespace App\Services;

use App\Models\WeatherData;

/**
 * Interface OpenMeteoInterface
 *
 * Définit le contrat pour tout client API météo.
 * En utilisant cette interface, nous pouvons facilement remplacer le client API
 * (par exemple, passer d'un client mocké à un client réel) sans affecter
 * les services qui l'utilisent, comme le CycloneRiskEvaluator.
 */
interface OpenMeteoInterface
{
    /**
     * Récupère les données météorologiques pour une latitude et une longitude données.
     *
     * @param float $latitude
     * @param float $longitude
     * @return WeatherData|null Un objet WeatherData si succès, sinon null.
     */
    public function getWeatherData(float $latitude, float $longitude): ?WeatherData;
}
