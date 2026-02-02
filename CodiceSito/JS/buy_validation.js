const campiForm = [
    { id: "email", validator: validateEmail },
    { id: "provincia", validator: validateProvincia },
    { id: "comune", validator: validateComune },
    { id: "via", validator: validateVia }
];

document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("buy-form");
    if(!form) return;
    form.setAttribute("novalidate", "");
    
    form.addEventListener("submit", (event) => {
        event.preventDefault();
        
        const campoInvalido = validateBuyForm();

        if(campoInvalido) {
            campoInvalido.focus();
            campoInvalido.scrollIntoView({behavior: "smooth", block: "center"});
            return;
        }

        form.submit();
    });
});

function validateBuyForm() {
    let campoInvalido = null;
    let primoErrore = false;
    for(const campo of campiForm) {
        const elemento = document.getElementById(campo.id);
        if(!elemento) return null;

        if(!campo.validator(elemento) && !primoErrore) {
            campoInvalido = elemento;
            primoErrore = true;
        }
    }
    return campoInvalido;
}

function setHtml(input, isValid, errorId, errorText) {
    const erroreInput = document.getElementById(errorId);
    if(!erroreInput) return;

    if(!isValid)
        input.setAttribute("aria-invalid", "true");
    else
        input.setAttribute("aria-invalid", "false");

    erroreInput.innerHTML = errorText;
}

function validateEmail(email) {
    const value = email.value.trim();
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let error = "";
    let isValid = true;

    if(value != "user" && value != "admin") {
        if(value.length == 0 || !regex.test(value)) {
            error += "L'<span lang=\"en\">email</span> non è valida.";
            isValid = false;
        }
    }
    setHtml(email, isValid, "email-error", error);

    return isValid;
}

function validateProvincia(provincia) {
    const value = provincia.value.trim();
    let error = "";
    let isValid = true;

    if(value == "") {
        error += "La provincia non è valida.";
        isValid = false;
    }
    setHtml(provincia, isValid, "provincia-error", error);

    return isValid;
}

function validateComune(comune) {
    const value = comune.value.trim();
    let error = "";
    let isValid = true;

    if(value == "") {
        error += "Il comune non è valido.";
        isValid = false;
    }
    setHtml(comune, isValid, "comune-error", error);

    return isValid;
}

function validateVia(via) {
    const value = via.value.trim();
    const regex = /^[A-Za-zÀ-ÖØ-öø-ÿ0-9\s\.\-\/']+$/;
    let error = "";
    let isValid = true;

    if(value.length == 0 || !regex.test(value)) {
        error += "La via non è valida.";
        isValid = false;
    }
    setHtml(via, isValid, "via-error", error);

    return isValid;
}