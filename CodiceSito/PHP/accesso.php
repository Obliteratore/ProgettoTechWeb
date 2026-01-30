<?php
if(session_status() !== PHP_SESSION_ACTIVE)
        session_start();

if(isset($_SESSION['email'])) {
    header('Location: ../PHP/profilo.php');
    exit;
}

require_once "db_connection.php";
use FM\FMAccess;

function pulisciInput($value) {
    $value = trim($value);
    $value = strip_tags($value);
    return $value;
}

function validateEmail($email, $connection) {
    return $connection->existEmail($email);
}

function validateUsername($username, $connection) {
    $regex = '/^[a-zA-Z0-9_.-]+$/';
    if(strlen($username) < 3 || strlen($username) > 30 || !preg_match($regex, $username))
        return false;
    else
        return $connection->existUsername($username);
}

function validateLogin($login, $isEmail, $connection) {
    if ($isEmail)
        return validateEmail($login, $connection);
    else
        return validateUsername($login, $connection);
}

function validatePassword($password) {
    if(strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/\d/', $password) || !preg_match('/[!?@#$%^&*]/', $password))
        return false;
    else
        return true;
}

function writeHtml($error) {
    $paginaHTML = file_get_contents('../HTML/accesso.html');

    $paginaHTML = str_replace('[invalid-username]', !empty($error) ? 'true' : 'false', $paginaHTML);
    $paginaHTML = str_replace('[invalid-password]', !empty($error) ? 'true' : 'false', $paginaHTML);
    $paginaHTML = str_replace('[errore-login]', $error, $paginaHTML);
    echo $paginaHTML;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $login = isset($_POST['username']) ? pulisciInput($_POST['username']) : '';
    $password = $_POST['password'] ?? '';

    if(!empty($login) && !empty($password)) {
        try{
            $connection = new FMAccess();
            $connection->openConnection();

            $isEmail = true;
            if(!filter_var($login, FILTER_VALIDATE_EMAIL))
                $isEmail = false;

            if($login != 'user' && $login != 'admin' && (!validateLogin($login, $isEmail, $connection) || !validatePassword($password))) {
                $error = 'Le credenziali inserite non sono valide.';
                writeHtml($error);
                exit;
            } else {
                $passwordHash= null;
                if($isEmail)
                    $passwordHash = $connection->getPasswordWithEmail($login);
                else
                    $passwordHash = $connection->getPasswordWithUsername($login);

                if(!$passwordHash || !password_verify($password, $passwordHash)) {
                    $error = 'Le credenziali inserite non sono valide.';
                    writeHtml($error);
                    exit;
                } else {
                    session_regenerate_id(true);
                    if($isEmail)
                        $_SESSION['email'] = $login;
                    else
                        $_SESSION['email'] = $connection->getEmailWithUsername($login);

                    header('Location: ../PHP/profilo.php');
                    exit;
                }
            }
        } catch(mysqli_sql_exception $e) {
            http_response_code(500);
            header('Location: ../HTML/error_500.html');
            exit;
        } finally {
            $connection->closeConnection();
        }
    } else {
        $error = 'Le credenziali inserite non sono valide.';
        writeHtml($error);
        exit;
    }
} else {
    writeHtml($error);
}
?>