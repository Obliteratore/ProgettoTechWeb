<?php
require_once "db_connection.php";
use FM\FMAccess;

$paginaHTML = file_get_contents('../HTML/home.html');

$pesciContainer = new PesceRepo($pdo);

$nuoviPesci = $pesciContainer->getNuovi(4);
$PiuVendutiPesci = $pesciContainer->getPiuVenduti(4);


try{
    $connection = new FMAccess();
    $connection->openConnection();
    
    function generaCard(array $pesci): string {
        $html = '';

        foreach ($pesci as $pesce) {
        $html .= '<li class="card-pesce">';
        $html .= '<img src="' . $pesce['immagine'] . '" alt=""/>';
        $html .= '<div class="dati-pesce">';
        $html .= '<p class="nome">' . htmlspecialchars($pesce['nome']) . '</p>';
        $html .= '</div>';
        $html .= '<p class="prezzo">' . ($pesce['prezzo']) . '€</p>';
        $html .= '</li>';
        }
    }
    return $html;
    
    $segnaposto = [
    '[nuovo_pesce]' -> generaCard($nuoviPesci),
    '[piu_venduto]' -> generaCard($PiuVendutiPesci)
    ];

    $pagina = str_replace(array_keys($segnaposto), array_values($segnaposto), $paginaHTML)

    }catch (mysqli_sql_exception $e){
    http_response_code(500);
    header('Location:../HTML/error_500.html');
    exit;
} finally {
    if(isset($connection)) $connection->closeConnection();
}
    echo $pagina
?>
