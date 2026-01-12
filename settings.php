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

// Fehler/Erfolg Meldungen
$message = '';
$messageType = '';

// Profil aktualisieren
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_profile') {
            $newUsername = $_POST['username'] ?? '';
            $newEmail = $_POST['email'] ?? '';
            
            // Validierung
            if (empty($newUsername)) {
                $message = 'Benutzername darf nicht leer sein!';
                $messageType = 'error';
            } elseif (empty($newEmail) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $message = 'Gültige E-Mail erforderlich!';
                $messageType = 'error';
            } else {
                // Benutzername aktualisieren, wenn geändert
                if ($newUsername !== $currentUser && isset($users[$newUsername])) {
                    $message = 'Benutzername existiert bereits!';
                    $messageType = 'error';
                } else {
                    // Alten Eintrag löschen und neuen erstellen, falls Name geändert
                    if ($newUsername !== $currentUser) {
                        $users[$newUsername] = $users[$currentUser];
                        unset($users[$currentUser]);
                        $_SESSION['user'] = $newUsername;
                        $_SESSION['current_user'] = $newUsername;
                        $currentUser = $newUsername;
                    }
                    
                    // E-Mail aktualisieren
                    $users[$currentUser]['email'] = $newEmail;
                    
                    // In JSON speichern
                    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    
                    $message = 'Profil erfolgreich aktualisiert!';
                    $messageType = 'success';
                }
            }
        } elseif ($_POST['action'] === 'change_password') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validierung
            if (!isset($users[$currentUser]['password']) || 
                !password_verify($currentPassword, $users[$currentUser]['password'])) {
                $message = 'Aktuelles Passwort ist falsch!';
                $messageType = 'error';
            } elseif (empty($newPassword) || strlen($newPassword) < 6) {
                $message = 'Passwort muss mindestens 6 Zeichen lang sein!';
                $messageType = 'error';
            } elseif ($newPassword !== $confirmPassword) {
                $message = 'Passwörter stimmen nicht überein!';
                $messageType = 'error';
            } else {
                $users[$currentUser]['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $message = 'Passwort erfolgreich geändert!';
                $messageType = 'success';
            }
        } elseif ($_POST['action'] === 'update_settings') {
            $dailyGoal = isset($_POST['daily_goal']) ? intval($_POST['daily_goal']) : 20;
            $learningMode = $_POST['learning_mode'] ?? 'normal';
            $notifications = isset($_POST['notifications']) ? 1 : 0;
            $darkMode = isset($_POST['dark_mode']) ? 1 : 0;
            
            if (!isset($users[$currentUser]['settings'])) {
                $users[$currentUser]['settings'] = [];
            }
            
            $users[$currentUser]['settings'] = [
                'daily_goal' => $dailyGoal,
                'learning_mode' => $learningMode,
                'notifications' => $notifications,
                'dark_mode' => $darkMode
            ];
            
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $message = 'Einstellungen erfolgreich gespeichert!';
            $messageType = 'success';
        }
    }
}

$userData = $users[$currentUser] ?? [];
$settings = $userData['settings'] ?? [
    'daily_goal' => 20,
    'learning_mode' => 'normal',
    'notifications' => 1,
    'dark_mode' => 0
];
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Einstellungen</title>
    <link rel="stylesheet" href="vok_alltag.css">
    <link rel="icon" type="image/x-icon" href="icons/favicon.ico">
    <script src="menu.js" defer></script>
    <style>
        .settings-container {
            max-width: 600px;
            margin: 100px auto 20px;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .settings-container h2 {
            font-size: 2.8rem;
            color: #2c3e50;
            margin-bottom: 25px;
            border-bottom: 2px solid #4a6fa5;
            padding-bottom: 10px;
        }

        .settings-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .settings-section h3 {
            font-size: 2.2rem;
            color: #4a6fa5;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="number"],
        .form-group select {
            width: 100%;
            padding: 12px;
            font-size: 1.6rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-group input[type="checkbox"],
        .form-group input[type="radio"] {
            margin-right: 8px;
            cursor: pointer;
            width: 18px;
            height: 18px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            font-size: 1.8rem;
            margin: 10px 0;
        }

        .form-group textarea {
            width: 100%;
            padding: 12px;
            font-size: 1.6rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: inherit;
            resize: vertical;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 1.8rem;
            display: none;
        }

        .message.active {
            display: block;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: flex-start;
        }

        .settings-btn {
            cursor: pointer;
            background: #4a6fa5;
            color: white;
            font-weight: bold;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 1.8rem;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .settings-btn:hover {
            background: #3a5a8a;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            font-size: 1.8rem;
            color: #4a6fa5;
            text-decoration: none;
            cursor: pointer;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="home-icon">
        <a href="home.php" style="text-decoration: none; color: #4a6fa5;">
            <img src="icons/homeDark.png" alt="Startseite" width="32" height="32">
        </a>
    </div>
    <div class="karteikarten-icon">
        <a href="app.php" class="karteikarten-link">
            <img src="icons/karteikarten.png" alt="Karteikarten" width="38" height="38">
        </a>
    </div>

    <div class="kyoryoku-icon"><img src="icons/favicon.png" alt="Profilbild" width="46" height="46"></div>
    <div class="kyoryoku-menu" id="kyoryokuMenu">
        <ul>
            <li class="user_row">
                <span><?php echo htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8'); ?></span>
            </li>
            <li>
                <a href="logout.php">Abmelden</a>
            </li>
        </ul>
    </div>

    <div class="settings-container">
        <a class="back-link" onclick="window.history.back();">← Zurück</a>

        <h2>Einstellungen</h2>

        <?php if ($message): ?>
            <div class="message active <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <!-- Profileinstellungen -->
        <div class="settings-section">
            <h3>👤 Profil</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="form-group">
                    <label for="username">Benutzername:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($currentUser, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">E-Mail:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>

                <div class="btn-group">
                    <button type="submit" class="settings-btn">Profil aktualisieren</button>
                </div>
            </form>
        </div>

        <!-- Passwortänderung -->
        <div class="settings-section">
            <h3>🔒 Passwort</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="change_password">
                
                <div class="form-group">
                    <label for="current_password">Aktuelles Passwort:</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">Neues Passwort:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Passwort bestätigen:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="btn-group">
                    <button type="submit" class="settings-btn">Passwort ändern</button>
                </div>
            </form>
        </div>

        <!-- Lerneinstellungen -->
        <div class="settings-section">
            <h3>📚 Lerneinstellungen</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="update_settings">
                
                <div class="form-group">
                    <label for="daily_goal">Tägliches Lernziel (Karten):</label>
                    <input type="number" id="daily_goal" name="daily_goal" value="<?php echo $settings['daily_goal'] ?? 20; ?>" min="1" max="500">
                </div>

                <div class="form-group">
                    <label>Lernmodus:</label>
                    <div class="checkbox-group">
                        <input type="radio" id="mode_normal" name="learning_mode" value="normal" <?php echo ($settings['learning_mode'] ?? 'normal') === 'normal' ? 'checked' : ''; ?>>
                        <label for="mode_normal" style="margin: 0; font-weight: normal;">Normal</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="radio" id="mode_fast" name="learning_mode" value="fast" <?php echo ($settings['learning_mode'] ?? 'normal') === 'fast' ? 'checked' : ''; ?>>
                        <label for="mode_fast" style="margin: 0; font-weight: normal;">Schnell</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="radio" id="mode_intensive" name="learning_mode" value="intensive" <?php echo ($settings['learning_mode'] ?? 'normal') === 'intensive' ? 'checked' : ''; ?>>
                        <label for="mode_intensive" style="margin: 0; font-weight: normal;">Intensiv</label>
                    </div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="notifications" name="notifications" <?php echo ($settings['notifications'] ?? 1) ? 'checked' : ''; ?>>
                    <label for="notifications" style="margin: 0; font-weight: normal;">🔔 Benachrichtigungen aktivieren</label>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="dark_mode" name="dark_mode" <?php echo ($settings['dark_mode'] ?? 0) ? 'checked' : ''; ?>>
                    <label for="dark_mode" style="margin: 0; font-weight: normal;">🌙 Dunkler Modus</label>
                </div>

                <div class="btn-group" style="margin-top: 20px;">
                    <button type="submit" class="settings-btn">Einstellungen speichern</button>
                </div>
            </form>
        </div>

        <!-- Datenverwaltung -->
        <div class="settings-section">
            <h3>⚙️ Datenverwaltung</h3>
            <p style="font-size: 1.8rem; color: #666; margin-bottom: 15px;">
                Daten exportieren oder Konto löschen (erweiterte Optionen)
            </p>
            <div class="btn-group">
                <button class="settings-btn" onclick="alert('Export-Funktion wird bald implementiert');">📥 Daten exportieren</button>
                <button class="settings-btn" style="background: #d9534f;" onclick="if(confirm('Wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.')) window.location.href='delete_account.php';">🗑️ Konto löschen</button>
            </div>
        </div>
    </div>
</body>
</html>
