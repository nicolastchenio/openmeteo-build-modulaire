document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('risk-form');
    const analyzeButton = document.getElementById('analyze-button');
    const resultContainer = document.getElementById('result-container');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');

    form.addEventListener('submit', async (event) => {
        event.preventDefault(); // Empêche le rechargement de la page

        const latitude = latitudeInput.value;
        const longitude = longitudeInput.value;

        // Validation simple côté client
        if (latitude === '' || longitude === '') {
            displayResult('Veuillez remplir les deux champs.', 'erreur');
            return;
        }
        
        // Affiche un état de chargement
        setLoadingState(true);

        try {
            // Envoi de la requête au backend
            const response = await fetch('/api/cyclone-risk', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    latitude: parseFloat(latitude),
                    longitude: parseFloat(longitude)
                })
            });

            const result = await response.json();

            if (!response.ok) {
                // Gère les erreurs HTTP (4xx, 5xx)
                const errorMessage = result.error || `Erreur HTTP ${response.status}`;
                displayResult(errorMessage, 'erreur');
            } else {
                // Affiche le résultat avec la classe de risque correspondante
                displayResult(result.message, result.risk.toLowerCase());
            }

        } catch (error) {
            // Gère les erreurs réseau (ex: backend non démarré)
            console.error('Erreur lors de la requête fetch:', error);
            displayResult('Impossible de contacter le serveur. Vérifiez que le backend est bien démarré.', 'erreur');
        } finally {
            // Rétablit l'état normal du bouton
            setLoadingState(false);
        }
    });

    /**
     * Active ou désactive l'état de chargement de l'interface.
     * @param {boolean} isLoading 
     */
    function setLoadingState(isLoading) {
        if (isLoading) {
            analyzeButton.disabled = true;
            analyzeButton.textContent = 'Analyse en cours...';
            resultContainer.innerHTML = '';
            resultContainer.className = 'result'; // Réinitialise les classes
        } else {
            analyzeButton.disabled = false;
            analyzeButton.textContent = 'Analyser';
        }
    }

    /**
     * Affiche le message de résultat dans le conteneur dédié.
     * @param {string} message Le message à afficher.
     * @param {string} riskLevel Le niveau de risque (ex: 'faible', 'eleve', 'erreur').
     */
    function displayResult(message, riskLevel) {
        resultContainer.innerHTML = `<p>${message}</p>`;
        // Applique la classe CSS correspondante au risque
        resultContainer.className = `result risk-${riskLevel}`;
    }
});
