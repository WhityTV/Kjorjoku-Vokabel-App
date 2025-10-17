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