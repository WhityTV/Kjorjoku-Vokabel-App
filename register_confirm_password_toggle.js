const toggleConfirm = document.getElementById('toggleConfirmPassword');
const confirmInput = document.getElementById('register_confirm_password');
const eyeIcon = toggleConfirm.querySelector('img');

toggleConfirm.addEventListener('click', () => {
    const isPassword = confirmInput.type === 'password';
    confirmInput.type = isPassword ? 'text' : 'password';
    eyeIcon.src = isPassword ? 'icons/ausblenden.png' : 'icons/auge.png';
});