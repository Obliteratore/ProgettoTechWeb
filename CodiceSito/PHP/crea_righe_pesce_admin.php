<?php
function crea_righe_pesce_admin(array $pesci) :string {
    $html='';

    foreach($pesci as $pesce) {
        $nomeLatino = htmlspecialchars($pesce['nome_latino']);

        $html .= '<tr>';

        $html .= '<th scope="row">' . $nomeLatino . '</th>';

        $html .= '<td data-label="Azione">';
        $html .= '<a 
            class="link-testuale" 
            href="modifica_pesce.php?nome_latino=' .urlencode($nomeLatino) . '"
            aria-label="Modifica il pesce con nome latino ' . $nomeLatino . '">
            Modifica
        </a>';

        $html .= '</td>';
        $html .= '</tr>';
    }

    return $html;
}