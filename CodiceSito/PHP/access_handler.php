<?php

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(session_status() !== PHP_SESSION_ACTIVE)
        session_start();

    $login = isset($_POST['username']) ? pulisciInput($_POST['username']) : null;
    $password = $_POST['password'] ?? null;

    if(!empty($login) && !empty($password)) {
        try{
            $connection = new FMAccess();
            $connection->openConnection();

            $isEmail = true;
            if(!filter_var($login, FILTER_VALIDATE_EMAIL))
                $isEmail = false;

            if($login != 'user' && $login != 'admin' && (!validateLogin($login, $isEmail, $connection) || !validatePassword($password))) {
                $connection->closeConnection();

                $_SESSION['error'] = 'Le credenziali inserite non sono valide.';
                header('Location: accesso.php');
                exit;
            } else {
                $passwordHash= null;
                if($isEmail)
                    $passwordHash = $connection->getPasswordWithEmail($login);
                else
                    $passwordHash = $connection->getPasswordWithUsername($login);

                if(!$passwordHash || !password_verify($password, $passwordHash)) {
                    $connection->closeConnection();

                    $_SESSION['error'] = 'Le credenziali inserite non sono valide.';
                    header('Location: accesso.php');
                    exit;
                } else {
                    session_regenerate_id(true); 
                    $_SESSION['email'] = $values['email'];
                    header('Location: ../HTML/profilo.html');
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
        $_SESSION['error'] = 'Le credenziali inserite non sono valide.';
        header('Location: accesso.php');
        exit;
    }
}
?>