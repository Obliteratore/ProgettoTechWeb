<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

if (!isset($_SESSION['email'])) {
    header('Location: accesso.php');
    exit;
}

require_once "db_connection.php";
use FM\FMAccess;
?>