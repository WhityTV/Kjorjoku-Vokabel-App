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
        <li>Profil</li>
        <li>Einstellungen</li>
        <li>Abmelden</li>
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