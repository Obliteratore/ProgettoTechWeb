<?php
function crea_card_pesce(array $pesci) : string {
    $stringaPesci = '';
    if($pesci != null) {
        $stringaPesci .= '<ul class="pesci">';
        foreach($pesci as $pesce) {
            $immaginePesceServer = $pesce['immagine'] ?? 'default.jpg';
            $percorsoImmagine = "../IMMAGINI/Pesci/" . $immaginePesceServer;

            $stringaPesci .= '<li class="card-pesce">';
            $stringaPesci .= '<img src="../IMMAGINI/Pesci/' . $pesce['immagine'] . '" alt="' . $pesce['nome_comune'] . '"/>';
            $stringaPesci .= '<div class="dati-pesce">';
            $stringaPesci .= '<p class="nome">' . htmlspecialchars($pesce['nome_comune']) . '</p>';
            $stringaPesci .= '</div>';
            $stringaPesci .= '<p class="prezzo">' . $pesce['prezzo'] . '€</p>';
            $stringaPesci .= '</li>';
        }

        $stringaPesci .= '</ul>';
    }
    return $stringaPesci;
}
?>