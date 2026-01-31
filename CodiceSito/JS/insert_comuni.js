const provinciaSelect = document.getElementById("provincia");
const comuneSelect = document.getElementById("comune");

if(!provinciaSelect || !comuneSelect) return;

provinciaSelect.addEventListener("change", () => {
    const provincia = provinciaSelect.value;

    getComuni(provincia);
});

function getComuni(provincia) {
    const comuneSelect = document.getElementById("comune");

    comuneSelect.innerHTML = '<option value="" selected>Seleziona un comune</option>';

    const fd = new FormData();
    fd.set('provincia', provincia);

    const root = getRootPath();
    const url = `${window.location.origin}/${root}/PHP/get_comuni.php`;

    fetch(url, { method: 'POST', body: fd })
        .then(response => response.json())
        .then(result => {
            if(!result.success) {
                window.location.href = '../HTML/error_500.html';
                return;
            }
            result.data.forEach(comune => {
                const option = document.createElement('option');
                option.value = comune.id_comune;
                option.textContent = comune.nome;
                comuneSelect.appendChild(option);
            });
        })
        .catch(() => {
            window.location.href = '../HTML/error_500.html';
        });
}

function getRootPath() {
    const path = window.location.pathname;
    const parts = path.split('/');
    return `${parts[1]}/${parts[2]}`;
}