document.addEventListener('DOMContentLoaded', () => {
    // Hole die Elemente über ihre IDs
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('login_password');
    
    if (togglePassword && passwordInput) {
        const eyeIcon = togglePassword.querySelector('img');

        // Allgemeine Funktion zum Umschalten von Zustand und ARIA-Label
        function toggleState() {
            const isPassword = passwordInput.type === 'password';
            
            // 1. Input-Typ umschalten
            passwordInput.type = isPassword ? 'text' : 'password';
            
            // 2. Bild und ARIA-Label umschalten
            eyeIcon.src = isPassword ? 'icons/ausblenden.png' : 'icons/auge.png';
            
            // Setze das aria-label auf den klickbaren Button (togglePassword)
            togglePassword.setAttribute('aria-label', isPassword ? 'Passwort ausblenden' : 'Passwort anzeigen');
        }

        // 1. Maus-Klick-Handler
        togglePassword.addEventListener('click', toggleState);
        
        // 2. Tastatur-Handler (für Enter und Space, wichtig wegen role="button" und tabindex="0")
        togglePassword.addEventListener('keydown', (event) => {
            // Prüfen, ob Enter (Code 13) oder Space (Code 32) gedrückt wurde
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault(); // Verhindert standardmäßiges Scrollen bei Space
                toggleState();
            }
        });
    }
});