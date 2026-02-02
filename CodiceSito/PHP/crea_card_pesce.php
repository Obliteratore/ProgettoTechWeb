<?php
function crea_card_pesce(array $pesci, $startId = 0) : string {
    $stringaPesci = '';
    if($pesci != null) {
        $id = $startId;
        foreach($pesci as $pesce) {
            $id++;

            $idTitolo = 'titolo-' . $id;
            $idPrezzo = 'prezzo-' . $id;

            $immagine = htmlspecialchars($pesce['immagine'] ?? 'default.jpg');
            $nome_comune = htmlspecialchars($pesce['nome_comune'] ?? '');
            $nome_latino = htmlspecialchars($pesce['nome_latino'] ?? '');
            
            $stringaPesci .= '<li class="card-pesce">';
            $stringaPesci .= '<article>';
            $stringaPesci .= '<a href="../PHP/pesce.php?nome_latino=' . urlencode($nome_latino) . '" aria-labelledby="' . $idTitolo . ' ' . $idPrezzo . '">';
            $stringaPesci .= '<img loading="lazy" height="683" width="1024" src="' . $immagine . '" alt="">';
            $stringaPesci .= '<div class="dati-pesce">';
            $stringaPesci .= '<h3 class="nome" id="' . $idTitolo . '">' . $nome_comune . '</h3>';
            $stringaPesci .= '</div>';
            $stringaPesci .= '<p class="prezzo" id="' . $idPrezzo . '">' . $pesce['prezzo'] . '€</p>';
            $stringaPesci .= '</a>';
            $stringaPesci .= '</article>';
            $stringaPesci .= '</li>';
        }
    }
    return $stringaPesci;
}
?>