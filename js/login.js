// login.js - Gestion de la page de connexion

document.addEventListener("DOMContentLoaded", function () {
  // === TOGGLE MOT DE PASSE (AFFICHER/MASQUER) ===
  const togglePasswordBtn = document.getElementById("toggle-password");
  const passwordInput = document.getElementById("password");

  if (togglePasswordBtn && passwordInput) {
    togglePasswordBtn.addEventListener("click", function () {
      // Basculer le type du champ
      const type = passwordInput.type === "password" ? "text" : "password";
      passwordInput.type = type;

      // Changer l'icône
      this.textContent = type === "password" ? "👁️" : "🙈";
    });
  }

  // === VALIDATION EN TEMPS RÉEL ===
  const emailInput = document.getElementById("email");

  if (emailInput) {
    emailInput.addEventListener("blur", function () {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (this.value && !emailRegex.test(this.value)) {
        this.style.borderColor = "#dc2626";
      } else if (this.value) {
        this.style.borderColor = "#10b981";
      } else {
        this.style.borderColor = "#e5e7eb";
      }
    });

    // Réinitialiser la couleur lors de la saisie
    emailInput.addEventListener("input", function () {
      this.style.borderColor = "#e5e7eb";
    });
  }

  // === VALIDATION DU FORMULAIRE ===
  const loginForm = document.querySelector(".form");

  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      const email = emailInput.value.trim();
      const password = passwordInput.value;

      // Validation côté client
      if (!email || !password) {
        e.preventDefault();
        alert("Veuillez remplir tous les champs");
        return false;
      }

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        e.preventDefault();
        alert("Veuillez entrer un email valide");
        emailInput.focus();
        return false;
      }

      // Désactiver le bouton pour éviter les doubles soumissions
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = "Connexion en cours...";
        submitBtn.style.opacity = "0.6";
      }

      return true;
    });
  }

  // === GESTION DU FOCUS ===
  const inputs = document.querySelectorAll(".form-input");

  inputs.forEach((input) => {
    // Ajouter une classe au focus
    input.addEventListener("focus", function () {
      this.parentElement.classList.add("form-input-group--focused");
    });

    input.addEventListener("blur", function () {
      this.parentElement.classList.remove("form-input-group--focused");
    });
  });

  // === ANIMATION D'ENTRÉE ===
  const formContainer = document.querySelector(".form-container");
  if (formContainer) {
    formContainer.style.opacity = "0";
    formContainer.style.transform = "translateY(20px)";

    setTimeout(() => {
      formContainer.style.transition = "opacity 0.5s ease, transform 0.5s ease";
      formContainer.style.opacity = "1";
      formContainer.style.transform = "translateY(0)";
    }, 100);
  }

  // === AUTO-FOCUS SUR LE PREMIER CHAMP EN ERREUR ===
  const alertError = document.querySelector(".alert--error");

  if (alertError) {
    // Scroll vers l'alerte
    alertError.scrollIntoView({ behavior: "smooth", block: "center" });

    // Focus sur l'email si erreur de connexion
    if (emailInput) {
      setTimeout(() => {
        emailInput.focus();
      }, 300);
    }
  }

  // === PERSISTANCE DU "SE SOUVENIR DE MOI" ===
  const rememberCheckbox = document.getElementById("remember_me");

  if (rememberCheckbox) {
    // Vérifier si l'utilisateur avait coché la case lors de la dernière visite
    const rememberMeChecked = localStorage.getItem("rememberMeChecked");

    if (rememberMeChecked === "true") {
      rememberCheckbox.checked = true;
    }

    // Sauvegarder la préférence
    rememberCheckbox.addEventListener("change", function () {
      localStorage.setItem("rememberMeChecked", this.checked);
    });
  }

  // === RACCOURCI CLAVIER (ENTER) ===
  document.addEventListener("keypress", function (e) {
    if (e.key === "Enter" && document.activeElement.tagName !== "BUTTON") {
      const submitBtn = loginForm.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.click();
      }
    }
  });

  // === AFFICHAGE TEMPORAIRE DU MESSAGE DE SUCCÈS ===
  const alertSuccess = document.querySelector(".alert--success");

  if (alertSuccess) {
    setTimeout(() => {
      alertSuccess.style.transition = "opacity 0.5s ease";
      alertSuccess.style.opacity = "0";

      setTimeout(() => {
        alertSuccess.remove();
      }, 500);
    }, 5000); // Disparaît après 5 secondes
  }
});

// === DÉTECTION DE CAPS LOCK ===
document.addEventListener("DOMContentLoaded", function () {
  const passwordInput = document.getElementById("password");

  if (passwordInput) {
    let capsLockWarning = null;

    passwordInput.addEventListener("keyup", function (e) {
      // Détecter si Caps Lock est activé
      if (e.getModifierState && e.getModifierState("CapsLock")) {
        // Afficher un avertissement si pas déjà affiché
        if (!capsLockWarning) {
          capsLockWarning = document.createElement("small");
          capsLockWarning.className = "form-hint form-hint--warning";
          capsLockWarning.style.color = "#f59e0b";
          capsLockWarning.textContent = "⚠️ La touche Caps Lock est activée";

          this.parentElement.parentElement.appendChild(capsLockWarning);
        }
      } else {
        // Supprimer l'avertissement si Caps Lock est désactivé
        if (capsLockWarning) {
          capsLockWarning.remove();
          capsLockWarning = null;
        }
      }
    });
  }
});
