<?php
session_start();

if (!isset($_SESSION["loggedin"]) || !isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Nicht authentifiziert']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Keine Datei hochgeladen']);
    exit;
}

$importFormat = $_POST['format'] ?? 'csv';
$mergeCards = isset($_POST['merge']) && $_POST['merge'] === 'true';
$uploadedFile = $_FILES['file']['tmp_name'];
$fileName = $_FILES['file']['name'];

try {
    // Dateiinhalt lesen
    $content = file_get_contents($uploadedFile);
    if ($content === false) {
        throw new Exception('Datei konnte nicht gelesen werden');
    }

    // Parse basierend auf Format
    $cards = [];
    
    if ($importFormat === 'anki') {
        $cards = parseAnkiFormat($content, $fileName);
    } elseif ($importFormat === 'remnote') {
        $cards = parseRemnoteFormat($content);
    } else {
        $cards = parseCSVFormat($content);
    }

    if (empty($cards)) {
        throw new Exception('Keine Karteikarten in der Datei gefunden');
    }

    // Karten speichern
    $userCardsFile = __DIR__ . '/user_cards/' . $_SESSION['user'] . '_cards.json';
    $userCardsDir = __DIR__ . '/user_cards';
    
    // Verzeichnis erstellen, falls nicht vorhanden
    if (!is_dir($userCardsDir)) {
        mkdir($userCardsDir, 0755, true);
    }

    $existingCards = [];
    if (file_exists($userCardsFile)) {
        $existingCards = json_decode(file_get_contents($userCardsFile), true) ?? [];
    }

    // Karten zusammenführen oder ersetzen
    if ($mergeCards) {
        $cards = array_merge($existingCards, $cards);
    }

    // Duplikate entfernen (basierend auf German + Kanji)
    $uniqueCards = [];
    $seen = [];
    foreach ($cards as $card) {
        $key = strtolower($card['german'] ?? '') . '|' . ($card['kanji'] ?? '');
        if (!isset($seen[$key])) {
            $uniqueCards[] = $card;
            $seen[$key] = true;
        }
    }

    if (file_put_contents($userCardsFile, json_encode($uniqueCards, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))) {
        echo json_encode([
            'success' => true,
            'message' => count($uniqueCards) . ' Karteikarten erfolgreich importiert',
            'count' => count($cards),
            'totalCards' => count($uniqueCards)
        ]);
    } else {
        throw new Exception('Karteikarten konnten nicht gespeichert werden');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

/**
 * Parse Anki Format (TXT oder einfaches Format)
 */
function parseAnkiFormat($content, $fileName) {
    $cards = [];
    
    // Überprüfe, ob es eine APKG-Datei ist
    if (strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) === 'apkg') {
        return parseAPKG($content);
    }

    // TXT Format (Tab- oder Newline-getrennt)
    $lines = array_filter(array_map('trim', explode("\n", $content)));
    
    foreach ($lines as $line) {
        if (empty($line) || $line[0] === '#') continue;
        
        // Versuche, die Zeile zu parsen
        if (strpos($line, "\t") !== false) {
            // Tab-getrennt
            $parts = array_map('trim', explode("\t", $line));
            if (count($parts) >= 2) {
                $cards[] = createCard($parts[0], $parts[1], $parts[2] ?? '', $parts[3] ?? '');
            }
        } else if (preg_match('/^(.+?)\s*[;:,]\s*(.+?)(?:\s*[;:,]\s*(.+?))?$/u', $line, $matches)) {
            $cards[] = createCard($matches[1], $matches[2], $matches[3] ?? '', '');
        }
    }
    
    return $cards;
}

/**
 * Parse RemNote CSV Format
 */
function parseRemnoteFormat($content) {
    $cards = [];
    $lines = array_filter(array_map('trim', explode("\n", $content)));
    $isFirstLine = true;
    
    foreach ($lines as $line) {
        if ($isFirstLine) {
            $isFirstLine = false;
            // Skip header line
            if (stripos($line, 'front') !== false || stripos($line, 'back') !== false) {
                continue;
            }
        }
        
        if (empty($line)) continue;
        
        // CSV parsen
        $parts = str_getcsv($line);
        if (count($parts) >= 2) {
            $front = trim($parts[0]);
            $back = trim($parts[1]);
            
            // Versuche, Front und Back zu parsen
            if (preg_match('/(.+?)\s*[;:]\s*(.+)/u', $front, $matches)) {
                $cards[] = createCard($matches[1], $matches[2], '', '');
            } else {
                $cards[] = createCard($front, $back, '', '');
            }
        }
    }
    
    return $cards;
}

/**
 * Parse Generic CSV Format
 */
function parseCSVFormat($content) {
    $cards = [];
    $lines = array_filter(array_map('trim', explode("\n", $content)));
    $isFirstLine = true;
    
    foreach ($lines as $line) {
        if ($isFirstLine) {
            $isFirstLine = false;
            // Skip if it looks like a header
            if (preg_match('/(german|english|word|front|back|kanji|kana)/i', $line)) {
                continue;
            }
        }
        
        if (empty($line)) continue;
        
        $parts = str_getcsv($line);
        
        if (count($parts) >= 2) {
            // Versuche, Spalten zu identifizieren
            $german = trim($parts[0]);
            $japanese = trim($parts[1]);
            $kana = isset($parts[2]) ? trim($parts[2]) : '';
            $romaji = isset($parts[3]) ? trim($parts[3]) : '';
            
            $cards[] = createCard($german, $japanese, $kana, $romaji);
        }
    }
    
    return $cards;
}

/**
 * Erstelle eine Kartendatenstruktur
 */
function createCard($german, $kanji, $kana = '', $romaji = '') {
    return [
        'german' => $german,
        'kanji' => $kanji,
        'kana' => $kana ?: extractKana($kanji),
        'romaji' => $romaji ?: extractRomaji($kanji)
    ];
}

/**
 * Extrahiere Kana aus Kanji (vereinfacht)
 */
function extractKana($kanji) {
    // Dies ist eine sehr vereinfachte Version
    // Eine echte Implementierung würde eine Datenbank oder API verwenden
    return '';
}

/**
 * Extrahiere Romaji aus Kanji (vereinfacht)
 */
function extractRomaji($kanji) {
    // Dies ist eine sehr vereinfachte Version
    return '';
}

/**
 * Parse APKG (ZIP-Datei)
 * APKG ist nur eine ZIP-Datei mit SQLite-Datenbank
 */
function parseAPKG($content) {
    $cards = [];
    
    // Speichere Datei temporär
    $tempFile = tempnam(sys_get_temp_dir(), 'apkg_');
    file_put_contents($tempFile, $content);
    
    try {
        // Öffne als ZIP
        $zip = new ZipArchive();
        if ($zip->open($tempFile) !== true) {
            throw new Exception('APKG-Datei ist beschädigt');
        }
        
        // Suche nach collection.anki2 (SQLite Datenbank)
        $dbFile = null;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if (strpos($stat['name'], 'collection.anki2') !== false) {
                $dbFile = $stat['name'];
                break;
            }
        }
        
        if (!$dbFile) {
            $zip->close();
            throw new Exception('Keine Anki-Datenbank in APKG-Datei gefunden');
        }
        
        // Extrahiere Datenbank
        $tempDb = tempnam(sys_get_temp_dir(), 'anki_');
        copy('zip://' . $tempFile . '#' . $dbFile, $tempDb);
        
        // Öffne SQLite Datenbank
        $db = new SQLite3($tempDb);
        $result = $db->query("SELECT flds, tags FROM notes");
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $fields = explode('', $row['flds']);
            if (count($fields) >= 2) {
                $cards[] = createCard($fields[0], $fields[1], $fields[2] ?? '', $fields[3] ?? '');
            }
        }
        
        $db->close();
        $zip->close();
        unlink($tempDb);
        
    } finally {
        @unlink($tempFile);
    }
    
    return $cards;
}
?>
