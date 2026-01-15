<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang=“it”>
    <head>
        <meta charset="utf-8" >
        <title>Accedi - FishMarket</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../CSS/style.css">
        <link rel="stylesheet" href="../CSS/desktop.css" media="(min-width: 768px)">
    </head>

    <body>
        <header></header>
        <script src="../JS/access_validation.js" defer></script>
        <main id="main-content">
            <div class="form-container">
                <h1 class="position">Accedi</h1>
                <p class="call-to-action position">Se non hai un <span lang="en">account</span>, <a href="registrazione.php">registrati</a>!</p>
                <p id="login-error" class="error-message" role="alert"><?= $error ?></p>
                <form id="login-form" class="data-form" action="access_handler.php" method="post" autocomplete="on">
                    <label for="username"><span lang="en">Username</span> o <span lang="en">Email</span>
                    <small class="required">(Obbligatorio)</small>
                    </label>
                    <input id="username" type="text" name="username" autocomplete="username" aria-describedby="username-hint login-error" aria-invalid="<?= !empty($error) ? 'true' : 'false' ?>" required/>
                    <small id="username-hint" class="hint"><abbr title="Esempio">Es</abbr>: maria.bianchi o mariabianchi@gmail.com</small>

                    <label for="password"><span lang="en">Password</span>
                    <small class="required">(Obbligatorio)</small>
                    </label>
                    <input id="password" type="password" name="password" autocomplete="current-password" aria-describedby="password-hint login-error" aria-invalid="<?= !empty($error) ? 'true' : 'false' ?>" required/>
                    <small id="password-hint" class="hint"><abbr title="Esempio">Es</abbr>: <span lang="en">Password</span>Sicura2!</small>

                    <input type="submit" value="Accedi"/>
                </form>
            </div>
        </main>
        <footer></footer>
    </body>

</html>