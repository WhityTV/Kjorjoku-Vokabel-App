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
        header("Location: home.php");
        exit;
    } else {
        $error = "Falscher Benutzername oder Passwort!";
    }

if (!isset($_SESSION['accounts'])) {
    $_SESSION['accounts'] = [];
}

// Füge aktuellen Benutzer zur Liste hinzu, falls noch nicht drin
if (!in_array($username, $_SESSION['accounts'])) {
    $_SESSION['accounts'][] = $username;
}

// Setze den aktuellen Benutzer
$_SESSION['user'] = $username;
$_SESSION['loggedin'] = true;
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
          
          <span class="toggle-password" id="togglePassword" role="button" tabindex="0" aria-label="Passwort anzeigen">
              <img src="icons/auge.png" width="20" height="20" alt="Passwort anzeigen/ausblenden">
          </span>
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