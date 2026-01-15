<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/db_connection.php";
require_once __DIR__ . "/crea_card_pesce.php";
use FM\FMAccess;

//purificazione dell'input
$nomeLatino = filter_input(INPUT_GET, 'nome_latino', FILTER_SANITIZE_SPECIAL_CHARS);
$nomeComune = filter_input(INPUT_GET, 'nome_comune', FILTER_SANITIZE_SPECIAL_CHARS);
$dimensione = filter_input(INPUT_GET, 'dimensione', FILTER_VALIDATE_INT);
//$volumeMinimo = filter_input(INPUT_GET, 'volume_minimo', FILTER_VALIDATE_INT);
//$colori = $_GET['color'] ?? [];
$prezzoMinimo = filter_input(INPUT_GET, 'prezzo_min', FILTER_VALIDATE_INT);
$prezzoMassimo = filter_input(INPUT_GET, 'prezzo_max', FILTER_VALIDATE_INT);

$condizioni = [];
$parametri = [];

if (!empty($nomeLatino)) {
	$condizioni[] = "nome_latino LIKE :nome_latino";
	$parametri[':nome_latino'] = '%' . $nomeLatino. '%';
}

if (!empty($nomeComune)) {
	$condizioni[] = "nome_comune LIKE :nome_comune";
	$parametri[':nome_comune'] = '%'. $nomeComune . '%';
}

if (!empty($dimensione)) {
	$condizioni[] = "dimensione = ?";
	$parametri[] = $dimensione;
}	

/*if (!empty($volumeMinimo)) {
	$condizioni[] = "volume_minimo >= :volume_minimo";
	$parametri[':volume_minimo'] = $volumeMinimo;
}*/
/*if (!empty($colori)) {
	foreach ($colori as $i => $colore) {
		$condizioni[] = "FIND_IN_SET(:c$i, colori)";
		$parametri[":c$i"] = $colore;
	}
}	*/
if (!empty($prezzoMinimo)) {
	$condizioni[] = "prezzo >= ?";
	$parametri[] = $prezzoMinimo;
}

if (!empty($prezzoMassimo)) {
	$condizioni[] = "prezzo <= ?";
	$parametri[] = $prezzoMassimo;
}



$pesci = [];

//try{
	$connection = new FMAccess();
	$connection->openConnection();
	$pesci = $connection->getPesci($condizioni,$parametri);
/*} catch(mysqli_sql_exception $e) {
	http_response_code(500);
	header('Location: ../HTML/error_500.html');
	exit;
} finally {*/
	$connection->closeConnection();
//}



$paginaHTML = "";

$paginaHTML = file_get_contents('../HTML/catalogo.html');

$stringaPesci = crea_card_pesce($pesci);

$paginaHTML = str_replace("[listaPesci]", $stringaPesci, $paginaHTML);
echo $paginaHTML;

?>