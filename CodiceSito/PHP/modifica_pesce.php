<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "db_connection.php";
use FM\FMAccess;

$paginaHTML = file_get_contents('../HTML/modifica_pesce.html');

if (!$paginaHTML) {
    die("Errore caricamento pagina");
}

$connection = new FMAccess();
$connection->openConnection();

if($_SERVER["REQUEST_METHOD"] === "GET"){
    if(!isset($_GET['nome_latino'])){
        die("Pesce non specificato");
    }

    $nomeLatino = $_GET['nome_latino'];

    $pesce = $connection->getPesciJOIN(["pesci.nome_latino = ?"],[$nomeLatino], "", "LEFT JOIN famiglie ON pesci.famiglia = famiglie.famiglia_latino");

    if (count($pesce)!= 1){
        die("Pesce non trovato");
    }

    $p=$pesce[0];

    $segnaposto = [ "[nome_latino]" => htmlspecialchars($p["nome_latino"]),
        "[nome_comune]" => htmlspecialchars($p["nome_comune"]),
        "[famiglia]" => htmlspecialchars($p["famiglia"]),
        "[tipo_acqua]" => htmlspecialchars($p["tipo_acqua"]),
        "[dimensione]" => $p["dimensione"],
        "[volume_minimo]" => $p["volume_minimo"],
        "[colori]" => htmlspecialchars($p["colori"]),
        "[prezzo]" => $p["prezzo"],
        "[disponibilita]" => $p["disponibilita"]
    ];

    echo str_replace(array_keys($segnaposto), array_values($segnaposto),$paginaHTML);

}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pesce= $connection->getPesciJOIN(["pesci.nome_latino = ?"], [$_POST['nome_latino']], "", "LEFT JOIN famiglie ON pesci.famiglia = famiglie.famiglia_latino");

    if(count($pesce) != 1){
        die("Pesce non trovato");
    }

    $p=$pesce[0];

    $nome_latino =$p["nome_latino"];
    $nome_comune = htmlspecialchars(trim($_POST["nome_comune"]));
    $famiglia = $p["famiglia"];
    $tipo_acqua = $p["tipo_acqua"];
    $dimensione = htmlspecialchars(trim($_POST["dimensione"]));
    $volume_minimo = htmlspecialchars(trim($_POST["volume_minimo"]));
    $colori = htmlspecialchars(trim($_POST["colori"]));
    $prezzo = htmlspecialchars(trim($_POST["prezzo"]));
    $disponibilita = htmlspecialchars(trim($_POST["disponibilita"]));

    if (!empty($_FILES["immagine"]["name"])) {
        $tipoPermesso = ['image/jpeg', 'image/jpg'];
        $tipoFile = mime_content_type($_FILES["immagine"]["tmp_name"]);
        if (!in_array($tipoFile, $tipoPermesso)) {
            die("Formato immagine non consentito. Usa JPG o JPEG");
        }
        $nomeFile = uniqid() . "_" . basename($_FILES["immagine"]["name"]);
        $percorso = "../IMMAGINI/Pesci/" . $nomeFile;

        if(!move_uploaded_file($_FILES["immagine"]["tmp_name"],$percorso)){
            die("Errore durante il caricamento dell'immagine");
        }

        $connection->updatePesceIMG($nome_latino, $nome_comune,$dimensione, $volume_minimo, $colori, $prezzo, $disponibilita, $percorso);
    
    }

    else {

        $connection->updatePesce($nome_latino, $nome_comune,$dimensione, $volume_minimo, $colori, $prezzo, $disponibilita);

    }
    
    header("Location: admin.php");
    exit;
}
$connection->closeConnection();
?>
