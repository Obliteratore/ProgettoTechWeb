document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("signin-form");

    form.addEventListener("submit", function(event) {
        // Evita l'invio automatico della form
        event.preventDefault();
        var dettagli_form = {
            "nome": [/^ $/, ""]
        };
        // Se tutto ok, invia la form
        form.submit();
    });
});
