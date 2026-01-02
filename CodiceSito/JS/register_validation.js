const campiForm = [
    { id: "nome", validator: validateNome },
    { id: "cognome", validator: validateCognome },
    { id: "via", validator: validateVia },
    { id: "email", validator: validateEmail },
    { id: "username", validator: validateUsername },
    { id: "password", validator: validatePassword },
    { id: "conferma-password", validator: validateConfermaPassword }
];

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("signin-form");
    form.setAttribute("novalidate", "");

    campiForm.forEach(campo => {
        const elemento = document.getElementById(campo.id);
        
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

        if(!campo.validator(elemento) && !primoErrore) {
            campoInvalido = elemento;
            primoErrore = true;
        }
    }
    return campoInvalido;
}


function setHtml(input, isValid, errorId, errorText) {
    const erroreInput = document.getElementById(errorId);

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
        error += "Il nome è un campo obbligatorio.";
        isValid = false;
    } else {
        if(value.length < 2) {
            error += "Il nome è troppo corto. ";
            isValid = false;
        }

        if(!regex.test(value)) {
            error += "Il nome contiene caratteri non validi.";
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
        error += "Il cognome è un campo obbligatorio.";
        isValid = false;
    } else {
        if(value.length < 2) {
            error += "Il cognome è troppo corto. ";
            isValid = false;
        }

        if(!regex.test(value)) {
            error += "Il cognome contiene caratteri non validi.";
            isValid = false;
        }
    }
    setHtml(cognome, isValid, "family-name-error", error);

    return isValid;
}

function validateProvincia(provincia) {
    
}

function validateCitta(citta) {
    
}

function validateCap(cap) {
    
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
        error += `L'<span lang="en">email</span> è un campo obbligatorio.`;
        isValid = false;
    } else {
        if(!regex.test(value)) {
            error += `L'<span lang="en">email</span> non è valida.`;
            isValid = false;
        }
    }
    setHtml(email, isValid, "email-error", error);

    return isValid;
}

function validateUsername(username) {
    const value = username.value.trim();
    let error = "";
    let isValid = true;

    if(value.length == 0) {
        error += "Il nome utente è un campo obbligatorio.";
        isValid = false;
    } else {
        if(value.length > 30) {
            error += "Il nome utente è troppo lungo.";
            isValid = false;
        }
    }
    setHtml(username, isValid, "username-error", error);

    return isValid;
}

function validatePassword(password) {
    const value = password.value.trim();
    let error = "";
    let isValid = true;

    if(value.length == 0) {
        error += `La <span lang="en">password</span> è un campo obbligatorio.`;
        isValid = false;
    } else {
        if(value.length < 8) {
            error += `La <span lang="en">password</span> è troppo corta. `;
            isValid = false;
        }
        if(!/[A-Z]/.test(value)) {
            error += "Manca una lettera maiuscola. ";
            isValid = false;
        }
        if(!/\d/.test(value)) {
            error += "Manca un numero. ";
            isValid = false;
        }
        if(!/[!?@#$%^&*_-]/.test(value)) {
            error += "Manca un carattere speciale.";
            isValid = false;
        }
    }
    setHtml(password, isValid, "password-error", error);

    return isValid;
}

function validateConfermaPassword(confermaPassword) {
    const password = document.getElementById("password");
    const value = confermaPassword.value.trim();
    let error = "";
    let isValid = true;

    if(value != password.value) {
        error += `Le <span lang="en">password</span> non coincidono.`;
        isValid = false;
    }
    setHtml(confermaPassword, isValid, "conferma-password-error", error);

    return isValid;
}