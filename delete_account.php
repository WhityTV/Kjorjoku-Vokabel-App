<?php
session_start();

// Sicherstellen, dass man eingeloggt ist
if (!isset($_SESSION["loggedin"]) || !isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$usersFile = __DIR__ . '/users.json';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
$currentUser = $_SESSION['user'];

// Konto löschen
if (isset($users[$currentUser])) {
    // Aus users.json entfernen
    unset($users[$currentUser]);
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // Aus eingeloggten Usern entfernen
    if (isset($_SESSION['loggedin_users'][$currentUser])) {
        unset($_SESSION['loggedin_users'][$currentUser]);
    }
    
    // Session komplett zerstören
    session_unset();
    session_destroy();
    
    // Zur Login-Seite mit Bestätigung weiterleiten
    header("Location: login.php?deleted=1");
    exit;
}

// Falls etwas schief geht, zurück zu den Einstellungen
header("Location: settings.php");
exit;
?>
