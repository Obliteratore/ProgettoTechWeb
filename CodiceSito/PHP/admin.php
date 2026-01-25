<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "db_connection.php";
require_once "crea_righe_pesce_admin.php";
use FM\FMAccess;

$paginaHTML = file_get_contents('../HTML/admin.html');

if ($paginaHTML === false) {
    die("Errore: non posso leggere admin.html");
}

try{
    $connection = new FMAccess();
    $connection->openConnection();

    $pesci = $connection->getPesci([],[],"");
    $righeHTML = crea_righe_pesce_admin($pesci);

    $paginaHTML = str_replace('[righe_pesci]', $righeHTML, $paginaHTML);


}
catch (mysqli_sql_exception $e) {
    http_response_code(500);
    header('Location:../HTML/error_500.html');
    exit;
} finally {
    if (isset($connection)) $connection->closeConnection();
}

echo $paginaHTML;
?>