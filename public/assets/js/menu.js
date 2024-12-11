$(document).ready(function () {
    $(document).on("click", ".add_cart", function (e) {
        e.preventDefault();
        let productId = $(this).data("id");

        $.ajax({
            url: "/cart/add",
            method: "POST",
            data: {
                product_id: productId,
                _token: $("meta[name='csrf-token']").attr("content"),
            },
            success: function (response) {
                if (response.status === "success") {
                    $('#success-alert').text(response.message).show();
                    setTimeout(function() { $('#success-alert').fadeOut(); }, 3000);
                    updateCartBadge();
                    loadCart();
                } else {
                    $('#error-alert').text("Erreur lors de l'ajout au panier.").show();
                    setTimeout(function() { $('#error-alert').fadeOut(); }, 3000);
                }
            },
            error: function () {
                $('#error-alert').text("Erreur lors de la requête.").show();
                setTimeout(function() { $('#error-alert').fadeOut(); }, 3000);
            },
        });
    });

    $(document).on("click", ".remove-item", function (e) {
        e.preventDefault();
        let productId = $(this).data("id");

        $.ajax({
            url: "/cart/remove",
            method: "POST",
            data: {
                product_id: productId,
                _token: $("meta[name='csrf-token']").attr("content"),
            },
            success: function (response) {
                if (response.status === "success") {
                    $('#success-alert').text(response.message).show();
                    setTimeout(function() { $('#success-alert').fadeOut(); }, 3000);
                    updateCartBadge();
                    loadCart();
                } else {
                    $('#error-alert').text("Erreur lors de la suppression du produit.").show();
                    setTimeout(function() { $('#error-alert').fadeOut(); }, 3000);
                }
            },
            error: function () {
                $('#error-alert').text("Erreur lors de la requête.").show();
                setTimeout(function() { $('#error-alert').fadeOut(); }, 3000);
            },
        });
    });

    function updateCartBadge() {
        $.ajax({
            url: "/cart/count",
            method: "GET",
            success: function (response) {
                $("#cart-badge").text(response.count);
            },
            error: function () {
                $('#error-alert').text("Erreur lors de la mise à jour de la badge.").show();
                setTimeout(function() { $('#error-alert').fadeOut(); }, 3000);
            },
        });
    }

    function loadCart() {
        // Met à jour le contenu du panier sans recharger la page
        $("#cart-items").load(location.href + " #cart-items", function() {
            // Optionnel : actions après le chargement (ex. mise à jour de l'affichage dynamique)
        });
    }

    updateCartBadge();
});
