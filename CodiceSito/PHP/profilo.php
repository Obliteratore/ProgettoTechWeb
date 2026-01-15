<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

/*if(!isset($_SESSION['email'])) {
    header('Location: accesso.php');
    exit;
}*/

require_once "db_connection.php";
use FM\FMAccess;

$nomeCognome = '';
$username = '';
$indirizzo = '';
$ordini = '';
$paginaHTML = '';
$paginaHTML = file_get_contents('../HTML/profilo.html');

try{
    $connection = new FMAccess();
    $connection->openConnection();

    $datiUtente = $connection->getProfiloUtente('user'); //$_SESSION['email']) al posto di 'user'
    $nomeCognome = $datiUtente['nome'] . ' ' . $datiUtente['cognome'];
    $username = $datiUtente['username'];
    $indirizzo = $datiUtente['via'] . ', ' . $datiUtente['comune'] . ', <abbr title="' . $datiUtente['provincia'] . '">' . $datiUtente['sigla_provincia'] . '</abbr>';
} catch(mysqli_sql_exception $e) {
    http_response_code(500);
    header('Location: ../HTML/error_500.html');
    exit;
} finally {
    $connection->closeConnection();
}

$paginaHTML = str_replace(['[nomeCognome]', '[username]', '[indirizzo]'], [$nomeCognome, $username, $indirizzo], $paginaHTML);
echo $paginaHTML;
?>