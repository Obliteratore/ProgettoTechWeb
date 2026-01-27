document.addEventListener('DOMContentLoaded', () => {

    function showErrors(input, errorElement, errors) {
        errorElement.innerHTML = errors.length > 0 ? errors.map(e => `<li>${e}</li>`).join('') : '';
        input.setAttribute('aria-invalid', errors.length > 0 ? 'true' : 'false');
    }

    
    function validateNomeComune() {
        const value = nomeComuneInput.value.trim();
        const errors = [];
        if (value.length < 2) errors.push("Deve avere almeno 2 caratteri.");
        if (/[^A-Za-zÀ-ÿ\s]/.test(value)) errors.push("Sono ammesse solo lettere.");
        showErrors(nomeComuneInput, nomeComuneError, errors);
    }

    function validateDimensione() {
        const value = parseFloat(dimensioneInput.value);
        const errors = [];
        if (isNaN(value) || value <= 0) errors.push("Inserisci un numero valido maggiore di 0.");
        showErrors(dimensioneInput, dimensioneError, errors);
    }

    function validateVolumeMinimo() {
        const value = parseFloat(volumeInput.value);
        const errors = [];
        if (isNaN(value) || value <= 0) errors.push("Inserisci un numero valido maggiore di 0.");
        showErrors(volumeInput, volumeError, errors);
    }

    function validateColori() {
        const value = coloriInput.value.trim();
        const errors = [];
        if (!/^[A-Za-zÀ-ÿ]+(,[A-Za-zÀ-ÿ]+)*$/.test(value)) {
            errors.push("Formato non valido. Usa lettere separate da virgole senza spazi, ad esempio: rosso,blu,verde");
        } else {
            value.split(',').forEach(c => {
                if (!ListaColori.includes(c.toLowerCase())) {
                    errors.push(`Il colore '${c}' non è nella lista consentita.`);
                }
            });
        }
        showErrors(coloriInput, coloriError, errors);
    }

    function validatePrezzo() {
        const value = parseFloat(prezzoInput.value);
        const errors = [];
        if (isNaN(value) || value <= 0) errors.push("Prezzo non valido.");
        showErrors(prezzoInput, prezzoError, errors);
    }

    function validateDisponibilita() {
        const value = parseInt(dispInput.value);
        const errors = [];
        if (isNaN(value) || value < 0) errors.push("Inserisci un numero valido.");
        showErrors(dispInput, dispError, errors);
    }

    const nomeComuneInput = document.getElementById('nome_comune');
    const nomeComuneError = document.getElementById('nome-comune-error');

    const dimensioneInput = document.getElementById('dimensione');
    const dimensioneError = document.getElementById('dimensione-error');

    const volumeInput = document.getElementById('volume_minimo');
    const volumeError = document.getElementById('volume-minimo-error');

    const coloriInput = document.getElementById('colori');
    const coloriError = document.getElementById('colori-error');
    const ListaColori = ['giallo','arancione','rosso','beige','rosa','blu','azzurro','verde','marrone','nero','grigio','trasparente'];

    const prezzoInput = document.getElementById('prezzo');
    const prezzoError = document.getElementById('prezzo-error');

    const dispInput = document.getElementById('disponibilita');
    const dispError = document.getElementById('disponibilita-error');

    nomeComuneInput.addEventListener('blur', validateNomeComune);
    dimensioneInput.addEventListener('blur', validateDimensione);
    volumeInput.addEventListener('blur', validateVolumeMinimo);
    coloriInput.addEventListener('blur', validateColori);
    prezzoInput.addEventListener('blur', validatePrezzo);
    dispInput.addEventListener('blur', validateDisponibilita);
});