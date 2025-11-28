<?php

namespace App\Controllers;

use App\Models\WeatherData;
use App\Services\OpenMeteoClient;
use App\Services\CycloneRiskEvaluator;
use App\Services\Logger;
use App\Exceptions\NetworkException;
use App\Exceptions\InvalidResponseException;

/**
 * Class ApiController
 *
 * Gère les requêtes API entrantes, orchestre les appels aux services,
 * la création des modèles et retourne la réponse finale en JSON.
 */
class ApiController
{
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }
    
    /**
     * Point d'entrée pour l'analyse de risque cyclonique.
     */
    public function handleCycloneRiskRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        $jsonInput = file_get_contents('php://input');
        $data = json_decode($jsonInput, true);

        if (!isset($data['latitude']) || !isset($data['longitude']) || !is_numeric($data['latitude']) || !is_numeric($data['longitude'])) {
            $this->logger->warning("Requête invalide reçue.", ['input' => $data]);
            $this->sendJsonResponse(['error' => 'Données d\'entrée invalides. "latitude" et "longitude" numériques sont requis.'], 400);
            return;
        }

        $latitude = (float)$data['latitude'];
        $longitude = (float)$data['longitude'];
        
        $this->logger->info(sprintf("Nouvelle requête d'analyse pour lat: %f, lon: %f", $latitude, $longitude));

        try {
            // 1. Appeler le client API pour récupérer les données brutes
            $meteoClient = new OpenMeteoClient();
            $atmosphericData = $meteoClient->fetchAtmosphericData($latitude, $longitude);
            $marineData = $meteoClient->fetchMarineData($latitude, $longitude);

            // 2. Construire l'objet modèle avec les données fusionnées
            $weatherData = new WeatherData($atmosphericData, $marineData);

            // 3. Appeler le service métier avec le modèle de données
            $evaluator = new CycloneRiskEvaluator();
            $result = $evaluator->evaluate($weatherData);
            
            $this->logger->info(sprintf("Évaluation terminée pour lat: %f, lon: %f. Risque: %s", $latitude, $longitude, $result['risk']));

            // 4. Envoyer la réponse
            $this->sendJsonResponse($result);

        } catch (NetworkException $e) {
            $this->logger->error("Erreur Réseau lors de la requête pour lat: $latitude, lon: $longitude. " . $e->getMessage());
            $this->sendJsonResponse(['risk' => 'Erreur Réseau', 'message' => "Impossible de contacter l'API météo : " . $e->getMessage()], 503);
        } catch (InvalidResponseException $e) {
            $this->logger->error("Erreur de Données lors de la requête pour lat: $latitude, lon: $longitude. " . $e->getMessage());
            $this->sendJsonResponse(['risk' => 'Erreur Données', 'message' => "La réponse de l'API était invalide : " . $e->getMessage()], 502);
        } catch (\Exception $e) {
            $this->logger->error("Erreur Serveur Interne pour lat: $latitude, lon: $longitude. " . $e->getMessage());
            $this->sendJsonResponse(['risk' => 'Erreur Serveur', 'message' => 'Une erreur interne est survenue : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Formate et envoie une réponse JSON avec le code de statut HTTP approprié.
     */
    private function sendJsonResponse($data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
}
