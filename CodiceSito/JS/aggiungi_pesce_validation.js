document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector("form");
    if(form){
        form.setAttribute('novalidate', '');
    }
    const ListaColori = ['giallo','arancione','rosso','beige','rosa','blu','azzurro','verde','marrone','nero','grigio','trasparente'];

    function showErrors(input, errorElement, errors) {
        if(!input || !errorElement) return;
        errorElement.innerHTML = errors.length > 0 ? errors.map(e => `<li>${e}</li>`).join('') : '';
        input.setAttribute('aria-invalid', errors.length > 0 ? 'true' : 'false');
    }

    //AJAX, utilizzato per controllare la presenza del nome latino nel database altrimenti il controllo si faceva solo server side e non anche client side.
    async function validateNomeLatino() {
        const input = document.getElementById('nome_latino');
        const target = document.getElementById('nome-latino-error');
        if(!input) return;
        const val = input.value.trim();
        const err = [];
        if(!val) err.push("Il nome latino è obbligatorio.");
        if (val.length < 2) err.push("Deve avere almeno 2 caratteri.");
        if (/[^A-Za-zÀ-ÿ\s]/.test(val)) err.push("Sono ammesse solo lettere.");
        if (err.length === 0) {
            const response = await fetch(`../PHP/aggiungi_pesce.php?check_nome=${encodeURIComponent(val)}`);
            const data = await response.json();
                if(data.exists){
                    err.push("Questo nome latino è già presente nel database. Scegline un altro.")
                }
        }
        showErrors(input, target, err);
    }

    function validateNomeComune() {
        const input = document.getElementById('nome_comune');
        const target = document.getElementById('nome-comune-error');
        const val = input.value.trim();
        const err = [];
        if (!val) err.push("Il nome comune è obbligatorio.");
        else if (val.length < 2) err.push("Deve avere almeno 2 caratteri.");
        if (val && /[^A-Za-zÀ-ÿ\s]/.test(val)) err.push("Sono ammesse solo lettere.");
        showErrors(input, target, err);
    }

    function validateFamiglia() {
        const input = document.getElementById('famiglia');
        const target = document.getElementById('famiglia-error');
        const err = (input.value === "") ? ["Seleziona una famiglia."] : [];
        showErrors(input, target, err);
    }

    function validateDimensione() {
        const input = document.getElementById('dimensione');
        const target = document.getElementById('dimensione-error');
        const val = parseFloat(input.value);
        const err = (isNaN(val) || val <= 0) ? ["Inserisci un numero valido maggiore di 0."] : [];
        showErrors(input, target, err);
    }

    function validateVolume() {
        const input = document.getElementById('volume_minimo');
        const target = document.getElementById('volume-minimo-error');
        const val = parseFloat(input.value);
        const err = (isNaN(val) || val <= 0) ? ["Inserisci un numero valido maggiore di 0."] : [];
        showErrors(input, target, err);
    }

    function validateColori() {
        const input = document.getElementById('colori');
        const target = document.getElementById('colori-error');
        const val = input.value.trim();
        const err = [];
        if (!/^[A-Za-zÀ-ÿ]+(,[A-Za-zÀ-ÿ]+)*$/.test(val)) {
            err.push("Formato non valido. Usa lettere separate da virgole senza spazi, ad esempio: rosso,blu,verde.");
        } else {
            val.split(',').forEach(c => {
                if (!ListaColori.includes(c.toLowerCase())) err.push(`Il colore '${c}' non è nella lista consentita.`);
            });
        }
        showErrors(input, target, err);
    }

    function validatePrezzo() {
        const input = document.getElementById('prezzo');
        const target = document.getElementById('prezzo-error');
        const val = parseFloat(input.value);
        const err = (isNaN(val) || val <= 0) ? ["Prezzo non valido."] : [];
        showErrors(input, target, err);
    }

    function validateDisponibilita() {
        const input = document.getElementById('disponibilita');
        const target = document.getElementById('disponibilita-error');
        const val = parseInt(input.value);
        const err = (isNaN(val) || val < 0) ? ["Inserisci un numero valido."] : [];
        showErrors(input, target, err);
    }

    function validateImmagine() {
        const input = document.getElementById('immagine');
        const target = document.getElementById('immagine-error');
        const err = [];

        if (input.files.length === 0) {
            err.push("L'immagine è obbligatoria.");
            showErrors(input, target, err);
            return;
        }

        const file = input.files[0];
        const validTypes = ["image/jpeg", "image/jpg"];
        if (!validTypes.includes(file.type)) {
            err.push("Usa solo JPG, JPEG o PNG.");
            showErrors(input, target, err);
            return;
        }

        const img = new Image();
        const objectUrl = URL.createObjectURL(file);
        img.onload = function() {
            if (this.width !== 1024 || this.height !== 683) {
                err.push("L'immagine deve essere alta 683 pixel e larga 1024 pixel.");
            }
            showErrors(input, target, err);
            URL.revokeObjectURL(objectUrl);
        };
        img.src = objectUrl;
    }

    document.getElementById('nome_latino')?.addEventListener('blur', validateNomeLatino);
    document.getElementById('nome_comune')?.addEventListener('blur', validateNomeComune);
    document.getElementById('famiglia')?.addEventListener('change', validateFamiglia);
    document.getElementById('dimensione')?.addEventListener('blur', validateDimensione);
    document.getElementById('volume_minimo')?.addEventListener('blur', validateVolume);
    document.getElementById('colori')?.addEventListener('blur', validateColori);
    document.getElementById('prezzo')?.addEventListener('blur', validatePrezzo);
    document.getElementById('disponibilita')?.addEventListener('blur', validateDisponibilita);
    document.getElementById('immagine')?.addEventListener('change', validateImmagine);

    form.addEventListener("submit", async (e) => {

        await validateNomeLatino();

        validateNomeComune();
        validateFamiglia();
        validateDimensione();
        validateVolume();
        validateColori();
        validatePrezzo();
        validateDisponibilita();
        validateImmagine();

        const primoErrore = document.querySelector('[aria-invalid="true"]');
        if (primoErrore) {
            e.preventDefault();
            setTimeout(() => {
                primoErrore.focus();
                primoErrore.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }
    });
});