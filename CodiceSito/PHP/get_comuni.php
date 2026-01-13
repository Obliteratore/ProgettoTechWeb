<?php

require_once "db_connection.php";
use FM\FMAccess;

header('Content-Type: application/json');

try{
    $connection = new FMAccess();
    $connection->openConnection();

    $provincia = $_POST['provincia'] ?? '';
 
    $comuni = $connection->getComuni($provincia);

    echo json_encode($comuni);
} catch(mysqli_sql_exception $e) {
    header('Location: ../HTML/error_500.html');
    exit;
} finally {
    $connection->closeConnection();
}
?>