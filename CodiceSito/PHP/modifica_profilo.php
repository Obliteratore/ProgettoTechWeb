<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

if(!isset($_SESSION['email'])) {
    header('Location: ../PHP/accesso.php');
    exit;
}

require_once "validate_functions.php";
require_once "db_connection.php";
use FM\FMAccess;

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
    $values['nome'] = isset($_POST['nome']) ? pulisciInput($_POST['nome']) : '';
    $values['cognome'] = isset($_POST['cognome']) ? pulisciInput($_POST['cognome']) : '';
    $values['provincia'] = isset($_POST['provincia']) ? pulisciInput($_POST['provincia']) : '';
    $values['comune'] = isset($_POST['comune']) ? pulisciInput($_POST['comune']) : '';
    $values['via'] = isset($_POST['via']) ? pulisciInput($_POST['via']) : '';
    $values['username'] = isset($_POST['username']) ? pulisciInput($_POST['username']) : '';
    $values['password'] = isset($_POST['password']) ? $_POST['password'] : '';
    $values['confermaPassword'] = isset($_POST['confermaPassword']) ? $_POST['confermaPassword'] : '';
}

function callValidators(&$errors, $values, $connection, $datiUtente) {
    if($values['nome'] !== $datiUtente['nome'])
        validateNome($errors, $values['nome']);
    if($values['cognome'] !== $datiUtente['cognome'])
        validateCognome($errors, $values['cognome']);
    if($values['provincia'] !== $datiUtente['sigla_provincia'])
        validateProvincia($errors, $values['provincia'], $connection);
    if($values['provincia'] !== $datiUtente['sigla_provincia'] || (int)$values['comune'] !== $datiUtente['id_comune'])
        validateComune($errors, $values['comune'], $values['provincia'], $connection);
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

function writeFirstHtml($datiUtente, $connection) {
    $paginaHTML = file_get_contents('../HTML/modifica_profilo.html');

    $provincie = '';
    $listaProvincie = $connection->getProvincie();
    foreach($listaProvincie as $provincia) {
        if($datiUtente['sigla_provincia'] === $provincia['sigla_provincia'])
            $provincie .= '<option value="'. htmlspecialchars($provincia['sigla_provincia']) . '" selected>' . htmlspecialchars($provincia['nome']) . '</option>';
        else
            $provincie .= '<option value="'. htmlspecialchars($provincia['sigla_provincia']) . '">' . htmlspecialchars($provincia['nome']) . '</option>';
    }

    $comuni = '';
    $listaComuni = $connection->getComuni($datiUtente['sigla_provincia']);
    foreach($listaComuni as $comune) {
        if($datiUtente['id_comune'] === $comune['id_comune'])
            $comuni .= '<option value="'. htmlspecialchars($comune['id_comune']) . '" selected>' . htmlspecialchars($comune['nome']) . '</option>';
        else
            $comuni .= '<option value="'. htmlspecialchars($comune['id_comune']) . '">' . htmlspecialchars($comune['nome']) . '</option>';
    }

    $valori = [];
    $invalid = [];
    $errori = [];
    foreach($datiUtente as $chiave => $valore) {
        switch($chiave) {
            case 'nome':
            case 'cognome':
            case 'via':
            case 'username':
                $valori['[valore-' . $chiave . ']'] = $valore;
                $invalid['[invalid-' . $chiave . ']'] = 'false';
                $errori['[errore-' . $chiave . ']'] = '';
                break;
            case 'provincia':
                $valori['[valore-' . $chiave . ']'] = $provincie;
                $invalid['[invalid-' . $chiave . ']'] = 'false';
                $errori['[errore-' . $chiave . ']'] = '';
                break;
            case 'comune':
                $valori['[valore-' . $chiave . ']'] = $comuni;
                $invalid['[invalid-' . $chiave . ']'] = 'false';
                $errori['[errore-' . $chiave . ']'] = '';
                break;
        }
    }
    $invalid['[invalid-password]'] = 'false';
    $errori['[errore-password]'] = '';
    $invalid['[invalid-confermaPassword]'] = 'false';
    $errori['[errore-confermaPassword]'] = '';
    $summary = '';

    $paginaHTML = strtr($paginaHTML, $valori + $invalid + $errori);
    $paginaHTML = str_replace('[errore-summary]', $summary, $paginaHTML);
    echo $paginaHTML;
}

function writeHtml($values, $errors, $connection) {
    $paginaHTML = file_get_contents('../HTML/modifica_profilo.html');

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
   
try {
    $connection = new FMAccess();
    $connection->openConnection();
    $datiUtente = $connection->getProfiloUtenteRegistrato($_SESSION['email']);
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $values = [];
        $errors = [];

        getValues($values);
        callValidators($errors, $values, $connection, $datiUtente);
        setSummary($errors);

        if(!empty($errors)) {
            writeHtml($values, $errors, $connection);
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
            
            if($values['provincia'] !== $datiUtente['sigla_provincia'] || (int)$values['comune'] !== $datiUtente['id_comune'] || $values['via'] !== $datiUtente['via']) {
                $connection->beginTransaction();
                try {
                    $idIndirizzo = $connection->insertIndirizzo($values['provincia'], (int)$values['comune'], $values['via']);

                    $connection->updateUtente($_SESSION['email'], $idIndirizzo);

                    $connection->commit();
                } catch(mysqli_sql_exception $e) {
                    $connection->rollback();
                    throw $e;
                }
            }

            header('Location: ../PHP/profilo.php');
            exit;
        }
    } else
        writeFirstHtml($datiUtente, $connection);
} catch(mysqli_sql_exception $e) {
    http_response_code(500);
    header('Location: ../HTML/error_500.html');
    exit;
} finally {
    $connection->closeConnection();
}
?>