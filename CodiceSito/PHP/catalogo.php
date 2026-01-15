<?php
require_once "db_connection.php";
require_once "crea_card_pesce";
use FM\FMAccess;

//purificazione dell'input
$nomeLatino = filter_input(INPUT_GET, 'nome_latino', FILTER_SANITIZE_SPECIAL_CHARS);
$nomeComune = filter_input(INPUT_GET, 'nome_comune', FILTER_SANITIZE_SPECIAL_CHARS);
$dimensione = filter_input(INPUT_GET, 'dimensione', FILTER_VALIDATE_INT);
$volumeMinimo = filter_input(INPUT_GET, 'volumeMinimo', FILTER_VALIDATE_INT);
//$colori = $_GET['color'] ?? [];
$nomeComune = filter_input(INPUT_GET, 'nome_comune', FILTER_SANITIZE_SPECIAL_CHARS);
$prezzoMinimo = filter_input(INPUT_GET, 'prezzoMinimo', FILTER_VALIDATE_INT);
$prezzoMassimo = filter_input(INPUT_GET, 'prezzoMassimo', FILTER_VALIDATE_INT);

$condizioni = [];
$parametri = [];

if (!empty($_GET['nome_latino'])) {
	$condizioni[] = "nome_latino LIKE :nome_latino";
	$parametri[':nome_latino'] = '%' . $_GET['nome_latino'] . '%';
}

if (!empty($_GET['nome_comune'])) {
	$condizioni[] = "nome_comune LIKE :nome_comune";
	$parametri[':nome_comune'] = '%'. $_GET['nome_comune'] . '%';
}

if (!empty($_GET['dimensione'])) {
	$condizioni[] = "dimensione = dimensione";
	$parametri[':dimensione'] = $_GET['dimensione'];
}	

if (!empty($_GET['volume_minimo'])) {
	$condizioni[] = "volume_minimo >= :volume_minimo";
	$parametri[':volume_minimo'] = $_GET['volume_minimo'];
}
/*if (!empty($colori)) {
	foreach ($colori as $i => $colore) {
		$condizioni[] = "FIND_IN_SET(:c$i, colori)";
		$parametri[":c$i"] = $colore;
	}
}	*/
if (!empty($_GET['prezzo_min'])) {
	$condizioni[] = "prezzo >= :prezzo_min";
	$parametri[':prezzo_min'] = $_GET['prezzo_min'];
}

if (!empty($_GET['prezzo_max'])) {
	$condizioni[] = "prezzo >= :prezzo_max";
	$parametri[':prezzo_max'] = $_GET['prezzo_max'];
}

$pesci = [];

try{
	$connection = new FMAccess();
	$connection->openConnection();
	$pesci = $connection->getPesci($condizioni,$parametri);
} catch(mysqli_sql_exception $e) {
	http_response_code(500);
	header('Location: ../HTML/error_500.html');
	exit;
} finally {
	$connection->closeConnection();
}



$paginaHTML = "";

$paginaHTML = file_get_contents('catalogo.html');

$stringaPesci = crea_card_pesce($pesci);

$paginaHTML = str_replace("[listaPesci]", $stringaPesci, $paginaHTML);
echo $paginaHTML;

?>