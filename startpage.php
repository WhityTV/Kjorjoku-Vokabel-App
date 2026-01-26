<?php
session_start();

// Wenn bereits eingeloggt, weiterleiten zu home.php (Dashboard)
if (isset($_SESSION["loggedin"]) && isset($_SESSION['user'])) {
    header("Location: home.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kyornot - Vokabeln lernen leichtgemacht</title>
    <link rel="stylesheet" href="vok_alltag.css">
    <link rel="icon" type="image/x-icon" href="icons/favicon.ico">
</head>
<body class="no-sidebar">
    <div class="header-home">
    <h1 class="title">Vokabeln lernen leichtgemacht</h1>
    </div>
    <p class="text-home">Kyornot begleitet dich beim täglichen Lernen <br /> einfach, intuitiv und zuverlässig <br /> damit Wissen wirklich bleibt.</p>
    <a href="login.php" style="text-decoration: none;">
        <button class="start_btn">Jetzt direkt loslegen ⭢</button>
    </a>
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
  © 2026 Kyornot | 
  <a href="impressum.php">Impressum</a> | 
  <a href="datenschutz.php">Datenschutz</a> | 
  <a href="credits.php">Credits & Urheberrecht</a>
</footer>

</html>
