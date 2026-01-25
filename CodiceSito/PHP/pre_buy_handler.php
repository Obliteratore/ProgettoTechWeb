<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

require_once "db_connection.php";
use FM\FMAccess;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = [];
    try {
        $connection = new FMAccess();
        $connection->openConnection();

        $prodotti = $connection->getCarrello($_SESSION['email']);
        if(isset($_SESSION['email']) && !empty($prodotti)) {
            $values = $connection->getProfiloUtenteRegistrato($_SESSION['email']);
            $values['email'] = $_SESSION['email'];
            $_SESSION['values'] = $values;
            $_SESSION['provinciaRegistrata'] = $values['provincia'];
            $_SESSION['comuneRegistrato'] = $values['comune'];
        } elseif(!isset($_SESSION['carrello_ospite']) || count($_SESSION['carrello_ospite']) < 1) {
            header('Location: carrello.php');
            exit;
        }
        header('Location: acquisto.php');
        exit;
    } catch(mysqli_sql_exception $e) {
        http_response_code(500);
        header('Location: ../HTML/error_500.html');
        exit;
    } finally {
        $connection->closeConnection();
    }
} else {
    header('Location: carrello.php');
    exit;
}
?>