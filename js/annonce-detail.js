// annonce-detail.js - JavaScript pour la page de détail d'annonce

document.addEventListener('DOMContentLoaded', function() {
    
    // === GALERIE DE PHOTOS ===
    const mainPhoto = document.getElementById('main-photo');
    const thumbs = document.querySelectorAll('.photo-gallery__thumb');
    
    thumbs.forEach(thumb => {
        thumb.addEventListener('click', function() {
            // Changer la photo principale
            const fullSrc = this.getAttribute('data-full');
            mainPhoto.src = fullSrc;
            
            // Mettre à jour les classes actives
            thumbs.forEach(t => t.classList.remove('photo-gallery__thumb--active'));
            this.classList.add('photo-gallery__thumb--active');
            
            // Animation de transition
            mainPhoto.style.opacity = '0';
            setTimeout(() => {
                mainPhoto.style.transition = 'opacity 0.3s ease';
                mainPhoto.style.opacity = '1';
            }, 50);
        });
    });
    
    // === BOUTON FAVORI ===
    const btnFavori = document.getElementById('btn-favori');
    
    if (btnFavori) {
        btnFavori.addEventListener('click', function() {
            const annonceId = this.getAttribute('data-annonce-id');
            const isActive = this.classList.contains('btn-favori--active');
            
            // Animation de clic
            this.style.transform = 'scale(1.1)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
            
            // Toggle l'état immédiatement pour le feedback UX
            this.classList.toggle('btn-favori--active');
            this.textContent = this.classList.contains('btn-favori--active') 
                ? '⭐ En favoris' 
                : '⭐ Ajouter aux favoris';
            
            // Envoi de la requête AJAX
            fetch('api/toggle-favori.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    annonce_id: annonceId,
                    action: isActive ? 'remove' : 'add'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // Annuler le toggle en cas d'erreur
                    this.classList.toggle('btn-favori--active');
                    this.textContent = this.classList.contains('btn-favori--active') 
                        ? '⭐ En favoris' 
                        : '⭐ Ajouter aux favoris';
                    alert('Erreur : ' + (data.message || 'Impossible de modifier les favoris'));
                } else {
                    // Afficher une notification de succès
                    showNotification(data.action === 'added' 
                        ? '✅ Ajouté aux favoris' 
                        : '❌ Retiré des favoris');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                // Annuler le toggle en cas d'erreur
                this.classList.toggle('btn-favori--active');
                this.textContent = this.classList.contains('btn-favori--active') 
                    ? '⭐ En favoris' 
                    : '⭐ Ajouter aux favoris';
            });
        });
    }
    
    // === VALIDATION DU FORMULAIRE DE CANDIDATURE ===
    const candidatureForm = document.querySelector('.candidature-form form');
    
    if (candidatureForm) {
        const textarea = candidatureForm.querySelector('textarea[name="message"]');
        
        // Compteur de caractères
        if (textarea) {
            const hint = candidatureForm.querySelector('.candidature-form__hint');
            const originalHint = hint.textContent;
            
            textarea.addEventListener('input', function() {
                const length = this.value.length;
                
                if (length < 20) {
                    hint.textContent = `${originalHint} - ${length}/20 caractères`;
                    hint.style.color = '#dc2626';
                } else {
                    hint.textContent = `${length} caractères`;
                    hint.style.color = '#10b981';
                }
            });
        }
        
        // Validation avant soumission
        candidatureForm.addEventListener('submit', function(e) {
            const message = textarea.value.trim();
            
            if (message.length < 20) {
                e.preventDefault();
                alert('Votre message doit contenir au moins 20 caractères.');
                textarea.focus();
                return false;
            }
            
            // Désactiver le bouton pour éviter les doubles soumissions
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.textContent = 'Envoi en cours...';
            btn.style.opacity = '0.6';
        });
    }
    
    // === FONCTION NOTIFICATION ===
    function showNotification(message) {
        // Créer la notification
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 2rem;
            background-color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            font-weight: 600;
            border-left: 4px solid var(--color-primary);
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        // Supprimer après 3 secondes
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Ajouter les animations CSS pour les notifications
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
    
    // === SCROLL SMOOTH VERS LES SECTIONS ===
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // === COPIER LA RÉFÉRENCE ===
    const refElement = document.querySelector('.info-box__item:last-child');
    
    if (refElement) {
        refElement.style.cursor = 'pointer';
        refElement.title = 'Cliquer pour copier la référence';
        
        refElement.addEventListener('click', function() {
            const ref = this.textContent.split(': ')[1];
            
            navigator.clipboard.writeText(ref).then(() => {
                const originalText = this.innerHTML;
                this.innerHTML = '<strong>Référence :</strong> ✅ Copiée !';
                this.style.color = '#10b981';
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.color = '';
                }, 2000);
            });
        });
    }
    
    // === PARTAGE DE L'ANNONCE ===
    function addShareButton() {
        const header = document.querySelector('.annonce-header__top');
        
        if (header && navigator.share) {
            const shareBtn = document.createElement('button');
            shareBtn.className = 'btn btn--share';
            shareBtn.innerHTML = '🔗 Partager';
            shareBtn.style.cssText = `
                background-color: white;
                color: var(--color-primary);
                border: 2px solid var(--color-primary);
            `;
            
            shareBtn.addEventListener('click', async function() {
                try {
                    await navigator.share({
                        title: document.title,
                        text: 'Découvrez cette annonce sur DormQuest !',
                        url: window.location.href
                    });
                } catch (error) {
                    // Fallback : copier l'URL
                    navigator.clipboard.writeText(window.location.href);
                    showNotification('🔗 Lien copié !');
                }
            });
            
            header.appendChild(shareBtn);
        }
    }
    
    addShareButton();
    
    // === ZOOM SUR LA PHOTO PRINCIPALE ===
    if (mainPhoto) {
        mainPhoto.style.cursor = 'zoom-in';
        
        mainPhoto.addEventListener('click', function() {
            // Créer un overlay pour le zoom
            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.9);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: zoom-out;
                animation: fadeIn 0.3s ease;
            `;
            
            const zoomedImg = document.createElement('img');
            zoomedImg.src = this.src;
            zoomedImg.style.cssText = `
                max-width: 90%;
                max-height: 90%;
                object-fit: contain;
                border-radius: 8px;
            `;
            
            overlay.appendChild(zoomedImg);
            document.body.appendChild(overlay);
            document.body.style.overflow = 'hidden';
            
            overlay.addEventListener('click', function() {
                this.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => {
                    this.remove();
                    document.body.style.overflow = '';
                }, 300);
            });
        });
    }
    
    // === AUTO-SCROLL VERS LES ERREURS ===
    const alertError = document.querySelector('.alert--error');
    
    if (alertError) {
        alertError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // === AUTO-MASQUAGE DES ALERTES DE SUCCÈS ===
    const alertSuccess = document.querySelector('.alert--success');
    
    if (alertSuccess) {
        setTimeout(() => {
            alertSuccess.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alertSuccess.style.opacity = '0';
            alertSuccess.style.transform = 'translateY(-20px)';
            
            setTimeout(() => {
                alertSuccess.remove();
            }, 500);
        }, 5000);
    }
    
    // === ANIMATIONS D'APPARITION ===
    const sections = document.querySelectorAll('.annonce-section, .contact-card, .candidature-form');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
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
    
    console.log('👁️ Page de détail d\'annonce initialisée !');
});