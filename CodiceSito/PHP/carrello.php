<?php

if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

require_once "db_connection.php";
use FM\FMAccess;

$pagina_html = file_get_contents("../HTML/carrello.html");

$contenuto_carrello = "";
$totale = 0;
$visibilita = "";

try{
    $connection = new FMAccess();
    $connection->openConnection();

    $prodotti = [];
    $logged_in = isset($_SESSION['email']);

    if($logged_in){
        $prodotti = $connection->getCarrello($_SESSION['email']);
    } else {
        if(isset($_SESSION['carrello_ospite']) && count($_SESSION['carrello_ospite']) > 0){

            $nomi_latini = array_keys($_SESSION['carrello_ospite']);
            $dettagli_pesci = $connection->getPesciPerCarrelloOspite($nomi_latini);
            foreach ($dettagli_pesci as $pesce){
                $nome = $pesce['nome_latino'];
                $pesce['quantita'] = $_SESSION['carrello_ospite'][$nome];
                $prodotti[]= $pesce;
            }
        }
    }
    if(count($prodotti) > 0) {
        $contenuto_carrello .= '<ul class="lista-carrello">';

        foreach($prodotti as $item) {
            $nome_latino = $item['nome_latino'];
            $nome_comune = $item['nome_comune'];
            $prezzo = (float)$item['prezzo'];
            $quantita = (int)$item['quantita'];
            $immagine = $item['immagine'];
            $subtotale = $prezzo * $quantita;
            $totale += $subtotale;

            $contenuto_carrello .= '
            <li class="item-carrello">
                <div class="item-img-container">
                    <img src="' . htmlspecialchars($immagine) . '" alt="Foto di ' . htmlspecialchars($nome_comune) . '" class="img-carrello">
                </div>

                <div class="item-info">
                    <span class="item-nome-comune">' . htmlspecialchars($nome_comune) . '</span>
                    <span class="item-nome-latino">' . htmlspecialchars($nome_latino) . '</span>
                </div>

                <div class="item-prezzo-singolo">
                    ' . number_format($prezzo, 2) . ' €
                </div>

                <div class="item-azioni">
                    <span class="quantita-badge">x ' . $quantita . '</span>

                    <form action="gestisci_carrello.php" method="POST" class="form-rimuovi">
                        <input type="hidden" name="azione" value="rimuovi">
                        <input type="hidden" name="nome_latino" value="' . htmlspecialchars($nome_latino) . '">
                        <button type="submit" class="btn-rimuovi" aria-label="Rimuovi ' . htmlspecialchars($nome_comune) . ' dal carrello">
                           <img src="../IMMAGINI/Icone/cross.svg" alt="Icona X per rimuovere dal carrello" class="icona-x">
                        </button>
                    </form>
                </div>

                <div class="item-totale-riga">
                    ' . number_format($subtotale, 2) . ' €
                </div>
            </li>';
        }
        $contenuto_carrello .= '</ul>';
    } else {
        $contenuto_carrello = "<div class=\"messaggio-vuoto\">
                                    <p>Il tuo carrello è vuoto. Cosa aspetti a riempirlo? </p>
                                    <p><a href='catalogo.php'>Torna al catalogo</a></p>
                                </div>";
        $visibilita = "hidden";
    }
} catch (Exception $e) {
    $contenuto_carrello = "<div class=\"messaggio-errore\">
                                <p>Errore nel caricamento del carrello.</p>
                                <p>Riprova più tardi.</p>
                           </div>";
    $visibilita = "hidden";
} finally {
    $connection->closeConnection();
}

$pagina_html = str_replace("[contenuto_carrello]", $contenuto_carrello, $pagina_html);
$pagina_html = str_replace("[totale]", number_format($totale, 2), $pagina_html);


if ($visibilita == "hidden") {
    $pagina_html = str_replace('class="riepilogo-ordine [visibilita]"', 'class="riepilogo-ordine hidden"', $pagina_html);
} else {
    $pagina_html = str_replace('[visibilita]', '', $pagina_html);
}

echo $pagina_html;
?>