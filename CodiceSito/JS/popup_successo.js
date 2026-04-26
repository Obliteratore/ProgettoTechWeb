document.addEventListener("DOMContentLoaded", () => {

    const dialog = document.getElementById("msg-successo");
    const params = new URLSearchParams(window.location.search);
    
    if (dialog && params.get("successo") === "aggiunto") {
        dialog.showModal();
    }
});