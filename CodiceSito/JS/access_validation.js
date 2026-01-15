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
    const username = document.getElementById(campiForm[0].id).value;

    if(username != "user" && username != "admin") {
        for(const campo of campiForm) {
            const elemento = document.getElementById(campo.id);
            const value = elemento.value.trim();

            if(!campo.validator(value) && !primoErrore) {
                campoInvalido = elemento;
                primoErrore = true;
            }
        }
    }

    for(const campo of campiForm) {
        const elemento = document.getElementById(campo.id);
        setHtml(elemento, primoErrore);
    }

    return campoInvalido;
}

function setHtml(input, erorre) {
    const erroreInput = document.getElementById("login-error");

    if(erorre) {
        input.setAttribute("aria-invalid", "true");
        erroreInput.textContent = "Le credenziali inserite non sono valide.";
    } else {
        input.setAttribute("aria-invalid", "false");
        erroreInput.textContent = "";
    }
}

function validateUsername(username) {
    const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const regexUsername = /^[a-zA-Z0-9_.-]+$/;
    let isValid = true;
    
    if(username.length != 0) {
        if (!regexEmail.test(username)) {
            if(username.length < 3 || username.length > 30 || !regexUsername.test(username))
                isValid = false;
        }
    } else {
        isValid = false;
    }
    
    return isValid;
}

function validatePassword(password) {
    const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!?@#$%^&*]).{8,}$/;
    let isValid = true;

    if(!regex.test(password))
        isValid = false;

    return isValid;
}