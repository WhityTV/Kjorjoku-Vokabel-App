document.querySelectorAll('.toggle-password').forEach(toggle => {
    const input = document.getElementById(toggle.dataset.target);
    const eyeIcon = toggle.querySelector('img');

    toggle.addEventListener('click', () => {
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        eyeIcon.src = isPassword ? 'icons/ausblenden.png' : 'icons/auge.png';
    });
});