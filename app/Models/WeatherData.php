<?php

namespace App\Models;

/**
 * Class WeatherData
 *
 * Représente la structure des données météorologiques consolidées.
 * Cet objet est utilisé par le CycloneRiskEvaluator pour effectuer ses calculs.
 * Il est conçu pour être simple et ne contenir que des propriétés publiques
 * pour un accès facile aux données.
 */
class WeatherData
{
    /**
     * @var float Vitesse du vent en km/h.
     */
    public float $windSpeed;

    /**
     * @var float Pression au niveau de la mer en hPa.
     */
    public float $seaLevelPressure;

    /**
     * @var int Humidité relative en pourcentage.
     */
    public int $relativeHumidity;

    public function __construct(float $windSpeed, float $seaLevelPressure, int $relativeHumidity)
    {
        $this->windSpeed = $windSpeed;
        $this->seaLevelPressure = $seaLevelPressure;
        $this->relativeHumidity = $relativeHumidity;
    }
}
