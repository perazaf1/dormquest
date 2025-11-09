<?php
// dashboard-loueur.php - Dashboard complet loueur
session_start();

// Inclure les fonctions d'authentification
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Vérifier que l'utilisateur est un loueur connecté
require_loueur();

// Récupérer les informations complètes du loueur
$user = get_user_info($pdo);
$user_id = get_user_id();

// Gestion de la suppression d'annonce
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $annonce_id = (int)$_GET['id'];
    
    try {
        // Vérifier que l'annonce appartient bien au loueur
        $stmt = $pdo->prepare("SELECT id FROM annonces WHERE id = ? AND idLoueur = ?");
        $stmt->execute([$annonce_id, $user_id]);
        
        if ($stmt->fetch()) {
            // Supprimer l'annonce
            $stmt = $pdo->prepare("DELETE FROM annonces WHERE id = ?");
            $stmt->execute([$annonce_id]);
            
            $success_message = "Annonce supprimée avec succès.";
        } else {
            $error_message = "Cette annonce ne vous appartient pas.";
        }
    } catch (PDOException $e) {
        $error_message = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Gestion de l'archivage/réactivation d'annonce
if (isset($_GET['action']) && in_array($_GET['action'], ['archive', 'activate']) && isset($_GET['id'])) {
    $annonce_id = (int)$_GET['id'];
    $new_status = $_GET['action'] === 'archive' ? 'archivee' : 'active';
    
    try {
        // Vérifier que l'annonce appartient bien au loueur
        $stmt = $pdo->prepare("SELECT id FROM annonces WHERE id = ? AND idLoueur = ?");
        $stmt->execute([$annonce_id, $user_id]);
        
        if ($stmt->fetch()) {
            // Changer le statut
            $stmt = $pdo->prepare("UPDATE annonces SET statut = ? WHERE id = ?");
            $stmt->execute([$new_status, $annonce_id]);
            
            $success_message = $new_status === 'archivee' ? "Annonce archivée avec succès." : "Annonce réactivée avec succès.";
        } else {
            $error_message = "Cette annonce ne vous appartient pas.";
        }
    } catch (PDOException $e) {
        $error_message = "Erreur lors de la modification : " . $e->getMessage();
    }
}

// Récupérer toutes les annonces du loueur
try {
    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            COUNT(DISTINCT c.id) as nb_candidatures,
            COUNT(DISTINCT f.id) as nb_favoris
        FROM annonces a
        LEFT JOIN candidatures c ON c.idAnnonce = a.id
        LEFT JOIN favoris f ON f.idAnnonce = a.id
        WHERE a.idLoueur = ?
        GROUP BY a.id
        ORDER BY a.dateCreation DESC
    ");
    $stmt->execute([$user_id]);
    $annonces = $stmt->fetchAll();
    
    // Statistiques globales
    $total_annonces = count($annonces);
    $annonces_actives = count(array_filter($annonces, fn($a) => $a['statut'] === 'active'));
    $total_candidatures = array_sum(array_column($annonces, 'nb_candidatures'));
    
} catch (PDOException $e) {
    $annonces = [];
    $error_message = "Erreur lors de la récupération des annonces : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes annonces - DormQuest</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
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
                <a href="dashboard-loueur.php" class="header__nav-link header__nav-link--active">Mes annonces</a>
                <a href="create-annonce.php" class="header__nav-link">Créer une annonce</a>
                <a href="profil.php" class="header__nav-link">Mon profil</a>
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

    <main class="dashboard">
        <div class="dashboard__container">
            <!-- En-tête du dashboard -->
            <div class="dashboard__header">
                <div class="dashboard__header-content">
                    <h1 class="dashboard__title">Mes annonces</h1>
                    <p class="dashboard__subtitle">Gérez vos logements en quelques clics</p>
                </div>
                <a href="create-annonce.php" class="dashboard__btn dashboard__btn--primary">
                    <span class="dashboard__btn-icon">➕</span>
                    Créer une annonce
                </a>
            </div>

            <!-- Messages -->
            <?php if (isset($_GET['success']) && $_GET['success'] === 'annonce_created'): ?>
                <div class="alert alert--success">
                    <strong>✅ Annonce créée avec succès !</strong>
                    <p>Votre annonce est maintenant visible par tous les étudiants.</p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success_message)): ?>
                <div class="alert alert--success">
                    <strong>✅ <?php echo htmlspecialchars($success_message); ?></strong>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert--error">
                    <strong>⚠️ <?php echo htmlspecialchars($error_message); ?></strong>
                </div>
            <?php endif; ?>

            <!-- Statistiques -->
            <div class="dashboard__stats">
                <div class="stat-card">
                    <div class="stat-card__icon">📋</div>
                    <div class="stat-card__content">
                        <div class="stat-card__value"><?php echo $total_annonces; ?></div>
                        <div class="stat-card__label">Annonce<?php echo $total_annonces > 1 ? 's' : ''; ?> totale<?php echo $total_annonces > 1 ? 's' : ''; ?></div>
                    </div>
                </div>
                <div class="stat-card stat-card--success">
                    <div class="stat-card__icon">✅</div>
                    <div class="stat-card__content">
                        <div class="stat-card__value"><?php echo $annonces_actives; ?></div>
                        <div class="stat-card__label">Annonce<?php echo $annonces_actives > 1 ? 's' : ''; ?> active<?php echo $annonces_actives > 1 ? 's' : ''; ?></div>
                    </div>
                </div>
                <div class="stat-card stat-card--info">
                    <div class="stat-card__icon">📬</div>
                    <div class="stat-card__content">
                        <div class="stat-card__value"><?php echo $total_candidatures; ?></div>
                        <div class="stat-card__label">Candidature<?php echo $total_candidatures > 1 ? 's' : ''; ?> reçue<?php echo $total_candidatures > 1 ? 's' : ''; ?></div>
                    </div>
                </div>
            </div>

            <!-- Liste des annonces -->
            <div class="dashboard__section">
                <?php if (empty($annonces)): ?>
                    <!-- Message si aucune annonce -->
                    <div class="empty-state">
                        <div class="empty-state__icon">🏠</div>
                        <h2 class="empty-state__title">Aucune annonce pour le moment</h2>
                        <p class="empty-state__text">
                            Commencez par créer votre première annonce de logement et touchez des milliers d'étudiants !
                        </p>
                        <a href="create-annonce.php" class="empty-state__btn">
                            Créer ma première annonce
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Filtres -->
                    <div class="dashboard__filters">
                        <button class="filter-btn filter-btn--active" data-filter="all">
                            Toutes (<?php echo $total_annonces; ?>)
                        </button>
                        <button class="filter-btn" data-filter="active">
                            Actives (<?php echo $annonces_actives; ?>)
                        </button>
                        <button class="filter-btn" data-filter="archivee">
                            Archivées (<?php echo $total_annonces - $annonces_actives; ?>)
                        </button>
                    </div>

                    <!-- Grille d'annonces -->
                    <div class="annonces-grid">
                        <?php foreach ($annonces as $annonce): ?>
                            <div class="annonce-card" data-status="<?php echo $annonce['statut']; ?>">
                                <!-- Badge statut -->
                                <?php if ($annonce['statut'] === 'archivee'): ?>
                                    <span class="annonce-card__badge annonce-card__badge--archived">Archivée</span>
                                <?php else: ?>
                                    <span class="annonce-card__badge annonce-card__badge--active">Active</span>
                                <?php endif; ?>

                                <!-- Image -->
                                <div class="annonce-card__image">
                                    <?php if (!empty($annonce['contactEmail'])): ?>
                                        <img src="https://via.placeholder.com/400x250/2563eb/ffffff?text=<?php echo urlencode(substr($annonce['titre'], 0, 20)); ?>" 
                                             alt="<?php echo htmlspecialchars($annonce['titre']); ?>">
                                    <?php endif; ?>
                                </div>

                                <!-- Contenu -->
                                <div class="annonce-card__content">
                                    <h3 class="annonce-card__title">
                                        <?php echo htmlspecialchars($annonce['titre']); ?>
                                    </h3>
                                    
                                    <div class="annonce-card__info">
                                        <span class="annonce-card__info-item">
                                            📍 <?php echo htmlspecialchars($annonce['ville']); ?>
                                        </span>
                                        <span class="annonce-card__info-item">
                                            💰 <?php echo number_format($annonce['prixMensuel'], 0, ',', ' '); ?> €/mois
                                        </span>
                                        <span class="annonce-card__info-item">
                                            📐 <?php echo $annonce['superficie']; ?> m²
                                        </span>
                                    </div>

                                    <div class="annonce-card__meta">
                                        <span class="annonce-card__meta-item">
                                            <strong><?php echo $annonce['nb_candidatures']; ?></strong> candidature<?php echo $annonce['nb_candidatures'] > 1 ? 's' : ''; ?>
                                        </span>
                                        <span class="annonce-card__meta-item">
                                            <strong><?php echo $annonce['nb_favoris']; ?></strong> favori<?php echo $annonce['nb_favoris'] > 1 ? 's' : ''; ?>
                                        </span>
                                    </div>

                                    <div class="annonce-card__date">
                                        Créée le <?php echo date('d/m/Y', strtotime($annonce['dateCreation'])); ?>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="annonce-card__actions">
                                    <a href="annonce.php?id=<?php echo $annonce['id']; ?>" 
                                       class="annonce-card__btn annonce-card__btn--view"
                                       title="Voir l'annonce">
                                        👁️ Voir
                                    </a>
                                    <a href="edit-annonce.php?id=<?php echo $annonce['id']; ?>" 
                                       class="annonce-card__btn annonce-card__btn--edit"
                                       title="Modifier l'annonce">
                                        ✏️ Modifier
                                    </a>
                                    
                                    <?php if ($annonce['statut'] === 'active'): ?>
                                        <a href="?action=archive&id=<?php echo $annonce['id']; ?>" 
                                           class="annonce-card__btn annonce-card__btn--archive"
                                           onclick="return confirm('Voulez-vous vraiment archiver cette annonce ?')"
                                           title="Archiver l'annonce">
                                            📦 Archiver
                                        </a>
                                    <?php else: ?>
                                        <a href="?action=activate&id=<?php echo $annonce['id']; ?>" 
                                           class="annonce-card__btn annonce-card__btn--activate"
                                           title="Réactiver l'annonce">
                                            ✅ Réactiver
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="?action=delete&id=<?php echo $annonce['id']; ?>" 
                                       class="annonce-card__btn annonce-card__btn--delete"
                                       onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette annonce ? Cette action est irréversible.')"
                                       title="Supprimer l'annonce">
                                        🗑️ Supprimer
                                    </a>
                                </div>

                                <!-- Candidatures (si présentes) -->
                                <?php if ($annonce['nb_candidatures'] > 0): ?>
                                    <div class="annonce-card__footer">
                                        <a href="candidatures-annonce.php?id=<?php echo $annonce['id']; ?>" 
                                           class="annonce-card__footer-link">
                                            📬 Voir les <?php echo $annonce['nb_candidatures']; ?> candidature<?php echo $annonce['nb_candidatures'] > 1 ? 's' : ''; ?> →
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="footer footer--minimal">
        <div class="footer__container">
            <p class="footer__copyright">
                &copy; 2024 DormQuest by Nyzer. Tous droits réservés.
            </p>
        </div>
    </footer>

    <script src="js/dashboard-loueur.js"></script>
</body>
</html>