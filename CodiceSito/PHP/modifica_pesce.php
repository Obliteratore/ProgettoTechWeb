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
        "[disponibilita]" => $p["disponibilita"],

        "[errore-nome-comune]" => '',
        "[errore-dimensione]" => '',
        "[errore-volume_minimo]" => '',
        "[errore-colori]" => '',
        "[errore-prezzo]" => '',
        "[errore-disponibilita]" => '',
        "[errore-immagine]" => '',
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

    $errors= [];

    if (strlen($nome_comune) < 2) {
    $errors['nome_comune'] = "Deve avere almeno 2 caratteri.";
    } elseif (preg_match('/[^A-Za-zÀ-ÿ\s]/', $nome_comune)) {
    $errors['nome_comune'] = "Sono ammesse solo lettere.";
    }

    if (!is_numeric($dimensione) || $dimensione <= 0) {
    $errors['dimensione'] = "Inserisci un numero valido maggiore di 0.";
    }

    if (!is_numeric($volume_minimo) || $volume_minimo <= 0) {
    $errors['volume_minimo'] = "Inserisci un numero valido maggiore di 0.";
    }

    if (strlen($colori) < 2) {
    $errors['colori'] = "Deve avere almeno 2 caratteri.";
    }

    if (!is_numeric($prezzo) || $prezzo < 0) {
    $errors['prezzo'] = "Prezzo non valido.";
    }

    if (!is_numeric($disponibilita) || $disponibilita < 0) {
    $errors['disponibilita'] = "Inserisci un numero valido.";
    }
    
    $percorso = null;

    if (!empty($_FILES["immagine"]["name"])) {
        $tipoPermesso = ['image/jpeg', 'image/jpg'];
        $tipoFile = mime_content_type($_FILES["immagine"]["tmp_name"]);

    if (!in_array($tipoFile, $tipoPermesso)) {
        $errors['immagine'] = "Formato immagine non consentito. Usa JPG o JPEG";
    } else {
        $nomeFile = uniqid() . "_" . basename($_FILES["immagine"]["name"]);
        $percorso = "../IMMAGINI/Pesci/" . $nomeFile;

        if (!move_uploaded_file($_FILES["immagine"]["tmp_name"], $percorso)) {
            $errors['immagine'] = "Errore durante il caricamento dell'immagine";
        }
    }
}
       if (!empty($errors)) {
        
        $paginaHTML = file_get_contents('../HTML/modifica_pesce.html');
        $segnaposto = [
            "[nome_latino]" => htmlspecialchars($nome_latino),
            "[nome_comune]" => htmlspecialchars($nome_comune),
            "[famiglia]" => htmlspecialchars($p['famiglia']),
            "[tipo_acqua]" => htmlspecialchars($p['tipo_acqua']),
            "[dimensione]" => htmlspecialchars($dimensione),
            "[volume_minimo]" => htmlspecialchars($volume_minimo),
            "[colori]" => htmlspecialchars($colori),
            "[prezzo]" => htmlspecialchars($prezzo),
            "[disponibilita]" => htmlspecialchars($disponibilita),

            
            "[errore-nome-comune]" => $errors['nome_comune'] ?? "",
            "[errore-dimensione]" => $errors['dimensione'] ?? "",
            "[errore-volume-minimo]" => $errors['volume_minimo'] ?? "",
            "[errore-colori]" => $errors['colori'] ?? "",
            "[errore-prezzo]" => $errors['prezzo'] ?? "",
            "[errore-disponibilita]" => $errors['disponibilita'] ?? "",
            "[errore-immagine]" => $errors['immagine'] ?? "",
        ];

        echo str_replace(array_keys($segnaposto), array_values($segnaposto), $paginaHTML);
        exit; 
    }

    
    if ($percorso) {
        $connection->updatePesceIMG($nome_latino, $nome_comune, $dimensione, $volume_minimo, $colori, $prezzo, $disponibilita, $percorso);
    } else {
        $connection->updatePesce($nome_latino, $nome_comune, $dimensione, $volume_minimo, $colori, $prezzo, $disponibilita);
    }

    
    header("Location: admin.php");
    exit;
}
$connection->closeConnection();
?>
