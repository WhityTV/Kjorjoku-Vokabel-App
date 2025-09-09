document.addEventListener("DOMContentLoaded", () => {
  const icon = document.querySelector('.kyoryoku-icon');
  const menu = document.getElementById('kyoryokuMenu');

  icon.addEventListener('click', (e) => {
    e.stopPropagation(); // Klick nicht weiterreichen
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
  });

  // Klick außerhalb schließt das Menü
  document.addEventListener('click', () => {
    menu.style.display = 'none';
  });

  // Klick im Menü selbst soll es nicht schließen
  menu.addEventListener('click', (e) => {
    e.stopPropagation();
  });
});

document.addEventListener('DOMContentLoaded', function() {
    const cards = [
      {
        german: "Vogel",
        kanji: "鳥",
        kana: "とり",
        romaji: "tori"
      },
      {
        german: "Schatten",
        kanji: "影",
        kana: "かげ",
        romaji: "kage"
      }
      // Weitere Karten hier einfügen
    ];
  
    const container = document.getElementById('flashcardContainer');
    // Lese den Index der letzten Karte aus
    let currentCardIndex = parseInt(localStorage.getItem('currentCardIndex')) || 0;
    // Fortschritt der aktuellen Karte
    let currentCardProgress = JSON.parse(localStorage.getItem('currentCardProgress')) || {};
  
    // Karteikarten erstellen
    function createFlashcards() {
      const template = document.getElementById('flashcardTemplate');
      cards.forEach((card, index) => {
        const clone = template.content.cloneNode(true);
        const cardDiv = clone.querySelector('.flashcard');
        if (index === currentCardIndex) cardDiv.classList.add('active');

        cardDiv.querySelector('.card-german').textContent = card.german;
        cardDiv.querySelector('.kanji').textContent = card.kanji;
        cardDiv.querySelector('.kana').textContent = card.kana;
        cardDiv.querySelector('.romaji').textContent = card.romaji;

        container.appendChild(cardDiv);
      });
    }
  
    // Event Delegation für Buttons
    container.addEventListener('click', function(e) {
        const target = e.target;
        const card = target.closest('.flashcard');
        if (target.classList.contains('kanji_btn')) {
            card.querySelector('.kanji').style.display = "inline";
            target.style.display = "none";
            card.querySelector('.kana_btn').style.display = "inline";
            // ReviewMenue2 anzeigen
            document.getElementById('reviewMenue2').style.display = "block";
            saveProgress();
        } else if (target.classList.contains('kana_btn')) {
            card.querySelector('.kana').style.display = "inline";
            target.style.display = "none";
            card.querySelector('.romaji_btn').style.display = "inline";
            // ReviewMenue2 anzeigen
            document.getElementById('reviewMenue2').style.display = "block";
            saveProgress();
        } else if (target.classList.contains('romaji_btn')) {
            card.querySelector('.romaji').style.display = "inline";
            target.style.display = "none";
            document.getElementById('reviewMenue2').style.display = "none";
            document.getElementById('reviewMenue').style.display = "block";
            saveProgress();
        }
    });

    document.getElementById('gewusst_btn').addEventListener('click', function() {
        document.getElementById('gewusst_optns').style.display = 'block';
        document.getElementById('reviewMenue').style.display = "none";
        document.getElementById('reviewMenue2').style.display = "none";
    });

    document.getElementById('gewusst_btn2').addEventListener('click', function() {
        document.getElementById('gewusst_optns').style.display = 'block'; // Korrigiert: richtiger ID Name
        document.getElementById('reviewMenue').style.display = "none";
        document.getElementById('reviewMenue2').style.display = "none";
    });

    // --- "Vergessen" Buttons ---
    document.getElementById('vergessen_btn').addEventListener('click', function() {
        document.getElementById('vergessen_optns').style.display = 'block';
        document.getElementById('reviewMenue').style.display = "none";
        document.getElementById('reviewMenue2').style.display = "none";
    });

    document.getElementById('vergessen_btn2').addEventListener('click', function() {
        document.getElementById('vergessen_optns').style.display = 'block';
        document.getElementById('reviewMenue').style.display = "none";
        document.getElementById('reviewMenue2').style.display = "none";
    });

    // --- Unter-Optionen für "Gewusst" ---
    document.getElementById('gewusst_komplett').addEventListener('click', function() {
        // Logik für "Komplett gewusst" (z.B. nächste Karte laden)
        nextCardAndReset();
        document.getElementById('gewusst_optns').style.display = 'none';
    });

    document.getElementById('gewusst_groesstenteils').addEventListener('click', function() {
        // Logik für "Größtenteils gewusst" (z.B. Karte später wiederholen)
        nextCardAndReset();
        document.getElementById('gewusst_optns').style.display = 'none';
    });

    document.getElementById('gewusst_teilweise').addEventListener('click', function() {
        // Logik für "Teilweise gewusst" (z.B. Karte bald wiederholen)
        nextCardAndReset();
        document.getElementById('gewusst_optns').style.display = 'none';
    });

    // Bereich vergessen Funktion
    document.getElementById('bereich_vergessen').addEventListener('click', function() {
        const currentCard = container.querySelector('.flashcard.active');
        if (!currentCard) return;
        // Überprüfen, wo wir uns befinden, und den entsprechenden Button anzeigen
        if (currentCard.querySelector('.romaji').style.display === "inline") {
            // Speichern des Index der nächsten Karte
            localStorage.setItem('currentCardIndex', (currentCardIndex + 1) % cards.length);
            // Wenn Romaji sichtbar ist, wechseln zur nächsten Karte (oder zurücksetzen)
            currentCard.classList.remove('active');
            currentCardIndex = (currentCardIndex + 1) % cards.length;
            const nextCard = container.children[currentCardIndex];
            nextCard.classList.add('active');
            resetCard(nextCard);
            document.getElementById('reviewMenue2').style.display = "none";
        } else if (currentCard.querySelector('.kana').style.display === "inline") {
            // Wenn Kana sichtbar ist, dann den Romaji-Button anzeigen
            currentCard.querySelector('.romaji_btn').style.display = "inline";
        } else {
            // Wenn Kanji sichtbar ist, dann den Kana-Button anzeigen
            currentCard.querySelector('.kana_btn').style.display = "inline";
        }
    // Menüs ausblenden
    document.getElementById('vergessen_optns').style.display = 'none';
    saveProgress();
    });

function saveProgress() {
  const card = container.children[currentCardIndex];
  currentCardProgress[currentCardIndex] = {
    kanji: card.querySelector('.kanji').style.display === "inline",
    kana: card.querySelector('.kana').style.display === "inline",
    romaji: card.querySelector('.romaji').style.display === "inline"
  };

  localStorage.setItem('currentCardIndex', currentCardIndex);
  localStorage.setItem('currentCardProgress', JSON.stringify(currentCardProgress));
}


// Reset-Funktion für Karten
function resetCard(card) {
    card.querySelectorAll('.kanji, .kana, .romaji').forEach(el => {
        el.style.display = 'none';
    });
    card.querySelectorAll('.btn').forEach(btn => {
        btn.style.display = 'inline';
    });
    // Blende nur den Kanji-Button der aktuellen Karte ein
    card.querySelector('.kanji_btn').style.display = 'inline';
    // Verstecke die anderen Buttons
    card.querySelector('.kana_btn').style.display = 'none';
    card.querySelector('.romaji_btn').style.display = 'none';
}

function applySavedProgress() {
  const card = container.children[currentCardIndex];
  const progress = currentCardProgress[currentCardIndex];

  if (!card || !progress) return;

  // Grundzustand
  resetCard(card);

  if (progress.kanji) {
    card.querySelector('.kanji').style.display = "inline";
    card.querySelector('.kanji_btn').style.display = "none";
    card.querySelector('.kana_btn').style.display = "inline";
  }
  if (progress.kana) {
    card.querySelector('.kana').style.display = "inline";
    card.querySelector('.kana_btn').style.display = "none";
    card.querySelector('.romaji_btn').style.display = "inline";
  }
  if (progress.romaji) {
    card.querySelector('.romaji').style.display = "inline";
    card.querySelector('.romaji_btn').style.display = "none";
    document.getElementById('reviewMenue').style.display = "block";
  } else if (progress.kana) {
    document.getElementById('reviewMenue2').style.display = "block";
  }
}

    // Initialisierung
    createFlashcards();
    applySavedProgress();
  });