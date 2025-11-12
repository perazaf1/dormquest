<?php
// annonces.php - Listing des annonces (catalogue)
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Paramètres de pagination
$annonces_par_page = 12;
$page_actuelle = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page_actuelle - 1) * $annonces_par_page;

// Récupération des filtres
$recherche = trim($_GET['recherche'] ?? '');
$type_logement = $_GET['type_logement'] ?? '';
$budget_min = isset($_GET['budget_min']) ? intval($_GET['budget_min']) : 0;
$budget_max = isset($_GET['budget_max']) ? intval($_GET['budget_max']) : 2000;
$meuble = isset($_GET['meuble']);
$eligible_apl = isset($_GET['eligible_apl']);
$parking = isset($_GET['parking']);

// Construction de la requête SQL avec filtres
$where_clauses = ["a.statut = 'active'"];
$params = [];

// Filtre recherche (ville ou code postal)
if (!empty($recherche)) {
    $where_clauses[] = "(a.ville LIKE ? OR a.codePostal LIKE ?)";
    $recherche_param = "%{$recherche}%";
    $params[] = $recherche_param;
    $params[] = $recherche_param;
}

// Filtre type de logement
if (!empty($type_logement)) {
    $where_clauses[] = "a.typeLogement = ?";
    $params[] = $type_logement;
}

// Filtre budget
$where_clauses[] = "a.prixMensuel BETWEEN ? AND ?";
$params[] = $budget_min;
$params[] = $budget_max;

// Filtres critères
if ($meuble) {
    $where_clauses[] = "c.meuble = 1";
}
if ($eligible_apl) {
    $where_clauses[] = "c.eligibleAPL = 1";
}
if ($parking) {
    $where_clauses[] = "c.parkingDisponible = 1";
}

$where_sql = implode(' AND ', $where_clauses);

try {
    // Compter le nombre total d'annonces
    $count_sql = "
        SELECT COUNT(DISTINCT a.id)
        FROM annonces a
        LEFT JOIN criteres_logement c ON c.idAnnonce = a.id
        WHERE {$where_sql}
    ";
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total_annonces = $stmt->fetchColumn();
    $total_pages = ceil($total_annonces / $annonces_par_page);
    
    // Récupérer les annonces avec pagination
    $sql = "
        SELECT 
            a.*,
            u.prenom AS loueur_prenom,
            u.nom AS loueur_nom,
            u.typeLoueur,
            c.meuble,
            c.eligibleAPL,
            c.parkingDisponible,
            c.accesPMR,
            COUNT(DISTINCT f.id) as nb_favoris
        FROM annonces a
        LEFT JOIN utilisateurs u ON a.idLoueur = u.id
        LEFT JOIN criteres_logement c ON c.idAnnonce = a.id
        LEFT JOIN favoris f ON f.idAnnonce = a.id
        WHERE {$where_sql}
        GROUP BY a.id
        ORDER BY a.dateCreation DESC
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $annonces_par_page;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $annonces = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $annonces = [];
    $total_annonces = 0;
    $total_pages = 0;
    $error = "Erreur lors de la récupération des annonces : " . $e->getMessage();
}

// Fonction pour vérifier si une annonce est en favoris
function is_favori($annonce_id, $pdo) {
    if (!is_logged_in() || !is_etudiant()) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM favoris WHERE idEtudiant = ? AND idAnnonce = ?");
        $stmt->execute([get_user_id(), $annonce_id]);
        return $stmt->fetch() !== false;
    } catch (PDOException $e) {
        return false;
    }
}




?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annonces - DormQuest</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/annonces.css">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header__container">
            <a href="index.php" class="header__logo">
                <img src="images/logo-dormquest.png" alt="DormQuest Logo" class="header__logo-img">
                <span class="header__logo-text">DormQuest</span>
            </a>
            <nav class="header__nav">
                <a href="annonces.php" class="header__nav-link header__nav-link--active">Annonces</a>
                <?php if (is_logged_in()): ?>
                    <?php if (is_etudiant()): ?>
                        <a href="favoris.php" class="header__nav-link">Mes favoris</a>
                        <a href="candidatures.php" class="header__nav-link">Mes candidatures</a>
                    <?php else: ?>
                        <a href="dashboard-loueur.php" class="header__nav-link">Mes annonces</a>
                        <a href="create-annonce.php" class="header__nav-link">Créer une annonce</a>
                    <?php endif; ?>
                    <a href="profil.php" class="header__nav-link">Profil</a>
                    <a href="logout.php" class="header__btn header__btn--logout">Déconnexion</a>
                <?php else: ?>
                    <a href="login.php" class="header__btn header__btn--login">Connexion</a>
                    <a href="register.php" class="header__btn header__btn--register">Inscription</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="annonces-page">
        <!-- Hero de recherche -->
        <section class="search-hero">
            <div class="search-hero__container">
                <h1 class="search-hero__title">Trouvez votre logement étudiant idéal</h1>
                <p class="search-hero__subtitle"><?php echo number_format($total_annonces, 0, ',', ' '); ?> logement<?php echo $total_annonces > 1 ? 's' : ''; ?> disponible<?php echo $total_annonces > 1 ? 's' : ''; ?></p>
                
                <!-- Barre de recherche principale -->
                <form method="GET" action="annonces.php" class="search-form">
                    <div class="search-form__main">
                        <input type="text" 
                               name="recherche" 
                               class="search-form__input" 
                               placeholder="🔍 Rechercher par ville ou code postal..."
                               value="<?php echo htmlspecialchars($recherche); ?>">
                        <button type="submit" class="search-form__btn">Rechercher</button>
                    </div>
                </form>
            </div>
        </section>
      
        <div class="annonces-page__container">
            <!-- Sidebar filtres -->
            <aside class="filters-sidebar">
                <form method="GET" action="annonces.php" id="filters-form">
                    <!-- Conserver la recherche -->
                    <?php if (!empty($recherche)): ?>
                        <input type="hidden" name="recherche" value="<?php echo htmlspecialchars($recherche); ?>">
                    <?php endif; ?>
                    
                    <div class="filters-sidebar__header">
                        <h2 class="filters-sidebar__title">🎯 Filtres</h2>
                        <a href="annonces.php" class="filters-sidebar__reset">Réinitialiser</a>
                    </div>

                    <!-- Type de logement -->
                    <div class="filter-group">
                        <label class="filter-group__label">Type de logement</label>
                        <select name="type_logement" class="filter-group__select">
                            <option value="">Tous les types</option>
                            <option value="studio" <?php echo $type_logement === 'studio' ? 'selected' : ''; ?>>Studio</option>
                            <option value="colocation" <?php echo $type_logement === 'colocation' ? 'selected' : ''; ?>>Colocation</option>
                            <option value="residence_etudiante" <?php echo $type_logement === 'residence_etudiante' ? 'selected' : ''; ?>>Résidence étudiante</option>
                            <option value="chambre_habitant" <?php echo $type_logement === 'chambre_habitant' ? 'selected' : ''; ?>>Chambre chez l'habitant</option>
                        </select>
                    </div>

                    <!-- Budget -->
                    <div class="filter-group">
                        <label class="filter-group__label">
                            Budget mensuel : 
                            <span id="budget-display"><?php echo $budget_min; ?>€ - <?php echo $budget_max; ?>€</span>
                        </label>
                        <div class="budget-slider">
                            <input type="range" 
                                   name="budget_min" 
                                   id="budget_min" 
                                   min="0" 
                                   max="2000" 
                                   step="50" 
                                   value="<?php echo $budget_min; ?>"
                                   class="budget-slider__input">
                            <input type="range" 
                                   name="budget_max" 
                                   id="budget_max" 
                                   min="0" 
                                   max="2000" 
                                   step="50" 
                                   value="<?php echo $budget_max; ?>"
                                   class="budget-slider__input">
                        </div>
                        <div class="budget-slider__labels">
                            <span>0€</span>
                            <span>2000€</span>
                        </div>
                    </div>

                    <!-- Critères -->
                    <div class="filter-group">
                        <label class="filter-group__label">Critères</label>
                        <div class="filter-checkboxes">
                            <label class="filter-checkbox">
                                <input type="checkbox" name="meuble" <?php echo $meuble ? 'checked' : ''; ?>>
                                <span>🛋️ Meublé</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="eligible_apl" <?php echo $eligible_apl ? 'checked' : ''; ?>>
                                <span>💰 Éligible APL</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="parking" <?php echo $parking ? 'checked' : ''; ?>>
                                <span>🚗 Parking</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="filters-sidebar__submit">
                        Appliquer les filtres
                    </button>
                </form>
            </aside>

            <!-- Contenu principal -->
            <div class="annonces-content">
                <!-- Barre d'infos -->
                <div class="annonces-toolbar">
                    <div class="annonces-toolbar__info">
                        <strong><?php echo number_format($total_annonces, 0, ',', ' '); ?></strong> 
                        résultat<?php echo $total_annonces > 1 ? 's' : ''; ?>
                        <?php if (!empty($recherche)): ?>
                            pour "<strong><?php echo htmlspecialchars($recherche); ?></strong>"
                        <?php endif; ?>
                    </div>
                    <button class="annonces-toolbar__filter-toggle" id="mobile-filter-toggle">
                        🎯 Filtres
                    </button>
                </div>

                <?php if (empty($annonces)): ?>
                    <!-- État vide -->
                    <div class="empty-state">
                        <div class="empty-state__icon">🏠</div>
                        <h2 class="empty-state__title">Aucune annonce trouvée</h2>
                        <p class="empty-state__text">
                            Essayez d'ajuster vos critères de recherche pour voir plus de résultats.
                        </p>
                        <a href="annonces.php" class="empty-state__btn">Voir toutes les annonces</a>
                    </div>
                <?php else: ?>
                    <!-- Grille d'annonces -->
                    <div class="annonces-grid">
                        <?php foreach ($annonces as $annonce): ?>
                            <div class="annonce-card">
                                <!-- Badge favori (pour étudiants connectés) -->
                                <?php if (is_logged_in() && is_etudiant()): ?>
                                    <button class="annonce-card__favori <?php echo is_favori($annonce['id'], $pdo) ? 'annonce-card__favori--active' : ''; ?>" 
                                            data-annonce-id="<?php echo $annonce['id']; ?>"
                                            title="Ajouter aux favoris">
                                        ⭐
                                    </button>
                                <?php endif; ?>

                                <!-- Image -->
                                <div class="annonce-card__image">
                                    <img src="https://via.placeholder.com/400x250/2563eb/ffffff?text=<?php echo urlencode(substr($annonce['typeLogement'], 0, 15)); ?>" 
                                         alt="<?php echo htmlspecialchars($annonce['titre']); ?>">
                                    
                                    <!-- Badge type -->
                                    <span class="annonce-card__badge">
                                        <?php 
                                        $types_labels = [
                                            'studio' => 'Studio',
                                            'colocation' => 'Colocation',
                                            'residence_etudiante' => 'Résidence',
                                            'chambre_habitant' => 'Chambre'
                                        ];
                                        echo $types_labels[$annonce['typeLogement']] ?? $annonce['typeLogement'];
                                        ?>
                                    </span>
                                </div>

                                <!-- Contenu -->
                                <div class="annonce-card__content">
                                    <h3 class="annonce-card__title">
                                        <?php echo htmlspecialchars($annonce['titre']); ?>
                                    </h3>

                                    <div class="annonce-card__location">
                                        📍 <?php echo htmlspecialchars($annonce['ville']); ?> 
                                        (<?php echo htmlspecialchars($annonce['codePostal']); ?>)
                                    </div>

                                    <div class="annonce-card__price">
                                        <?php echo number_format($annonce['prixMensuel'], 0, ',', ' '); ?> €
                                        <span class="annonce-card__price-label">/mois</span>
                                    </div>

                                    <div class="annonce-card__details">
                                        <span class="annonce-card__detail">
                                            📐 <?php echo $annonce['superficie']; ?> m²
                                        </span>
                                        <span class="annonce-card__detail">
                                            🚪 <?php echo $annonce['nombrePieces']; ?> pièce<?php echo $annonce['nombrePieces'] > 1 ? 's' : ''; ?>
                                        </span>
                                    </div>

                                    <!-- Tags critères -->
                                    <div class="annonce-card__tags">
                                        <?php if ($annonce['meuble']): ?>
                                            <span class="annonce-card__tag">🛋️ Meublé</span>
                                        <?php endif; ?>
                                        <?php if ($annonce['eligibleAPL']): ?>
                                            <span class="annonce-card__tag">💰 APL</span>
                                        <?php endif; ?>
                                        <?php if ($annonce['parkingDisponible']): ?>
                                            <span class="annonce-card__tag">🚗 Parking</span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Bouton voir plus -->
                                    <a href="annonce.php?id=<?php echo $annonce['id']; ?>" 
                                       class="annonce-card__btn">
                                        Voir plus
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php
                            // Construire l'URL de base avec les filtres
                            $base_url = 'annonces.php?';
                            $query_params = [];
                            if (!empty($recherche)) $query_params[] = 'recherche=' . urlencode($recherche);
                            if (!empty($type_logement)) $query_params[] = 'type_logement=' . urlencode($type_logement);
                            if ($budget_min != 0) $query_params[] = 'budget_min=' . $budget_min;
                            if ($budget_max != 2000) $query_params[] = 'budget_max=' . $budget_max;
                            if ($meuble) $query_params[] = 'meuble=1';
                            if ($eligible_apl) $query_params[] = 'eligible_apl=1';
                            if ($parking) $query_params[] = 'parking=1';
                            $base_url .= implode('&', $query_params);
                            if (!empty($query_params)) $base_url .= '&';
                            ?>

                            <!-- Première page -->
                            <?php if ($page_actuelle > 1): ?>
                                <a href="<?php echo $base_url; ?>page=1" class="pagination__link">«</a>
                                <a href="<?php echo $base_url; ?>page=<?php echo $page_actuelle - 1; ?>" class="pagination__link">‹</a>
                            <?php endif; ?>

                            <!-- Pages -->
                            <?php
                            $start = max(1, $page_actuelle - 2);
                            $end = min($total_pages, $page_actuelle + 2);
                            
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <a href="<?php echo $base_url; ?>page=<?php echo $i; ?>" 
                                   class="pagination__link <?php echo $i === $page_actuelle ? 'pagination__link--active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <!-- Dernière page -->
                            <?php if ($page_actuelle < $total_pages): ?>
                                <a href="<?php echo $base_url; ?>page=<?php echo $page_actuelle + 1; ?>" class="pagination__link">›</a>
                                <a href="<?php echo $base_url; ?>page=<?php echo $total_pages; ?>" class="pagination__link">»</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

        <!-- Footer -->
    <footer class="footer">
        <div class="footer__container">
            <div class="footer__section">
                <h4 class="footer__title">DormQuest</h4>
                <p class="footer__text">
                    Trouvez le logement parfait pour vos études !
                </p>
            </div>
            <div class="footer__section">
                <h4 class="footer__title">Navigation</h4>
                <ul class="footer__list">
                    <li class="footer__item"><a href="#annonces" class="footer__link">Annonces</a></li>
                    <li class="footer__item"><a href="#avantages" class="footer__link">Avantages</a></li>
                    <li class="footer__item"><a href="#apropos" class="footer__link">À propos</a></li>
                    <li class="footer__item"><a href="#faq" class="footer__link">FAQ</a></li>
                </ul>
            </div>
            <div class="footer__section">
                <h4 class="footer__title">Liens utiles</h4>
                <ul class="footer__list">
                    <li class="footer__item"><a href="register.php" class="footer__link">Inscription</a></li>
                    <li class="footer__item"><a href="login.php" class="footer__link">Connexion</a></li>
                    <li class="footer__item"><a href="contact.php" class="footer__link">Contact</a></li>
                    <li class="footer__item"><a href="CGU.php" class="footer__link" target="blank">CGU</a></li>
                    <li class="footer__item"><a href="mentions-legales.php" class="footer__link" target="blank">Mentions légales</a></li>
                </ul>
            </div>
            <div class="footer__section">
                <h4 class="footer__title">Powered by</h4>
                <img src="images/logo-nyzer.png" alt="Nyzer" class="footer__nyzer-logo">
            </div>
        </div>
        <div class="footer__bottom">
            <p class="footer__copyright">
                &copy; 2025 DormQuest by Nyzer. Tous droits réservés.
            </p>
        </div>
    </footer>

    <script src="js/annonces.js"></script>
    <script>
const annoncesData = <?php echo $annonces_json; ?>;
</script>

</body>
</html>