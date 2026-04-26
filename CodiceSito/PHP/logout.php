<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

if(!isset($_SESSION['email'])) {
    header('Location: ../PHP/accesso.php');
    exit;
}

$_SESSION = [];
session_destroy();
setcookie(session_name(), '', time() - 3600, '/', '', true, true);

header('Location: ../PHP/accesso.php');
exit;
?>