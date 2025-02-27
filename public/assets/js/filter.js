$(document).ready(function () {
    $('.category-filter, .status-filter').change(function () {
        let selectedCategories = [];
        let selectedStatuses = [];

        // Récupérer les catégories cochées
        $('.category-filter:checked').each(function () {
            selectedCategories.push($(this).val());
        });

        // Récupérer les statuts cochés
        $('.status-filter:checked').each(function () {
            selectedStatuses.push($(this).val());
        });

        $.ajax({
            url: '{{ route("products.index") }}',
            method: 'GET',
            data: { categories: selectedCategories, statuses: selectedStatuses },
            success: function (response) {
                $('#product-list').html($(response).find('#product-list').html());
            }
        });
    });
});
