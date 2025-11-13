// profil.js - JavaScript pour la page de profil

document.addEventListener('DOMContentLoaded', function() {
    
    // === PREVIEW DE LA PHOTO DE PROFIL ===
    const photoInput = document.getElementById('photo');
    const previewPhoto = document.getElementById('preview-photo');
    
    if (photoInput && previewPhoto) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Vérifier la taille (2MB max)
                const maxSize = 2 * 1024 * 1024;
                
                if (file.size > maxSize) {
                    alert('La photo ne doit pas dépasser 2MB');
                    this.value = '';
                    return;
                }
                
                // Vérifier le type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Format non autorisé. Utilisez JPG, JPEG ou PNG');
                    this.value = '';
                    return;
                }
                
                // Créer un aperçu
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewPhoto.src = event.target.result;
                    
                    // Animation
                    previewPhoto.style.opacity = '0';
                    setTimeout(() => {
                        previewPhoto.style.transition = 'opacity 0.3s ease';
                        previewPhoto.style.opacity = '1';
                    }, 50);
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // === VALIDATION DES MOTS DE PASSE ===
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    if (newPassword && confirmPassword) {
        function validatePasswords() {
            if (confirmPassword.value === '') return;
            
            if (newPassword.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Les mots de passe ne correspondent pas');
                confirmPassword.style.borderColor = '#dc2626';
            } else {
                confirmPassword.setCustomValidity('');
                confirmPassword.style.borderColor = '#10b981';
            }
        }
        
        newPassword.addEventListener('input', validatePasswords);
        confirmPassword.addEventListener('input', validatePasswords);
    }
    
    // === FORMATAGE DU TÉLÉPHONE ===
    const telephoneInput = document.getElementById('telephone');
    
    if (telephoneInput) {
        telephoneInput.addEventListener('input', function(e) {
            // Supprimer tout sauf les chiffres
            let value = this.value.replace(/\D/g, '');
            
            // Limiter à 10 chiffres
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            
            this.value = value;
            
            // Validation visuelle
            if (value.length === 10) {
                this.style.borderColor = '#10b981';
            } else if (value.length > 0) {
                this.style.borderColor = '#dc2626';
            } else {
                this.style.borderColor = '#e5e7eb';
            }
        });
    }
    
    // === VALIDATION DU BUDGET ===
    const budgetInput = document.getElementById('budget');
    
    if (budgetInput) {
        budgetInput.addEventListener('input', function() {
            const value = parseFloat(this.value);
            
            if (value > 0) {
                this.style.borderColor = '#10b981';
            } else if (this.value) {
                this.style.borderColor = '#dc2626';
            } else {
                this.style.borderColor = '#e5e7eb';
            }
        });
    }
    
    // === SUPPRESSION DE COMPTE ===
    const deleteAccountBtn = document.getElementById('delete-account-btn');
    
    if (deleteAccountBtn) {
        deleteAccountBtn.addEventListener('click', function() {
            const confirmed = confirm(
                '⚠️ ATTENTION - SUPPRESSION DE COMPTE\n\n' +
                'Êtes-vous ABSOLUMENT SÛR de vouloir supprimer votre compte ?\n\n' +
                '❌ Toutes vos données seront définitivement supprimées :\n' +
                '• Vos informations personnelles\n' +
                '• Vos favoris et candidatures (si étudiant)\n' +
                '• Vos annonces et candidatures reçues (si loueur)\n\n' +
                '⚠️ CETTE ACTION EST IRRÉVERSIBLE !\n\n' +
                'Tapez "SUPPRIMER" pour confirmer :'
            );
            
            if (confirmed) {
                const verification = prompt('Tapez "SUPPRIMER" en majuscules pour confirmer :');
                
                if (verification === 'SUPPRIMER') {
                    // Rediriger vers la page de suppression
                    if (confirm('Dernière confirmation : êtes-vous vraiment sûr ?')) {
                        window.location.href = 'delete-account.php';
                    }
                } else {
                    alert('Suppression annulée. Texte de confirmation incorrect.');
                }
            }
        });
    }
    
    // === VALIDATION DU FORMULAIRE ===
    const profilForm = document.querySelector('.profil-form');
    
    if (profilForm) {
        profilForm.addEventListener('submit', function(e) {
            // Vérification finale des mots de passe
            const newPass = newPassword ? newPassword.value : '';
            const confirmPass = confirmPassword ? confirmPassword.value : '';
            
            if (newPass && newPass !== confirmPass) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas');
                confirmPassword.focus();
                return false;
            }
            
            if (newPass && newPass.length < 8) {
                e.preventDefault();
                alert('Le nouveau mot de passe doit contenir au moins 8 caractères');
                newPassword.focus();
                return false;
            }
            
            // Désactiver le bouton pour éviter les doubles soumissions
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = '💾 Enregistrement en cours...';
            submitBtn.style.opacity = '0.6';
            
            return true;
        });
    }
    
    // === AUTO-MASQUAGE DES ALERTES ===
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // === COMPTEUR ANIMÉ POUR LES STATISTIQUES ===
    function animateCounter(element, target, duration = 1000) {
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }
    
    // Animer les statistiques
    const statValues = document.querySelectorAll('.stats-card__content strong');
    
    statValues.forEach(stat => {
        const target = parseInt(stat.textContent);
        
        if (!isNaN(target) && target > 0) {
            stat.textContent = '0';
            
            setTimeout(() => {
                animateCounter(stat, target, 1500);
            }, 300);
        }
    });
    
    // === CONFIRMATION AVANT QUITTER AVEC MODIFICATIONS NON SAUVEGARDÉES ===
    let formModified = false;
    const formInputs = profilForm.querySelectorAll('input, select, textarea');
    
    formInputs.forEach(input => {
        input.addEventListener('change', function() {
            formModified = true;
        });
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (formModified) {
            e.preventDefault();
            e.returnValue = '';
            return 'Vous avez des modifications non sauvegardées. Êtes-vous sûr de vouloir quitter ?';
        }
    });
    
    // Réinitialiser le flag lors de la soumission
    if (profilForm) {
        profilForm.addEventListener('submit', function() {
            formModified = false;
        });
    }
    
    // === TOOLTIP SUR LA PHOTO ===
    if (previewPhoto) {
        previewPhoto.style.cursor = 'pointer';
        previewPhoto.title = 'Cliquez sur "Parcourir" ci-dessous pour changer la photo';
        
        previewPhoto.addEventListener('click', function() {
            if (photoInput) {
                photoInput.click();
            }
        });
    }
    
    // === VÉRIFICATION EMAIL EN TEMPS RÉEL ===
    const emailInput = document.getElementById('email');
    
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (this.value && !emailRegex.test(this.value)) {
                this.style.borderColor = '#dc2626';
            } else if (this.value) {
                this.style.borderColor = '#10b981';
            } else {
                this.style.borderColor = '#e5e7eb';
            }
        });
    }
    
    // === AUTO-CAPITALISATION ===
    const prenomInput = document.getElementById('prenom');
    const nomInput = document.getElementById('nom');
    const villeInput = document.getElementById('ville_recherche');
    
    function capitalizeFirstLetter(input) {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = this.value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
        });
    }
    
    if (prenomInput) capitalizeFirstLetter(prenomInput);
    if (nomInput) capitalizeFirstLetter(nomInput);
    if (villeInput) capitalizeFirstLetter(villeInput);
    
    // === ANIMATIONS D'ENTRÉE ===
    const sections = document.querySelectorAll('.form-section');
    
    sections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            section.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, 100 * (index + 1));
    });
    
    console.log('⚙️ Page de profil initialisée !');
});