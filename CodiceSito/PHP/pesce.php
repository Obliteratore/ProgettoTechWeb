<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "db_connection.php";
use FM\FMAccess;

$nome_comune = "";
$nome_latino_db = "";
$prezzo = "";
$disponibilita = 0;
$famiglia = "";
$descrizione = "";
$dimensione = "";
$percorso = "";
$volumemin = 0;
$habitat = "";
$colori = "";

$paginaHTML = file_get_contents('../HTML/pesce.html');

try{
    $connection = new FMAccess();
    $connection->openConnection();
    if($connection->openConnection());
    $nome_latinos = $_GET['nome_latino'] ?? '';
    $pesce = null;
    if(!empty($nome_latinos)){
        $pesce = $connection->getPesce($nome_latinos);
        if($pesce!= null){
            $nome_comune = htmlspecialchars($pesce['nome_comune']);
            $nome_latino_db = htmlspecialchars($pesce['nome_latino']);
            $prezzo = number_format($pesce['prezzo'], 2, ',', '.');
            $disponibilita = (int)$pesce['disponibilita'];
            $famiglia = htmlspecialchars($pesce['famiglia']);
            $descrizione = htmlspecialchars($pesce['descrizione']);
            $dimensione = htmlspecialchars($pesce['dimensione']);
            $percorso = "../IMMAGINI/Pesci/Pesci Dolci/Agujeta.jpg"; //da modificare in $pesce['immagine'];
            $volumemin = $pesce['volume_minimo'];
            $habitat = ($pesce['tipo_acqua'] === 'dolce') ? "Acqua Dolce" : "Acqua Marina";
            $colori = htmlspecialchars($pesce['colori']);

            $paginaHTML = str_replace("[nomepesce]", $nome_comune, $paginaHTML);
            $paginaHTML = str_replace("[nomelatino]", $nome_latino_db, $paginaHTML);
            $paginaHTML = str_replace("[prezzopesce]", $prezzo, $paginaHTML);
            $paginaHTML = str_replace("[disponibilita-pesce]", $disponibilita, $paginaHTML);
            $paginaHTML = str_replace("[famiglia]", $famiglia, $paginaHTML);
            $paginaHTML = str_replace("[habitat]", $habitat, $paginaHTML);
            $paginaHTML = str_replace("[volume-min]", $volumemin, $paginaHTML);
            $paginaHTML = str_replace("[dimensione]", $dimensione, $paginaHTML);
            $paginaHTML = str_replace("[descrizione-pesce]", $descrizione, $paginaHTML);
            $paginaHTML = str_replace("[percorso]", $percorso, $paginaHTML);
            $paginaHTML = str_replace("[colori]", $colori, $paginaHTML);

        } else {
            header('Location:../HTML/errore_404.html');
            exit;
        }
    } else {
        header('Location:../HTML/errore_404.html');
        exit;
    }
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    header('Location:../HTML/error_500.html');
    exit;
} finally {
    $connection->closeConnection();
}

echo $paginaHTML;
?>