<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

if(!isset($_SESSION['email'])) {
    header('Location: ../PHP/accesso.php');
    exit;
}

if($_SESSION['email'] !== 'admin') {
    header('Location: ../PHP/profilo.php');
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "db_connection.php";
use FM\FMAccess;

function crea_righe_pesce_admin(array $pesci) :string {
    $html='';

    foreach($pesci as $pesce) {
        $nomeLatino = htmlspecialchars($pesce['nome_latino']);

        $html .= '<tr>';

        $html .= '<th scope="row">' . $nomeLatino . '</th>';

        $html .= '<td data-label="Azione">';
        $html .= '<a 
            class="bottone-standard" 
            href="../PHP/modifica_pesce.php?nome_latino=' .urlencode($nomeLatino) . '"
            aria-label="Modifica il pesce con nome latino ' . $nomeLatino . '">
            Modifica
        </a>';

        $html .= '</td>';
        $html .= '</tr>';
    }

    return $html;
}

$paginaHTML = file_get_contents('../HTML/admin.html');

if ($paginaHTML === false) {
    die("Errore: non posso leggere admin.html");
}

try{
    $connection = new FMAccess();
    $connection->openConnection();

    $pesci = $connection->getPesci([],[],"ORDER BY nome_latino");
    $righeHTML = crea_righe_pesce_admin($pesci);

    $paginaHTML = str_replace('[righe_pesci]', $righeHTML, $paginaHTML);


}
catch (mysqli_sql_exception $e) {
    http_response_code(500);
    header('Location:../HTML/error_500.html');
    exit;
} finally {
    if (isset($connection)) $connection->closeConnection();
}

echo $paginaHTML;
?>