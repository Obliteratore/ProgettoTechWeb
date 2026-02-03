<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

if(!isset($_SESSION['email'])) {
    header('Location: ../PHP/accesso.php');
    exit;
}

if($_SESSION['email'] !== 'admin') {
    header('Location: ../PHP/profilo.php');
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "db_connection.php";
use FM\FMAccess;

$paginaHTML = file_get_contents('../HTML/modifica_pesce.html');

$connection = new FMAccess();
$connection->openConnection();

if($_SERVER["REQUEST_METHOD"] === "GET"){
    if(!isset($_GET['nome_latino'])){
        header('Location: ../HTML/error_404.html');
        exit;
    }

    $nomeLatino = $_GET['nome_latino'];

    $pesce = $connection->getPesci(["p.nome_latino = ?"],[$nomeLatino]);

    if (count($pesce)!= 1){
        header('Location: ../HTML/error_404.html');
        exit;
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
        "[errore-volume-minimo]" => '',
        "[errore-colori]" => '',
        "[errore-prezzo]" => '',
        "[errore-disponibilita]" => '',
        "[errore-immagine]" => '',
        "[invalid-nome-comune]" => 'false',
        "[invalid-dimensione]" => 'false',
        "[invalid-volume-minimo]" => 'false',
        "[invalid-colori]" => 'false',
        "[invalid-prezzo]" => 'false',
        "[invalid-disponibilita]" => 'false',
        "[invalid-immagine]" =>"false",
    ];

    echo str_replace(array_keys($segnaposto), array_values($segnaposto),$paginaHTML);

}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pesce = $connection->getPesci(["p.nome_latino = ?"],[$_POST['nome_latino']]);

    if(count($pesce) != 1){
         header('Location: ../HTML/error_404.html');
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
    $percorso = null;

    $errore= [
    'nome_comune' => [],
    'dimensione' => [],
    'volume_minimo' => [],
    'colori' => [],
    'prezzo' => [],
    'disponibilita' => [],
    'immagine' => []
    ];

    if (strlen($nome_comune) < 2 || preg_match('/[^A-Za-zÀ-ÿ\s]/', $nome_comune)) {
    $errore['nome_comune'][] = "Nome comune non valido.";
    } 
    
    if (!is_numeric($dimensione) || $dimensione <= 0) {
    $errore['dimensione'][] = "Dimensione non valida.";
    }

    if (!is_numeric($volume_minimo) || $volume_minimo <= 0) {
    $errore['volume_minimo'][] = "Volume minimo non valido.";
    }

    $listaColori = ['giallo','arancione','rosso','beige','rosa','blu','azzurro','verde','marrone','nero','grigio','trasparente'];
    if(!preg_match('/^[A-Za-zÀ-ÿ]+(,[A-Za-zÀ-ÿ]+)*$/', $colori)) {
        $errore['colori'][] = "Formato non valido. Usa lettere separate da virgole senza spazi, ad esempio: rosso,blu,verde";
    } else {
        foreach(explode(',', $colori) as $c) {
            if(!in_array(strtolower($c), $listaColori)) {
                $errore['colori'][] = "Il colore '$c' non è nella lista consentita.";
            }
        }
    }

    if (!is_numeric($prezzo) || $prezzo <= 0) {
    $errore['prezzo'][] = "Prezzo non valido.";
    }

    if (!is_numeric($disponibilita) || $disponibilita < 0) {
    $errore['disponibilita'][] = "Disponibilità non valida.";
    }
    
    if (!empty($_FILES["immagine"]["name"])) {
    $tipoPermesso = ['image/jpeg', 'image/jpg', 'image/webp'];
    $tipoFile = mime_content_type($_FILES["immagine"]["tmp_name"]);

    if (!in_array($tipoFile, $tipoPermesso)) {
        $errore['immagine'][] = "Formato immagine non consentito.";
    } else {
        
        list($larg, $alt) = getimagesize($_FILES["immagine"]["tmp_name"]);
        if ($larg !== 1024 || $alt !== 683) {
            $errore['immagine'][] = "Dimensioni non valide."; 
        } else {
            $nomeFile = uniqid() . "_" . basename($_FILES["immagine"]["name"]);
            $percorso = "../IMMAGINI/Pesci/" . $nomeFile;

            if (!move_uploaded_file($_FILES["immagine"]["tmp_name"], $percorso)) {
                $errore['immagine'][] = "Errore durante il caricamento dell'immagine";
            }
        }
    }
}
     foreach ($errore as $key => $arr) {
        $errore[$key] = empty($arr) ? '' : implode('', array_map(fn($e) => "<li>$e</li>", $arr));
    }

    $anyError = false;
    foreach ($errore as $e) {
        if (!empty($e)) { $anyError = true; break; }
    }
    
       if ($anyError) {
        
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

            "[errore-nome-comune]" => $errore['nome_comune'] ?? "",
            "[errore-dimensione]" => $errore['dimensione'] ?? "",
            "[errore-volume-minimo]" => $errore['volume_minimo'] ?? "",
            "[errore-colori]" => $errore['colori'] ?? "",
            "[errore-prezzo]" => $errore['prezzo'] ?? "",
            "[errore-disponibilita]" => $errore['disponibilita'] ?? "",
            "[errore-immagine]" => $errore['immagine'] ?? "",

            "[invalid-nome-comune]" => !empty($errore['nome_comune']) ? "true" : "false",
            "[invalid-dimensione]" => !empty($errore['dimensione']) ? "true" : "false",
            "[invalid-volume-minimo]" => !empty($errore['volume_minimo']) ? "true" : "false",
            "[invalid-colori]" => !empty($errore['colori']) ? "true" : "false",
            "[invalid-prezzo]" => !empty($errore['prezzo']) ? "true" : "false",
            "[invalid-disponibilita]" => !empty($errore['disponibilita']) ? "true" : "false",
            "[invalid-immagine]" => !empty($errore['immagine']) ? "true" : "false",
        ];

        echo str_replace(array_keys($segnaposto), array_values($segnaposto), $paginaHTML);
        exit; 
    }

    
    if ($percorso) {
        $connection->updatePesceIMG($nome_latino, $nome_comune, $dimensione, $volume_minimo, $colori, $prezzo, $disponibilita, $percorso);
    } else {
        $connection->updatePesce($nome_latino, $nome_comune, $dimensione, $volume_minimo, $colori, $prezzo, $disponibilita);
    }

    
    header("Location: ../PHP/admin.php");
    exit;
}
$connection->closeConnection();
?>
