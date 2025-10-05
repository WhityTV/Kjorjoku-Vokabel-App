document.addEventListener('DOMContentLoaded', () => {
    
    // Allgemeine Handler-Funktion, um die Logik für beide Toggles zu kapseln
    function setupToggle(toggleElement, inputElement, eyeIconElement) {
        
        function toggleState() {
            const isPassword = inputElement.type === 'password';
            inputElement.type = isPassword ? 'text' : 'password';
            
            // Bild und ARIA-Label umschalten
            eyeIconElement.src = isPassword ? 'icons/ausblenden.png' : 'icons/auge.png';
            toggleElement.setAttribute('aria-label', isPassword ? 'Passwort ausblenden' : 'Passwort anzeigen');
        }

        // 1. Maus-Klick-Handler
        toggleElement.addEventListener('click', toggleState);
        
        // 2. Tastatur-Handler (für Enter und Space, dank tabindex="0")
        toggleElement.addEventListener('keydown', (event) => {
            // Prüfen, ob Enter oder Space gedrückt wurde
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault(); // Verhindert Standardaktionen wie Scrollen
                toggleState();
            }
        });
    }

    // 1. Hauptpasswort-Feld
    const toggleRegister = document.getElementById('toggleRegisterPassword');
    const registerInput = document.getElementById('register_password');
    if (toggleRegister && registerInput) {
        setupToggle(toggleRegister, registerInput, toggleRegister.querySelector('img'));
    }
    
    // 2. "Passwort bestätigen"-Feld
    const toggleConfirm = document.getElementById('toggleConfirmPassword');
    const confirmInput = document.getElementById('register_confirm_password');
    if (toggleConfirm && confirmInput) {
        setupToggle(toggleConfirm, confirmInput, toggleConfirm.querySelector('img'));
    }
});