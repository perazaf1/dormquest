// create-annonce.js - JavaScript pour la création d'annonce

document.addEventListener('DOMContentLoaded', function() {
    
    // === COMPTEUR DE CARACTÈRES POUR LA DESCRIPTION ===
    const descriptionInput = document.getElementById('description');
    const charCount = document.getElementById('char-count');
    
    if (descriptionInput && charCount) {
        // Initialiser le compteur
        charCount.textContent = descriptionInput.value.length;
        
        // Mettre à jour en temps réel
        descriptionInput.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            // Changer la couleur selon la longueur
            if (length < 50) {
                charCount.style.color = '#dc2626'; // Rouge
            } else if (length < 100) {
                charCount.style.color = '#f59e0b'; // Orange
            } else {
                charCount.style.color = '#10b981'; // Vert
            }
        });
    }
    
    // === VALIDATION DU TITRE (MIN 10 CARACTÈRES) ===
    const titreInput = document.getElementById('titre');
    
    if (titreInput) {
        titreInput.addEventListener('input', function() {
            const length = this.value.length;
            
            if (length > 0 && length < 10) {
                this.style.borderColor = '#dc2626';
            } else if (length >= 10) {
                this.style.borderColor = '#10b981';
            } else {
                this.style.borderColor = '#e5e7eb';
            }
        });
    }
    
    // === VALIDATION DU CODE POSTAL ===
    const codePostalInput = document.getElementById('code_postal');
    
    if (codePostalInput) {
        codePostalInput.addEventListener('input', function(e) {
            // Supprimer tout sauf les chiffres
            let value = this.value.replace(/\D/g, '');
            
            // Limiter à 5 chiffres
            if (value.length > 5) {
                value = value.substring(0, 5);
            }
            
            this.value = value;
            
            // Validation visuelle
            if (value.length === 5) {
                this.style.borderColor = '#10b981';
            } else if (value.length > 0) {
                this.style.borderColor = '#dc2626';
            } else {
                this.style.borderColor = '#e5e7eb';
            }
        });
    }
    
    // === FORMATAGE DU TÉLÉPHONE ===
    const telephoneInput = document.getElementById('contact_telephone');
    
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
    
    // === VALIDATION DU PRIX ET SUPERFICIE ===
    const prixInput = document.getElementById('prix_mensuel');
    const superficieInput = document.getElementById('superficie');
    
    function validateNumberInput(input) {
        input.addEventListener('input', function() {
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
    
    if (prixInput) validateNumberInput(prixInput);
    if (superficieInput) validateNumberInput(superficieInput);
    
    // === AUTO-CAPITALISATION DE LA VILLE ===
    const villeInput = document.getElementById('ville');
    
    if (villeInput) {
        villeInput.addEventListener('blur', function() {
            if (this.value) {
                // Mettre en majuscule la première lettre de chaque mot
                this.value = this.value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
        });
    }
    
    // === SUGGESTION DE TITRE SELON LE TYPE ===
    const typeLogementSelect = document.getElementById('type_logement');
    
    if (typeLogementSelect && titreInput) {
        typeLogementSelect.addEventListener('change', function() {
            // Ne suggérer que si le titre est vide
            if (!titreInput.value) {
                const suggestions = {
                    'studio': 'Studio lumineux',
                    'colocation': 'Colocation sympa',
                    'residence_etudiante': 'Studio en résidence étudiante',
                    'chambre_habitant': 'Chambre meublée chez l\'habitant'
                };
                
                const suggestion = suggestions[this.value];
                if (suggestion) {
                    titreInput.placeholder = 'Ex: ' + suggestion;
                }
            }
        });
    }
    
    // === CALCUL AUTOMATIQUE DU PRIX PAR M² ===
    if (prixInput && superficieInput) {
        function calculatePrixParM2() {
            const prix = parseFloat(prixInput.value);
            const superficie = parseFloat(superficieInput.value);
            
            if (prix > 0 && superficie > 0) {
                const prixParM2 = (prix / superficie).toFixed(2);
                
                // Créer ou mettre à jour l'info
                let infoDiv = document.getElementById('prix-par-m2-info');
                
                if (!infoDiv) {
                    infoDiv = document.createElement('small');
                    infoDiv.id = 'prix-par-m2-info';
                    infoDiv.className = 'form-hint';
                    infoDiv.style.color = 'var(--color-primary)';
                    infoDiv.style.fontWeight = '600';
                    prixInput.parentElement.appendChild(infoDiv);
                }
                
                infoDiv.textContent = `💡 Prix au m² : ${prixParM2} €/m²`;
            }
        }
        
        prixInput.addEventListener('input', calculatePrixParM2);
        superficieInput.addEventListener('input', calculatePrixParM2);
    }
    
    // === VALIDATION FINALE AVANT SOUMISSION ===
    const form = document.getElementById('form-annonce');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const titre = document.getElementById('titre').value;
            const description = document.getElementById('description').value;
            const prix = parseFloat(document.getElementById('prix_mensuel').value);
            const superficie = parseFloat(document.getElementById('superficie').value);
            
            // Vérifications supplémentaires
            if (titre.length < 10) {
                e.preventDefault();
                alert('Le titre doit contenir au moins 10 caractères');
                document.getElementById('titre').focus();
                return false;
            }
            
            if (description.length < 50) {
                e.preventDefault();
                alert('La description doit contenir au moins 50 caractères');
                document.getElementById('description').focus();
                return false;
            }
            
            if (prix <= 0) {
                e.preventDefault();
                alert('Le prix mensuel doit être supérieur à 0');
                document.getElementById('prix_mensuel').focus();
                return false;
            }
            
            if (superficie <= 0) {
                e.preventDefault();
                alert('La superficie doit être supérieure à 0');
                document.getElementById('superficie').focus();
                return false;
            }
            
            // Désactiver le bouton pour éviter les doubles soumissions
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Publication en cours...';
            submitBtn.style.opacity = '0.6';
            
            return true;
        });
    }
    
    // === ANIMATION D'ENTRÉE DES SECTIONS ===
    const sections = document.querySelectorAll('.form-section');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    sections.forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(30px)';
        section.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(section);
    });
    
    // === SAUVEGARDE AUTOMATIQUE EN BROUILLON (localStorage) ===
    const formInputs = form.querySelectorAll('input, textarea, select');
    
    // Charger le brouillon au chargement
    function loadDraft() {
        const draft = localStorage.getItem('annonce_draft');
        
        if (draft) {
            const draftData = JSON.parse(draft);
            const loadDraftBtn = document.createElement('div');
            loadDraftBtn.className = 'alert alert--info';
            loadDraftBtn.innerHTML = `
                <strong>💾 Brouillon détecté</strong>
                <p>Un brouillon d'annonce a été trouvé. Voulez-vous le récupérer ?</p>
                <button id="load-draft-btn" class="form-btn form-btn--primary" style="margin-top: 1rem;">
                    Récupérer le brouillon
                </button>
                <button id="delete-draft-btn" class="form-btn form-btn--secondary" style="margin-top: 1rem; margin-left: 0.5rem;">
                    Ignorer
                </button>
            `;
            
            const formHeader = document.querySelector('.form-header');
            formHeader.after(loadDraftBtn);
            
            document.getElementById('load-draft-btn').addEventListener('click', function() {
                Object.keys(draftData).forEach(key => {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = draftData[key];
                        } else {
                            input.value = draftData[key];
                        }
                    }
                });
                
                loadDraftBtn.remove();
                
                // Mettre à jour le compteur de caractères
                if (descriptionInput && charCount) {
                    charCount.textContent = descriptionInput.value.length;
                }
            });
            
            document.getElementById('delete-draft-btn').addEventListener('click', function() {
                localStorage.removeItem('annonce_draft');
                loadDraftBtn.remove();
            });
        }
    }
    
    loadDraft();
    
    // Sauvegarder automatiquement toutes les 30 secondes
    setInterval(function() {
        const draftData = {};
        
        formInputs.forEach(input => {
            if (input.name) {
                if (input.type === 'checkbox') {
                    draftData[input.name] = input.checked;
                } else {
                    draftData[input.name] = input.value;
                }
            }
        });
        
        localStorage.setItem('annonce_draft', JSON.stringify(draftData));
        
        // Afficher un indicateur discret
        let indicator = document.getElementById('draft-indicator');
        if (!indicator) {
            indicator = document.createElement('small');
            indicator.id = 'draft-indicator';
            indicator.style.cssText = `
                position: fixed;
                bottom: 2rem;
                right: 2rem;
                background-color: #10b981;
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 8px;
                font-size: 0.875rem;
                z-index: 1000;
                opacity: 0;
                transition: opacity 0.3s ease;
            `;
            indicator.textContent = '💾 Brouillon sauvegardé';
            document.body.appendChild(indicator);
        }
        
        indicator.style.opacity = '1';
        setTimeout(() => {
            indicator.style.opacity = '0';
        }, 2000);
    }, 30000); // 30 secondes
    
    // Supprimer le brouillon après soumission réussie
    form.addEventListener('submit', function() {
        localStorage.removeItem('annonce_draft');
    });
    
    // === AUTO-SCROLL VERS LES ERREURS ===
    const alertError = document.querySelector('.alert--error');
    
    if (alertError) {
        alertError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    console.log('📝 Formulaire de création d\'annonce initialisé !');
});