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
    $exist = $connection->existProvincia($provincia);
    if(!$exist)
        $errors['provincia'] = 'La provincia selezionata non esiste.';
}

function validateComune(&$errors, $comune, $provincia, $connection) {
    $exist = $connection->existComune($comune, $provincia);
    if(!$exist)
        $errors['comune'] = 'Il comune selezionato non esiste.';
}

function validatePassword(&$errors, $password) {
    if(strlen($password) < 8)
        $errors['password'] = '<li>La <span lang="en">password</span> è troppo corta.</li>';
    if(!preg_match('/[A-Z]/', $password))
        $errors['password'] = ($errors['password'] ?? '') . '<li>Manca una lettera maiuscola.</li>';
    if(!preg_match('/\d/', $password))
        $errors['password'] = ($errors['password'] ?? '') . '<li>Manca un numero.</li>';
    if(!preg_match('/[!?@#$%^&*]/', $password))
        $errors['password'] = ($errors['password'] ?? '') . '<li>Manca un carattere speciale.</li>';
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

function callValidators(&$errors, $values, $connection, $datiUtente) {
    if($values['nome'] !== $datiUtente['nome'])
        validateNome($errors, $values['nome']);
    if($values['cognome'] !== $datiUtente['cognome'])
        validateCognome($errors, $values['cognome']);
    if(!empty($values['provincia']) && $values['provincia'] !== $datiUtente['sigla_provincia'])
        validateProvincia($errors, $values['provincia'], $connection);
    if((!empty($values['provincia']) && $values['provincia'] !== $datiUtente['sigla_provincia']) || (!empty($values['comune']) && $values['comune'] !== $datiUtente['id_comune']))
        validateComune($errors, empty($values['comune']) ? $datiUtente['id_comune'] : $values['comune'], empty($values['provincia']) ? $datiUtente['sigla_provincia'] : $values['provincia'], $connection);
    if($values['via'] !== $datiUtente['via'])
        validateVia($errors, $values['via']);
    if($values['username'] !== $datiUtente['username'])
        validateUsername($errors, $values['username'], $connection);
    if(!empty($values['password']))
        validatePassword($errors, $values['password']);
    if(!empty($values['password']) || !empty($values['confermaPassword']))
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
    
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = [];
    $errors = [];

    getValues($values);

    try {
        $connection = new FMAccess();
        $connection->openConnection();
        $datiUtente = $connection->getProfiloUtenteRegistrato($_SESSION['email']);
        callValidators($errors, $values, $connection, $datiUtente);
        setSummary($errors);

        if(!empty($errors)) {
            $connection->closeConnection();

            $_SESSION['errors'] = $errors;
            $_SESSION['values'] = $values;
            header('Location: modifica_profilo.php');
            exit;
        } else {
            $set = [];
            $parametri = [];

            if ($values['nome'] !== $datiUtente['nome']) {
                $set[] = 'nome = ?';
                $parametri[] = $values['nome'];
            }
            if ($values['cognome'] !== $datiUtente['cognome']) {
                $set[] = 'cognome = ?';
                $parametri[] = $values['cognome'];
            }

            if($values['username'] !== $datiUtente['username']) {
                $set[] = 'username = ?';
                $parametri[] = $values['username'];
            }

            if(!empty($values['password'])) {
                $set[] = 'password = ?';
                $parametri[] = password_hash($values['password'], PASSWORD_DEFAULT);
            }

            if(!empty($set)) {
                $parametri[] = $_SESSION['email'];
                $connection->updateProfiloUtenteRegistrato($set, $parametri);
            }
            
            if((!empty($values['provincia']) && $values['provincia'] !== $datiUtente['sigla_provincia']) || (!empty($values['comune']) && $values['comune'] !== $datiUtente['id_comune']) || $values['via'] !== $datiUtente['via']) {
                $connection->beginTransaction();
                try {
                    $idIndirizzo = $connection->insertIndirizzo(empty($values['provincia']) ? $datiUtente['sigla_provincia'] : $values['provincia'], empty($values['comune']) ? $datiUtente['id_comune'] : $values['comune'], $values['via']);

                    $connection->updateUtente($_SESSION['email'], $idIndirizzo);

                    $connection->commit();
                } catch(mysqli_sql_exception $e) {
                    $connection->rollback();
                    throw $e;
                }
            }

            header('Location: profilo.php');
            exit;
        }
    } catch(mysqli_sql_exception $e) {
        http_response_code(500);
        header('Location: ../HTML/error_500.html');
        exit;
    } finally {
        $connection->closeConnection();
    }
} else {
    header('Location: profilo.php');
    exit;
}
?>