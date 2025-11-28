# prompt1 #
Contexte :
Tu es un expert en architecture logicielle, en PHP moderne, en développement frontend HTML/CSS/JS, et en conception d’applications desktop légères. Ta mission est de créer l’architecture complète d’une application permettant de détecter un risque cyclonique à partir de données météorologiques. Le code du backend, du frontend et une base de tests unitaires peuvent être générés immédiatement. Toutefois, l’intégration réelle de l’API Open-Meteo doit être reportée à une étape ultérieure : cela signifie que toute la structure API doit être en place, mais la logique interne des appels HTTP doit rester vide, simulée ou mockée.

Objectifs de cette étape :

Générer l’architecture complète du projet,

Générer les fichiers, classes, dossiers et organisation interne,

Générer les squelettes complets du backend et frontend,

Générer des tests unitaires de base,

Préparer proprement l’espace où la future API réelle sera implémentée,

S’assurer que tout est modulaire et prêt pour l’étape suivante d’intégration API.

Contraintes techniques :

Backend en PHP 8 minimum.

Utilisation obligatoire de cURL pour les appels API, mais ces appels ne doivent pas encore être implémentés (méthodes vides ou simulation).

Frontend uniquement HTML/CSS/JS embarqué (pas d’Electron, pas de frameworks, pas de clients lourds).

Un mini backend PHP local servira l’interface via HTTP.

L’architecture doit séparer clairement :

Controllers

Services

Models

Module API (non encore implémenté)

Tests unitaires

Frontend

L’architecture doit permettre de remplacer les mocks API par la vraie API sans aucun impact sur les autres couches.

Spécifications sur la partie API (à ne pas implémenter maintenant) :

Créer un fichier OpenMeteoClient.php dans /app/Services.

Il doit contenir la structure d’une classe prête à utiliser cURL.

Les méthodes doivent exister mais retourner des valeurs simulées ou nulles.

Aucune logique réelle de requête HTTP ne doit être écrite.

Créer une interface ou une abstraction (OpenMeteoInterface.php) pour faciliter les tests et les futurs remplacements.

Les tests unitaires doivent mocker les réponses API.

Composants obligatoires de l’architecture :

/app/Controllers/ApiController.php

reçoit les requêtes POST du frontend

appelle les services internes

renvoie les données JSON

aucune dépendance directe à Open-Meteo

/app/Services/OpenMeteoClient.php

squelette de client API

méthodes non implémentées, prêtes pour cURL

retourne des données factices pour l’instant

/app/Services/CycloneRiskEvaluator.php

contient la logique simple d’analyse

utilise les données du modèle WeatherData

/app/Models/WeatherData.php

structure des données météo fusionnées

doit être prête à recevoir les données API plus tard

/backend/index.php

point d’entrée du backend

route les requêtes vers ApiController

/frontend/index.html

interface utilisateur : champs latitude/longitude, bouton Analyser, affichage résultat

/frontend/app.js

envoie les requêtes AJAX au backend

gère l’affichage

/frontend/style.css

styles minimalistes

/tests

tests unitaires initiaux

tests mockés du CycloneRiskEvaluator

tests pour vérifier que OpenMeteoClient est bien instanciable

Livrables attendus dans la réponse :

Arborescence complète du projet en texte brut.

Description précise du rôle de chaque dossier et fichier.

Explication du découplage API réel / API mockée.

Le code complet et commenté pour :

ApiController.php

OpenMeteoClient.php (méthodes vides ou simulation)

CycloneRiskEvaluator.php

WeatherData.php

index.php backend

frontend index.html, app.js, style.css

Un diagramme de flux interne (format texte, ASCII ou Mermaid).

Un diagramme Backend <-> Frontend (ASCII ou Mermaid).

Les conventions de nommage et bonnes pratiques pour la suite.

Les tests unitaires initiaux (PHPUnit), avec mocks et sans appel API réel.

Instructions pour exécuter l’application en local.

Contraintes anti-hallucination :

Ne jamais inventer de frameworks PHP ou JS.

Ne pas proposer Electron ou équivalents.

Ne pas écrire de code utilisant la vraie API Open-Meteo.

Les seules classes autorisées sont celles mentionnées.

Ne jamais ajouter de nouvelles variables ou endpoints API.

Ne pas inclure de bibliothèques externes.

Respect strict des spécifications.

Format attendu :
Uniquement du texte descriptif + code + diagrammes. Aucune mise en forme superflue. Aucune décoration visuelle. Réponse complète et structurée.


# prompt2 #
Contexte :
Tu es un expert en intégration d’API, en PHP moderne et en architecture logicielle. Une première étape du projet a déjà créé toute l’architecture de l’application ainsi que les fichiers nécessaires, avec un module OpenMeteoClient.php encore vide ou mocké. Dans cette seconde étape, ta mission est d’implémenter réellement les appels API vers Open-Meteo dans le module dédié, en utilisant obligatoirement cURL. Tu dois intégrer entièrement la logique API dans la structure existante, sans modifier l’organisation précédente. L’ensemble du backend doit maintenant fonctionner avec de vraies données fournies par Open-Meteo.

Objectifs de cette étape :

Compléter OpenMeteoClient.php avec des appels HTTP réels utilisant cURL.

Implémenter toutes les méthodes prévues dans le client API.

Gérer les erreurs réseau, erreurs JSON, timeouts, absence de données.

Contrôler que les variables Open-Meteo utilisées sont exclusivement celles autorisées.

Intégrer les données récupérées dans WeatherData.php.

Assurer que ApiController.php utilise désormais la vraie API plutôt que les valeurs simulées.

Mettre à jour les tests unitaires pour intégrer des tests d’intégration, avec mocks ou fixtures.

Conserver la compatibilité frontend existante.

Spécifications exactes de l’intégration API :
Tu dois implémenter deux appels distincts :

Endpoint atmosphérique :
https://api.open-meteo.com/v1/forecast

Paramètres obligatoires :
latitude
longitude
elevation=0
forecast_days=4
timezone=auto
hourly=temperature_2m,relative_humidity_2m,pressure_msl,wind_speed_10m,wind_direction_10m,precipitation

Endpoint marin :
https://marine-api.open-meteo.com/v1/marine

Paramètres obligatoires :
latitude
longitude
elevation=0
forecast_days=4
timezone=auto
hourly=sea_surface_temperature,wave_height

Les noms des variables Open-Meteo doivent être utilisés exactement tels qu’indiqués. Aucun autre paramètre n’est autorisé.

Contraintes techniques obligatoires :

Utilisation stricte de cURL pour effectuer les deux requêtes HTTP.

Chaque appel API doit être encapsulé dans une méthode dédiée :

fetchAtmosphericData($latitude, $longitude)

fetchMarineData($latitude, $longitude)

Le code doit gérer :

timeouts

absence de connexion

HTTP status codes non 200

JSON invalide ou incomplet

valeurs nulles

Les réponses API doivent être fusionnées dans un objet WeatherData complet.

ApiController.php doit appeler OpenMeteoClient.php, construire WeatherData, puis transmettre les données à CycloneRiskEvaluator.php.

Exigences sur OpenMeteoClient.php :
Tu dois écrire le code complet, incluant :

Le constructeur,

La configuration cURL,

Une méthode interne privée pour exécuter une requête HTTP,

Deux méthodes publiques pour atmosphère et océan,

La validation des réponses,

La conversion en tableau PHP propre,

Les exceptions ou erreurs gérées proprement.

Exemple de structure recommandée :

class OpenMeteoClient {
public function fetchAtmosphericData($lat, $lon) { ... }
public function fetchMarineData($lat, $lon) { ... }
private function executeCurlRequest($url) { ... }
}

Le code doit être entièrement fonctionnel.

Mise à jour de ApiController.php :

Remplacer la logique précédente (mock) par la vraie récupération API.

Extraire les données nécessaires.

Instancier WeatherData avec les valeurs récupérées.

Passer ces données à CycloneRiskEvaluator.

Mise à jour de WeatherData.php :

Ajouter si nécessaire des propriétés pour accueillir toutes les données utiles.

Structurer proprement les données horaires.

Permettre au RiskEvaluator d’accéder facilement aux valeurs récentes (ex: dernière heure).

Tests à produire :

Tests unitaires des méthodes publiques de OpenMeteoClient avec mocks (pas de requêtes réseau directes).

Tests d’intégration optionnels pouvant utiliser de vraies réponses API.

Tests pour ApiController afin de vérifier la cohérence du flux complet.

Tests pour WeatherData (validation structure et fusion).

Tests pour CycloneRiskEvaluator (déjà implémenté dans l’étape précédente mais à réexécuter avec données API réelles ou mockées).

Contrainte anti-hallucination :

Ne jamais inventer de variables Open-Meteo.

Ne jamais inventer d’endpoints.

Ne jamais ajouter de dépendances externes.

N’utiliser que cURL, pas Guzzle, pas HttpClient, pas de framework.

Ne pas modifier l’architecture précédemment définie.

Ne pas modifier la structure du frontend.

Respecter strictement la liste des variables :
temperature_2m
relative_humidity_2m
pressure_msl
wind_speed_10m
wind_direction_10m
precipitation
sea_surface_temperature
wave_height

Livrables attendus :

Code complet de OpenMeteoClient.php avec appels cURL réels.

Mise à jour complète de ApiController.php pour utiliser l’API.

Mise à jour complète de WeatherData.php si nécessaire.

Mise à jour de CycloneRiskEvaluator.php si besoin mineur.

Mise à jour des tests unitaires.

Explication textuelle de la logique d’intégration API.

Instructions pour vérifier et tester l’intégration API en local.

Diagramme de flux mis à jour (avec API réelle).

Format attendu :
Uniquement du texte et du code. Aucune décoration inutile. Aucune mise en forme Markdown. Aucune omission. Réponse complète.


# prompt3 #
Contexte :
L’architecture de l’application PHP/HTML/JS est déjà en place. Le backend inclut un module OpenMeteoClient, ApiController, WeatherData, CycloneRiskEvaluator, ainsi qu’un frontend embarqué. L’API Open-Meteo a été structurée ou partiellement implémentée.
Dans cette nouvelle étape, tu dois améliorer la robustesse générale du système sans introduire de fichiers .env ni de systèmes d’environnement externes.

Objectifs :

Ajouter un timeout configurable pour toutes les requêtes cURL.

Ajouter un système minimaliste de logs pour tracer les erreurs, avertissements et événements importants.

Améliorer la stabilité globale du backend.

Conserver toute l’architecture existante sans la modifier.

Travaux demandés :

Timeout configurable :

Dans OpenMeteoClient, ajouter des propriétés pour configurer :
API_TIMEOUT_CONNECT
API_TIMEOUT_TOTAL

Définir ces valeurs dans un fichier de configuration PHP interne, par exemple config.php, ou directement via des constantes statiques dans une classe Config.

Appliquer ces timeouts aux options cURL :
CURLOPT_CONNECTTIMEOUT
CURLOPT_TIMEOUT

Retourner une erreur propre si un timeout survient.

Logger les timeouts via le système de logs simple.

Système simple de logs :

Créer un fichier Logger.php dans /app/Services.

Celui-ci doit :

Ecrire les logs dans un fichier local, par exemple /logs/app.log.

Ajouter automatiquement un timestamp à chaque ligne.

Supporter au minimum les niveaux : info, warning, error.

Fonctionner uniquement en mode fichier (pas d’affichage frontend).

Le logger doit être utilisable depuis :

OpenMeteoClient (erreurs cURL, invalid JSON, timeouts)

ApiController (appel utilisateur, erreurs d’analyse)

Fichier de configuration interne (sans .env) :

Créer un fichier config.php à la racine du projet ou un équivalent.

Ce fichier doit contenir :

API_TIMEOUT_CONNECT

API_TIMEOUT_TOTAL

LOG_ENABLED

LOG_FILEPATH

Le système doit lire cette configuration via une classe statique Config ou une fonction équivalente.

Mise à jour du backend :

Modifier OpenMeteoClient pour utiliser les nouveaux timeouts et le logger.

Ajouter des logs pour :

les URL appelées

la durée des requêtes

les erreurs cURL

les réponses non valides

Modifier ApiController pour logguer les requêtes entrantes et leurs résultats.

Tests unitaires :

Implémenter des tests pour :

Vérifier la lecture du fichier de configuration.

Tester le logger avec un fichier temporaire.

Tester OpenMeteoClient en simulant un timeout (mock cURL).

Tester ApiController avec logs activés et désactivés.

Contraintes anti-hallucination :

Ne jamais introduire .env ou une bibliothèque externe (.env parser, Monolog, etc.).

Ne pas introduire de frameworks PHP (Laravel, Symfony, etc.).

Utiliser uniquement cURL, pas de Guzzle ni autre client HTTP.

Ne jamais inventer de variables Open-Meteo ou de nouveaux endpoints API.

Ne jamais modifier le frontend.

Ne jamais proposer de technologies non mentionnées.

Livrables attendus :

Le fichier config.php (ou classe Config).

Le code complet de Logger.php.

Le code mis à jour de OpenMeteoClient.php avec timeouts et logs.

Le code mis à jour de ApiController.php avec logs.

Les tests unitaires correspondants.

Une explication textuelle du fonctionnement global.

Les instructions pour modifier le timeout et activer/désactiver les logs.

Format attendu :
Uniquement texte et code. Aucun décor, aucune mise en forme spéciale, aucune omission.