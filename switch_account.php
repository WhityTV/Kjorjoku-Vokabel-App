<?php
session_start();

if (isset($_POST['user']) && in_array($_POST['user'], $_SESSION['accounts'])) {
    $_SESSION['user'] = $_POST['user'];
}

header("Location: home.php"); // zurück zur Startseite
exit;
?>