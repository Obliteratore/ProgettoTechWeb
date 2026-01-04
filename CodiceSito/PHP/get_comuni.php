<?php

require_once "db_connection.php";
use FM\FMAccess;

header('Content-Type: application/json');

$connection = new FMAccess();
$connectionOk = $connection->openConnection();

if($connectionOk) {
    $provincia = $_GET['provincia'] ?? '';

    $comuni = $connection->getComuni($provincia);

    $connection->closeConnection();

    echo json_encode($comuni);
}
?>