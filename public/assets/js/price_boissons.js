document.addEventListener('DOMContentLoaded', () => {
    // Sélection des éléments du DOM
    const categorySelect = document.getElementById('category_id');
    const priceNormal = document.getElementById('price-normal');
    const priceBoissons = document.getElementById('price-boissons');

    // Liste des slugs associés aux boissons naturelles (injecté depuis Blade)
    const boissonsSlugs = JSON.parse(document.getElementById('boissons-slugs').textContent);
    console.log("Slugs détectés:", document.getElementById('boissons-slugs').textContent);

    // Fonction pour afficher/masquer les champs de prix
    const togglePriceFields = () => {
        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        const categorySlug = selectedOption ? selectedOption.getAttribute('data-slug') : null;

        if (boissonsSlugs.includes(categorySlug)) {
            priceNormal.style.display = 'none';
            priceBoissons.style.display = 'block';
        } else {
            priceNormal.style.display = 'block';
            priceBoissons.style.display = 'none';
        }
    };

    // Initialisation de l'état initial des champs
    togglePriceFields();

    // Ajout d'un écouteur d'événement sur le changement de catégorie
    categorySelect.addEventListener('change', togglePriceFields);
});
