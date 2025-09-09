<?php
session_start();

$usersFile = __DIR__ . "/users.json";
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    $email = trim($_POST["email"] ?? "");

    // Validierungen
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = "Bitte gib eine gültige E-Mail-Adresse ein.";
    }
    if ($password !== $confirm_password) {
      $errors['password'] = "Passwörter stimmen nicht überein.";
    }
    if (strlen($password) < 8) {
      $errors['password'] = "Das Passwort muss mindestens 8 Zeichen lang sein!";
    } elseif (!preg_match('/[A-Z]/', $password)) {
      $errors['password'] = "Das Passwort muss mindestens einen Großbuchstaben enthalten!";
    } elseif (!preg_match('/[a-z]/', $password)) {
      $errors['password'] = "Das Passwort muss mindestens einen Kleinbuchstaben enthalten!";
    } elseif (!preg_match('/[0-9]/', $password)) {
      $errors['password'] = "Das Passwort muss mindestens eine Zahl enthalten!";
    } elseif (!preg_match('/[\W_]/', $password)) {
      $errors['password'] = "Das Passwort muss mindestens ein Sonderzeichen enthalten!";
    } elseif ($password === $username) {
      $errors['password'] = "Passwort und Benutzername dürfen nicht identisch sein!";
    }
    if (isset($users[$username])) {
      $errors['username'] = "Benutzername bereits vergeben.";
    }
    // Mindestlänge Username
    if (strlen($username) < 3) {
      $errors['username'] = "Der Benutzername muss mindestens 3 Zeichen lang sein.";
    }
    // Wenn keine Fehler -> speichern
    if (empty($errors)) {
        $uid = uniqid("user_", true); // erzeugt eindeutige User-ID
        $users[$username] = [
            "uid" => $uid,
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "email" => $email
        ];
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
        $_SESSION["loggedin"] = true;
        $_SESSION["user"] = $username;
        $_SESSION["uid"] = $uid; // optional für Session
        header("Location: app.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Registrieren</title>
  <link rel="stylesheet" href="vok_alltag.css">
  <script src="register_password_toggle.js" defer></script>
  <script src="register_confirm_password_toggle.js" defer></script>
</head>
<body>
<div class="login_register">
<h4>Konto erstellen</h4>

  <form method="post" novalidate>
    <div class="input-group">
      <input class="login_btn2 <?= isset($errors['username']) ? 'error' : '' ?>" 
             type="text" name="username" id="register_username" required placeholder=" "
             value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      <label for="register_username">Benutzername</label>
      <small class="error-message <?= isset($errors['username']) ? 'active' : '' ?>">
          <?= $errors['username'] ?? '' ?>
      </small>
    </div>

    <div class="input-group">
      <input class="login_btn2 <?= isset($errors['password']) ? 'error' : '' ?>" 
             type="password" name="password" id="register_password" required placeholder=" ">
      <label for="register_password">Passwort</label>
      <small class="password_hint">Das Passwort soll mind. 8 Zeichen lang sein, sowie einen Groß-/Kleinbuchstaben, eine Zahl und ein Sonderzeichen enthalten!</small>
      <span class="toggle-password" data-target="register_password" style="cursor:pointer;"><img src="icons/auge.png" width="20" height="20"></span>
    </div>

    <div class="input-group">
      <input class="login_btn2 <?= isset($errors['password']) ? 'error' : '' ?>" 
             type="password" name="confirm_password" id="register_confirm_password" required placeholder=" ">
      <label for="register_confirm_password">Passwort bestätigen</label>
      <span class="toggle-password" data-target="register_password" id="toggleConfirmPassword" style="cursor:pointer;"><img src="icons/auge.png" width="20" height="20"></span>
      <small class="error-message <?= isset($errors['password']) ? 'active' : '' ?>">
          <?= $errors['password'] ?? '' ?>
      </small>
    </div>

    <div class="input-group">
      <input class="login_btn2 <?= isset($errors['email']) ? 'error' : '' ?>" 
            type="email" name="email" id="register_email" required placeholder=" "
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      <label for="register_email">E-Mail-Adresse</label>
      <small class="error-message <?= isset($errors['email']) ? 'active' : '' ?>">
          <?= htmlspecialchars($errors['email'] ?? '') ?>
      </small>
    </div>

    <button class="login_btn" type="submit">Registrieren</button>
    <p class="text">
      Du hast bereits ein Konto?
      <a href="login.php" class="switch-link">LOGIN</a>
    </p>
  </form>
</div>
</body>
</html>