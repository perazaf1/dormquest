// candidatures.js - JavaScript pour les pages de candidatures

document.addEventListener('DOMContentLoaded', function() {
    
    // === SYSTÈME DE FILTRES (page étudiant) ===
    const filterButtons = document.querySelectorAll('.filter-btn');
    const candidatureCards = document.querySelectorAll('[data-status]');
    
    if (filterButtons.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Mettre à jour les boutons actifs
                filterButtons.forEach(btn => btn.classList.remove('filter-btn--active'));
                this.classList.add('filter-btn--active');
                
                // Filtrer les cartes
                candidatureCards.forEach(card => {
                    const status = card.getAttribute('data-status');
                    
                    if (filter === 'all') {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateX(0)';
                        }, 10);
                    } else if (status === filter) {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateX(0)';
                        }, 10);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateX(-30px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });
    }
    
    // === TOGGLE MESSAGE (afficher/masquer) ===
    const toggleButtons = document.querySelectorAll('[data-toggle]');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const messageContainer = this.closest('.candidature-message');
            const content = messageContainer.querySelector('[data-content]');
            
            if (messageContainer.classList.contains('candidature-message--collapsed')) {
                messageContainer.classList.remove('candidature-message--collapsed');
                this.textContent = 'Masquer';
                
                // Animation d'ouverture
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.opacity = '1';
            } else {
                messageContainer.classList.add('candidature-message--collapsed');
                this.textContent = 'Voir';
                
                // Animation de fermeture
                content.style.maxHeight = '0';
                content.style.opacity = '0';
            }
        });
    });
    
    // === CONFIRMATION AVANT ACTION ===
    const actionForms = document.querySelectorAll('.candidature-actions__form');
    
    actionForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const action = this.querySelector('button[type="submit"]').value;
            let message = '';
            
            if (action === 'accepter') {
                message = '✅ Confirmer l\'acceptation de cette candidature ?\n\n' +
                         'L\'étudiant recevra vos coordonnées pour vous contacter.';
            } else if (action === 'refuser') {
                message = '❌ Confirmer le refus de cette candidature ?\n\n' +
                         'Cette action est irréversible.';
            }
            
            if (message && !confirm(message)) {
                e.preventDefault();
            }
        });
    });
    
    // === AUTO-MASQUAGE DES ALERTES ===
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // === COMPTEUR ANIMÉ POUR LES STATS ===
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
    const statValues = document.querySelectorAll('.stat-card__value');
    
    statValues.forEach(stat => {
        const target = parseInt(stat.textContent);
        if (!isNaN(target)) {
            stat.textContent = '0';
            setTimeout(() => {
                animateCounter(stat, target, 1000);
            }, 200);
        }
    });
    
    // === HIGHLIGHT DES CANDIDATURES RÉCENTES ===
    const cards = document.querySelectorAll('.candidature-card, .candidature-card-etudiant');
    
    cards.forEach(card => {
        const dateElement = card.querySelector('.candidature-dates__item:first-child');
        
        if (dateElement) {
            const dateText = dateElement.textContent;
            const match = dateText.match(/(\d{2}\/\d{2}\/\d{4})/);
            
            if (match) {
                const dateString = match[1];
                const parts = dateString.split('/');
                const candidatureDate = new Date(parts[2], parts[1] - 1, parts[0]);
                const today = new Date();
                const diffDays = Math.floor((today - candidatureDate) / (1000 * 60 * 60 * 24));
                
                // Ajouter un badge "Nouveau" si moins de 3 jours
                if (diffDays <= 3) {
                    const badge = document.createElement('span');
                    badge.className = 'badge-new';
                    badge.textContent = '🆕 Nouveau';
                    badge.style.cssText = `
                        position: absolute;
                        top: 1rem;
                        left: 1rem;
                        padding: 0.375rem 0.75rem;
                        background-color: var(--color-secondary);
                        color: var(--color-text);
                        border-radius: 15px;
                        font-size: 0.75rem;
                        font-weight: 700;
                        z-index: 11;
                    `;
                    card.style.position = 'relative';
                    card.appendChild(badge);
                }
            }
        }
    });
    
    // === COPIER EMAIL AU CLIC ===
    const emailLinks = document.querySelectorAll('a[href^="mailto:"]');
    
    emailLinks.forEach(link => {
        const copyBtn = document.createElement('button');
        copyBtn.textContent = '📋';
        copyBtn.title = 'Copier l\'email';
        copyBtn.style.cssText = `
            margin-left: 0.5rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            opacity: 0.6;
            transition: opacity 0.3s ease;
        `;
        
        copyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const email = link.href.replace('mailto:', '');
            
            navigator.clipboard.writeText(email).then(() => {
                this.textContent = '✅';
                setTimeout(() => {
                    this.textContent = '📋';
                }, 2000);
            });
        });
        
        copyBtn.addEventListener('mouseenter', function() {
            this.style.opacity = '1';
        });
        
        copyBtn.addEventListener('mouseleave', function() {
            this.style.opacity = '0.6';
        });
        
        link.parentNode.insertBefore(copyBtn, link.nextSibling);
    });
    
    // === SCROLL VERS LES ERREURS ===
    const alertError = document.querySelector('.alert--error');
    
    if (alertError) {
        alertError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // === OBSERVER POUR ANIMATIONS ===
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateX(0)';
            }
        });
    }, observerOptions);
    
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateX(-30px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
    
    // === SAUVEGARDE DU FILTRE ACTIF ===
    if (filterButtons.length > 0) {
        const activeFilter = localStorage.getItem('candidatures_filter');
        
        if (activeFilter) {
            const filterBtn = document.querySelector(`[data-filter="${activeFilter}"]`);
            if (filterBtn) {
                filterBtn.click();
            }
        }
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                localStorage.setItem('candidatures_filter', filter);
            });
        });
    }
    
    // === TEMPS ÉCOULÉ DEPUIS L'ENVOI ===
    const dateElements = document.querySelectorAll('.candidature-dates__item:first-child');
    
    dateElements.forEach(element => {
        const dateText = element.textContent;
        const match = dateText.match(/(\d{2}\/\d{2}\/\d{4}) à (\d{2}):(\d{2})/);
        
        if (match) {
            const [_, date, hours, minutes] = match;
            const parts = date.split('/');
            const candidatureDate = new Date(parts[2], parts[1] - 1, parts[0], hours, minutes);
            const now = new Date();
            const diffMs = now - candidatureDate;
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
            const diffMinutes = Math.floor(diffMs / (1000 * 60));
            
            let timeAgo = '';
            
            if (diffMinutes < 60) {
                timeAgo = `il y a ${diffMinutes} min`;
            } else if (diffHours < 24) {
                timeAgo = `il y a ${diffHours}h`;
            } else if (diffDays === 1) {
                timeAgo = 'hier';
            } else if (diffDays < 7) {
                timeAgo = `il y a ${diffDays} jours`;
            }
            
            if (timeAgo) {
                const timeSpan = document.createElement('span');
                timeSpan.textContent = ` (${timeAgo})`;
                timeSpan.style.color = 'var(--color-gray)';
                timeSpan.style.fontWeight = 'normal';
                element.appendChild(timeSpan);
            }
        }
    });
    
    console.log('📨 Page candidatures initialisée !');
});