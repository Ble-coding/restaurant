// Récupérer l'élément de la modale
const modal = document.getElementById('videoModal');

// Fonction pour ouvrir la modale
function openModal() {
    modal.style.display = 'flex'; // Affiche la modale
}

// Fermer le modal lorsqu'on clique en dehors de la zone de contenu
modal.addEventListener('click', function(event) {
    if (event.target === modal) { // Si l'on clique sur le fond (la partie opaque)
        modal.style.display = 'none'; // Ferme la modale
    }
});
