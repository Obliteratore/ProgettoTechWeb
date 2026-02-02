<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

if(!isset($_SESSION['email'])) {
    header('Location: ../PHP/accesso.php');
    exit;
}

if($_SESSION['email'] === 'admin') {
    header('Location: ../PHP/admin.php');
    exit;
}

require_once "db_connection.php";
use FM\FMAccess;

$nomeCognome = '';
$email = htmlspecialchars($_SESSION['email']);
$username = '';
$indirizzo = '';
$ordini = '';
$paginaHTML = '';
$paginaHTML = file_get_contents('../HTML/profilo.html');

$datiUtente = [];
$listaOrdini = [];
try{
    $connection = new FMAccess();
    $connection->openConnection();

    $datiUtente = $connection->getProfiloUtenteRegistrato($_SESSION['email']);
    $nomeCognome = htmlspecialchars($datiUtente['nome']) . ' ' . htmlspecialchars($datiUtente['cognome']);
    $username .= htmlspecialchars($datiUtente['username']);
    $indirizzo .= htmlspecialchars($datiUtente['via']) . ', ' . htmlspecialchars($datiUtente['comune']) . ', <abbr title="' . htmlspecialchars($datiUtente['provincia']) . '">' . htmlspecialchars($datiUtente['sigla_provincia']) . '</abbr>';

    $listaOrdini = $connection->getOrdiniUtenteRegistrato($_SESSION['email']);
    if(!empty($listaOrdini)) {
        $prezzoTotale = [];
        foreach($listaOrdini as $ordine) {
            if (!isset($prezzoTotale[(int)$ordine['id_ordine']])) {
                $prezzoTotale[(int)$ordine['id_ordine']] = '0.00';
            }
            $parziale = bcmul($ordine['prezzo_unitario'], (string) $ordine['quantita'], 2);

            $prezzoTotale[$ordine['id_ordine']] = bcadd($prezzoTotale[(int)$ordine['id_ordine']], $parziale, 2);
        }

        $idOrdinePrec = null;
        $ordini .= '<ul class="lista-ordini">';
        foreach($listaOrdini as $ordine) {
            if((int)$ordine['id_ordine'] !== (int)$idOrdinePrec) {
                if($idOrdinePrec !== null) {
                    $ordini .= '</tbody></table>';
                    $ordini .= '</article></li>';
                }

                $data_visibile = (new DateTime($ordine['data_ora']))->format('d/m/Y');
                $data_datetime = (new DateTime($ordine['data_ora']))->format('Y-m-d');

                $ordini .= '<li><article>';
                $ordini .= '<div class="dati-ordine">';
                $ordini .= '<h3>ORDINE #' . (int)$ordine['id_ordine'] . ' - ';
                $ordini .= '<time datetime="' . $data_datetime . '">';
                $ordini .= $data_visibile . '</time></h3>';
                $ordini .= '<dl>';
                $ordini .= '<dt>TOTALE:</dt><dd>' . $prezzoTotale[(int)$ordine['id_ordine']] . ' €</dd>';
                $ordini .= '<dt>INDIRIZZO:</dt><dd>' . htmlspecialchars($ordine['via']) . ', ' . htmlspecialchars($ordine['comune']) . ', <abbr title="' . htmlspecialchars($ordine['provincia']) . '">' . htmlspecialchars($ordine['sigla_provincia']) . '</abbr>' . '</dd>';
                $ordini .= '</dl>';
                $ordini .= '</div>';

                $ordini .= '<p id="descrizione_' . (int)$ordine['id_ordine'] . '" class="screen-reader">La tabella è organizzata per colonne e contiene l\'elenco dei pesci acquistati con l\'ordine #' . (int)$ordine['id_ordine'] . '. Ogni riga riguarda un pesce con il suo prezzo e la quantità acquistata.</p>';

                $ordini .= '<table aria-describedby="descrizione_' . (int)$ordine['id_ordine'] . '">';
                $ordini .= '<caption class="screen-reader">Pesci acquistati con l\'ordine #' . (int)$ordine['id_ordine'] . '</caption>';
                $ordini .= '<thead>';
                $ordini .= '<tr>';
                $ordini .= '<th scope="col">PESCE</th><th scope="col">PREZZO</th><th scope="col">QUANTITÀ</th>';
                $ordini .= '</tr>';
                $ordini .= '</thead>';
                $ordini .= '<tbody>';
            }
            $ordini .= '<tr>';
            $ordini .= '<th scope="row"><a href="../PHP/pesce.php?nome_latino=' . urlencode(htmlspecialchars($ordine['nome_latino'])) . '">' . htmlspecialchars($ordine['nome_comune']) . '</a></th>';
            $ordini .= '<td data-title="Prezzo">' . number_format($ordine['prezzo_unitario'], 2, ',', '.') . ' €</td>';
            $ordini .= '<td data-title="Quantità">' . (int)$ordine['quantita'] . '</td>';
            $ordini .= '</tr>';
            
            $idOrdinePrec = (int)$ordine['id_ordine'];
        }
        $ordini .= '</tbody></table></article></li></ul>';
    } else {
        $ordini = '<p class="call-to-action position">Non hai ancora effettuato nessun ordine. Vai al <a class="link-testuale" href="catalogo.php">catalogo</a> per trovare il pesce perfetto per te!<p>';
    }
} catch(mysqli_sql_exception $e) {
    http_response_code(500);
    header('Location: ../HTML/error_500.html');
    exit;
} finally {
    $connection->closeConnection();
}

$paginaHTML = str_replace(['[nomeCognome]', '[email]', '[username]', '[indirizzo]', '[ordini]'], [$nomeCognome, $email, $username, $indirizzo, $ordini], $paginaHTML);
echo $paginaHTML;
?>