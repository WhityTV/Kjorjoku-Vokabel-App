    document.addEventListener("DOMContentLoaded", () => {
        const icon = document.querySelector('.kyoryoku-icon');
        const menu = document.getElementById('kyoryokuMenu');

        icon.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        });

        document.addEventListener('click', () => {
            menu.style.display = 'none';
            // Untermenü auch schließen
            const switchContainer = document.querySelector('.switch_acc_container');
            if(switchContainer) switchContainer.classList.remove('active');
        });

        menu.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Untermenü per Klick auf Button öffnen
        const switchBtn = document.querySelector('.switch_acc_btn');
        const switchContainer = document.querySelector('.switch_acc_container');

        if(switchBtn && switchContainer){
            switchBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                switchContainer.classList.toggle('active');
            });
        }
    });