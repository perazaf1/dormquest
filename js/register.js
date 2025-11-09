// register.js - Gestion du formulaire d'inscription

document.addEventListener("DOMContentLoaded", function () {
  // === GESTION DU CHANGEMENT DE RÔLE ===
  const roleInputs = document.querySelectorAll('input[name="role"]');
  const etudiantFields = document.getElementById("etudiant-fields");
  const loueurFields = document.getElementById("loueur-fields");

  function toggleRoleFields() {
    const selectedRole = document.querySelector(
      'input[name="role"]:checked'
    ).value;

    if (selectedRole === "etudiant") {
      etudiantFields.style.display = "block";
      loueurFields.style.display = "none";

      // Rendre les champs étudiant obligatoires
      document
        .getElementById("ville_recherche")
        .setAttribute("required", "required");
      document.getElementById("budget").setAttribute("required", "required");

      // Retirer l'obligation des champs loueur
      document.getElementById("type_loueur").removeAttribute("required");
      document.getElementById("telephone").removeAttribute("required");
    } else if (selectedRole === "loueur") {
      etudiantFields.style.display = "none";
      loueurFields.style.display = "block";

      // Retirer l'obligation des champs étudiant
      document.getElementById("ville_recherche").removeAttribute("required");
      document.getElementById("budget").removeAttribute("required");

      // Rendre les champs loueur obligatoires
      document
        .getElementById("type_loueur")
        .setAttribute("required", "required");
      document.getElementById("telephone").setAttribute("required", "required");
    }

    // Animation d'apparition
    const visibleSection =
      selectedRole === "etudiant" ? etudiantFields : loueurFields;
    visibleSection.style.opacity = "0";
    setTimeout(() => {
      visibleSection.style.transition = "opacity 0.3s ease";
      visibleSection.style.opacity = "1";
    }, 10);
  }

  // Écouter les changements de rôle
  roleInputs.forEach((input) => {
    input.addEventListener("change", toggleRoleFields);
  });

  // Initialiser au chargement
  toggleRoleFields();

  // === VALIDATION DU MOT DE PASSE EN TEMPS RÉEL ===
  const password = document.getElementById("password");
  const passwordConfirm = document.getElementById("password_confirm");

  function validatePassword() {
    if (passwordConfirm.value === "") return;

    if (password.value !== passwordConfirm.value) {
      passwordConfirm.setCustomValidity(
        "Les mots de passe ne correspondent pas"
      );
      passwordConfirm.style.borderColor = "#dc2626";
    } else {
      passwordConfirm.setCustomValidity("");
      passwordConfirm.style.borderColor = "#10b981";
    }
  }

  password.addEventListener("input", validatePassword);
  passwordConfirm.addEventListener("input", validatePassword);

  // === VALIDATION DE L'EMAIL ===
  const emailInput = document.getElementById("email");

  emailInput.addEventListener("blur", function () {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (this.value && !emailRegex.test(this.value)) {
      this.style.borderColor = "#dc2626";
    } else if (this.value) {
      this.style.borderColor = "#10b981";
    }
  });

  // === FORMATAGE DU TÉLÉPHONE ===
  const telephoneInput = document.getElementById("telephone");

  if (telephoneInput) {
    telephoneInput.addEventListener("input", function (e) {
      // Supprimer tout sauf les chiffres
      let value = this.value.replace(/\D/g, "");

      // Limiter à 10 chiffres
      if (value.length > 10) {
        value = value.substring(0, 10);
      }

      this.value = value;

      // Validation visuelle
      if (value.length === 10) {
        this.style.borderColor = "#10b981";
      } else if (value.length > 0) {
        this.style.borderColor = "#dc2626";
      }
    });
  }

  // === PREVIEW DE LA PHOTO ===
  const photoInput = document.getElementById("photo");

  if (photoInput) {
    photoInput.addEventListener("change", function (e) {
      const file = e.target.files[0];

      if (file) {
        // Vérifier la taille (2MB max)
        const maxSize = 2 * 1024 * 1024;

        if (file.size > maxSize) {
          alert("La photo ne doit pas dépasser 2MB");
          this.value = "";
          return;
        }

        // Vérifier le type
        const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];

        if (!allowedTypes.includes(file.type)) {
          alert("Format non autorisé. Utilisez JPG, JPEG ou PNG");
          this.value = "";
          return;
        }

        // Optionnel : Créer un aperçu
        const reader = new FileReader();
        reader.onload = function (event) {
          // Créer un élément d'aperçu si il n'existe pas
          let preview = document.getElementById("photo-preview");

          if (!preview) {
            preview = document.createElement("div");
            preview.id = "photo-preview";
            preview.style.marginTop = "1rem";
            preview.innerHTML =
              '<img style="max-width: 150px; border-radius: 8px; border: 2px solid var(--color-primary);">';
            photoInput.parentElement.appendChild(preview);
          }

          preview.querySelector("img").src = event.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // === VALIDATION DU BUDGET ===
  const budgetInput = document.getElementById("budget");

  if (budgetInput) {
    budgetInput.addEventListener("input", function () {
      const value = parseFloat(this.value);

      if (value > 0) {
        this.style.borderColor = "#10b981";
      } else if (this.value) {
        this.style.borderColor = "#dc2626";
      }
    });
  }

  // === SOUMISSION DU FORMULAIRE ===
  const form = document.querySelector(".form");

  form.addEventListener("submit", function (e) {
    // Vérification finale avant soumission
    const role = document.querySelector('input[name="role"]:checked').value;

    if (role === "etudiant") {
      const ville = document.getElementById("ville_recherche").value;
      const budget = document.getElementById("budget").value;

      if (!ville || !budget) {
        e.preventDefault();
        alert(
          "Veuillez remplir tous les champs obligatoires pour les étudiants"
        );
        return;
      }
    } else if (role === "loueur") {
      const type = document.getElementById("type_loueur").value;
      const tel = document.getElementById("telephone").value;

      if (!type || !tel) {
        e.preventDefault();
        alert("Veuillez remplir tous les champs obligatoires pour les loueurs");
        return;
      }
    }

    // Désactiver le bouton pour éviter les double-soumissions
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = "Inscription en cours...";
    submitBtn.style.opacity = "0.6";
  });

  // === AUTO-SCROLL VERS LES ERREURS ===
  const alertError = document.querySelector(".alert--error");

  if (alertError) {
    alertError.scrollIntoView({ behavior: "smooth", block: "center" });
  }
});

// === FONCTION UTILITAIRE : Capitaliser la première lettre ===
function capitalizeFirstLetter(input) {
  input.addEventListener("blur", function () {
    if (this.value) {
      this.value =
        this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();
    }
  });
}

// Appliquer aux champs prénom et nom
document.addEventListener("DOMContentLoaded", function () {
  const prenomInput = document.getElementById("prenom");
  const nomInput = document.getElementById("nom");

  if (prenomInput) capitalizeFirstLetter(prenomInput);
  if (nomInput) capitalizeFirstLetter(nomInput);
});
