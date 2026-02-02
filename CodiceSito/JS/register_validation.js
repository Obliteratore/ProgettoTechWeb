const campiForm = [
    { id: "nome", validator: validateNome },
    { id: "cognome", validator: validateCognome },
    { id: "provincia", validator: validateProvincia },
    { id: "comune", validator: validateComune },
    { id: "via", validator: validateVia },
    { id: "email", validator: validateEmail },
    { id: "username", validator: validateUsername },
    { id: "password", validator: validatePassword },
    { id: "confermaPassword", validator: validateConfermaPassword }
];

document.addEventListener("DOMContentLoaded", () => {
    const summary = document.getElementById('signin-error');
    const listError = document.getElementById('list-errors');
    if(summary && listError && listError.children.length > 0) {
        summary.focus();
        summary.scrollIntoView({behavior: "smooth", block: "center"});
    }

    const form = document.getElementById("signin-form");
    if(!form) return;
    form.setAttribute("novalidate", "");

    campiForm.forEach(campo => {
        const elemento = document.getElementById(campo.id);
        if(!elemento) return;
        
        elemento.addEventListener("blur", () => {
            campo.validator(elemento);
        });
    });
    
    form.addEventListener("submit", (event) => {
        event.preventDefault();
        
        const campoInvalido = validateSignInForm();

        if(campoInvalido) {
            campoInvalido.focus();
            campoInvalido.scrollIntoView({behavior: "smooth", block: "center"});
            return;
        }

        form.submit();
    });
});

function validateSignInForm() {
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

function validateNome(nome) {
    const value = nome.value.trim();
    const regex = /^[A-Za-zÀ-ÖØ-öø-ÿ]+([ '-][A-Za-zÀ-ÖØ-öø-ÿ]+)*$/;
    let error = "";
    let isValid = true;

    if(value.length == 0) {
        error += "<li>Il nome è un campo obbligatorio.</li>";
        isValid = false;
    } else {
        if(value.length < 2) {
            error += "<li>Il nome è troppo corto.</li>";
            isValid = false;
        }

        if(!regex.test(value)) {
            error += "<li>Il nome contiene caratteri non validi.</li>";
            isValid = false;
        }
    }
    setHtml(nome, isValid, "given-name-error", error);

    return isValid;
}

function validateCognome(cognome) {
    const value = cognome.value.trim();
    const regex = /^[A-Za-zÀ-ÖØ-öø-ÿ]+([ '-][A-Za-zÀ-ÖØ-öø-ÿ]+)*$/;
    let error = "";
    let isValid = true;

    if(value.length == 0) {
        error += "<li>Il cognome è un campo obbligatorio.</li>";
        isValid = false;
    } else {
        if(value.length < 2) {
            error += "<li>Il cognome è troppo corto.</li>";
            isValid = false;
        }

        if(!regex.test(value)) {
            error += "<li>Il cognome contiene caratteri non validi.</li>";
            isValid = false;
        }
    }
    setHtml(cognome, isValid, "family-name-error", error);

    return isValid;
}

function validateProvincia(provincia) {
    const value = provincia.value.trim();
    let error = "";
    let isValid = true;

    if(value == "") {
        error += "La provincia è un campo obbligatorio.";
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
        error += "Il comune è un campo obbligatorio.";
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

    if(value.length == 0) {
        error += "La via è un campo obbligatorio.";
        isValid = false;
    } else {
        if(!regex.test(value)) {
            error += "La via contiene caratteri non validi.";
            isValid = false;
        }
    }
    setHtml(via, isValid, "via-error", error);

    return isValid;
}

function validateEmail(email) {
    const value = email.value.trim();
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let error = "";
    let isValid = true;

    if(value.length == 0) {
        error += "L'<span lang=\"en\">email</span> è un campo obbligatorio.";
        isValid = false;
    } else {
        if(!regex.test(value)) {
            error += "L'<span lang=\"en\">email</span> non è valida.";
            isValid = false;
        }
    }
    setHtml(email, isValid, "email-error", error);

    return isValid;
}

function validateUsername(username) {
    const value = username.value.trim();
    const regex = /^[a-zA-Z0-9_.-]+$/;
    let error = "";
    let isValid = true;

    if(value.length == 0) {
        error += "<li>Il nome utente è un campo obbligatorio.</li>";
        isValid = false;
    } else {
        if(value.length < 3) {
            error += "<li>Il nome utente è troppo corto.</li>";
            isValid = false;
        }
        else if(value.length > 30) {
            error += "<li>Il nome utente è troppo lungo.</li>";
            isValid = false;
        }
        if(!regex.test(value)) {
            error += "<li>Il nome utente contiene caratteri non validi.</li>";
            isValid = false;
        }
    }
    setHtml(username, isValid, "username-error", error);

    return isValid;
}

function validatePassword(password) {
    const value = password.value;
    let error = "";
    let isValid = true;

    if(value.length == 0) {
        error += "<li>La <span lang=\"en\">password</span> è un campo obbligatorio.</li>";
        isValid = false;
    } else {
        if(value.length < 8) {
            error += "<li>La <span lang=\"en\">password</span> è troppo corta.</li>";
            isValid = false;
        }
        if(!/[A-Z]/.test(value)) {
            error += "<li>Manca una lettera maiuscola.</li>";
            isValid = false;
        }
        if(!/\d/.test(value)) {
            error += "<li>Manca un numero.</li>";
            isValid = false;
        }
        if(!/[!?@#$%^&*]/.test(value)) {
            error += "<li>Manca un carattere speciale.</li>";
            isValid = false;
        }
    }
    setHtml(password, isValid, "password-error", error);

    return isValid;
}

function validateConfermaPassword(confermaPassword) {
    const password = document.getElementById("password");
    const value = confermaPassword.value;
    let error = "";
    let isValid = true;

    if(value != password.value) {
        error += "Le <span lang=\"en\">password</span> non coincidono.";
        isValid = false;
    }
    setHtml(confermaPassword, isValid, "confermaPassword-error", error);

    return isValid;
}