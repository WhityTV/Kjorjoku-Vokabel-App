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
    <script src="menu.js" defer></script>
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
                                <button type="submit" class="account_switch" style="display: flex; align-items: center; gap: 15px; width: auto; white-space: nowrap;">Account hinzufügen <img src="icons/plus.png" alt="Account hinzufügen" width="24" height="24"></button>
                            </form>
                        </div> 
                    </div>
            </li>
            <li>
                <a href="settings.php" style="text-decoration: none; color: inherit;">Einstellungen</a>
            </li>
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
  © 2026 Kyoryoku | 
  <a href="impressum.php">Impressum</a> | 
  <a href="datenschutz.php">Datenschutz</a> | 
  <a href="credits.php">Credits & Urheberrecht</a>
</footer>


</html>