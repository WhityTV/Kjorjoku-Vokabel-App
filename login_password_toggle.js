const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('login_password');
const eyeIcon = togglePassword.querySelector('img');

togglePassword.addEventListener('click', () => {
    const isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
    eyeIcon.src = isPassword ? 'icons/ausblenden.png' : 'icons/auge.png';
});