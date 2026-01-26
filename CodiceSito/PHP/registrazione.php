<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

if(isset($_SESSION['email'])) {
    header('Location: profilo.php');
    exit;
}

require_once "validate_functions.php";
require_once "db_connection.php";
use FM\FMAccess;

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

function getValues(&$values) {
    $values['nome'] = isset($_POST['nome']) ? pulisciInput($_POST['nome']) : '';
    $values['cognome'] = isset($_POST['cognome']) ? pulisciInput($_POST['cognome']) : '';
    $values['provincia'] = isset($_POST['provincia']) ? pulisciInput($_POST['provincia']) : '';
    $values['comune'] = isset($_POST['comune']) ? pulisciInput($_POST['comune']) : '';
    $values['via'] = isset($_POST['via']) ? pulisciInput($_POST['via']) : '';
    $values['email'] = isset($_POST['email']) ? pulisciInput($_POST['email']) : '';
    $values['username'] = isset($_POST['username']) ? pulisciInput($_POST['username']) : '';
    $values['password'] = isset($_POST['password']) ? $_POST['password'] : '';
    $values['confermaPassword'] = isset($_POST['confermaPassword']) ? $_POST['confermaPassword'] : '';
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

function writeHtml($values, $errors, $connection) {
    $paginaHTML = file_get_contents('../HTML/registrazione.html');

    $provincie = '';
    $listaProvincie = $connection->getProvincie();
    foreach($listaProvincie as $provincia) {
        if($values['provincia'] === $provincia['sigla_provincia'])
            $provincie .= '<option value="'. htmlspecialchars($provincia['sigla_provincia']) . '" selected>' . htmlspecialchars($provincia['nome']) . '</option>';
        else
            $provincie .= '<option value="'. htmlspecialchars($provincia['sigla_provincia']) . '">' . htmlspecialchars($provincia['nome']) . '</option>';
    }

    $comuni = '';
    if(!empty($values['provincia']) && !empty($values['comune'])) {
        $listaComuni = $connection->getComuni($values['provincia']);
        foreach($listaComuni as $comune) {
            if($values['comune'] === (string)$comune['id_comune'])
                $comuni .= '<option value="'. htmlspecialchars($comune['id_comune']) . '" selected>' . htmlspecialchars($comune['nome']) . '</option>';
            else
                $comuni .= '<option value="'. htmlspecialchars($comune['id_comune']) . '">' . htmlspecialchars($comune['nome']) . '</option>';
        }
    }

    $valori = [];
    $invalid = [];
    $errori = [];
    foreach($values as $chiave => $valore) {
        if($chiave !== 'provincia' && $chiave !== 'comune')
            $valori['[valore-' . $chiave . ']'] = $valore;
        elseif($chiave === 'provincia')
            $valori['[valore-' . $chiave . ']'] = $provincie;
        else
            $valori['[valore-' . $chiave . ']'] = $comuni;

        $invalid['[invalid-' . $chiave . ']'] = isset($errors[$chiave]) ? 'true' : 'false';
        $errori['[errore-' . $chiave . ']'] = $errors[$chiave] ?? '';
    }
    $summary = $errors['summary'] ?? '';

    $paginaHTML = strtr($paginaHTML, $valori + $invalid + $errori);
    $paginaHTML = str_replace('[errore-summary]', $summary, $paginaHTML);
    echo $paginaHTML;
}

$values = [];
$errors = [];

getValues($values);

try {
    $connection = new FMAccess();
    $connection->openConnection();
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
        callValidators($errors, $values, $connection);
        setSummary($errors);

        if(!empty($errors)) {
            writeHtml($values, $errors, $connection);
            exit;
        } else {
            $connection->beginTransaction();
            try {
                $idIndirizzo = $connection->insertIndirizzo($values['provincia'], $values['comune'], $values['via']);
                
                if(!$connection->existEmailUtenteNonRegistrato($values['email']))
                    $connection->insertUtente($values['email'], $idIndirizzo);
                else
                    $connection->updateUtente($values['email'], $idIndirizzo);
                
                $connection->insertUtenteRegistrato($values['email'], $values['username'], $values['password'], $values['nome'], $values['cognome']);

                $connection->commit();
            } catch(mysqli_sql_exception $e) {
                $connection->rollback();
                throw $e;
            }
            session_regenerate_id(true); 
            $_SESSION['email'] = $values['email'];
            header('Location: profilo.php');
            exit;
        }
    } else
        writeHtml($values, $errors, $connection);
} catch(mysqli_sql_exception $e) {
    http_response_code(500);
    header('Location: ../HTML/error_500.html');
    exit;
} finally {
    $connection->closeConnection();
}
?>