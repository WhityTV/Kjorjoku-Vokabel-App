document.addEventListener('DOMContentLoaded', function() {
    // --- FLASHCARD LOGIK ---
    const cards = [
        {
            id: 1,
            german: "Vogel",
            kanji: "鳥",
            kana: "とり",
            romaji: "tori",
            interval: 0,
            repetitions: 0,
            easeFactor: 2.5,
            learning: true,
            nextReview: Date.now()
        },
        {
            id: 2,
            german: "Schatten",
            kanji: "影",
            kana: "かげ",
            romaji: "kage",
            interval: 0,
            repetitions: 0,
            easeFactor: 2.5,
            learning: true,
            nextReview: Date.now()
        }
    ];

    const container = document.getElementById('flashcardContainer');
    let currentCardIndex = parseInt(localStorage.getItem('currentCardIndex')) || 0;
    let currentCardProgress = JSON.parse(localStorage.getItem('currentCardProgress')) || {};

    // --- FUNKTIONEN (in korrekter Reihenfolge definiert) ---
    function getNextDueCard() {
        const now = Date.now();
        const dueCards = cards.filter(c => c.nextReview <= now);
        if (dueCards.length === 0) {
            alert("🎉 Keine fälligen Karten mehr!");
            return null;
        }
        return dueCards[0];
    }

    function nextCardAndReset() {
        const currentCard = container.querySelector('.flashcard.active');
        if (currentCard) currentCard.classList.remove('active');

        const nextCard = getNextDueCard();
        if (!nextCard) return;

        currentCardIndex = cards.findIndex(c => c.id === nextCard.id);
        const nextCardDiv = container.children[currentCardIndex];
        nextCardDiv.classList.add('active');
        resetCard(nextCardDiv);

        document.getElementById('reviewMenue').style.display = "none";
        document.getElementById('reviewMenue2').style.display = "none";
        saveProgress();
    }

    function updateCard(cardObj, grade) {
        if (grade === 0) {
            cardObj.repetitions = 0;
            cardObj.interval = 1;
            cardObj.learning = true;
        } else {
            if (cardObj.learning) {
                cardObj.repetitions++;
                if (cardObj.repetitions >= 2) {
                    cardObj.learning = false;
                    cardObj.interval = 1;
                } else {
                    cardObj.interval = grade === 1 ? 5 : 10;
                }
            } else {
                cardObj.repetitions++;
                cardObj.easeFactor = Math.max(1.3, cardObj.easeFactor + (grade - 1) * 0.1);
                cardObj.interval = Math.round(cardObj.interval * cardObj.easeFactor);
            }
        }

        let millis = cardObj.learning
            ? cardObj.interval * 60 * 1000
            : cardObj.interval * 24 * 60 * 60 * 1000;
        cardObj.nextReview = Date.now() + millis;
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
        card.querySelectorAll('.kanji, .kana, .romaji').forEach(el => el.style.display = 'none');
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
            cardDiv.querySelector('.card-german').textContent = card.german;
            cardDiv.querySelector('.kanji').textContent = card.kanji;
            cardDiv.querySelector('.kana').textContent = card.kana;
            cardDiv.querySelector('.romaji').textContent = card.romaji;
            if (index === currentCardIndex) cardDiv.classList.add('active');
            container.appendChild(clone);
        });
    }

    function setupReviewButtons() {
        const gewusstButtons = [
            ['gewusst_btn', 3],
            ['gewusst_komplett', 3],
            ['gewusst_groesstenteils', 2],
            ['gewusst_teilweise', 1]
        ];
        const vergessenButtons = [
            ['vergessen_btn', 0],
            ['alles_vergessen', 0],
            ['bereich_vergessen', 0]
        ];

        gewusstButtons.forEach(([id, grade]) => {
            const btn = document.getElementById(id);
            if (!btn) return;
            btn.addEventListener('click', function() {
                const cardObj = cards[currentCardIndex];
                updateCard(cardObj, grade);
                if (id.includes('komplett') || id.includes('groesstenteils') || id.includes('teilweise')) {
                    nextCardAndReset();
                    document.getElementById('gewusst_optns').style.display = 'none';
                } else {
                    document.getElementById('gewusst_optns').style.display = 'block';
                    document.getElementById('reviewMenue').style.display = "none";
                    document.getElementById('reviewMenue2').style.display = "none";
                }
            });
        });

        vergessenButtons.forEach(([id, grade]) => {
            const btn = document.getElementById(id);
            if (!btn) return;
            btn.addEventListener('click', function() {
                const cardObj = cards[currentCardIndex];
                updateCard(cardObj, grade);
                if (id.includes('alles') || id.includes('bereich')) {
                    if (id.includes('bereich')) {
                        const currentCard = container.querySelector('.flashcard.active');
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
                    } else {
                        nextCardAndReset();
                    }
                    document.getElementById('vergessen_optns').style.display = 'none';
                } else {
                    document.getElementById('vergessen_optns').style.display = 'block';
                    document.getElementById('reviewMenue').style.display = "none";
                    document.getElementById('reviewMenue2').style.display = "none";
                }
            });
        });
    }

    // --- INITIALISIERUNG ---
    // Das gesamte Initialisierungs-Code wird nach den Funktionen ausgeführt
    // So sind alle Funktionen bereits deklariert
    
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
    createFlashcards();
    applySavedProgress();
    setupReviewButtons();
    
    // --- BUTTON EVENT-LISTENER ---
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
});