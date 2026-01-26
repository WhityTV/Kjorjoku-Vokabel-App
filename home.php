<?php
session_start();

// Sicherstellen, dass man eingeloggt ist
if (!isset($_SESSION["loggedin"]) || !isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Benutzerdaten laden für Profilbild
$usersFile = __DIR__ . '/users.json';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
$currentUser = $_SESSION['user'];
$userData = $users[$currentUser] ?? [];
$profilePicture = isset($userData['profile_picture']) && file_exists(__DIR__ . '/' . $userData['profile_picture']) 
    ? $userData['profile_picture'] 
    : 'icons/favicon.png';
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
    <div class="kyoryoku-icon-sidebar" data-title="Seitenleiste">
        <img src="icons/dark_kyoryoku.png" alt="Kyoryoku" class="kyoryoku-default" width="40" height="40">
        <img src="icons/seitenleiste.png" alt="Kyoryoku Hover" class="kyoryoku-hover" width="32" height="32">
    </div>
    <div class="home-icon <?php echo basename($_SERVER['PHP_SELF']) === 'home.php' ? 'active' : ''; ?>">
        <a href="home.php" data-title="Dashboard">
            <img src="icons/homeDark.png" alt="Startseite" width="32" height="32">
        </a>
    </div>
    <div class="karteikarten-icon">
        <a href="app.php" class="karteikarten-link" data-title="Karteikarten lernen">
            <img src="icons/karteikarten.png" alt="Karteikarten" width="38" height="38">
        </a>
    </div>
    <button id="importBtn" class="import-icon-btn" data-title="Karteikarten importieren">
        <img src="icons/datentransfer.png" alt="Import" width="38" height="38">
    </button>
    <div class="kyoryoku-icon"><img src="<?php echo htmlspecialchars($profilePicture, ENT_QUOTES, 'UTF-8'); ?>" alt="Profilbild" width="46" height="46" style="border-radius: 50%; object-fit: cover;"></div>
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
    
    <div class="header">
        <h1 class="title">Dashboard</h1>
    </div>

    <div style="max-width: 1200px; margin: 50px auto; padding: 0 30px;">
        <p style="font-size: 2.4rem; margin-bottom: 30px;">Willkommen zurück, <?php echo htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8'); ?>!</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 40px;">
            
            <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                <h2 style="font-size: 2.4rem; margin-bottom: 15px; color: #4a6fa5;">📚 Karteikarten</h2>
                <p style="font-size: 1.8rem; color: #666; margin-bottom: 20px;">Starte deine Lernsession</p>
                <a href="app.php" style="text-decoration: none;">
                    <button class="btn" style="width: 100%; padding: 15px; font-size: 1.8rem;">Zum Lernen</button>
                </a>
            </div>

            <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                <h2 style="font-size: 2.4rem; margin-bottom: 15px; color: #4a6fa5;">📊 Fortschritt</h2>
                <p style="font-size: 1.8rem; color: #666; margin-bottom: 10px;">Noch zu lernen: <strong>24 Karten</strong></p>
                <p style="font-size: 1.8rem; color: #666;">Gelernt: <strong>156 Karten</strong></p>
            </div>

            <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                <h2 style="font-size: 2.4rem; margin-bottom: 15px; color: #4a6fa5;">⚙️ Einstellungen</h2>
                <p style="font-size: 1.8rem; color: #666; margin-bottom: 20px;">Profil bearbeiten</p>
                <a href="settings.php" style="text-decoration: none;">
                    <button class="btn" style="width: 100%; padding: 15px; font-size: 1.8rem;">Zu den Einstellungen</button>
                </a>
            </div>

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