<?php
$usersFile = __DIR__ . '/users.json'; // Pfad zur JSON-Datei



session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $action = $_POST["action"]; // "login" oder "registrieren"
        $username = $_POST["username"] ?? "";
        $password = $_POST["password"] ?? "";
    // Benutzer aus JSON laden
    $users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

if ($action === "register") {
    $email = $_POST["email"] ?? "";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Ungültige E-Mail-Adresse!";
    }
    
    $confirm_password = $_POST["confirm_password"] ?? "";

    if ($password !== $confirm_password) {
        $error = "Passwörter stimmen nicht überein!";
    } elseif (!isset($users[$username])) {
        $users[$username] = password_hash($password, PASSWORD_DEFAULT);
        file_put_contents($usersFile, json_encode($users));
        $_SESSION["loggedin"] = true;
        $_SESSION["user"] = $username;
        header("Location: app.php");
        exit;
    } else {
        $error = "Benutzer existiert schon!";
    }
}   elseif ($action === "login") {
        if (isset($users[$username]) && password_verify($password, $users[$username])) {
            $_SESSION["loggedin"] = true;
            $_SESSION["user"] = $username;
            header("Location: app.php");
            exit;
        } else {
            $error = "Falscher Benutzername oder Passwort!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Login / Registrierung</title>
<link rel="stylesheet" href="vok_alltag.css">
<link rel="icon" type="image/x-icon" href="icons/favicon.ico">
</head>
<div class="login">
<body>
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
    </div>
    <input type="hidden" name="action" value="login">
    <button class="login_btn" type="submit">Anmelden</button>
    <p class="text">
        <a href="#" id="showForgottPassword" class="switch-link">PASSWORT VERGESSEN?</a> | <a href="#" id="showRegister" class="switch-link">KONTO ERSTELLEN</a>
    </p>
  </form>
</div>

<div id="registerForm" style="display:none;">
  <h4>Registrieren</h4>
  <form method="post">
    <div class="input-group">
        <input class="login_btn2" type="text" name="username" id="register_username" required placeholder=" ">
        <label for="register_username">Benutzername</label>
    </div>
    <div class="input-group">
        <input class="login_btn2" type="password" name="password" id="register_password" required placeholder=" ">
        <label for="register_password">Passwort</label>
    </div>
    <div class="input-group">
        <input class="login_btn2" type="password" name="confirm_password" id="confirm_password" required placeholder=" ">
        <label for="confirm_password">Passwort bestätigen</label>
    </div>
    <div class="input-group">
        <input class="login_btn2" type="text" name="email" id="email" required placeholder=" ">
        <label for="email">E-Mail_Adresse</label>
    </div>
    <input type="hidden" name="action" value="register">
    <button class="login_btn" type="submit">Konto erstellen</button>
    <p class="text">
        Du hast bereits ein Konto? <a href="#" id="showLogin" class="switch-link">LOGIN</a>
    </p>
  </form>
</div>
</div>

<script>
const loginForm = document.getElementById("loginForm");
const registerForm = document.getElementById("registerForm");
const showLoginBtn = document.getElementById("showLogin");
const showRegisterBtn = document.getElementById("showRegister");

showLoginBtn.addEventListener("click", () => {
  loginForm.style.display = "block";
  registerForm.style.display = "none";
});

showRegisterBtn.addEventListener("click", () => {
  loginForm.style.display = "none";
  registerForm.style.display = "block";
});
</script>
</body>
</html>