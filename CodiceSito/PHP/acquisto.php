<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

require_once "db_connection.php";
use FM\FMAccess;

function pulisciInput($value) {
    $value = trim($value);
    $value = strip_tags($value);
    return $value;
}

function validateEmail(&$errors, $email, $connection) {
    if($email !== 'user') {
        if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors['email'] = 'L\'<span lang="en">email</span> non è valida.';
    }
}

function validateProvincia(&$errors, $provincia, $connection) {
    if(empty($provincia))
        $errors['provincia'] = 'La provincia non è valida.';
    else {
        $exist = $connection->existProvincia($provincia);
        if(!$exist)
            $errors['provincia'] = 'La provincia non è valida.';
    }
}

function validateComune(&$errors, $comune, $provincia, $connection) {
    if(empty($comune))
        $errors['comune'] = 'Il comune non è valido.';
    else {
        $exist = $connection->existComune($comune, $provincia);
        if(!$exist)
            $errors['comune'] = 'Il comune non è valido.';
    }
}

function validateVia(&$errors, $via) {
    $regex = '/^[A-Za-zÀ-ÖØ-öø-ÿ0-9\s\.\-\/\']+$/';

    if(empty($via) || !preg_match($regex, $via))
        $errors['via'] = 'La via non è valida.';
}

function getValues(&$values) {
    $values['email'] = isset($_POST['email']) ? pulisciInput($_POST['email']) : '';
    $values['provincia'] = isset($_POST['provincia']) ? pulisciInput($_POST['provincia']) : '';
    $values['comune'] = isset($_POST['comune']) ? pulisciInput($_POST['comune']) : '';
    $values['via'] = isset($_POST['via']) ? pulisciInput($_POST['via']) : '';
}

function callValidators(&$errors, $values, $connection) {
    validateEmail($errors, $values['email'], $connection);
    validateProvincia($errors, $values['provincia'], $connection);
    validateComune($errors, $values['comune'], $values['provincia'], $connection);
    validateVia($errors, $values['via']);
}

function writeFirstHtml($datiUtente, $connection) {
    $paginaHTML = file_get_contents('../HTML/acquisto.html');

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

    $valori['[valore-email]'] = $_SESSION['email'];
    $invalid['[invalid-email]'] = 'false';
    $errori['[errore-email]'] = '';

    foreach($datiUtente as $chiave => $valore) {
        if($chiave === 'via') {
            $valori['[valore-' . $chiave . ']'] = $valore;
            $invalid['[invalid-' . $chiave . ']'] = 'false';
            $errori['[errore-' . $chiave . ']'] = '';
        } elseif($chiave === 'provincia') {
            $valori['[valore-' . $chiave . ']'] = $provincie;
            $invalid['[invalid-' . $chiave . ']'] = 'false';
            $errori['[errore-' . $chiave . ']'] = '';
        } elseif($chiave === 'comune') {
            $valori['[valore-' . $chiave . ']'] = $comuni;
                $invalid['[invalid-' . $chiave . ']'] = 'false';
                $errori['[errore-' . $chiave . ']'] = '';
        }
    }

    $paginaHTML = strtr($paginaHTML, $valori + $invalid + $errori);
    echo $paginaHTML;
}

function writeHtml($values, $errors, $connection) {
    $paginaHTML = file_get_contents('../HTML/acquisto.html');

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

    $paginaHTML = strtr($paginaHTML, $valori + $invalid + $errori);
    echo $paginaHTML;
}

try {
    $connection = new FMAccess();
    $connection->openConnection();

    $email = $_SESSION['email'] ?? '';
    $values = [];
    $errors = [];
    getValues($values);

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        callValidators($errors, $values, $connection, $datiUtente);

        if(!empty($errors)) {
            writeHtml($values, $errors, $connection);
            exit;
        } else {
            $prodotti = $connection->getCarrello($email);
            if(!empty($email) && !empty($prodotti)) {
                $idIndirizzo = $connection->existIndirizzo($values['provincia'], (int)$values['comune'], $values['via']);
                if(!isset($idIndirizzo)) {
                    $idIndirizzo = $connection->insertIndirizzo($values['provincia'], (int)$values['comune'], $values['via']);
                }

                $connection->beginTransaction();
                try {
                    $idOrdine = $connection->insertOrdine($email, $idIndirizzo);

                    foreach($prodotti as $prodotto) {
                        $connection->insertDettaglioOrdine($idOrdine, $prodotto['nome_latino'], $prodotto['quantita'], $prodotto['prezzo']);

                        $pesce_db = $connection->getPesce($prodotto['nome_latino']);
                        $disponibilita = (int)$pesce_db['disponibilita']-(int)$prodotto['quantita'];
                        $connection->updateDisponibilitaPesce($prodotto['nome_latino'], $disponibilita);

                        $connection->cancellaDaCarrello($email, $prodotto['nome_latino']);
                    }

                    $connection->commit();
                } catch(mysqli_sql_exception $e) {
                    $connection->rollback();
                    throw $e;
                }

                header('Location: ../PHP/carrello.php');
                exit;
            } elseif(isset($_SESSION['carrello_ospite']) && count($_SESSION['carrello_ospite']) > 0) {
                $email = $values['email'];
                $prodotti = [];
                $nomi_latini = array_keys($_SESSION['carrello_ospite']);
                $prodotti = $connection->getPesciPerCarrelloOspite($nomi_latini);

                $existEmail = $connection->existEmailUtenteNonRegistrato($email);
                if(!$existEmail)
                    $connection->insertUtente($email, null);

                $idIndirizzo = $connection->existIndirizzo($values['provincia'], (int)$values['comune'], $values['via']);
                if(!isset($idIndirizzo)) {
                    $idIndirizzo = $connection->insertIndirizzo($values['provincia'], (int)$values['comune'], $values['via']);
                }

                $connection->beginTransaction();
                try {
                    $idOrdine = $connection->insertOrdine($email, $idIndirizzo);

                    foreach($prodotti as $prodotto) {
                        $connection->insertDettaglioOrdine($idOrdine, $prodotto['nome_latino'], $_SESSION['carrello_ospite'][$prodotto['nome_latino']], $prodotto['prezzo']);

                        $disponibilita = (int)$prodotto['disponibilita']-$_SESSION['carrello_ospite'][$prodotto['nome_latino']];
                        $connection->updateDisponibilitaPesce($prodotto['nome_latino'], $disponibilita);
                    }

                    $connection->commit();
                } catch(mysqli_sql_exception $e) {
                    $connection->rollback();
                    throw $e;
                }

                unset($_SESSION['carrello_ospite']);
                header('Location: ../PHP/carrello.php');
                exit;
            } else {
                header('Location: ../PHP/carrello.php');
                exit;
            }
        }
    } else {
        $prodotti = $connection->getCarrello($email);
        if(!empty($email) && !empty($prodotti)) {
            $datiUtente = $connection->getProfiloUtenteRegistrato($email);
            writeFirstHtml($datiUtente, $connection);
            exit;
        } elseif(isset($_SESSION['carrello_ospite']) && count($_SESSION['carrello_ospite']) > 0) {
            writeHtml($values, $errors, $connection);
        } else {
            header('Location: ../PHP/carrello.php');
            exit;
        }
    }
} catch(mysqli_sql_exception $e) {
    http_response_code(500);
    header('Location: ../HTML/error_500.html');
    exit;
} finally {
    $connection->closeConnection();
}
?>