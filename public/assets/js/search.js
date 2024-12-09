  // Définir un délai avant la soumission du formulaire
  let debounceTimer;
  const searchInput = document.getElementById('search');

  searchInput.addEventListener('input', function () {
      clearTimeout(debounceTimer); // Réinitialise le délai si l'utilisateur continue de taper
      debounceTimer = setTimeout(() => {
          document.getElementById('search-form').submit(); // Soumet le formulaire après le délai
      }, 500); // Temps d'attente en millisecondes
  });
