# prompt #
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

