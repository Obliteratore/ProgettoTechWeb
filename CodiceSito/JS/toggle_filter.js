function isMobile() {
    return window.innerWidth <= 767;
}

function toggleFilters() {
    const toggleButton = document.getElementById('toggle-filter');
    const filters = document.getElementById('filters');

    if(!toggleButton || !filters) return;

    function setState(state) {
        filters.classList.toggle('hidden', !state);
        toggleButton.textContent = state ? "Nascondi filtri" : "Mostra filtri";
        toggleButton.setAttribute('aria-expanded', state.toString());
    }

    setState(false);

    toggleButton.addEventListener('click', () => {
        const isOpen = toggleButton.getAttribute('aria-expanded') === 'true';
        setState(!isOpen);
    });

    window.addEventListener('resize', () => {
        if(!isMobile())
            setState(false);
    });
}

toggleFilters();