<?php

namespace App\Controllers;

use App\Services\CycloneRiskEvaluator;
use App\Services\OpenMeteoClient; // Implémentation concrète pour l'instanciation

/**
 * Class ApiController
 *
 * Gère les requêtes API entrantes du frontend.
 * Il ne contient aucune logique métier. Son rôle est de :
 * 1. Valider et décoder la requête.
 * 2. Appeler le service approprié (CycloneRiskEvaluator).
 * 3. Formater et retourner la réponse en JSON.
 *
 * Il n'a aucune dépendance directe avec le client API Open-Meteo.
 * L'injection de dépendances est gérée ici manuellement pour plus de simplicité.
 */
class ApiController
{
    private CycloneRiskEvaluator $riskEvaluator;

    public function __construct()
    {
        // Injection de dépendance : on fournit au service l'implémentation
        // du client API. C'est le seul endroit où le client concret est instancié.
        // Pour changer de client (ex: passer au client réel), il n'y aura
        // qu'à modifier cette ligne.
        $meteoClient = new OpenMeteoClient();
        $this->riskEvaluator = new CycloneRiskEvaluator($meteoClient);
    }

    /**
     * Point d'entrée pour l'analyse de risque cyclonique.
     * Attend une requête POST avec un corps JSON contenant "latitude" et "longitude".
     */
    public function handleCycloneRiskRequest(): void
    {
        // 1. S'assurer que la méthode est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonResponse(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        // 2. Récupérer et décoder le corps de la requête
        $jsonInput = file_get_contents('php://input');
        $data = json_decode($jsonInput, true);

        // 3. Valider les données d'entrée
        if (!isset($data['latitude']) || !isset($data['longitude']) || !is_numeric($data['latitude']) || !is_numeric($data['longitude'])) {
            $this->sendJsonResponse(['error' => 'Données d\'entrée invalides. "latitude" et "longitude" numériques sont requis.'], 400);
            return;
        }

        $latitude = (float)$data['latitude'];
        $longitude = (float)$data['longitude'];

        // 4. Appeler le service métier
        $result = $this->riskEvaluator->evaluate($latitude, $longitude);

        // 5. Envoyer la réponse JSON
        $this->sendJsonResponse($result);
    }

    /**
     * Formate et envoie une réponse JSON avec le code de statut HTTP approprié.
     *
     * @param mixed $data
     * @param int $statusCode
     */
    private function sendJsonResponse($data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
}
