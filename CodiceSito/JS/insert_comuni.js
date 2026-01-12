const provinciaSelect = document.getElementById("provincia");
const comuneSelect = document.getElementById("comune");

provinciaSelect.addEventListener("change", () => {
    const provincia = provinciaSelect.value;

    getComuni(provincia);
});

function getComuni(provincia) {
    const comuneSelect = document.getElementById("comune");

    // svuota la select e aggiunge l'opzione iniziale
    comuneSelect.innerHTML = '<option value="" selected>Seleziona un comune</option>';

    // crea il payload da inviare al server
    const fd = new FormData();
    fd.set('provincia', provincia);

    // costruisce l'URL dinamicamente
    const root = getRootPath();
    const url = `${window.location.origin}/${root}/PHP/get_comuni.php`;

    // invia la richiesta POST
    fetch(url, { method: 'POST', body: fd })
        .then(response => response.json())
        .then(data => {
            data.forEach(comune => {
                const option = document.createElement('option');
                option.value = comune.id_comune;
                option.textContent = comune.nome;
                comuneSelect.appendChild(option);
                console.log(comune.id_comune);
            });
        });
}

function getRootPath() {
    const path = window.location.pathname;
    const parts = path.split('/');
    return `${parts[1]}/${parts[2]}`;
}