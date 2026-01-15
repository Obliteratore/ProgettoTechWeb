<?php

require_once "db_connection.php";
use FM\FMAccess;

header('Content-Type: application/json');

try{
    $connection = new FMAccess();
    $connection->openConnection();

    $provincia = $_POST['provincia'] ?? '';
 
    $comuni = $connection->getComuni($provincia);
    echo json_encode(['success' => true, 'data' => $comuni]);
} catch(mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false]);
} finally {
    $connection->closeConnection();
}
?>