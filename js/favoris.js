// favoris.js - JavaScript pour la page des favoris

document.addEventListener('DOMContentLoaded', function() {
    
    // === SYSTÈME DE COMPARAISON ===
    let selectedForComparison = [];
    const compareButtons = document.querySelectorAll('.favori-card__btn--compare');
    const compareMainBtn = document.getElementById('compare-btn');
    const modal = document.getElementById('comparison-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const comparisonBody = document.getElementById('comparison-body');
    
    // Sélection des annonces à comparer
    compareButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const annonceId = parseInt(this.getAttribute('data-annonce-id'));
            
            if (this.classList.contains('active')) {
                // Retirer de la sélection
                selectedForComparison = selectedForComparison.filter(id => id !== annonceId);
                this.classList.remove('active');
            } else {
                // Ajouter à la sélection (max 3)
                if (selectedForComparison.length >= 3) {
                    alert('Vous ne pouvez comparer que 3 annonces maximum');
                    return;
                }
                selectedForComparison.push(annonceId);
                this.classList.add('active');
            }
            
            // Mettre à jour le bouton principal
            updateCompareButton();
        });
    });
    
    // Mettre à jour le bouton principal de comparaison
    function updateCompareButton() {
        if (compareMainBtn) {
            if (selectedForComparison.length >= 2) {
                compareMainBtn.textContent = `📊 Comparer (${selectedForComparison.length})`;
                compareMainBtn.style.backgroundColor = 'var(--color-primary)';
                compareMainBtn.style.color = 'white';
                compareMainBtn.disabled = false;
            } else {
                compareMainBtn.textContent = '📊 Comparer les favoris';
                compareMainBtn.style.backgroundColor = 'white';
                compareMainBtn.style.color = 'var(--color-primary)';
                compareMainBtn.disabled = false;
            }
        }
    }
    
    // Ouvrir la modal de comparaison
    if (compareMainBtn) {
        compareMainBtn.addEventListener('click', function() {
            if (selectedForComparison.length < 2) {
                alert('Veuillez sélectionner au moins 2 annonces à comparer');
                return;
            }
            
            showComparisonModal();
        });
    }
    
    // Afficher la modal de comparaison
    function showComparisonModal() {
        // Récupérer les données des annonces sélectionnées
        const selectedAnnonces = favorisData.filter(f => selectedForComparison.includes(f.id));
        
        // Construire le tableau de comparaison
        let html = '<table class="comparison-table">';
        
        // En-têtes
        html += '<thead><tr>';
        html += '<th>Critère</th>';
        selectedAnnonces.forEach(annonce => {
            html += `<th>${annonce.titre.substring(0, 30)}...</th>`;
        });
        html += '</tr></thead>';
        
        // Corps du tableau
        html += '<tbody>';
        
        // Ville
        html += '<tr>';
        html += '<td class="comparison-table__label">📍 Ville</td>';
        selectedAnnonces.forEach(a => {
            html += `<td class="comparison-table__value">${a.ville}</td>`;
        });
        html += '</tr>';
        
        // Prix
        html += '<tr>';
        html += '<td class="comparison-table__label">💰 Prix mensuel</td>';
        selectedAnnonces.forEach(a => {
            html += `<td class="comparison-table__value">${parseFloat(a.prixMensuel).toLocaleString('fr-FR')} €</td>`;
        });
        html += '</tr>';
        
        // Superficie
        html += '<tr>';
        html += '<td class="comparison-table__label">📐 Superficie</td>';
        selectedAnnonces.forEach(a => {
            html += `<td class="comparison-table__value">${a.superficie} m²</td>`;
        });
        html += '</tr>';
        
        // Prix au m²
        html += '<tr>';
        html += '<td class="comparison-table__label">💵 Prix au m²</td>';
        selectedAnnonces.forEach(a => {
            const prixM2 = (a.prixMensuel / a.superficie).toFixed(2);
            html += `<td class="comparison-table__value">${prixM2} €/m²</td>`;
        });
        html += '</tr>';
        
        // Nombre de pièces
        html += '<tr>';
        html += '<td class="comparison-table__label">🚪 Pièces</td>';
        selectedAnnonces.forEach(a => {
            html += `<td class="comparison-table__value">${a.nombrePieces}</td>`;
        });
        html += '</tr>';
        
        // Type de logement
        html += '<tr>';
        html += '<td class="comparison-table__label">🏠 Type</td>';
        selectedAnnonces.forEach(a => {
            const types = {
                'studio': 'Studio',
                'colocation': 'Colocation',
                'residence_etudiante': 'Résidence',
                'chambre_habitant': 'Chambre'
            };
            html += `<td class="comparison-table__value">${types[a.typeLogement] || a.typeLogement}</td>`;
        });
        html += '</tr>';
        
        // Meublé
        html += '<tr>';
        html += '<td class="comparison-table__label">🛋️ Meublé</td>';
        selectedAnnonces.forEach(a => {
            html += `<td class="comparison-table__value">${a.meuble ? '✅ Oui' : '❌ Non'}</td>`;
        });
        html += '</tr>';
        
        // Éligible APL
        html += '<tr>';
        html += '<td class="comparison-table__label">💰 Éligible APL</td>';
        selectedAnnonces.forEach(a => {
            html += `<td class="comparison-table__value">${a.eligibleAPL ? '✅ Oui' : '❌ Non'}</td>`;
        });
        html += '</tr>';
        
        // Parking
        html += '<tr>';
        html += '<td class="comparison-table__label">🚗 Parking</td>';
        selectedAnnonces.forEach(a => {
            html += `<td class="comparison-table__value">${a.parkingDisponible ? '✅ Oui' : '❌ Non'}</td>`;
        });
        html += '</tr>';
        
        // Accès PMR
        html += '<tr>';
        html += '<td class="comparison-table__label">♿ Accès PMR</td>';
        selectedAnnonces.forEach(a => {
            html += `<td class="comparison-table__value">${a.accesPMR ? '✅ Oui' : '❌ Non'}</td>`;
        });
        html += '</tr>';
        
        // Actions
        html += '<tr>';
        html += '<td class="comparison-table__label">👁️ Actions</td>';
        selectedAnnonces.forEach(a => {
            html += `<td><a href="annonce.php?id=${a.id}" class="favori-card__btn favori-card__btn--view" style="display: inline-block; margin-top: 0.5rem;">Voir</a></td>`;
        });
        html += '</tr>';
        
        html += '</tbody></table>';
        
        comparisonBody.innerHTML = html;
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    
    // Fermer la modal
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }
    
    if (modal) {
        modal.querySelector('.comparison-modal__overlay').addEventListener('click', closeModal);
    }
    
    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Échap pour fermer la modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.style.display === 'block') {
            closeModal();
        }
    });
    
    // === EXPORT EN PDF (simulation) ===
    const exportBtn = document.getElementById('export-btn');
    
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            // En production, tu pourrais utiliser jsPDF ou une librairie similaire
            alert('🔜 Fonctionnalité d\'export PDF en cours de développement !\n\nPour l\'instant, vous pouvez faire une capture d\'écran de la page.');
            
            // Exemple de ce qu'on pourrait faire :
            // window.print(); // Ouvrir la boîte de dialogue d'impression
        });
    }
    
    // === CONFIRMATION DE SUPPRESSION AMÉLIORÉE ===
    const removeButtons = document.querySelectorAll('.favori-card__btn--remove');
    
    removeButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const card = this.closest('.favori-card');
            const title = card.querySelector('.favori-card__title').textContent;
            
            const confirmed = confirm(
                `❌ Retirer des favoris ?\n\n` +
                `Êtes-vous sûr de vouloir retirer cette annonce de vos favoris ?\n\n` +
                `"${title}"\n\n` +
                `Vous pourrez toujours la rajouter plus tard.`
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
        }, 5000);
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
    
    const cards = document.querySelectorAll('.favori-card');
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
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
        const text = stat.textContent.replace(/[^\d]/g, '');
        const target = parseInt(text);
        
        if (!isNaN(target) && target > 0) {
            stat.textContent = '0';
            
            setTimeout(() => {
                animateCounter(stat, target, 1500);
                
                // Remettre les unités après animation
                setTimeout(() => {
                    if (stat.parentElement.querySelector('.stat-card__label').textContent.includes('Prix')) {
                        stat.textContent = target.toLocaleString('fr-FR') + ' €';
                    } else if (stat.parentElement.querySelector('.stat-card__label').textContent.includes('Superficie')) {
                        stat.textContent = target.toLocaleString('fr-FR') + ' m²';
                    }
                }, 1600);
            }, 300);
        }
    });
    
    // === FILTRE RAPIDE (optionnel) ===
    function addQuickFilters() {
        const header = document.querySelector('.favoris-header');
        
        if (header && favorisData.length > 3) {
            const filterDiv = document.createElement('div');
            filterDiv.className = 'quick-filters';
            filterDiv.style.cssText = `
                margin-top: 1rem;
                display: flex;
                gap: 0.5rem;
                flex-wrap: wrap;
            `;
            
            // Filtre par ville
            const villes = [...new Set(favorisData.map(f => f.ville))];
            
            if (villes.length > 1) {
                villes.forEach(ville => {
                    const btn = document.createElement('button');
                    btn.textContent = ville;
                    btn.className = 'filter-tag';
                    btn.style.cssText = `
                        padding: 0.5rem 1rem;
                        background-color: white;
                        border: 2px solid var(--color-primary);
                        color: var(--color-primary);
                        border-radius: 20px;
                        cursor: pointer;
                        font-size: 0.875rem;
                        font-weight: 600;
                        transition: all 0.3s ease;
                    `;
                    
                    btn.addEventListener('click', function() {
                        filterByCity(ville);
                    });
                    
                    filterDiv.appendChild(btn);
                });
                
                header.appendChild(filterDiv);
            }
        }
    }
    
    function filterByCity(city) {
        const cards = document.querySelectorAll('.favori-card');
        
        cards.forEach(card => {
            const location = card.querySelector('.favori-card__location').textContent;
            
            if (location.includes(city)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    // Activer les filtres rapides
    // addQuickFilters();
    
    console.log('⭐ Page favoris initialisée !');
    console.log(`${favorisData.length} favori(s) chargé(s)`);
});