<?php
// favoris.php - Page des favoris de l'étudiant
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Vérifier que l'utilisateur est un étudiant connecté
require_etudiant();

$etudiant_id = get_user_id();
$success = '';
$errors = [];

// Gestion de la suppression d'un favori
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    $annonce_id = intval($_GET['id']);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM favoris WHERE idEtudiant = ? AND idAnnonce = ?");
        $stmt->execute([$etudiant_id, $annonce_id]);
        
        $success = "Annonce retirée des favoris.";
    } catch (PDOException $e) {
        $errors[] = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Récupérer tous les favoris de l'étudiant
try {
    $stmt = $pdo->prepare("
        SELECT 
            f.id AS favori_id,
            f.dateAjout,
            a.*,
            u.prenom AS loueur_prenom,
            u.nom AS loueur_nom,
            u.typeLoueur,
            c.meuble,
            c.eligibleAPL,
            c.parkingDisponible,
            c.accesPMR,
            (SELECT COUNT(*) FROM candidatures WHERE idAnnonce = a.id AND idEtudiant = ?) as has_candidated
        FROM favoris f
        JOIN annonces a ON f.idAnnonce = a.id
        JOIN utilisateurs u ON a.idLoueur = u.id
        LEFT JOIN criteres_logement c ON c.idAnnonce = a.id
        WHERE f.idEtudiant = ? AND a.statut = 'active'
        ORDER BY f.dateAjout DESC
    ");
    
    $stmt->execute([$etudiant_id, $etudiant_id]);
    $favoris = $stmt->fetchAll();
    
    // Calculer des statistiques
    $total_favoris = count($favoris);
    
    if ($total_favoris > 0) {
        $prix_moyen = array_sum(array_column($favoris, 'prixMensuel')) / $total_favoris;
        $superficie_moyenne = array_sum(array_column($favoris, 'superficie')) / $total_favoris;
    } else {
        $prix_moyen = 0;
        $superficie_moyenne = 0;
    }
    
} catch (PDOException $e) {
    $favoris = [];
    $errors[] = "Erreur lors de la récupération des favoris : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes favoris - DormQuest</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/favoris.css">
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
                <a href="annonces.php" class="header__nav-link">Annonces</a>
                <a href="candidatures.php" class="header__nav-link">Mes candidatures</a>
                <a href="dashboard-etudiant.php" class="header__nav-link">Mon dashboard</a>
                <div class="header__user">
                    <img src="<?php echo htmlspecialchars(get_user_photo()); ?>" 
                         alt="Photo de profil" 
                         class="header__user-photo"
                         onerror="this.src='images/default-avatar.png'">
                    <span class="header__user-name"><?php echo htmlspecialchars(get_user_prenom()); ?></span>
                </div>
                <a href="logout.php" class="header__btn header__btn--logout">Déconnexion</a>
            </nav>
        </div>
    </header>

    <main class="favoris-page">
        <div class="favoris-page__container">
            
            <!-- En-tête -->
            <div class="favoris-header">
                <div class="favoris-header__content">
                    <h1 class="favoris-header__title">⭐ Mes annonces favorites</h1>
                    <p class="favoris-header__subtitle">
                        Les logements qui vous intéressent le plus
                    </p>
                </div>
                <a href="annonces.php" class="favoris-header__btn">
                    🔍 Découvrir plus d'annonces
                </a>
            </div>

            <!-- Messages -->
            <?php if ($success): ?>
                <div class="alert alert--success">
                    <strong>✅ <?php echo htmlspecialchars($success); ?></strong>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert--error">
                    <strong>⚠️ Erreurs :</strong>
                    <ul class="alert__list">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (empty($favoris)): ?>
                <!-- État vide -->
                <div class="empty-state">
                    <div class="empty-state__icon">⭐</div>
                    <h2 class="empty-state__title">Aucun favori pour le moment</h2>
                    <p class="empty-state__text">
                        Parcourez les annonces et cliquez sur l'étoile pour ajouter vos logements préférés ici.
                    </p>
                    <a href="annonces.php" class="empty-state__btn">
                        Explorer les annonces
                    </a>
                </div>
            <?php else: ?>
                
                <!-- Statistiques -->
                <div class="favoris-stats">
                    <div class="stat-card">
                        <div class="stat-card__icon">⭐</div>
                        <div class="stat-card__content">
                            <div class="stat-card__value"><?php echo $total_favoris; ?></div>
                            <div class="stat-card__label">Favori<?php echo $total_favoris > 1 ? 's' : ''; ?></div>
                        </div>
                    </div>
                    <div class="stat-card stat-card--info">
                        <div class="stat-card__icon">💰</div>
                        <div class="stat-card__content">
                            <div class="stat-card__value"><?php echo number_format($prix_moyen, 0, ',', ' '); ?> €</div>
                            <div class="stat-card__label">Prix moyen</div>
                        </div>
                    </div>
                    <div class="stat-card stat-card--success">
                        <div class="stat-card__icon">📐</div>
                        <div class="stat-card__content">
                            <div class="stat-card__value"><?php echo number_format($superficie_moyenne, 0, ',', ' '); ?> m²</div>
                            <div class="stat-card__label">Superficie moyenne</div>
                        </div>
                    </div>
                </div>

                <!-- Actions groupées -->
                <div class="favoris-actions">
                    <button class="favoris-actions__btn" id="compare-btn">
                        📊 Comparer les favoris
                    </button>
                    <button class="favoris-actions__btn favoris-actions__btn--secondary" id="export-btn">
                        📥 Exporter en PDF
                    </button>
                </div>

                <!-- Liste des favoris -->
                <div class="favoris-grid">
                    <?php foreach ($favoris as $favori): ?>
                        <div class="favori-card" data-favori-id="<?php echo $favori['favori_id']; ?>">
                            
                            <!-- Badge ajouté le -->
                            <div class="favori-card__date">
                                Ajouté le <?php echo date('d/m/Y', strtotime($favori['dateAjout'])); ?>
                            </div>

                            <!-- Image -->
                            <div class="favori-card__image">
                                <img src="https://via.placeholder.com/400x250/2563eb/ffffff?text=<?php echo urlencode(substr($favori['titre'], 0, 15)); ?>" 
                                     alt="<?php echo htmlspecialchars($favori['titre']); ?>">
                                
                                <!-- Badge type -->
                                <span class="favori-card__badge">
                                    <?php 
                                    $types_labels = [
                                        'studio' => 'Studio',
                                        'colocation' => 'Colocation',
                                        'residence_etudiante' => 'Résidence',
                                        'chambre_habitant' => 'Chambre'
                                    ];
                                    echo $types_labels[$favori['typeLogement']] ?? $favori['typeLogement'];
                                    ?>
                                </span>
                            </div>

                            <!-- Contenu -->
                            <div class="favori-card__content">
                                <h3 class="favori-card__title">
                                    <?php echo htmlspecialchars($favori['titre']); ?>
                                </h3>

                                <div class="favori-card__location">
                                    📍 <?php echo htmlspecialchars($favori['ville']); ?> 
                                    (<?php echo htmlspecialchars($favori['codePostal']); ?>)
                                </div>

                                <div class="favori-card__price">
                                    <?php echo number_format($favori['prixMensuel'], 0, ',', ' '); ?> €
                                    <span class="favori-card__price-label">/mois</span>
                                </div>

                                <div class="favori-card__details">
                                    <span class="favori-card__detail">
                                        📐 <?php echo $favori['superficie']; ?> m²
                                    </span>
                                    <span class="favori-card__detail">
                                        🚪 <?php echo $favori['nombrePieces']; ?> pièce<?php echo $favori['nombrePieces'] > 1 ? 's' : ''; ?>
                                    </span>
                                </div>

                                <!-- Tags critères -->
                                <div class="favori-card__tags">
                                    <?php if ($favori['meuble']): ?>
                                        <span class="favori-card__tag">🛋️ Meublé</span>
                                    <?php endif; ?>
                                    <?php if ($favori['eligibleAPL']): ?>
                                        <span class="favori-card__tag">💰 APL</span>
                                    <?php endif; ?>
                                    <?php if ($favori['parkingDisponible']): ?>
                                        <span class="favori-card__tag">🚗 Parking</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Statut candidature -->
                                <?php if ($favori['has_candidated']): ?>
                                    <div class="favori-card__status favori-card__status--candidated">
                                        ✅ Candidature envoyée
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Actions -->
                            <div class="favori-card__actions">
                                <a href="annonce.php?id=<?php echo $favori['id']; ?>" 
                                   class="favori-card__btn favori-card__btn--view">
                                    👁️ Voir l'annonce
                                </a>
                                
                                <?php if (!$favori['has_candidated']): ?>
                                    <a href="annonce.php?id=<?php echo $favori['id']; ?>#candidature" 
                                       class="favori-card__btn favori-card__btn--apply">
                                        📨 Postuler
                                    </a>
                                <?php endif; ?>
                                
                                <button class="favori-card__btn favori-card__btn--compare" 
                                        data-annonce-id="<?php echo $favori['id']; ?>">
                                    📊 Comparer
                                </button>
                                
                                <a href="?action=remove&id=<?php echo $favori['id']; ?>" 
                                   class="favori-card__btn favori-card__btn--remove"
                                   onclick="return confirm('Êtes-vous sûr de vouloir retirer cette annonce de vos favoris ?')">
                                    ❌ Retirer
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Modal de comparaison -->
                <div class="comparison-modal" id="comparison-modal" style="display: none;">
                    <div class="comparison-modal__overlay"></div>
                    <div class="comparison-modal__content">
                        <div class="comparison-modal__header">
                            <h2 class="comparison-modal__title">📊 Comparaison des annonces</h2>
                            <button class="comparison-modal__close" id="close-modal">✕</button>
                        </div>
                        <div class="comparison-modal__body" id="comparison-body">
                            <!-- Contenu dynamique -->
                        </div>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </main>

    <footer class="footer footer--minimal">
        <div class="footer__container">
            <p class="footer__copyright">
                &copy; 2025 DormQuest by Nyzer. Tous droits réservés.
            </p>
        </div>
    </footer>

    <script>
        // Données des favoris pour JavaScript
        const favorisData = <?php echo json_encode($favoris); ?>;
    </script>
    <script src="js/favoris.js"></script>
</body>
</html>