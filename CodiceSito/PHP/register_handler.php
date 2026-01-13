<?php

require_once "db_connection.php";
use FM\FMAccess;

function pulisciInput($value) {
    $value = trim($value);
    $value = strip_tags($value);
    return $value;
}

function getValues(&$values) {
    $values['nome'] = isset($_POST['nome']) ? pulisciInput($_POST['nome']) : null;
    $values['cognome'] = isset($_POST['cognome']) ? pulisciInput($_POST['cognome']) : null;
    $values['provincia'] = isset($_POST['provincia']) ? pulisciInput($_POST['provincia']) : null;
    $values['comune'] = isset($_POST['comune']) ? pulisciInput($_POST['comune']) : null;
    $values['via'] = isset($_POST['via']) ? pulisciInput($_POST['via']) : null;
    $values['email'] = isset($_POST['email']) ? pulisciInput($_POST['email']) : null;
    $values['username'] = isset($_POST['username']) ? pulisciInput($_POST['username']) : null;
    $values['password'] = isset($_POST['password']) ? $_POST['password'] : null;
    $values['confermaPassword'] = isset($_POST['confermaPassword']) ? $_POST['confermaPassword'] : null;
}

function validateNome(&$errors, $nome) {
    $regex = '/^[A-Za-zÀ-ÖØ-öø-ÿ]+([ \'-][A-Za-zÀ-ÖØ-öø-ÿ]+)*$/';

    if(empty($nome))
        $errors['nome'] = '<li>Il nome è un campo obbligatorio.</li>';
    else {
        if(strlen($nome) < 2)
            $errors['nome'] = '<li>Il nome è troppo corto.</li>';

        if(!preg_match($regex, $nome))
            $errors['nome'] = ($errors['nome'] ?? '') . '<li>Il nome contiene caratteri non validi.</li>';
    }
}

function validateCognome(&$errors, $cognome) {
    $regex = '/^[A-Za-zÀ-ÖØ-öø-ÿ]+([ \'-][A-Za-zÀ-ÖØ-öø-ÿ]+)*$/';

    if(empty($cognome))
        $errors['cognome'] = '<li>Il cognome è un campo obbligatorio.</li>';
    else {
        if(strlen($cognome) < 2)
            $errors['cognome'] = '<li>Il cognome è troppo corto.</li>';

        if(!preg_match($regex, $cognome))
            $errors['cognome'] = ($errors['cognome'] ?? '') . '<li>Il cognome contiene caratteri non validi.</li>';
    }
}

function validateProvincia(&$errors, $provincia, $connection) {
    if(empty($provincia))
        $errors['provincia'] = 'La provincia è un campo obbligatorio.';
    else {
        $exist = $connection->existProvincia($provincia);
        if(!$exist)
            $errors['provincia'] = 'La provincia selezionata non esiste.';
    }
}

function validateComune(&$errors, $comune, $provincia, $connection) {
    if(empty($comune))
        $errors['comune'] = 'Il comune è un campo obbligatorio.';
    else {
        $exist = $connection->existComune($comune, $provincia);
        if(!$exist)
            $errors['comune'] = 'Il comune selezionato non esiste.';
    }
}

function validateVia(&$errors, $via) {
    $regex = '/^[A-Za-zÀ-ÖØ-öø-ÿ0-9\s\.\-\/\']+$/';

    if(empty($via))
        $errors['via'] = 'La via è un campo obbligatorio.';
    else {
        if(!preg_match($regex, $via))
            $errors['via'] = 'La via contiene caratteri non validi.';
    }
}

function validateEmail(&$errors, $email, $connection) {
    if(empty($email))
        $errors['email'] = 'L\'<span lang="en">email</span> è un campo obbligatorio.';
    else {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors['email'] = 'L\'<span lang="en">email</span> non è valida.';
    }
    if (!isset($errors['email'])) {
        $exist = $connection->existEmail($email);
        if($exist)
            $errors['email'] = 'Questa <span lang="en">email</span> è già registrata. Usane un\'altra o <a href="accesso.php">accedi</a>.';
    }
}

function validateUsername(&$errors, $username, $connection) {
    $regex = '/^[a-zA-Z0-9_.-]+$/';
    if(empty($username))
        $errors['username'] = '<li>Il nome utente è un campo obbligatorio.</li>';
    else {
        if(strlen($username) < 3)
            $errors['username'] = '<li>Il nome utente è troppo corto.</li>';
        elseif(strlen($username) > 30)
            $errors['username'] = '<li>Il nome utente è troppo lungo.</li>';
        if(!preg_match($regex, $username))
            $errors['username'] = ($errors['username'] ?? '') . '<li>Il nome utente contiene caratteri non validi.</li>';
    }
    if (!isset($errors['username'])) {
        $exist = $connection->existUsername($username);
        if($exist)
            $errors['username'] = '<li>Questo nome utente è già in uso. Scegline un altro o <a href="accesso.php">accedi</a>.</li>';
    }
}

function validatePassword(&$errors, $password) {
    if(empty($password))
        $errors['password'] = '<li>La <span lang="en">password</span> è un campo obbligatorio.</li>';
    else {
        if(strlen($password) < 8)
            $errors['password'] = '<li>La <span lang="en">password</span> è troppo corta.</li>';
        if(!preg_match('/[A-Z]/', $password))
            $errors['password'] = ($errors['password'] ?? '') . '<li>Manca una lettera maiuscola.</li>';
        if(!preg_match('/\d/', $password))
            $errors['password'] = ($errors['password'] ?? '') . '<li>Manca un numero.</li>';
        if(!preg_match('/[!?@#$%^&*]/', $password))
            $errors['password'] = ($errors['password'] ?? '') . '<li>Manca un carattere speciale.</li>';
    }
}

function validateConfermaPassword(&$errors, $password, $confermaPassword) {
    if($confermaPassword != $password)
        $errors['confermaPassword'] = 'Le <span lang="en">password</span> non coincidono.';
}

function callValidators(&$errors, $values, $connection) {
    validateNome($errors, $values['nome']);
    validateCognome($errors, $values['cognome']);
    validateProvincia($errors, $values['provincia'], $connection);
    validateComune($errors, $values['comune'], $values['provincia'], $connection);
    validateVia($errors, $values['via']);
    validateEmail($errors, $values['email'], $connection);
    validateUsername($errors, $values['username'], $connection);
    validatePassword($errors, $values['password']);
    validateConfermaPassword($errors, $values['password'], $values['confermaPassword']);
}

function setSummary(&$errors) {
    if (isset($errors['nome']))
        $errors['summary'] = '<li><a href="#nome">Il nome non è valido.</a></li>';
    if (isset($errors['cognome']))
        $errors['summary'] = ($errors['summary'] ?? '') . '<li><a href="#cognome">Il cognome non è valido.</a></li>';
    if (isset($errors['provincia']))
        $errors['summary'] = ($errors['summary'] ?? '') . '<li><a href="#provincia">La provincia non è valida.</a></li>';
    if (isset($errors['comune']))
        $errors['summary'] = ($errors['summary'] ?? '') . '<li><a href="#comune">Il comune non è valido.</a></li>';
    if (isset($errors['via']))
        $errors['summary'] = ($errors['summary'] ?? '') . '<li><a href="#via">La via non è valida.</a></li>';
    if (isset($errors['email']))
        $errors['summary'] = ($errors['summary'] ?? '') . '<li><a href="#email">L\'<span lang="en">email</span> non è valida.</a></li>';
    if (isset($errors['username']))
        $errors['summary'] = ($errors['summary'] ?? '') . '<li><a href="#username">Il nome utente non è valido.</a></li>';
    if (isset($errors['password']))
        $errors['summary'] = ($errors['summary'] ?? '') . '<li><a href="#password">La <span lang="en">password</span> non è valida.</a></li>';
    if (isset($errors['confermaPassword']))
        $errors['summary'] = ($errors['summary'] ?? '') . '<li><a href="#confermaPassword">La conferma <span lang="en">password</span> non è valida.</a></li>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /*if(session_status() !== PHP_SESSION_ACTIVE)*/
    if(!isset($_SESSION))
        session_start();

    $values = [];
    $errors = [];

    getValues($values);

    try {
        $connection = new FMAccess();
        $connection->openConnection();
        callValidators($errors, $values, $connection);
        setSummary($errors);

        if(!empty($errors)) {
            $connection->closeConnection();

            $_SESSION['errors'] = $errors;
            $_SESSION['values'] = $values;
            header('Location: registrazione.php');
            exit;
        } else {
            /*$connection->beginTransaction();
            try {
                $connection->insertUtente($values['email']);
                $connection->insertUtenteRegistrato($values['email'], $values['username'], $values['password'], $values['nome'], $values['cognome']);

                $idIndirizzo = $connection->insertIndirizzo($values['provincia'], $values['comune'], $values['via']);
                $connection->insertUtenteRegistratoIndirizzo($values['email'], $idIndirizzo);

                $connection->commit();
            } catch(mysqli_sql_exception $e) {
                $connection->rollback();
                throw $e;
            }*/
            session_regenerate_id(true); 
            $_SESSION['email'] = $values['email'];
            header('Location: accesso.php'); //area personale
            exit;
        }
    } catch(mysqli_sql_exception $e) {
        header('Location: ../HTML/error_500.html');
        exit;
    } finally {
        $connection->closeConnection();
    }
}
?>