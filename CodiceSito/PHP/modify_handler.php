<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

if(!isset($_SESSION['email'])) {
    header('Location: accesso.php');
    exit;
}

require_once "validate_functions.php";
require_once "db_connection.php";
use FM\FMAccess;

function validateProvincia(&$errors, $provincia, $connection) {
    if(!empty($provincia)) {
        $exist = $connection->existProvincia($provincia);
        if(!$exist)
            $errors['provincia'] = 'La provincia selezionata non esiste.';
    }
}

function validateComune(&$errors, $comune, $provincia, $connection) {
    if(!empty($comune)) {
        $exist = $connection->existComune($comune, $provincia);
        if(!$exist)
            $errors['comune'] = 'Il comune selezionato non esiste.';
    }
}

function validatePassword(&$errors, $password) {
    if(!empty($password)) {
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

function getValues(&$values) {
    $values['nome'] = isset($_POST['nome']) ? pulisciInput($_POST['nome']) : null;
    $values['cognome'] = isset($_POST['cognome']) ? pulisciInput($_POST['cognome']) : null;
    $values['provincia'] = isset($_POST['provincia']) ? pulisciInput($_POST['provincia']) : null;
    $values['comune'] = isset($_POST['comune']) ? pulisciInput($_POST['comune']) : null;
    $values['via'] = isset($_POST['via']) ? pulisciInput($_POST['via']) : null;
    $values['username'] = isset($_POST['username']) ? pulisciInput($_POST['username']) : null;
    $values['password'] = isset($_POST['password']) ? $_POST['password'] : null;
    $values['confermaPassword'] = isset($_POST['confermaPassword']) ? $_POST['confermaPassword'] : null;
}

function callValidators(&$errors, $values, $connection) {
    validateNome($errors, $values['nome']);
    validateCognome($errors, $values['cognome']);
    validateProvincia($errors, $values['provincia'], $connection);
    validateComune($errors, $values['comune'], $values['provincia'], $connection);
    validateVia($errors, $values['via']);
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
    if (isset($errors['username']))
        $errors['summary'] = ($errors['summary'] ?? '') . '<li><a href="#username">Il nome utente non è valido.</a></li>';
    if (isset($errors['password']))
        $errors['summary'] = ($errors['summary'] ?? '') . '<li><a href="#password">La <span lang="en">password</span> non è valida.</a></li>';
    if (isset($errors['confermaPassword']))
        $errors['summary'] = ($errors['summary'] ?? '') . '<li><a href="#confermaPassword">La conferma <span lang="en">password</span> non è valida.</a></li>';
}
    
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(session_status() !== PHP_SESSION_ACTIVE)
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
            header('Location: modifica_profilo.php');
            exit;
        } else {
            //continuo procedimento per modificare i dati dell'utente
            header('Location: profilo.php');
        }
    } catch(mysqli_sql_exception $e) {
        http_response_code(500);
        header('Location: ../HTML/error_500.html');
        exit;
    } finally {
        $connection->closeConnection();
    }
}
?>