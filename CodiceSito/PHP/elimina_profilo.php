<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

if(!isset($_SESSION['email'])) {
    header('Location: ../PHP/accesso.php');
    exit;
}

require_once "db_connection.php";
use FM\FMAccess;

try {
    $connection = new FMAccess();
    $connection->openConnection();
    
    $connection->deleteUtenteRegistrato($_SESSION['email']);

    $_SESSION = [];
    session_destroy();
    setcookie(session_name(), '', time() - 3600, '/', '', true, true);

    header('Location: ../HTML/profilo_eliminato.html');
    exit;
} catch(mysqli_sql_exception $e) {
    http_response_code(500);
    header('Location: ../HTML/error_500.html');
    exit;
} finally {
    $connection->closeConnection();
}
?>