// Sélectionner le bouton de toggle et l'icône
const toggleButton = document.querySelector('.navbar-toggler');
const navbarCollapse = document.querySelector('#navbarNav');

// Ajouter un écouteur d'événement pour surveiller les clics sur le bouton
toggleButton.addEventListener('click', () => {
    // Bascule la classe 'active' pour afficher ou masquer le "X"
    toggleButton.classList.toggle('active');
    
    // Modifier l'icône en fonction de l'état
    if (toggleButton.classList.contains('active')) {
        // Ajouter un "X" lorsque le menu est ouvert
        toggleButton.innerHTML = '❌';
    } else {
        // Restaurer l'icône par défaut lorsque le menu est fermé
        toggleButton.innerHTML = '<img src="./assets/images/header/hamburger_menu_button.png" width="40" height="40" alt="logo_site_ci_drinks&foods" />';
    }
});

document.querySelectorAll('.nav-item.dropdown').forEach(function (dropdown) {
    dropdown.addEventListener('touchstart', function () {
        let menu = this.querySelector('.dropdown-menu');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    });
});

// document.addEventListener("DOMContentLoaded", function () {
//     const navbar = document.querySelector(".navbar");
//     const heroSection = document.querySelector(".hero-section");

//     window.addEventListener("scroll", function () {
//         const heroBottom = heroSection.offsetHeight; // Bas de la section hero
//         if (window.scrollY > heroBottom) {
//             navbar.classList.add("scrolled"); // Ajoute un fond noir
//         } else {
//             navbar.classList.remove("scrolled"); // Rend la navbar transparente
//         }
//     });
// });

