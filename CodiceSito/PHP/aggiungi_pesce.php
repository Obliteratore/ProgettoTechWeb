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
require_once 'db_connection.php';
use FM\FMAccess;
$paginaHTML = file_get_contents('../HTML/aggiungi_pesce.html');
$connection = new FMAccess();
$connection->openConnection();

if(isset($_GET['check_nome'])) {
    header('Content-Type: application/json');
    $nome = trim($_GET['check_nome']);
    $esistente = $connection->getPesci(["p.nome_latino = ?"], [$nome]);
    echo json_encode(['exists' => count($esistente) > 0]);
    exit;
}

$famiglie = $connection->getFamiglie();

$listaColori= ['nero','verde','rosso','rosa','blu','azzurro','trasparente','marrone','grigio','beige','arancione','giallo'];
$segnaposto = [ 
    "[nome_latino]" => "",
    "[nome_comune]" => "",
    "[famiglia-options]" => "",
    "[dimensione]" => "",
    "[volume_minimo]" => "",
    "[colori]" => "",
    "[prezzo]" => "",
    "[disponibilita]" => "",
    
    "[errore-nome-latino]" => "",
    "[errore-nome-comune]" => "",
    "[errore-famiglia]" => "",
    "[errore-dimensione]" => "",
    "[errore-volume-minimo]" => "",
    "[errore-colori]" => "",
    "[errore-prezzo]" => "",
    "[errore-disponibilita]" => "",
    "[errore-immagine]" => "",
    
    "[invalid-nome-latino]" => "false",
    "[invalid-nome-comune]" => "false",
    "[invalid-famiglia]" => "false",
    "[invalid-dimensione]" => "false",
    "[invalid-volume-minimo]" => "false",
    "[invalid-colori]" => "false",
    "[invalid-prezzo]" => "false",
    "[invalid-disponibilita]" => "false",
    "[invalid-immagine]" =>"false",
];

$famiglieOptions = '';
foreach ($famiglie as $f) {
    $selected = ($f['nome'] === ($_POST['famiglia'] ?? '')) ? 'selected' : '';
    $famiglieOptions .= "<option value=\"" . htmlspecialchars($f['nome']) . "\" $selected>" . htmlspecialchars($f['nome']) . "</option>";
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nomeLatino = trim($_POST['nome_latino'] ?? '');
    $nomeComune = trim($_POST['nome_comune'] ?? '');
    $famiglia = trim($_POST['famiglia'] ?? '');
    $dimensione = floatval($_POST['dimensione'] ?? 0);
    $volumeMinimo = floatval($_POST['volume_minimo'] ?? 0);
    $colori = trim($_POST['colori'] ?? '');
    $prezzo = floatval($_POST['prezzo'] ?? 0);
    $disponibilita = intval($_POST['disponibilita'] ?? 0);
    $percorso = NULL;

     $errore = [
        'nome_latino' => [],
        'nome_comune' => [],
        'famiglia' => [],
        'dimensione' => [],
        'volume_minimo' => [],
        'colori' => [],
        'prezzo' => [],
        'disponibilita' => [],
        'immagine' => []
    ];

     if(empty($nomeLatino)){
        $errore['nome_latino'][] = "Il nome latino è obbligatorio.";
    }

     if (strlen($nomeLatino) < 2 || preg_match('/[^A-Za-zÀ-ÿ\s]/', $nomeLatino)) {
        $errore['nome_latino'][] = "Nome latino non valido.";
    }

    if (empty($errore['nome_latino'])) {
    $pesceEsistente = $connection->getPesci(["p.nome_latino = ?"], [$nomeLatino]);
    if (count($pesceEsistente) > 0) {
        $errore['nome_latino'][] = "Questo nome latino è già presente nel database. Scegline un altro.";
        }
    }   

    if (strlen($nomeComune) < 2 || preg_match('/[^A-Za-zÀ-ÿ\s]/', $nomeComune)) {
        $errore['nome_comune'][] = "Nome comune non valido.";
    }
    
    if ($famiglia === '') {
        $errore['famiglia'][] = "Seleziona una famiglia valida.";
    }
    
    if (!is_numeric($dimensione) || $dimensione <= 0) {
        $errore['dimensione'][] = "Dimensione non valida.";
    }

    if (!is_numeric($volumeMinimo) || $volumeMinimo <= 0) {
        $errore['volume_minimo'][] = "Volume minimo non valido.";
    }

    if(!preg_match('/^[A-Za-zÀ-ÿ]+(,[A-Za-zÀ-ÿ]+)*$/', $colori)) {
        $errore['colori'][] = "Formato non valido.";
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

    if (!is_numeric($disponibilita) || $disponibilita < 0 || $_POST['disponibilita'] === '') {
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
                $errore['immagine'][] = "Errore durante il caricamento dell'immagine.";
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
        $segnaposto= [
            "[nome_latino]" => htmlspecialchars($nomeLatino),
            "[nome_comune]" => htmlspecialchars($nomeComune),
            "[famiglia-options]" => $famiglieOptions,
            "[dimensione]" => htmlspecialchars($dimensione),
            "[volume_minimo]" => htmlspecialchars($volumeMinimo),
            "[colori]" => htmlspecialchars($colori),
            "[prezzo]" => htmlspecialchars($prezzo),
            "[disponibilita]" => htmlspecialchars($disponibilita),

            "[errore-nome-latino]" => $errore['nome_latino'],
            "[errore-nome-comune]" => $errore['nome_comune'],
            "[errore-famiglia]" => $errore['famiglia'],
            "[errore-dimensione]" => $errore['dimensione'],
            "[errore-volume-minimo]" => $errore['volume_minimo'],
            "[errore-colori]" => $errore['colori'],
            "[errore-prezzo]" => $errore['prezzo'],
            "[errore-disponibilita]" => $errore['disponibilita'],
            "[errore-immagine]" => $errore['immagine'],

            "[invalid-nome-latino]" => !empty($errore['nome_latino']) ? "true" : "false",
            "[invalid-nome-comune]" => !empty($errore['nome_comune']) ? "true" : "false",
            "[invalid-famiglia]" => !empty($errore['famiglia']) ? "true" : "false",
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

    $connection->insertPesce($nomeLatino, $nomeComune, $famiglia, $dimensione, $volumeMinimo, $colori, $prezzo, $disponibilita, $percorso);

    header("Location: ../PHP/admin.php");
    exit;

}
$segnaposto["[famiglia-options]"] = $famiglieOptions;
echo str_replace(array_keys($segnaposto), array_values($segnaposto), $paginaHTML);
    

$connection->closeConnection();
?>