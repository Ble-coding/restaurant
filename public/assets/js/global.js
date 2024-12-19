    $(document).ready(function () {
        $('#permissions').select2({
            placeholder: "Choisissez les permissions",
            allowClear: true,
            theme: 'bootstrap4',  // Assurez-vous que le thème est disponible
            width: '100%',         // Pour occuper toute la largeur du conteneur
        });

        $('#roles').select2({
                placeholder: "Choisissez les rôles", // Texte du placeholder
                allowClear: true,                    // Permet de désélectionner
                theme: 'bootstrap4',                // Thème à utiliser
                width: '100%'                        // Largeur adaptative
            });


        $('#user_id').select2({
            placeholder: "Choisir un utilisateur", // Texte du placeholder
            allowClear: true,                    // Permet de désélectionner
            theme: 'bootstrap4',                // Thème à utiliser
            width: '100%'                        // Largeur adaptative
        });
                  // Initialisation de Select2
        $('#status').select2({
                placeholder: "Choisissez un statut...", // Placeholder pour le champ
                allowClear: true,                    // Permet de désélectionner
                theme: 'bootstrap4',                // Thème à utiliser
                width: '100%'
            });
    });



    window.addEventListener('DOMContentLoaded', (event) => {
    let successAlert = document.getElementById('success-alert');
        if (successAlert) {
            console.log('Success alert found'); // Pour déboguer
            setTimeout(() => {
                successAlert.style.display = 'none';
            }, 5000);
        }

    let errorAlert = document.getElementById('error-alert');
        if (errorAlert) {
            console.log('Error alert found'); // Pour déboguer
            setTimeout(() => {
                errorAlert.style.display = 'none';
            }, 5000);
        }
    });

    const phoneInput = document.querySelector("#phone");
    const countryCodeInput = document.querySelector("#country_code");

    const iti = intlTelInput(phoneInput, {
        initialCountry: "fr", // Default country
        preferredCountries: ["fr", "us", "gb"], // Preferred countries
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
    });

    // Lorsque l'utilisateur modifie le pays, on met à jour le champ caché avec le code du pays
    phoneInput.addEventListener("input", function() {
        countryCodeInput.value = iti.getSelectedCountryData().dialCode; // Capture le code du pays sélectionné
    });

    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(`toggle-${inputId}`);

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }


