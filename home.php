<?php
session_start();

// Sicherstellen, dass man eingeloggt ist
if (!isset($_SESSION["loggedin"]) || !isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="de">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Startseite</title>
    <link rel="stylesheet" href="vok_alltag.css">
    <link rel="icon" type="image/x-icon" href="icons/favicon.ico">
    <script src="home.js" defer></script>
</head>
<body>
    <div class="home-icon <?php echo basename($_SERVER['PHP_SELF']) === 'home.php' ? 'active' : ''; ?>">
        <img src="icons/homeDark.png" alt="Startseite" width="32" height="32">
    </div>
    <div class="karteikarten-icon">
        <a href="app.php" class="karteikarten-link">
            <img src="icons/karteikarten.png" alt="Karteikarten" width="38" height="38">
        </a>
    </div>

    <div class="kyoryoku-icon"><img src="icons/favicon.png" alt="Profilbild" width="46" height="46"></div>
    <div class="kyoryoku-menu" id="kyoryokuMenu">
        <ul>
            <li class="user_row">
                <span><?php echo htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <div class="switch_acc_container">
                        <button class="switch_acc_btn" alt="Change account"> ⇄ </button>
                        <div class="accounts_list"> <?php if (isset($_SESSION['loggedin_users'])) {
                            foreach ($_SESSION['loggedin_users'] as $account => $active) {
                                if ($account !== $_SESSION['current_user']) {
                                    echo '<form method="post" action="switch_account.php" style="margin:0;">
                                    <input type="hidden" name="user" value="'.$account.'">
                                    <button type="submit" class="account_switch">'.$account.'</button> </form>';
                                }
                            }
                        }
                        ?>
                            <form method="get" action="login.php" style="margin-top:5px;">
                                <button type="submit" class="account_switch">Account hinzufügen</button><img src="icons/plus.png" alt="Account hinzufügen" width="25" height="25" style="vertical-align: top; margin-left: 8px;">
                            </form>
                        </div> 
                    </div>
            </li>
            <li>Einstellungen</li>
            <li>
                <a href="logout.php">Abmelden</a>
            </li>
        </ul>
    </div>
    <div class="header-home">
    <h1 class="title">Vokabeln lernen leichtgemacht</h1>
    </div>
    <p class="text-home">Kyoryoku begleitet dich beim täglichen Lernen <br /> einfach, intuitiv und zuverlässig <br /> damit Wissen wirklich bleibt.</p>
    <button class="start_btn">Jetzt direkt loslegen ⭢</button>
    <div class="example_imgs">
        <div class="example_img1">
            <img src="icons/beispiel1.png" alt="Beispiel1" width="300" height="170">
        </div>
        <div class="example_img2">
            <img src="icons/beispiel2.png" alt="Beispiel2" width="320" height="200">
        </div>
        <div class="example_img3">
            <img src="icons/beispiel3.png" alt="Beispiel3" width="320" height="200">
        </div>
        <div class="example_img4">
            <img src="icons/beispiel4.png" alt="Beispiel4" width="320" height="197">
        </div>
    </div>
</body>

<footer>
  Urheberrecht - (Icons von: 
  <a href="https://www.flaticon.com" target="_blank" rel="noopener noreferrer">Flaticon</a> | Home Icon von Freepik, Augen-Icon beim Passwort-Feld von Gregor Cresnar, X-Icon bei Fehlermeldungen beim Registrieren von Alfredo Hernandez, I/Info/Tooltip Icon von IconsBox, Karteikarten Icon von Tempo_doloe) | Kyoryoku-Icon mit KI erstellt
</footer>


</html>