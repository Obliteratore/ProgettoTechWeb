<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/db_connection.php";
require_once __DIR__ . "/crea_card_pesce.php";
use FM\FMAccess;

$nomeLatino = filter_input(INPUT_GET, 'nome_latino', FILTER_SANITIZE_SPECIAL_CHARS);
$nomeComune = filter_input(INPUT_GET, 'nome_comune', FILTER_SANITIZE_SPECIAL_CHARS);
$dimensione = filter_input(INPUT_GET, 'dimensione', FILTER_VALIDATE_INT);
$volumeMinimo = filter_input(INPUT_GET, 'volume_min', FILTER_VALIDATE_INT);
$colore = filter_input(INPUT_GET, 'colore', FILTER_SANITIZE_SPECIAL_CHARS);
$prezzoMinimo = filter_input(INPUT_GET, 'prezzo_min', FILTER_VALIDATE_INT);
$prezzoMassimo = filter_input(INPUT_GET, 'prezzo_max', FILTER_VALIDATE_INT);
$tipoAcqua = filter_input(
    INPUT_GET,
    'tipo_acqua',
    FILTER_SANITIZE_SPECIAL_CHARS,
    FILTER_FORCE_ARRAY
) ?? [];
$filtro = filter_input(INPUT_GET, 'filtro', FILTER_SANITIZE_SPECIAL_CHARS);


$allowed = ['marina', 'dolce'];
$tipoAcqua = array_intersect($tipoAcqua, $allowed);

$condizioni = [];
$parametri = [];

if (!empty($nomeLatino)) {
	$condizioni[] = "nome_latino LIKE ?";
	$parametri[] = '%' . $nomeLatino . '%';
}

if (!empty($nomeComune)) {
	$condizioni[] = "nome_comune LIKE ?";
	$parametri[] = '%' . $nomeComune . '%';
}

if (!empty($dimensione)) {
	$condizioni[] = "dimensione = ?";
	$parametri[] = $dimensione;
}	

if (!empty($volumeMinimo)) {
	$condizioni[] = "volume_minimo >= ?";
	$parametri[] = $volumeMinimo;
}

if (!empty($colore) && $colore !== 'tutti') {
    $condizioni[] = 'FIND_IN_SET(?, colori)';
    $parametri[] = $colore;
}


if (!empty($prezzoMinimo)) {
	$condizioni[] = "prezzo >= ?";
	$parametri[] = $prezzoMinimo;
}

if (!empty($prezzoMassimo)) {
	$condizioni[] = "prezzo <= ?";
	$parametri[] = $prezzoMassimo;
}

if (!empty($tipoAcqua)) {
    $placeholders = implode(',', array_fill(0, count($tipoAcqua), '?'));
    $condizioni[] = "f.tipo_acqua IN ($placeholders)";
    $parametri = array_merge($parametri, $tipoAcqua);
}

$pesci = [];

try{
	$connection = new FMAccess();
	$connection->openConnection();
	if ($filtro == "nuovi_arrivi") {
		$pesci = $connection->getPesci($condizioni,$parametri, "ORDER BY data_inserimento DESC");
	} else if ($filtro == "piu_venduti") {
		$piuVenduti = $connection->getPiuVenduti();
		$filtrati   = $connection->getPesci($condizioni, $parametri);

		$idsFiltrati = array_column($filtrati, 'nome_latino');

		$pesci = array_filter($piuVenduti, function($pesce) use ($idsFiltrati) {
    	return in_array($pesce['nome_latino'], $idsFiltrati);});
	} else {
		$pesci = $connection->getPesci($condizioni,$parametri);
	}
} catch(mysqli_sql_exception $e) {
	http_response_code(500);
	header('Location: ../HTML/error_500.html');
	exit;
} finally {
	$connection->closeConnection();
}



$paginaHTML = "";

$paginaHTML = file_get_contents('../HTML/catalogo.html');

$stringaPesci = crea_card_pesce($pesci);

$paginaHTML = str_replace("[listaPesci]", $stringaPesci, $paginaHTML);

$paginaHTML = str_replace("[nomeComune]", htmlspecialchars($nomeComune ?? ''), $paginaHTML);
$paginaHTML = str_replace("[nomeLatino]", htmlspecialchars($nomeLatino ?? ''), $paginaHTML);
$paginaHTML = str_replace("[volumeMin]", htmlspecialchars($volumeMinimo ?? ''), $paginaHTML);
$paginaHTML = str_replace("[prezzoMin]", htmlspecialchars($prezzoMinimo ?? ''), $paginaHTML);
$paginaHTML = str_replace("[prezzoMax]", htmlspecialchars($prezzoMassimo ?? ''), $paginaHTML);

$paginaHTML = str_replace("[tipoAcquaMarina]", in_array('marina', $tipoAcqua ?? [], true) ? 'checked' : '', $paginaHTML);
$paginaHTML = str_replace("[tipoAcquaDolce]", in_array('dolce', $tipoAcqua ?? [], true) ? 'checked' : '', $paginaHTML);

$paginaHTML = str_replace("[filtroAlfabetico]", ($filtro === 'alfabetico' ? 'checked' : ''), $paginaHTML);
$paginaHTML = str_replace("[filtroNuoviArrivi]", ($filtro === 'nuovi_arrivi' ? 'checked' : ''), $paginaHTML);
$paginaHTML = str_replace("[filtroPiuVenduti]", ($filtro === 'piu_venduti' ? 'checked' : ''), $paginaHTML);

$paginaHTML = str_replace("[coloreGiallo]", ($colore === 'giallo' ? 'checked' : ''), $paginaHTML);
$paginaHTML = str_replace("[coloreArancione]", ($colore === 'arancione' ? 'checked' : ''), $paginaHTML);
$paginaHTML = str_replace("[coloreRosso]", ($colore === 'rosso' ? 'checked' : ''), $paginaHTML);
$paginaHTML = str_replace("[coloreBeige]", ($colore === 'beige' ? 'checked' : ''), $paginaHTML);
$paginaHTML = str_replace("[coloreRosa]", ($colore === 'rosa' ? 'checked' : ''), $paginaHTML);
$paginaHTML = str_replace("[coloreBlu]", ($colore === 'blu' ? 'checked' : ''), $paginaHTML);
$paginaHTML = str_replace("[coloreAzzurro]", ($colore === 'azzurro' ? 'checked' : ''), $paginaHTML);
$paginaHTML = str_replace("[coloreVerde]", ($colore === 'verde' ? 'checked' : ''), $paginaHTML);
$paginaHTML = str_replace("[coloreMarrone]", ($colore === 'marrone' ? 'checked' : ''), $paginaHTML);
$paginaHTML = str_replace("[coloreNero]", ($colore === 'nero' ? 'checked' : ''), $paginaHTML);
$paginaHTML = str_replace("[coloreGrigio]", ($colore === 'grigio' ? 'checked' : ''), $paginaHTML);
$paginaHTML = str_replace("[coloreTrasparente]", ($colore === 'trasparente' ? 'checked' : ''), $paginaHTML);
$paginaHTML = str_replace("[coloreTutti]", ($colore === 'tutti' ? 'checked' : ''), $paginaHTML);



echo $paginaHTML;

?>