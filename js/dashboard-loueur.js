// dashboard-loueur.js - JavaScript pour le dashboard loueur

document.addEventListener('DOMContentLoaded', function() {
    
    // === SYSTÈME DE FILTRES ===
    const filterButtons = document.querySelectorAll('.filter-btn');
    const annonceCards = document.querySelectorAll('.annonce-card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Mettre à jour les boutons actifs
            filterButtons.forEach(btn => btn.classList.remove('filter-btn--active'));
            this.classList.add('filter-btn--active');
            
            // Filtrer les cartes
            annonceCards.forEach(card => {
                const status = card.getAttribute('data-status');
                
                if (filter === 'all') {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 10);
                } else if (status === filter) {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 10);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
        });
    });
    
    // === CONFIRMATION DE SUPPRESSION AMÉLIORÉE ===
    const deleteButtons = document.querySelectorAll('.annonce-card__btn--delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const confirmed = confirm(
                '⚠️ ATTENTION !\n\n' +
                'Êtes-vous absolument sûr de vouloir supprimer cette annonce ?\n\n' +
                '• Toutes les candidatures associées seront perdues\n' +
                '• Cette action est IRRÉVERSIBLE\n' +
                '• Les étudiants ayant mis cette annonce en favori seront notifiés\n\n' +
                'Confirmez-vous la suppression ?'
            );
            
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });
    
    // === CONFIRMATION D'ARCHIVAGE ===
    const archiveButtons = document.querySelectorAll('.annonce-card__btn--archive');
    
    archiveButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const confirmed = confirm(
                '📦 Archiver cette annonce ?\n\n' +
                '• L\'annonce ne sera plus visible par les étudiants\n' +
                '• Vous pourrez la réactiver à tout moment\n' +
                '• Les candidatures existantes resteront accessibles\n\n' +
                'Confirmez-vous l\'archivage ?'
            );
            
            if (!confirmed) {
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
            alert.style.transform = 'translateY(-10px)';
            
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000); // Disparaît après 5 secondes
    });
    
    // === ANIMATIONS AU SCROLL ===
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
    
    // Observer toutes les cartes d'annonces
    annonceCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
    
    // === TOOLTIPS PERSONNALISÉS ===
    const actionButtons = document.querySelectorAll('.annonce-card__btn');
    
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            const title = this.getAttribute('title');
            
            if (title) {
                const tooltip = document.createElement('div');
                tooltip.className = 'custom-tooltip';
                tooltip.textContent = title;
                tooltip.style.cssText = `
                    position: absolute;
                    background-color: rgba(0, 0, 0, 0.8);
                    color: white;
                    padding: 0.5rem 1rem;
                    border-radius: 6px;
                    font-size: 0.875rem;
                    white-space: nowrap;
                    z-index: 1000;
                    pointer-events: none;
                    bottom: 120%;
                    left: 50%;
                    transform: translateX(-50%);
                    opacity: 0;
                    transition: opacity 0.3s ease;
                `;
                
                this.style.position = 'relative';
                this.appendChild(tooltip);
                
                setTimeout(() => {
                    tooltip.style.opacity = '1';
                }, 10);
            }
        });
        
        button.addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('.custom-tooltip');
            if (tooltip) {
                tooltip.style.opacity = '0';
                setTimeout(() => {
                    tooltip.remove();
                }, 300);
            }
        });
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
    
    // Animer les valeurs des statistiques
    const statValues = document.querySelectorAll('.stat-card__value');
    
    statValues.forEach(stat => {
        const target = parseInt(stat.textContent);
        if (!isNaN(target)) {
            stat.textContent = '0';
            animateCounter(stat, target, 1500);
        }
    });
    
    // === RECHERCHE RAPIDE (optionnel) ===
    // Ajouter un champ de recherche dynamique si besoin
    const searchInput = document.querySelector('.dashboard__search');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            
            annonceCards.forEach(card => {
                const title = card.querySelector('.annonce-card__title').textContent.toLowerCase();
                const ville = card.querySelector('.annonce-card__info-item').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || ville.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
    
    // === GESTION DU RESPONSIVE (menu mobile) ===
    const headerNav = document.querySelector('.header__nav');
    
    if (window.innerWidth <= 768) {
        // Créer un bouton burger pour mobile si nécessaire
        const burgerBtn = document.createElement('button');
        burgerBtn.className = 'header__burger';
        burgerBtn.innerHTML = '☰';
        burgerBtn.setAttribute('aria-label', 'Menu');
        
        const headerContainer = document.querySelector('.header__container');
        headerContainer.appendChild(burgerBtn);
        
        burgerBtn.addEventListener('click', function() {
            headerNav.classList.toggle('header__nav--mobile-active');
            this.innerHTML = headerNav.classList.contains('header__nav--mobile-active') ? '✕' : '☰';
        });
    }
    
    // === COPIER LE LIEN DE L'ANNONCE ===
    function addCopyLinkFeature() {
        annonceCards.forEach(card => {
            const viewBtn = card.querySelector('.annonce-card__btn--view');
            
            if (viewBtn) {
                const copyBtn = document.createElement('button');
                copyBtn.className = 'annonce-card__copy-link';
                copyBtn.innerHTML = '🔗';
                copyBtn.title = 'Copier le lien';
                copyBtn.style.cssText = `
                    position: absolute;
                    top: 1rem;
                    left: 1rem;
                    background-color: white;
                    border: none;
                    border-radius: 50%;
                    width: 40px;
                    height: 40px;
                    font-size: 1.25rem;
                    cursor: pointer;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                    transition: all 0.3s ease;
                    z-index: 10;
                `;
                
                copyBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const annonceUrl = viewBtn.getAttribute('href');
                    const fullUrl = window.location.origin + '/' + annonceUrl;
                    
                    navigator.clipboard.writeText(fullUrl).then(() => {
                        this.innerHTML = '✅';
                        setTimeout(() => {
                            this.innerHTML = '🔗';
                        }, 2000);
                    });
                });
                
                copyBtn.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.1)';
                });
                
                copyBtn.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
                
                card.style.position = 'relative';
                card.insertBefore(copyBtn, card.firstChild);
            }
        });
    }
    
    addCopyLinkFeature();
    
    // === SAUVEGARDE DU FILTRE ACTIF ===
    const activeFilter = localStorage.getItem('dashboard_filter');
    
    if (activeFilter) {
        const filterBtn = document.querySelector(`[data-filter="${activeFilter}"]`);
        if (filterBtn) {
            filterBtn.click();
        }
    }
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            localStorage.setItem('dashboard_filter', filter);
        });
    });
    
    console.log('🏠 Dashboard loueur initialisé avec succès !');
});