<?php
require_once "crea_card_pesce.php";
require_once "db_connection.php";
use FM\FMAccess;

$paginaHTML = file_get_contents('../HTML/home.html');

if ($paginaHTML === false) {
    die("Errore: non posso leggere home.html");
}
try {
    $connection = new FMAccess();
    $connection->openConnection();

    $nuoviPesci = $connection->getPesci([], [], "ORDER BY data_inserimento DESC LIMIT 4");
    $piuVendutiPesci = $connection->getPiuVenduti(4);

    $newHTML = crea_card_pesce($nuoviPesci);
    $vendutiHTML = crea_card_pesce($piuVendutiPesci);

    $segnaposto = [
        '[nuovo_pesce]' => $newHTML,
        '[piu_venduto]' => $vendutiHTML
    ];
    $pagina = str_replace(array_keys($segnaposto), array_values($segnaposto), $paginaHTML);

} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    header('Location:../HTML/error_500.html');
    exit;
} finally {
    if (isset($connection)) $connection->closeConnection();
}

echo $pagina;
?>