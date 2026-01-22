<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

require_once "db_connection.php";
use FM\FMAccess;

$logged_in = isset($_SESSION['email']);
$email_utente = $logged_in ? $_SESSION['email'] : null;

try{
    $connection = new FMAccess();
    $connection->openConnection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $azione = $_POST['azione'] ?? '';
        if($azione === 'aggiungi'){
            $pesce = $_POST['nome_latino'] ?? '';
            $quantita = (int)($_POST['quantita'] ?? 0);

            if (!empty($pesce) && $quantita > 0){
                $pesce_db = $connection->getPesce($pesce);
                if($pesce_db){
                    $disponibilita_max = (int)$pesce_db['disponibilita'];
                    if($quantita <= $disponibilita_max) {
                        if($logged_in) {
                            $connection->inserisciCarrello($email_utente, $pesce, $quantita);
                        } else {

                            if(!isset($_SESSION['carrello_ospite'])){
                                $_SESSION['carrello_ospite'] = [];
                            }
                            
                            if (isset($_SESSION['carrello_ospite'][$pesce])) $_SESSION['carrello_ospite'][$pesce] += $quantita;
                            else $_SESSION['carrello_ospite'][$pesce] = $quantita;
                        }

                        header("Location: carrello.php");
                        exit();
                    } else {
                        header("Location: pesce.php?nome_latino=" . $pesce . "&errore=non_disponibile");
                        exit();
                    }
                } else {
                    header("Location: ../HTML/errore_404.html");
                }
            }
        } elseif ($azione === 'rimuovi') {
            $pesce_rimuovere = $_POST['nome_latino'] ?? '';

            if(!empty($pesce_rimuovere)) {
                if ($logged_in) $connection->cancellaDaCarrello($email_utente,$pesce_rimuovere);
                elseif (isset($_SESSION['carrello_ospite'][$pesce_rimuovere])) unset($_SESSION['carrello_ospite'][$pesce_rimuovere]);
            }
            header("Location: carrello.php");
            exit();
        }
    }
} catch (mysqli_sql_exception $e) {
	http_response_code(500);
    header("Location: ../HTML/error_500.html");
    exit;
} catch (Exception $e) {
    header("Location: ../HTML/error_404.html");
    exit;
} finally {
    $connection->closeConnection();
}
?>