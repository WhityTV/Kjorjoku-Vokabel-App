<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $_POST["username"] ?? "";
    $pass = $_POST["password"] ?? "";

    if ($user === "test" && $pass === "1234") {
        $_SESSION["loggedin"] = true;
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
  <title>Login - Kyoryoku</title>
</head>
<body>
  <h2>Login</h2>
  <?php if (!empty($error)) echo "<p>$error</p>"; ?>
  <form method="post">
    <input type="text" name="username" placeholder="Benutzername" required><br>
    <input type="password" name="password" placeholder="Passwort" required><br>
    <button type="submit">Anmelden</button>
  </form>
</body>
</html>
