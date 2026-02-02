<?php
function crea_card_pesce(array $pesci) : string {
    $stringaPesci = '';
    if($pesci != null) {
        foreach($pesci as $pesce) {
            $immagine = htmlspecialchars($pesce['immagine'] ?? 'default.jpg');
            $nome_comune = htmlspecialchars($pesce['nome_comune'] ?? '');
            $nome_latino = htmlspecialchars($pesce['nome_latino'] ?? '');

            $stringaPesci .= '<li class="card-pesce">';
            $stringaPesci .= '<article>';
            $stringaPesci .= '<a href="../PHP/pesce.php?nome_latino=' . $nome_latino . '">';
            $stringaPesci .= '<img height="683" width="1024" src="' . $immagine . '" alt="' . $nome_comune . '">';
            $stringaPesci .= '<div class="dati-pesce">';
            $stringaPesci .= '<h3 class="nome">' . $nome_comune . '</h3>';
            $stringaPesci .= '</div>';
            $stringaPesci .= '<p class="prezzo">' . $pesce['prezzo'] . '€</p>';
            $stringaPesci .= '</a>';
            $stringaPesci .= '</article>';
            $stringaPesci .= '</li>';
        }
    }
    return $stringaPesci;
}
?>