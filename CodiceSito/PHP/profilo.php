<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

/*if(!isset($_SESSION['email'])) {
    header('Location: accesso.php');
    exit;
}*/

require_once "db_connection.php";
use FM\FMAccess;

$nomeCognome = '';
$username = 'Nome utente: ';
$indirizzo = 'Indirizzo: ';
$ordini = '';
$paginaHTML = '';
$paginaHTML = file_get_contents('../HTML/profilo.html');

$datiUtente = [];
$listaOrdini = [];
try{
    $connection = new FMAccess();
    $connection->openConnection();

    $datiUtente = $connection->getProfiloUtente('user'); //$_SESSION['email']) al posto di 'user'
    $nomeCognome = $datiUtente['nome'] . ' ' . $datiUtente['cognome'];
    $username .= $datiUtente['username'];
    $indirizzo .= $datiUtente['via'] . ', ' . $datiUtente['comune'] . ', <abbr title="' . $datiUtente['provincia'] . '">' . $datiUtente['sigla_provincia'] . '</abbr>';

    $listaOrdini = $connection->getOrdiniUtente('user');
    if(!empty($listaOrdini)) {
        $prezzoTotale = [];
        foreach($listaOrdini as $ordine) {
            if (!isset($prezzoTotale[$ordine['id_ordine']])) {
                $prezzoTotale[$ordine['id_ordine']] = '0.00';
            }
            $parziale = bcmul($ordine['prezzo_unitario'], (string) $ordine['quantita'], 2);

            $prezzoTotale[$ordine['id_ordine']] = bcadd($prezzoTotale[$ordine['id_ordine']], $parziale, 2);
        }

        $idOrdinePrec = null;
        $ordini = '<ul>';
        foreach($listaOrdini as $ordine) {
            if($ordine['id_ordine'] !== $idOrdinePrec) {
                if($idOrdinePrec !== null) {
                    $ordini .= '</article></li>';
                }

                $data = (new DateTime($ordine['data_ora']))->format('d/m/Y');
                $ordini .= '<li><article>';
                $ordini .= '<dl>';
                $ordini .= '<dt>ORDINE EFFETTUATO IL:</dt><dd>' . $data . '</dd>';
                $ordini .= '<dt>TOTALE:</dt><dd>' . $prezzoTotale[$ordine['id_ordine']] . '</dd>';
                $ordini .= '<dt>INDIRIZZO DI SPEDIZIONE:</dt><dd>' . $ordine['id_indirizzo'] . '</dd>';
                $ordini .= '</dl>';
            }
            $ordini .= '<p>' . $ordine['prezzo_unitario'] . ' x ' . $ordine['quantita'] . '</p>'; //Qua ci andrebbe la tabella o quello che sarà
            
            $idOrdinePrec = $ordine['id_ordine'];
        }
        $ordini .= '</article></li></ul>';
    } else {
        $ordini = '<p class="call-to-action position">Non hai ancora effettuato nessun ordine. Vai al <a href="catalogo.php">catalogo</a> per trovare il pesce perfetto per te!<p>';
    }
} catch(mysqli_sql_exception $e) {
    http_response_code(500);
    header('Location: ../HTML/error_500.html');
    exit;
} finally {
    $connection->closeConnection();
}

$paginaHTML = str_replace(['[nomeCognome]', '[username]', '[indirizzo]', '[ordini]'], [$nomeCognome, $username, $indirizzo, $ordini], $paginaHTML);
echo $paginaHTML;
?>