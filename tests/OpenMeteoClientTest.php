<?php

namespace Tests;

use App\Services\OpenMeteoClient;
use PHPUnit\Framework\TestCase;

/**
 * Class OpenMeteoClientTest
 *
 * Teste le client API réel OpenMeteoClient.
 */
class OpenMeteoClientTest extends TestCase
{
    /**
     * Test simple pour s'assurer que le client peut être instancié.
     * Un "smoke test" de base.
     */
    public function testClientCanBeInstantiated()
    {
        $client = new OpenMeteoClient();
        $this->assertInstanceOf(OpenMeteoClient::class, $client);
    }

    /**
     * NOTE SUR LES TESTS D'INTÉGRATION ET LES MOCKS
     *
     * Pour tester ce client plus en profondeur sans faire de vrais appels réseau
     * à chaque exécution des tests, plusieurs stratégies avancées existent :
     *
     * 1. Mocking des fonctions cURL globales :
     *    On peut utiliser des outils (comme des extensions PHPUnit) pour intercepter
     *    et simuler les fonctions `curl_exec`, `curl_getinfo`, etc. C'est puissant
     *    mais peut être complexe à mettre en place.
     *
     * 2. Refactorisation avec Injection de Dépendances :
     *    On pourrait créer une petite classe "CurlExecutor" qui encapsule les appels
     *    cURL, et l'injecter dans le constructeur de OpenMeteoClient. En test, on
     *    pourrait alors injecter un "MockCurlExecutor" qui retourne des réponses
     *    prédéfinies. C'est une approche très propre.
     *
     * 3. Tests d'intégration séparés :
     *    On peut marquer des tests comme `@group integration` dans PHPUnit et ne les
     *    lancer qu'occasionnellement (par exemple, en pré-production). Ces tests
     *    feraient de vrais appels API pour valider le contrat de l'API externe.
     *    Ils sont utiles mais lents et dépendants du réseau.
     */
}
