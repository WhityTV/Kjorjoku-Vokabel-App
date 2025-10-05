<?php
session_start();

$usersFile = __DIR__ . '/users.json';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    if (isset($users[$username]) && password_verify($password, $users[$username]["password"])) {
        $_SESSION["loggedin"] = true;
        $_SESSION["user"] = $username;
        header("Location: app.php");
        exit;
    } else {
        $error = "Falscher Benutzername oder Passwort!";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="vok_alltag.css">
  <link rel="icon" type="image/x-icon" href="icons/favicon.ico">
  <script src="login_password_toggle.js" defer></script>
</head>
<body>
<div class="login_register">

  <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <h1></h1>
  <div id="loginForm">
    <h4>Login</h4>
    <form method="post">
      <div class="input-group">
        <input class="login_btn2" type="text" name="username" id="login_username" required placeholder=" ">
        <label for="login_username">Benutzername</label>
      </div>
      <div class="input-group">
        <input class="login_btn2" type="password" name="password" id="login_password" required placeholder=" ">
        <label for="login_password">Passwort</label>
      <span id="togglePassword" style="cursor:pointer;"><img src="icons/auge.png" alt="Auge" width="20" height="20"></span>
      </div>
      <button class="login_btn" type="submit">Anmelden</button>
      <p class="text">
        <a href="#" id="showForgottPassword" class="switch-link">PASSWORT VERGESSEN?</a> | 
        <a href="register.php" class="switch-link">KONTO ERSTELLEN</a>
      </p>
    </form>
  </div>

</div>
</body>
</html>