<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

if(!isset($_SESSION['email'])) {
    header('Location: accesso.php');
    exit;
}

require_once "db_connection.php";
use FM\FMAccess;

$values = [];

try {
    $connection = new FMAccess();
    $connection->openConnection();

    $values = $connection->getProfiloUtenteRegistrato($_SESSION['email']);
    $_SESSION['values'] = $values;
    $_SESSION['provinciaRegistrata'] = $values['provincia'];
    $_SESSION['comuneRegistrato'] = $values['comune'];
    header('Location: modifica_profilo.php');
    exit;
} catch(mysqli_sql_exception $e) {
    http_response_code(500);
    header('Location: ../HTML/error_500.html');
    exit;
} finally {
    $connection->closeConnection();
}
?>