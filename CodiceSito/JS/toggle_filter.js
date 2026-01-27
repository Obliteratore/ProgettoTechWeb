function toggleFilters() {
    const toggleButton = document.getElementById('toggle-filter');
    const filters = document.getElementById('filters');

    if (!filters) return;

    // Controlla se siamo su mobile
    const isMobile = window.matchMedia("(max-width: 767px)").matches;

    if (isMobile) {
        // Mobile: apri inizialmente, poi chiudi subito
        filters.classList.add('hidden');
        if (toggleButton) toggleButton.textContent = "Mostra filtri";
    } else {
        // Desktop: filtri sempre aperti
        filters.classList.remove('hidden');
        if (toggleButton) toggleButton.textContent = "Nascondi filtri";
    }

    // Gestione toggle bottone
    if (toggleButton) {
        toggleButton.addEventListener('click', () => {
            filters.classList.toggle('hidden');
            toggleButton.textContent = filters.classList.contains('hidden')
                ? "Mostra filtri"
                : "Nascondi filtri";
        });
    }
}

toggleFilters();
