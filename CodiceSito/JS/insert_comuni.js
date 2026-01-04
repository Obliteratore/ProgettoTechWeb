const provinciaSelect = document.getElementById("provincia");
const comuneSelect = document.getElementById("comune");

provinciaSelect.addEventListener("change", () => {
    const provincia = provinciaSelect.value;

    // Svuota le opzioni precedenti
    comuneSelect.innerHTML = '<option value="" selected>Seleziona un comune</option>';
  /*
    fetch(`/fbalestr/CodiceSito/PHP/get_comuni.php?provincia=${encodeURIComponent(provincia)}`)
    .then(response => response.json())
    .then(data => {
      data.forEach(comune => {
        const option = document.createElement("option");
        option.value = comune.id;
        option.textContent = comune.nome;
        comuneSelect.appendChild(option);
      });
    });*/
});