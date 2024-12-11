let debounceTimer;
const searchInput = document.getElementById('search');
const expiredSelect = document.getElementById('expired');

// Gestion de la recherche avec un délai
function handleSearch() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        document.getElementById('search-form').submit(); // Soumet le formulaire après un délai
    }, 500); // Temps d'attente en millisecondes
}

// Écouteurs d'événements
searchInput.addEventListener('input', handleSearch);
expiredSelect.addEventListener('change', handleSearch);
