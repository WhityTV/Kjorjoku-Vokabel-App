document.addEventListener('DOMContentLoaded', function() {
    // --- KYORYOKU MENU LOGIK ---
    const kyoryokuIcon = document.querySelector('.kyoryoku-icon');
    const kyoryokuMenu = document.getElementById('kyoryokuMenu');

    kyoryokuIcon.addEventListener('click', (e) => {
        e.stopPropagation();
        kyoryokuMenu.style.display = kyoryokuMenu.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', () => {
        kyoryokuMenu.style.display = 'none';
    });

    kyoryokuMenu.addEventListener('click', (e) => {
        e.stopPropagation();
    });

    // --- FLASHCARD LOGIK ---
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
    ];
  
    const container = document.getElementById('flashcardContainer');
    let currentCardIndex = parseInt(localStorage.getItem('currentCardIndex')) || 0;
    let currentCardProgress = JSON.parse(localStorage.getItem('currentCardProgress')) || {};

    // HILFSFUNKTIONEN
    function nextCardAndReset() {
        const currentCard = container.querySelector('.flashcard.active');
        if (currentCard) {
            currentCard.classList.remove('active');
            currentCardIndex = (currentCardIndex + 1) % cards.length;
            const nextCard = container.children[currentCardIndex];
            nextCard.classList.add('active');
            resetCard(nextCard);
            document.getElementById('reviewMenue').style.display = "none";
            document.getElementById('reviewMenue2').style.display = "none";
            saveProgress();
        }
    }

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

    function resetCard(card) {
        card.querySelectorAll('.kanji, .kana, .romaji').forEach(el => {
            el.style.display = 'none';
        });
        card.querySelector('.kanji_btn').style.display = 'inline';
        card.querySelector('.kana_btn').style.display = 'none';
        card.querySelector('.romaji_btn').style.display = 'none';
    }

    function applySavedProgress() {
        const card = container.children[currentCardIndex];
        const progress = currentCardProgress[currentCardIndex];

        if (!card) return;
        resetCard(card);

        if (!progress) {
            document.getElementById('reviewMenue').style.display = "none";
            document.getElementById('reviewMenue2').style.display = "none";
            return;
        }

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
            document.getElementById('reviewMenue2').style.display = "none";
        } else if (progress.kana) {
            document.getElementById('reviewMenue2').style.display = "block";
            document.getElementById('reviewMenue').style.display = "none";
        } else {
            document.getElementById('reviewMenue').style.display = "none";
            document.getElementById('reviewMenue2').style.display = "none";
        }
    }

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

    // EVENT-LISTENER
    container.addEventListener('click', function(e) {
        const target = e.target;
        const card = target.closest('.flashcard');
        if (!card) return;

        if (target.classList.contains('kanji_btn')) {
            card.querySelector('.kanji').style.display = "inline";
            target.style.display = "none";
            card.querySelector('.kana_btn').style.display = "inline";
            document.getElementById('reviewMenue2').style.display = "block";
            document.getElementById('reviewMenue').style.display = "none";
            saveProgress();
        } else if (target.classList.contains('kana_btn')) {
            card.querySelector('.kana').style.display = "inline";
            target.style.display = "none";
            card.querySelector('.romaji_btn').style.display = "inline";
            document.getElementById('reviewMenue2').style.display = "block";
            document.getElementById('reviewMenue').style.display = "none";
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
        document.getElementById('gewusst_optns').style.display = 'block';
        document.getElementById('reviewMenue').style.display = "none";
        document.getElementById('reviewMenue2').style.display = "none";
    });

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

    document.getElementById('gewusst_komplett').addEventListener('click', function() {
        nextCardAndReset();
        document.getElementById('gewusst_optns').style.display = 'none';
    });

    document.getElementById('gewusst_groesstenteils').addEventListener('click', function() {
        nextCardAndReset();
        document.getElementById('gewusst_optns').style.display = 'none';
    });

    document.getElementById('gewusst_teilweise').addEventListener('click', function() {
        nextCardAndReset();
        document.getElementById('gewusst_optns').style.display = 'none';
    });
    
    document.getElementById('alles_vergessen').addEventListener('click', function() {
        nextCardAndReset();
        document.getElementById('vergessen_optns').style.display = 'none';
    });

    document.getElementById('bereich_vergessen').addEventListener('click', function() {
        const currentCard = container.querySelector('.flashcard.active');
        if (!currentCard) return;

        if (currentCard.querySelector('.romaji').style.display === "inline") {
            currentCard.querySelector('.romaji').style.display = "none";
            currentCard.querySelector('.romaji_btn').style.display = "inline";
            document.getElementById('reviewMenue').style.display = "none";
            document.getElementById('reviewMenue2').style.display = "block";
        } else if (currentCard.querySelector('.kana').style.display === "inline") {
            currentCard.querySelector('.kana').style.display = "none";
            currentCard.querySelector('.kana_btn').style.display = "inline";
            document.getElementById('reviewMenue').style.display = "none";
            document.getElementById('reviewMenue2').style.display = "none";
        }
        document.getElementById('vergessen_optns').style.display = 'none';
        saveProgress();
    });

    // INITIALISIERUNG
    createFlashcards();
    applySavedProgress();
});