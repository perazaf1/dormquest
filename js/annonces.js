// annonces.js - JavaScript pour la page des annonces

document.addEventListener('DOMContentLoaded', function() {
    
    // === SLIDER DE BUDGET DOUBLE === 
    const budgetMin = document.getElementById('budget_min');
    const budgetMax = document.getElementById('budget_max');
    const budgetDisplay = document.getElementById('budget-display');
    
    if (budgetMin && budgetMax && budgetDisplay) {
        function updateBudgetDisplay() {
            const min = parseInt(budgetMin.value);
            const max = parseInt(budgetMax.value);
            
            // S'assurer que min < max
            if (min >= max) {
                if (this === budgetMin) {
                    budgetMin.value = max - 50;
                } else {
                    budgetMax.value = min + 50;
                }
            }
            
            budgetDisplay.textContent = `${budgetMin.value}€ - ${budgetMax.value}€`;
            
            // Mettre à jour visuellement le slider
            const minPercent = (budgetMin.value / budgetMin.max) * 100;
            const maxPercent = (budgetMax.value / budgetMax.max) * 100;
            
            // Créer l'effet visuel de la plage sélectionnée
            const slider = budgetMin.parentElement;
            let rangeTrack = slider.querySelector('.budget-slider__track');
            
            if (!rangeTrack) {
                rangeTrack = document.createElement('div');
                rangeTrack.className = 'budget-slider__track';
                rangeTrack.style.cssText = `
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    height: 6px;
                    background: linear-gradient(90deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
                    border-radius: 3px;
                    pointer-events: none;
                `;
                slider.appendChild(rangeTrack);
            }
            
            rangeTrack.style.left = `${minPercent}%`;
            rangeTrack.style.width = `${maxPercent - minPercent}%`;
        }
        
        budgetMin.addEventListener('input', updateBudgetDisplay);
        budgetMax.addEventListener('input', updateBudgetDisplay);
        
        // Initialiser
        updateBudgetDisplay.call(budgetMin);
    }
    
    // === GESTION DES FAVORIS ===
    const favoriBtns = document.querySelectorAll('.annonce-card__favori');
    
    favoriBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const annonceId = this.getAttribute('data-annonce-id');
            const isActive = this.classList.contains('annonce-card__favori--active');
            
            // Animation de clic
            this.style.transform = 'scale(1.3)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
            
            // Toggle l'état
            this.classList.toggle('annonce-card__favori--active');
            
            // Envoi de la requête AJAX (à implémenter)
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
                    this.classList.toggle('annonce-card__favori--active');
                    alert('Erreur : ' + (data.message || 'Impossible de modifier les favoris'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                // Annuler le toggle en cas d'erreur
                this.classList.toggle('annonce-card__favori--active');
            });
        });
    });
    
    // === FILTRE MOBILE ===
    const mobileFilterToggle = document.getElementById('mobile-filter-toggle');
    const filtersSidebar = document.querySelector('.filters-sidebar');
    
    if (mobileFilterToggle && filtersSidebar) {
        mobileFilterToggle.addEventListener('click', function() {
            filtersSidebar.classList.toggle('filters-sidebar--mobile-active');
            
            // Créer l'overlay si il n'existe pas
            let overlay = document.getElementById('filters-overlay');
            
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'filters-overlay';
                overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: rgba(0, 0, 0, 0.5);
                    z-index: 9998;
                    display: none;
                `;
                document.body.appendChild(overlay);
                
                overlay.addEventListener('click', function() {
                    filtersSidebar.classList.remove('filters-sidebar--mobile-active');
                    this.style.display = 'none';
                    document.body.style.overflow = '';
                });
            }
            
            if (filtersSidebar.classList.contains('filters-sidebar--mobile-active')) {
                overlay.style.display = 'block';
                document.body.style.overflow = 'hidden';
            } else {
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    }
    
    // === AUTO-SUBMIT DES FILTRES (optionnel) ===
    const filterInputs = document.querySelectorAll('.filter-group__select, .filter-checkbox input');
    
    // Décommenter pour activer l'auto-submit
    /*
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Attendre un peu avant de soumettre (debounce)
            clearTimeout(window.filterTimeout);
            window.filterTimeout = setTimeout(() => {
                document.getElementById('filters-form').submit();
            }, 500);
        });
    });
    */
    
    // === SCROLL TO TOP ===
    let scrollToTopBtn = document.getElementById('scroll-to-top');
    
    if (!scrollToTopBtn) {
        scrollToTopBtn = document.createElement('button');
        scrollToTopBtn.id = 'scroll-to-top';
        scrollToTopBtn.innerHTML = '↑';
        scrollToTopBtn.style.cssText = `
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        `;
        document.body.appendChild(scrollToTopBtn);
        
        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollToTopBtn.style.opacity = '1';
            scrollToTopBtn.style.visibility = 'visible';
        } else {
            scrollToTopBtn.style.opacity = '0';
            scrollToTopBtn.style.visibility = 'hidden';
        }
    });
    
    // === ANIMATIONS AU SCROLL ===
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
    
    const cards = document.querySelectorAll('.annonce-card');
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
    
    // === SAUVEGARDE DES FILTRES DANS L'URL ===
    // Permet de partager l'URL avec les filtres actifs
    const filtersForm = document.getElementById('filters-form');
    
    if (filtersForm) {
        filtersForm.addEventListener('submit', function(e) {
            // Supprimer les champs vides pour avoir une URL propre
            const inputs = this.querySelectorAll('input[type="text"], input[type="number"], select');
            
            inputs.forEach(input => {
                if (!input.value || input.value === '') {
                    input.disabled = true;
                }
            });
        });
    }
    
    // === TOOLTIP SUR LES FAVORIS ===
    favoriBtns.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            const isActive = this.classList.contains('annonce-card__favori--active');
            const tooltip = document.createElement('div');
            tooltip.className = 'favori-tooltip';
            tooltip.textContent = isActive ? 'Retirer des favoris' : 'Ajouter aux favoris';
            tooltip.style.cssText = `
                position: absolute;
                top: -35px;
                left: 50%;
                transform: translateX(-50%);
                background-color: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 6px;
                font-size: 0.875rem;
                white-space: nowrap;
                pointer-events: none;
                z-index: 100;
            `;
            
            this.style.position = 'relative';
            this.appendChild(tooltip);
        });
        
        btn.addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('.favori-tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        });
    });
    
    // === COMPTEUR DE RÉSULTATS ANIMÉ ===
    const resultsCount = document.querySelector('.annonces-toolbar__info strong');
    
    if (resultsCount) {
        const target = parseInt(resultsCount.textContent.replace(/\s/g, ''));
        
        if (!isNaN(target) && target > 0) {
            let current = 0;
            const increment = Math.ceil(target / 50);
            const duration = 1000;
            const stepTime = duration / (target / increment);
            
            const timer = setInterval(() => {
                current += increment;
                
                if (current >= target) {
                    resultsCount.textContent = target.toLocaleString('fr-FR');
                    clearInterval(timer);
                } else {
                    resultsCount.textContent = current.toLocaleString('fr-FR');
                }
            }, stepTime);
        }
    }
    
    // === LAZY LOADING DES IMAGES (optionnel) ===
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        });
        
        const images = document.querySelectorAll('.annonce-card__image img');
        images.forEach(img => imageObserver.observe(img));
    }
    
    // === HIGHLIGHTS DE RECHERCHE ===
    const searchTerm = new URLSearchParams(window.location.search).get('recherche');
    
    if (searchTerm) {
        const titles = document.querySelectorAll('.annonce-card__title, .annonce-card__location');
        
        titles.forEach(title => {
            const text = title.textContent;
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            
            if (regex.test(text)) {
                title.innerHTML = text.replace(regex, '<mark style="background-color: var(--color-secondary); padding: 0 0.25rem; border-radius: 3px;">$1</mark>');
            }
        });
    }
    
    console.log('🏠 Page annonces initialisée !');
});


