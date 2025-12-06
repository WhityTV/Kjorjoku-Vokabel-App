<?php
session_start();

$usersFile = __DIR__ . '/users.json';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

if (isset($_POST['user']) && isset($_SESSION['loggedin_users'][$_POST['user']]) && isset($users[$_POST['user']])) {
    $_SESSION['current_user'] = $_POST['user'];
}

header("Location: home.php");
exit;
?>