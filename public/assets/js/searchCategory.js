
    let debounceTimer;

    function handleSearch() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            document.getElementById('search-form').submit();
        }, 500); // Temps d'attente en millisecondes
    }

    document.getElementById('search').addEventListener('input', handleSearch);
    document.getElementById('price').addEventListener('input', handleSearch);
    document.getElementById('status').addEventListener('change', handleSearch);
    document.getElementById('category_id').addEventListener('change', handleSearch);

