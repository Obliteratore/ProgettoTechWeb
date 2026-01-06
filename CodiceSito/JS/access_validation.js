const campiForm = [
    { id: "username", validator: validateUsername },
    { id: "password", validator: validatePassword },
];

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("login-form");
    
    form.addEventListener("submit", (event) => {
        event.preventDefault();
        
        const campoInvalido = validateLogInForm();

        if(campoInvalido) {
            campoInvalido.focus();
            campoInvalido.scrollIntoView({behavior: "smooth", block: "center"});
            return;
        }

        form.submit();
    });
});

function validateLogInForm() {
    let campoInvalido = null;
    let primoErrore = false;
    for(const campo of campiForm) {
        const elemento = document.getElementById(campo.id);

        if(!campo.validator(elemento) && !primoErrore) {
            campoInvalido = elemento;
            primoErrore = true;
        }
    }
    return campoInvalido;
}

function setHtml(input, isValid) {
    const erroreInput = document.getElementById("login-error");

    if(!isValid) {
        input.setAttribute("aria-invalid", "true");
        erroreInput.textContent = "Le credenziali inserite non sono valide.";
    } else {
        input.setAttribute("aria-invalid", "false");
        erroreInput.textContent = "";
    }
}

function validateUsername(username) {
    const value = username.value.trim();
    let isValid = true;

    if(value.length == 0)
        isValid = false;

    setHtml(username, isValid);

    return isValid;
}

function validatePassword(password) {
    const value = password.value;
    const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!?@#$%^&*]).{8,}$/;
    let isValid = true;

    if(!regex.test(value))
        isValid = false;

    setHtml(password, isValid);

    return isValid;
}