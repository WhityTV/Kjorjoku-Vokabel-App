document.addEventListener('DOMContentLoaded', function() {
  const importBtn = document.getElementById('importBtn');
  const importModal = document.getElementById('importModal');
  const importCloseBtn = document.querySelector('.import-close-btn');
  const importCancelBtn = document.getElementById('importCancelBtn');
  const importFile = document.getElementById('importFile');
  const importSubmitBtn = document.getElementById('importSubmitBtn');
  const importPreview = document.getElementById('importPreview');
  const previewList = document.getElementById('previewList');
  const previewCount = document.getElementById('previewCount');
  const importStatus = document.getElementById('importStatus');
  const formatRadios = document.querySelectorAll('input[name="importFormat"]');

  // Modal öffnen
  if (importBtn) {
    importBtn.addEventListener('click', function() {
      importModal.style.display = 'flex';
    });
  }

  // Modal schließen
  function closeModal() {
    importModal.style.display = 'none';
    resetForm();
  }

  if (importCloseBtn) {
    importCloseBtn.addEventListener('click', closeModal);
  }

  if (importCancelBtn) {
    importCancelBtn.addEventListener('click', closeModal);
  }

  // Modal schließen bei Klick außerhalb
  window.addEventListener('click', function(event) {
    if (event.target === importModal) {
      closeModal();
    }
  });

  // Datei ausgewählt
  if (importFile) {
    importFile.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        previewImportFile(file);
      }
    });
  }

  // Datei-Vorschau
  async function previewImportFile(file) {
    try {
      const content = await file.text();
      const format = document.querySelector('input[name="importFormat"]:checked').value;
      
      const cards = parseFile(content, format, file.name);
      
      if (cards.length > 0) {
        displayPreview(cards);
        importSubmitBtn.disabled = false;
      } else {
        showError('Keine Karteikarten in der Datei gefunden');
        importSubmitBtn.disabled = true;
      }
    } catch (error) {
      showError('Fehler beim Lesen der Datei: ' + error.message);
      importSubmitBtn.disabled = true;
    }
  }

  // Datei-Format parsen
  function parseFile(content, format, fileName) {
    let cards = [];

    if (format === 'anki') {
      cards = parseAnkiFile(content, fileName);
    } else if (format === 'remnote') {
      cards = parseRemnoteFile(content);
    } else {
      cards = parseCSVFile(content);
    }

    return cards.filter(card => card.german && card.kanji);
  }

  // Anki Format parsen
  function parseAnkiFile(content, fileName) {
    const cards = [];
    const lines = content.split('\n').filter(line => line.trim() && !line.trim().startsWith('#'));

    lines.forEach(line => {
      const parts = line.split(/[\t;:,]/);
      if (parts.length >= 2) {
        cards.push({
          german: parts[0].trim(),
          kanji: parts[1].trim(),
          kana: parts[2]?.trim() || '',
          romaji: parts[3]?.trim() || ''
        });
      }
    });

    return cards;
  }

  // RemNote Format parsen
  function parseRemnoteFile(content) {
    const cards = [];
    const lines = content.split('\n').filter(line => line.trim());
    let skipFirst = true;

    lines.forEach(line => {
      if (skipFirst && (line.includes('front') || line.includes('back'))) {
        skipFirst = false;
        return;
      }

      const parts = parseCSVLine(line);
      if (parts.length >= 2) {
        const front = parts[0].trim();
        const back = parts[1].trim();
        
        const frontParts = front.split(/[;:]/);
        cards.push({
          german: frontParts[0].trim(),
          kanji: frontParts[1]?.trim() || back,
          kana: parts[2]?.trim() || '',
          romaji: parts[3]?.trim() || ''
        });
      }
    });

    return cards;
  }

  // CSV Format parsen
  function parseCSVFile(content) {
    const cards = [];
    const lines = content.split('\n').filter(line => line.trim());
    let skipFirst = true;

    lines.forEach(line => {
      if (skipFirst && /^(german|english|word|front|back|kanji|kana)/i.test(line)) {
        skipFirst = false;
        return;
      }

      const parts = parseCSVLine(line);
      if (parts.length >= 2) {
        cards.push({
          german: parts[0].trim(),
          kanji: parts[1].trim(),
          kana: parts[2]?.trim() || '',
          romaji: parts[3]?.trim() || ''
        });
      }
    });

    return cards;
  }

  // CSV-Zeile parsen
  function parseCSVLine(line) {
    const result = [];
    let current = '';
    let inQuotes = false;

    for (let i = 0; i < line.length; i++) {
      const char = line[i];
      
      if (char === '"') {
        inQuotes = !inQuotes;
      } else if ((char === ',' || char === ';') && !inQuotes) {
        result.push(current);
        current = '';
      } else {
        current += char;
      }
    }
    result.push(current);

    return result;
  }

  // Vorschau anzeigen
  function displayPreview(cards) {
    previewCount.textContent = cards.length;
    previewList.innerHTML = '';

    const maxPreview = Math.min(5, cards.length);
    for (let i = 0; i < maxPreview; i++) {
      const card = cards[i];
      const cardEl = document.createElement('div');
      cardEl.className = 'preview-card';
      cardEl.innerHTML = `
        <div class="preview-german">${escapeHtml(card.german)}</div>
        <div class="preview-japanese">
          <span class="preview-kanji">${escapeHtml(card.kanji)}</span>
          ${card.kana ? `<span class="preview-kana">${escapeHtml(card.kana)}</span>` : ''}
          ${card.romaji ? `<span class="preview-romaji">${escapeHtml(card.romaji)}</span>` : ''}
        </div>
      `;
      previewList.appendChild(cardEl);
    }

    if (cards.length > maxPreview) {
      const moreEl = document.createElement('div');
      moreEl.className = 'preview-more';
      moreEl.textContent = `... und ${cards.length - maxPreview} weitere`;
      previewList.appendChild(moreEl);
    }

    importPreview.style.display = 'block';
  }

  // Fehler anzeigen
  function showError(message) {
    importStatus.style.display = 'block';
    importStatus.className = 'import-status error';
    importStatus.textContent = message;
  }

  // Import durchführen
  if (importSubmitBtn) {
    importSubmitBtn.addEventListener('click', async function() {
      const file = importFile.files[0];
      if (!file) {
        showError('Bitte eine Datei auswählen');
        return;
      }

      const format = document.querySelector('input[name="importFormat"]:checked').value;
      const merge = document.getElementById('mergeCards').checked;

      importSubmitBtn.disabled = true;
      importStatus.style.display = 'block';
      importStatus.className = 'import-status loading';
      importStatus.textContent = 'Importiere Karteikarten...';

      const formData = new FormData();
      formData.append('file', file);
      formData.append('format', format);
      formData.append('merge', merge ? 'true' : 'false');

      try {
        const response = await fetch('import_cards.php', {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (data.success) {
          importStatus.className = 'import-status success';
          importStatus.textContent = '✓ ' + data.message;
          
          // Modal nach 2 Sekunden schließen und Seite neu laden
          setTimeout(() => {
            closeModal();
            location.reload();
          }, 2000);
        } else {
          showError('Fehler: ' + data.message);
          importSubmitBtn.disabled = false;
        }
      } catch (error) {
        showError('Fehler beim Import: ' + error.message);
        importSubmitBtn.disabled = false;
      }
    });
  }

  // Form zurücksetzen
  function resetForm() {
    importFile.value = '';
    previewList.innerHTML = '';
    importPreview.style.display = 'none';
    importStatus.style.display = 'none';
    importSubmitBtn.disabled = true;
  }

  // Format-Wechsel
  formatRadios.forEach(radio => {
    radio.addEventListener('change', function() {
      if (importFile.files.length > 0) {
        previewImportFile(importFile.files[0]);
      }
    });
  });

  // HTML escapen
  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
});
