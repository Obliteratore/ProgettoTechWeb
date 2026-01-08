<?php

require_once "db_connection.php";
use FM\FMAccess;

function pulisciInput($value) {
    $value = trim($value);
    $value = strip_tags($value);
    return $value;
}

function validateNome(&$errors, $nome) {
    $regex = "/^[A-Za-zÀ-ÖØ-öø-ÿ]+([ '-][A-Za-zÀ-ÖØ-öø-ÿ]+)*$/";

    if(empty($nome))
        $errors['nome'] = "<li>Il nome è un campo obbligatorio.</li>";
    else {
        if(strlen($nome) < 2)
            $errors['nome'] = "<li>Il nome è troppo corto.</li>";

        if(!preg_match($regex, $nome))
            $errors['nome'] .= "<li>Il nome contiene caratteri non validi.</li>";
    }
}

function validateCognome(&$errors, $cognome) {
    $regex = "/^[A-Za-zÀ-ÖØ-öø-ÿ]+([ '-][A-Za-zÀ-ÖØ-öø-ÿ]+)*$/";

    if(empty($cognome))
        $errors['cognome'] = "<li>Il cognome è un campo obbligatorio.</li>";
    else {
        if(strlen($cognome) < 2)
            $errors['cognome'] = "<li>Il cognome è troppo corto.</li>";

        if(!preg_match($regex, $cognome))
            $errors['cognome'] .= "<li>Il cognome contiene caratteri non validi.</li>";
    }
}

function validateProvincia(&$errors, $provincia) {
    if(empty($provincia))
        $errors['provincia'] = "La provincia è un campo obbligatorio.";
}

function validateComune(&$errors, $comune) {
    if(empty($comune))
        $errors['comune'] = "Il comune è un campo obbligatorio.";
}

function validateVia(&$errors, $via) {
    $regex = "/^[A-Za-zÀ-ÖØ-öø-ÿ0-9\s\.\-\/']+$/";

    if(empty($via))
        $errors['via'] = "La via è un campo obbligatorio.";
    else {
        if(!preg_match($regex, $via))
            $errors['via'] = "La via contiene caratteri non validi.";
    }
}

function validateEmail(&$errors, $email) {
    if(empty($email))
        $errors['email'] = `L'<span lang="en">email</span> è un campo obbligatorio.`;
    else {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors['email'] = `L'<span lang="en">email</span> non è valida.`;
    }
}

function validateUsername(&$errors, $username) {
    $regex = "/^[a-zA-Z0-9_.-]+$/";
    if(empty($username))
        $errors['username'] = "<li>Il nome utente è un campo obbligatorio.</li>";
    else {
        if(strlen($username) < 3)
            $errors['username'] = "<li>Il nome utente è troppo corto.</li>";
        elseif(strlen($username) > 30)
            $errors['username'] = "<li>Il nome utente è troppo lungo.</li>";
        if(!preg_match($regex, $username))
            $errors['username'] .= "<li>Il nome utente contiene caratteri non validi.</li>";
    }
}

function validatePassword(&$errors, $password) {
    if(empty($password))
        $errors['password'] = `<li>La <span lang="en">password</span> è un campo obbligatorio.</li>`;
    else {
        if(strlen($password) < 8)
            $errors['password'] = `<li>La <span lang="en">password</span> è troppo corta.</li>`;
        if(!preg_match('/[A-Z]/', $password))
            $errors['password'] .= "<li>Manca una lettera maiuscola.</li>";
        if(!preg_match('/\d/', $password))
            $errors['password'] .= "<li>Manca un numero.</li>";
        if(!preg_match('/[!?@#$%^&*]/', $password))
            $errors['password'] .= "<li>Manca un carattere speciale.</li>";
    }
}

function validateConfermaPassword(&$errors, $password, $confermaPassword) {
    if($confermaPassword != $password)
        $errors['confermaPassword'] .= `Le <span lang="en">password</span> non coincidono.`;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION))
        session_start();

    $values = [];
    $errors = [];

    $values['nome'] = isset($_POST['nome']) ? pulisciInput($_POST['nome']) : null;
    $values['cognome'] = isset($_POST['cognome']) ? pulisciInput($_POST['cognome']) : null;
    $values['provincia'] = isset($_POST['provincia']) ? pulisciInput($_POST['provincia']) : null;
    $values['comune'] = isset($_POST['comune']) ? pulisciInput($_POST['comune']) : null;
    $values['via'] = isset($_POST['via']) ? pulisciInput($_POST['via']) : null;
    $values['email'] = isset($_POST['email']) ? pulisciInput($_POST['email']) : null;
    $values['username'] = isset($_POST['username']) ? pulisciInput($_POST['username']) : null;
    $values['password'] = isset($_POST['password']) ? $_POST['password'] : null;
    $values['confermaPassword'] = isset($_POST['confermaPassword']) ? $_POST['confermaPassword'] : null;

    validateNome($errors, $values['nome']);
    validateCognome($errors, $values['cognome']);
    validateProvincia($errors, $values['provincia']);
    validateComune($errors, $values['comune']);
    validateVia($errors, $values['via']);
    validateEmail($errors, $values['email']);
    validateUsername($errors, $values['username']);
    validatePassword($errors, $values['password']);
    validateConfermaPassword($errors, $values['password'], $values['confermaPassword']);

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['values'] = $values;
        header('Location: registrazione.php');
        exit;
    } else {
        //Query al database per controllare se esiste già email o username e se non ci sono ed è tutto a posto, creo il nuovo utente nel database
        /*
        $connection = new FMAccess();
        $connectionOk = $connection->openConnection();

        if($connectionOk) {
    
        }*/
    }
}
?>