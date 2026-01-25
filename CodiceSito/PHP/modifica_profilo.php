<?php
if(session_status() !== PHP_SESSION_ACTIVE)
    session_start();

if(!isset($_SESSION['email'])) {
    header('Location: accesso.php');
    exit;
}

$errors = $_SESSION['errors'] ?? [];
$values = $_SESSION['values'] ?? [];
$provinciaRegistrata = $_SESSION['provinciaRegistrata'];
$comuneRegistrato = $_SESSION['comuneRegistrato'];
unset($_SESSION['errors']);
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="utf-8" >
        <title>Modifica Profilo - FishMarket</title>
        <meta name="description" content="FishMarket è un negozio online di pesci esotici e tropicali da tutto il mondo. Porta l'esotico a casa tua con dei pesci unici.">
        <meta name="keywords" content="pesci esotici, vendita pesci online, pesci tropicali, negozio pesci esotici, FishMarket">
        <meta name="author" content="Aaron Gingillino, Francesco Balestro, Bilal Sabic, Valerio Solito">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&family=Inclusive+Sans:ital,wght@0,300..700;1,300..700&family=Lexend:wght@100..900&display=swap" rel="stylesheet">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../CSS/style.css">
        <link rel="stylesheet" href="../CSS/desktop.css" media="(min-width: 768px)">
        <link rel="stylesheet" href="../CSS/stampa.css" media="print">

        <link rel="icon" href="../IMMAGINI/Icone/fish.png" type="image/png">
    </head>

    <body id="top">
        <nav aria-label="Aiuti alla navigazione">
            <a id="skip-to-content" href="#main-content">Salta al contenuto</a>
        </nav>
        <header>
            <div class="header-content sezione-standard">
                <img src="../IMMAGINI/Icone/fish.png" alt=""/>
                <nav id="menu" aria-label="Menù di navigazione">
                    <ul>
                        <li lang="en"><a href="../PHP/home.php">Home</a></li>
                        <li><a href="../PHP/catalogo.php">Catalogo</a></li>
                        <li><a href="../HTML/chiSiamo.html">Chi Siamo</a></li>
                        <li><a href="../PHP/profilo.php">Profilo</a></li>
                    </ul>
                </nav>
                <div>
                    <a id="cart-link" href="carrello.html" aria-label="Vai al carrello"><img src="../IMMAGINI/Icone/shopping-cart.svg" alt="" aria-hidden="true"/></a>
                    <button  id="hamburger-menu-btn" aria-controls="hamburger-menu" aria-expanded="false">
                        <img id="open-hamburger-menu" src="../IMMAGINI/Icone/menu-burger.svg" alt="" aria-hidden="true"/>
                        <span class="screen-reader">Apri l'<span lang="en">hamburger</span> menù</span>
                        <img id="close-hamburger-menu" src="../IMMAGINI/Icone/cross.svg" alt="" aria-hidden="true"/>
                        <span class="screen-reader">Chiudi l'<span lang="en">hamburger</span> menù</span>
                    </button>
                </div>
                <nav id="hamburger-menu" aria-label="Menù di navigazione per cellulare">
                    <ul>
                        <li lang="en"><a href="../PHP/home.php">Home</a></li>
                        <li><a href="../PHP/catalogo.php">Catalogo</a></li>
                        <li><a href="../HTML/chiSiamo.html">Chi Siamo</a></li>
                        <li><a href="../PHP/profilo.php">Profilo</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <script src="../JS/hamburger_menu.js" defer></script>
        <script src="../JS/modify_validation.js" defer></script>

        <nav id="breadcrumb" class="sezione-standard" aria-label="Percorso di navigazione">
            <ol>
                <li lang="en"><a href="../PHP/home.php">Home</a></li>
                <li><a href="../PHP/profilo.php">Profilo</a></li>
                <li aria-current="page">Modifica profilo</li>
            </ol>
        </nav>
        
        <main id="main-content">
            <div class="form-container sezione-standard">
                <h1 class="position">Modifica i tuoi dati</h1>
                <ul id="modify-error" class="error-message" role="alert"><?= $errors['summary'] ?? '' ?></ul>
                <form id="modify-form" class="data-form" action="modify_handler.php" method="post" autocomplete="on">
                    <fieldset>
                        <legend>Informazioni personali</legend>
                        <label for="nome">Nome</label>
                        <input id="nome" type="text" name="nome" value="<?= htmlspecialchars($values['nome'] ?? '') ?>" autocomplete="given-name" aria-describedby="given-name-help given-name-error" aria-invalid="<?= isset($errors['nome']) ? 'true' : 'false' ?>" required/>
                        <p id="given-name-help" class="help">Deve avere almeno 2 caratteri e non sono ammessi numeri o caratteri speciali.</p>
                        <ul id="given-name-error" class="error-message" aria-live="polite"><?= $errors['nome'] ?? '' ?></ul>

                        <label for="cognome">Cognome</label>
                        <input id="cognome" type="text" name="cognome" value="<?= htmlspecialchars($values['cognome'] ?? '') ?>" autocomplete="family-name" aria-describedby="family-name-help family-name-error" aria-invalid="<?= isset($errors['cognome']) ? 'true' : 'false' ?>" required/>
                        <p id="family-name-help" class="help">Deve avere almeno 2 caratteri e non sono ammessi numeri o caratteri speciali.</p>
                        <ul id="family-name-error" class="error-message" aria-live="polite"><?= $errors['cognome'] ?? '' ?></ul>
                    </fieldset>

                    <fieldset>
                        <legend>Indirizzo</legend>
                        <label for="provincia">Provincia</label>
                        <p id="provincia-registrata-help" class="help">Provincia attuale registrata: <?= htmlspecialchars($provinciaRegistrata ?? '') ?></p>
                        <select id="provincia" name="provincia" aria-describedby="provincia-registrata-help provincia-help provincia-error" aria-invalid="<?= isset($errors['provincia']) ? 'true' : 'false' ?>">
                            <option value="" selected>Lascia vuoto per non modificare</option>
                            <option value="AG">Agrigento</option>
                            <option value="AL">Alessandria</option>
                            <option value="AN">Ancona</option>
                            <option value="AO">Aosta</option>
                            <option value="AQ">L'Aquila</option>
                            <option value="AR">Arezzo</option>
                            <option value="AP">Ascoli Piceno</option>
                            <option value="AT">Asti</option>
                            <option value="AV">Avellino</option>
                            <option value="BA">Bari</option>
                            <option value="BT">Barletta-Andria-Trani</option>
                            <option value="BL">Belluno</option>
                            <option value="BN">Benevento</option>
                            <option value="BG">Bergamo</option>
                            <option value="BI">Biella</option>
                            <option value="BO">Bologna</option>
                            <option value="BZ">Bolzano</option>
                            <option value="BS">Brescia</option>
                            <option value="BR">Brindisi</option>
                            <option value="CA">Cagliari</option>
                            <option value="CL">Caltanissetta</option>
                            <option value="CB">Campobasso</option>
                            <option value="CE">Caserta</option>
                            <option value="CT">Catania</option>
                            <option value="CZ">Catanzaro</option>
                            <option value="CH">Chieti</option>
                            <option value="CO">Como</option>
                            <option value="CS">Cosenza</option>
                            <option value="CR">Cremona</option>
                            <option value="KR">Crotone</option>
                            <option value="CN">Cuneo</option>
                            <option value="EN">Enna</option>
                            <option value="FM">Fermo</option>
                            <option value="FE">Ferrara</option>
                            <option value="FI">Firenze</option>
                            <option value="FG">Foggia</option>
                            <option value="FC">Forlì-Cesena</option>
                            <option value="FR">Frosinone</option>
                            <option value="GE">Genova</option>
                            <option value="GO">Gorizia</option>
                            <option value="GR">Grosseto</option>
                            <option value="IM">Imperia</option>
                            <option value="IS">Isernia</option>
                            <option value="SP">La Spezia</option>
                            <option value="LT">Latina</option>
                            <option value="LE">Lecce</option>
                            <option value="LC">Lecco</option>
                            <option value="LI">Livorno</option>
                            <option value="LO">Lodi</option>
                            <option value="LU">Lucca</option>
                            <option value="MC">Macerata</option>
                            <option value="MN">Mantova</option>
                            <option value="MS">Massa-Carrara</option>
                            <option value="MT">Matera</option>
                            <option value="ME">Messina</option>
                            <option value="MI">Milano</option>
                            <option value="MO">Modena</option>
                            <option value="MB">Monza e Brianza</option>
                            <option value="NA">Napoli</option>
                            <option value="NO">Novara</option>
                            <option value="NU">Nuoro</option>
                            <option value="CI">Carbonia-Iglesias</option>
                            <option value="VS">Medio Campidano</option>
                            <option value="OG">Ogliastra</option>
                            <option value="OR">Oristano</option>
                            <option value="PD">Padova</option>
                            <option value="PA">Palermo</option>
                            <option value="PR">Parma</option>
                            <option value="PV">Pavia</option>
                            <option value="PG">Perugia</option>
                            <option value="PU">Pesaro e Urbino</option>
                            <option value="PE">Pescara</option>
                            <option value="PC">Piacenza</option>
                            <option value="PI">Pisa</option>
                            <option value="PT">Pistoia</option>
                            <option value="PN">Pordenone</option>
                            <option value="PZ">Potenza</option>
                            <option value="PO">Prato</option>
                            <option value="RG">Ragusa</option>
                            <option value="RA">Ravenna</option>
                            <option value="RC">Reggio Calabria</option>
                            <option value="RE">Reggio Emilia</option>
                            <option value="RI">Rieti</option>
                            <option value="RN">Rimini</option>
                            <option value="RM">Roma</option>
                            <option value="RO">Rovigo</option>
                            <option value="SA">Salerno</option>
                            <option value="SS">Sassari</option>
                            <option value="SV">Savona</option>
                            <option value="SI">Siena</option>
                            <option value="SR">Siracusa</option>
                            <option value="SO">Sondrio</option>
                            <option value="TA">Taranto</option>
                            <option value="TE">Teramo</option>
                            <option value="TR">Terni</option>
                            <option value="TO">Torino</option>
                            <option value="TP">Trapani</option>
                            <option value="TN">Trento</option>
                            <option value="TV">Treviso</option>
                            <option value="TS">Trieste</option>
                            <option value="UD">Udine</option>
                            <option value="VA">Varese</option>
                            <option value="VE">Venezia</option>
                            <option value="VB">Verbano-Cusio-Ossola</option>
                            <option value="VC">Vercelli</option>
                            <option value="VR">Verona</option>
                            <option value="VV">Vibo Valentia</option>
                            <option value="VI">Vicenza</option>
                            <option value="VT">Viterbo</option>
                        </select>
                        <p id="provincia-help" class="help">Lascia questo campo vuoto se non vuoi cambiare la tua provincia di residenza.</p>
                        <p id="provincia-error" class="error-message" aria-live="polite"><?= $errors['provincia'] ?? '' ?></p>

                        <label for="comune">Comune</label>
                        <p id="comune-registrato-help" class="help">Comune attuale registrato: <?= htmlspecialchars($comuneRegistrato ?? '') ?></p>
                        <select id="comune" name="comune" aria-describedby="comune-registrato-help comune-help comune-error" aria-invalid="<?= isset($errors['comune']) ? 'true' : 'false' ?>">
                            <option value="" selected>Lascia vuoto per non modificare</option>
                        </select>
                        <p id="comune-help" class="help">Lascia questo campo vuoto se non vuoi cambiare il tuo comune di residenza.</p>
                        <p id="comune-error" class="error-message" aria-live="polite"><?= $errors['comune'] ?? '' ?></p>

                        <label for="via">Via</label>
                        <input id="via" type="text" name="via" value="<?= htmlspecialchars($values['via'] ?? '') ?>" autocomplete="street-address" aria-describedby="via-help via-error" aria-invalid="<?= isset($errors['via']) ? 'true' : 'false' ?>" required/>
                        <p id="via-help" class="help">L'indirizzo completo con via, piazza o contrada e numero civico.</p>
                        <p id="via-error" class="error-message" aria-live="polite"><?= $errors['via'] ?? '' ?></p>
                    </fieldset>

                    <fieldset>
                        <legend>Credenziali d'accesso</legend>
                        <label for="username"><span lang="en">Username</span></label>
                        <input id="username" type="text" name="username" value="<?= htmlspecialchars($values['username'] ?? '') ?>" autocomplete="username" aria-describedby="username-help username-error" aria-invalid="<?= isset($errors['username']) ? 'true' : 'false' ?>" required/>
                        <p id="username-help" class="help">Scegli un nome utente di lunghezza tra 3 e 30 caratteri. Non sono ammessi spazi o caratteri speciali.</p>
                        <ul id="username-error" class="error-message" aria-live="polite"><?= $errors['username'] ?? '' ?></ul>

                        <label for="password"><span lang="en">Password</span></label>
                        <input id="password" type="password" name="password" autocomplete="new-password" aria-describedby="password-help password-error" aria-invalid="<?= isset($errors['password']) ? 'true' : 'false' ?>"/>
                        <div id="password-help" class="help">
                            <p>Lascia questo campo vuoto se non vuoi cambiare la <span lang="en">password</span>. Deve contenere:</p>
                            <ul>
                                <li>Almeno 8 caratteri</li>
                                <li>Almeno una lettera maiuscola</li>
                                <li>Almeno un numero</li>
                                <li>Almeno un carattere speciale</li>
                            </ul>
                        </div>
                        <ul id="password-error" class="error-message" aria-live="polite"><?= $errors['password'] ?? '' ?></ul>

                        <label for="confermaPassword">Conferma <span lang="en">Password</span></label>
                        <input id="confermaPassword" type="password" name="confermaPassword" autocomplete="new-password" aria-describedby="confermaPassword-help confermaPassword-error" aria-invalid="<?= isset($errors['confermaPassword']) ? 'true' : 'false' ?>"/>
                        <p id="confermaPassword-help" class="help">Lascia questo campo vuoto se non vuoi cambiare la <span lang="en">password</span>.</p>
                        <p id="confermaPassword-error" class="error-message" aria-live="polite"><?= $errors['confermaPassword'] ?? '' ?></p>
                    </fieldset>
                    <input type="submit" value="Conferma"/>
                </form>
                <a id="cancelModifyBtn" href="profilo.php">Annulla</a>
            </div>
        </main>
        <script src="../JS/insert_comuni.js" defer></script>
        <footer>

        </footer>
        <a href="#top" class="back-to-top" aria-label="Torna all'inizio"><img src="../IMMAGINI/Icone/arrow-up.svg" alt="" aria-hidden="true"/></a>
    </body>
</html>