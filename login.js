const registerFormElement = document.getElementById("registerFormElement");

registerFormElement.addEventListener("submit", function (e) {
  let valid = true;

  // Felder zurücksetzen
  const inputs = registerFormElement.querySelectorAll(".login_btn2");
  inputs.forEach(input => {
    input.classList.remove("error");
    input.nextElementSibling?.nextElementSibling?.classList?.remove("active");
  });

  // Username prüfen
  const username = document.getElementById("register_username");
  if (username.value.trim().length < 3) {
    showError(username, "Benutzername muss mindestens 3 Zeichen haben.");
    valid = false;
  }

  // Passwort prüfen
  const pw = document.getElementById("register_password");
  if (pw.value.length < 6) {
    showError(pw, "Passwort muss mindestens 6 Zeichen haben.");
    valid = false;
  }

  // Passwort bestätigen
  const confirm = document.getElementById("confirm_password");
  if (confirm.value !== pw.value) {
    showError(confirm, "Passwörter stimmen nicht überein.");
    valid = false;
  }

  // E-Mail prüfen
  const email = document.getElementById("email");
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!regex.test(email.value)) {
    showError(email, "Ungültige E-Mail-Adresse.");
    valid = false;
  }

  if (!valid) {
    e.preventDefault(); // Formular wird nicht abgeschickt
  }
});

function showError(input, message) {
  input.classList.add("error");
  const errorMessage = input.parentElement.querySelector(".error-message");
  if (errorMessage) {
    errorMessage.textContent = message;
    errorMessage.classList.add("active");
  }
}
