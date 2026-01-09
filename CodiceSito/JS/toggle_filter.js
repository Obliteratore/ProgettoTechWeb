function toggleFilters() {
    const toggleButton = document.getElementById('toggle-filter');
    const filters = document.getElementById('filters');

    toggleButton.addEventListener('click', () => {
        filters.classList.toggle('hidden');

        if (filters.classList,contains('hidden')) {
            toggleButton.textContent = "Mostra filtri";
        }else {
                toggleButton.textContent = "Nascondi filtri";
        }
    })
}

toggleFilters();