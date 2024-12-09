document.addEventListener('DOMContentLoaded', () => {
    // Références aux champs et à la checkbox
    const nameField = document.querySelector('input[name="name"]');
    const emailField = document.querySelector('input[name="email"]');
    const websiteField = document.querySelector('input[name="website"]');
    const phoneField = document.querySelector('input[name="phone"]');
    const saveInfoCheckbox = document.getElementById('save-info');

    // Charger les informations depuis localStorage si elles existent
    if (localStorage.getItem('saveInfo') === 'true') {
        saveInfoCheckbox.checked = true; // Cochez la checkbox
        nameField.value = localStorage.getItem('name') || '';
        emailField.value = localStorage.getItem('email') || '';
        phoneField.value = localStorage.getItem('website') || '';
        websiteField.value = localStorage.getItem('phone') || '';
    }

    // Sauvegarder les informations lorsque la checkbox est cochée/décochée
    saveInfoCheckbox.addEventListener('change', () => {
        if (saveInfoCheckbox.checked) {
            // Sauvegarder les valeurs dans localStorage
            localStorage.setItem('name', nameField.value);
            localStorage.setItem('email', emailField.value);
            localStorage.setItem('website', websiteField.value);
            localStorage.setItem('phone', phoneField.value);
            localStorage.setItem('saveInfo', 'true');
        } else {
            // Supprimer les informations si la checkbox est décochée
            localStorage.removeItem('name');
            localStorage.removeItem('email');
            localStorage.removeItem('website');
            localStorage.removeItem('phone');
            localStorage.setItem('saveInfo', 'false');
        }
    });

    // Mettre à jour les valeurs dans localStorage lors de la modification des champs
    [nameField, emailField, websiteField , phoneField].forEach(field => {
        field.addEventListener('input', () => {
            if (saveInfoCheckbox.checked) {
                localStorage.setItem(field.name, field.value);
            }
        });
    });
});
