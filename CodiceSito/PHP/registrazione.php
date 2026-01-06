<?php

require_once "db_connection.php";
use FM\FMAccess;

function pulisciInput($value){
    $value = trim($value);
    $value = strip_tags($value);
    return $value;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nome = if(isset($_POST['nome'])) ? pulisciInput($_POST['nome']) : null;
    $cognome = if(isset($_POST['cognome'])) ? pulisciInput($_POST['cognome']) : null;
    $provincia = if(isset($_POST['provincia'])) ? pulisciInput($_POST['provincia']) : null;
    $comune = if(isset($_POST['comune'])) ? pulisciInput($_POST['comune']) : null;
    $via = if(isset($_POST['via'])) ? pulisciInput($_POST['via']) : null;
    $email = if(isset($_POST['email'])) ? pulisciInput($_POST['email']) : null;
    $username = if(isset($_POST['username'])) ? pulisciInput($_POST['username']) : null;
    $password = if(isset($_POST['password'])) ? $_POST['password'] : null;
    $confermaPassword = if(isset($_POST['confermaPassword'])) ? $_POST['confermaPassword'] : null;

    $errors = [];
}

$connection = new FMAccess();
$connectionOk = $connection->openConnection();

if($connectionOk) {
    
}
?>