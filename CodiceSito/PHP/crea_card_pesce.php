<?php
public function($pesci) {
    if($pesci != null) {
        $stringaPesci .= '<ul class="pesci">';
        foreach($pesci as $pesce) {
            $stringaPesci .= '<li class="card-pesce">';
            $stringaPesci .= '<img src="' . $pesce['immagine'] . '"/>';
            $stringaPesci .= '<div class="dati-pesce">';
            $stringaPesci .= '<p class="nome"' . htmlspecialchars($pesce['nome']) . '</p>';
            $stringaPesci .= '</div>';
            $stringaPesci .= '<p class="prezzo">' . $pesce['prezzo'] . '€</p>';
            $stringaPesci .= '</li>';
        }

        $stringaPesci .= '</ul>';
        return $stringaPesci;
    }
}
?>