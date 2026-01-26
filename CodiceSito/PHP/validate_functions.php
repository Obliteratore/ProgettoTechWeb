<?php
function pulisciInput($value) {
    $value = trim($value);
    $value = strip_tags($value);
    return $value;
}

function validateNome(&$errors, $nome) {
    $regex = '/^[A-Za-zÀ-ÖØ-öø-ÿ]+([ \'-][A-Za-zÀ-ÖØ-öø-ÿ]+)*$/';

    if(empty($nome))
        $errors['nome'] = '<li>Il nome è un campo obbligatorio.</li>';
    else {
        if(strlen($nome) < 2)
            $errors['nome'] = '<li>Il nome è troppo corto.</li>';

        if(!preg_match($regex, $nome))
            $errors['nome'] = ($errors['nome'] ?? '') . '<li>Il nome contiene caratteri non validi.</li>';
    }
}

function validateCognome(&$errors, $cognome) {
    $regex = '/^[A-Za-zÀ-ÖØ-öø-ÿ]+([ \'-][A-Za-zÀ-ÖØ-öø-ÿ]+)*$/';

    if(empty($cognome))
        $errors['cognome'] = '<li>Il cognome è un campo obbligatorio.</li>';
    else {
        if(strlen($cognome) < 2)
            $errors['cognome'] = '<li>Il cognome è troppo corto.</li>';

        if(!preg_match($regex, $cognome))
            $errors['cognome'] = ($errors['cognome'] ?? '') . '<li>Il cognome contiene caratteri non validi.</li>';
    }
}

function validateProvincia(&$errors, $provincia, $connection) {
    if(empty($provincia))
        $errors['provincia'] = 'La provincia è un campo obbligatorio.';
    else {
        $exist = $connection->existProvincia($provincia);
        if(!$exist)
            $errors['provincia'] = 'La provincia selezionata non esiste.';
    }
}

function validateComune(&$errors, $comune, $provincia, $connection) {
    if(empty($comune))
        $errors['comune'] = 'Il comune è un campo obbligatorio.';
    else {
        $exist = $connection->existComune($comune, $provincia);
        if(!$exist)
            $errors['comune'] = 'Il comune selezionato non esiste.';
    }
}

function validateVia(&$errors, $via) {
    $regex = '/^[A-Za-zÀ-ÖØ-öø-ÿ0-9\s\.\-\/\']+$/';

    if(empty($via))
        $errors['via'] = 'La via è un campo obbligatorio.';
    else {
        if(!preg_match($regex, $via))
            $errors['via'] = 'La via contiene caratteri non validi.';
    }
}

function validateEmail(&$errors, $email, $connection) {
    if(empty($email))
        $errors['email'] = 'L\'<span lang="en">email</span> è un campo obbligatorio.';
    else {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors['email'] = 'L\'<span lang="en">email</span> non è valida.';
    }
    if (!isset($errors['email'])) {
        $exist = $connection->existEmail($email);
        if($exist)
            $errors['email'] = 'Questa <span lang="en">email</span> è già registrata. Usane un\'altra o <a href="accesso.php">accedi</a>.';
    }
}

function validateUsername(&$errors, $username, $connection) {
    $regex = '/^[a-zA-Z0-9_.-]+$/';
    if(empty($username))
        $errors['username'] = '<li>Il nome utente è un campo obbligatorio.</li>';
    else {
        if(strlen($username) < 3)
            $errors['username'] = '<li>Il nome utente è troppo corto.</li>';
        elseif(strlen($username) > 30)
            $errors['username'] = '<li>Il nome utente è troppo lungo.</li>';
        if(!preg_match($regex, $username))
            $errors['username'] = ($errors['username'] ?? '') . '<li>Il nome utente contiene caratteri non validi.</li>';
    }
    if (!isset($errors['username'])) {
        $exist = $connection->existUsername($username);
        if($exist)
            $errors['username'] = '<li>Questo nome utente è già in uso. Scegline un altro o <a href="accesso.php">accedi</a>.</li>';
    }
}

function validateConfermaPassword(&$errors, $password, $confermaPassword) {
    if($confermaPassword !== $password)
        $errors['confermaPassword'] = 'Le <span lang="en">password</span> non coincidono.';
}
?>