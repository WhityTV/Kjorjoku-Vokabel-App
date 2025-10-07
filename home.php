<?php

session_start();
if (!isset($_SESSION["loggedin"])) {
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
</head>
<body>
    <div class="home-icon <?php echo basename($_SERVER['PHP_SELF']) === 'home.php' ? 'active' : ''; ?>">
        <img src="icons/homeDark.png" alt="Startseite" width="32" height="32">
    </div>
    <div class="karteikarten-icon">
        <a href="app.html" class="karteikarten-link">
            <img src="icons/karteikarten.png" alt="Karteikarten" width="38" height="38">
        </a>
    </div>

    <div class="kyoryoku-icon"><img src="icons/favicon.png" alt="Profilbild" width="46" height="46"></div>
    <div class="kyoryoku-menu" id="kyoryokuMenu">
        <ul>
        <li>Profil</li>
        <li>Einstellungen</li>
        <li>Abmelden</li>
        </ul>
    </div>

</body>

<footer>
  Urheberrecht - (Icons von: 
  <a href="https://www.flaticon.com" target="_blank" rel="noopener noreferrer">Flaticon</a> | Home Icon von Freepik, Augen-Icon beim Passwort-Feld von Gregor Cresnar, X-Icon bei Fehlermeldungen beim Registrieren von Alfredo Hernandez, I/Info/Tooltip Icon von IconsBox, Karteikarten Icon von Tempo_doloe) | Kyoryoku-Icon mit KI erstellt
</footer>


</html>