<?php
session_start();

// Sicherstellen, dass man eingeloggt ist
if (!isset($_SESSION["loggedin"]) || !isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Benutzerdaten laden für Profilbild
$usersFile = __DIR__ . '/users.json';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
$currentUser = $_SESSION['user'];
$userData = $users[$currentUser] ?? [];
$profilePicture = isset($userData['profile_picture']) && file_exists(__DIR__ . '/' . $userData['profile_picture']) 
    ? $userData['profile_picture'] 
    : 'icons/standard_pb.png';
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Kyoryoku</title>
  <link rel="stylesheet" href="vok_alltag.css">
  <link rel="icon" type="image/x-icon" href="icons/favicon.ico">
  <script src="menu.js" defer></script>
  <script src="vok_alltag.js" defer></script>
  <script src="import_cards.js" defer></script>
</head>
<body>
  <div class="kyoryoku-icon-sidebar" data-title="Seitenleiste">
    <img src="icons/dark_kyoryoku.png" alt="Kyoryoku" class="kyoryoku-default">
    <img src="icons/seitenleiste.png" alt="Kyoryoku Hover" class="kyoryoku-hover">
  </div>
  <div class="home-icon">
    <a href="home.php" data-title="Dashboard">
      <img src="icons/homeDark.png" alt="Startseite" width="32" height="32">
    </a>
  </div>
  <div class="karteikarten-icon <?php echo basename($_SERVER['PHP_SELF']) === 'app.php' ? 'active' : ''; ?>">
    <a href="app.php" class="karteikarten-link" data-title="Karteikarten lernen">
      <img 
        src="icons/<?php echo basename($_SERVER['PHP_SELF']) === 'app.php' ? 'karteikarten.png' : 'karteikarten.png'; ?>" 
        alt="Karteikarten" 
        width="38" height="38">
    </a>
  </div>
  <button id="importBtn" class="import-icon-btn" data-title="Karteikarten importieren">
    <img src="icons/datentransfer.png" alt="Import" width="38" height="38">
  </button>
  <div class="kyoryoku-icon"><img src="<?php echo htmlspecialchars($profilePicture, ENT_QUOTES, 'UTF-8'); ?>" alt="Profilbild" width="46" height="46" style="border-radius: 50%; object-fit: cover;"></div>
  <div class="kyoryoku-menu" id="kyoryokuMenu">
    <ul>
      <li class="user_row">
        <span><?php echo htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8'); ?></span>
          <div class="switch_acc_container">
            <button class="switch_acc_btn" alt="Change account"> ⇄ </button>
            <div class="accounts_list"> <?php if (isset($_SESSION['loggedin_users'])) {
              foreach ($_SESSION['loggedin_users'] as $account => $active) {
                if ($account !== $_SESSION['current_user']) {
                  echo '<form method="post" action="switch_account.php" style="margin:0;">
                  <input type="hidden" name="user" value="'.$account.'">
                  <button type="submit" class="account_switch">'.$account.'</button> </form>';
                }
              }
            }
            ?>
              <form method="get" action="login.php" style="margin-top:5px;">
                <button type="submit" class="account_switch" style="display: flex; align-items: center; gap: 15px; width: auto; white-space: nowrap;">Account hinzufügen <img src="icons/plus.png" alt="Account hinzufügen" width="24" height="24"></button>
              </form>
            </div> 
          </div>
      </li>
      <li>
        <a href="settings.php" style="text-decoration: none; color: inherit;">Einstellungen</a>
      </li>
      <li>
        <a href="logout.php">Abmelden</a>
      </li>
    </ul>
  </div>
  <div class="header">
    <h1 class="title">Alltags Vokabeln</h1>
  </div>

  <!-- Import Modal -->
  <div id="importModal" class="import-modal" style="display: none;">
    <div class="import-modal-content">
      <button class="import-close-btn">&times;</button>
      <h2>Karteikarten importieren</h2>
      
      <div class="import-format-selector">
        <label>
          <input type="radio" name="importFormat" value="anki" checked>
          <span>Anki (.txt, .apkg)</span>
        </label>
        <label>
          <input type="radio" name="importFormat" value="remnote">
          <span>RemNote (CSV)</span>
        </label>
        <label>
          <input type="radio" name="importFormat" value="csv">
          <span>CSV (Generic)</span>
        </label>
      </div>

      <div class="import-file-input">
        <input type="file" id="importFile" accept=".txt,.apkg,.csv">
        <small>Unterstützte Formate: TXT, APKG, CSV</small>
      </div>

      <div class="import-preview" id="importPreview" style="display: none;">
        <h3>Vorschau (<span id="previewCount">0</span> Karten)</h3>
        <div id="previewList" class="preview-list"></div>
      </div>

      <div class="import-options">
        <label>
          <input type="checkbox" id="mergeCards" checked>
          <span>Mit existierenden Karten zusammenführen</span>
        </label>
      </div>

      <div class="import-buttons">
        <button id="importCancelBtn" class="btn-secondary">Abbrechen</button>
        <button id="importSubmitBtn" class="btn-primary" disabled>Importieren</button>
      </div>

      <div id="importStatus" class="import-status" style="display: none;"></div>
    </div>
  </div>

  <template id="flashcardTemplate">
    <div class="flashcard">
      <p class="solid card-german"></p>
      
      <div class="japanese-display">
        <p class="kanji solid2"></p>
        <p class="kana solid2"></p>
        <p class="romaji solid2"></p>
      </div>
      
      <div class="button-group">
        <button class="kanji_btn btn default">➥</button>
        <button class="kana_btn btn default" style="display: none;">➥</button>
        <button class="romaji_btn btn default" style="display: none;">➥</button>
      </div>
    </div>
  </template>

  <div id="flashcardContainer"></div>

  <div id="reviewMenue">
      <button id="gewusst_btn">Gewusst</button>
      <button id="vergessen_btn">Vergessen</button>
  </div>

  <div id="reviewMenue2">
    <button id="gewusst_btn2">Gewusst</button>
    <button id="vergessen_btn2">Vergessen</button>
  </div>

  <div id="gewusst_optns" style="display: none;">
    <button id="gewusst_komplett">Komplett</button>
    <button id="gewusst_groesstenteils">Größtenteils</button>
    <button id="gewusst_teilweise">Teilweise</button>
    <button id="gewusst_back">↩</button>
  </div>

  <div id="vergessen_optns" style="display: none;">
      <button id="alles_vergessen">Alles vergessen</button>
      <button id="bereich_vergessen">Bereich vergessen</button>
      <button id="vergessen_back">↩</button>
  </div>

  <div id="nextContainer" style="display: none;">
    <button id="next_btn">Next</button>
  </div>

</body>

<footer>
  © 2026 Kyoryoku | 
  <a href="impressum.php">Impressum</a> | 
  <a href="datenschutz.php">Datenschutz</a> | 
  <a href="credits.php">Credits & Urheberrecht</a>
</footer>

</html>