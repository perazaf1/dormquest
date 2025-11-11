<?php
// mes-candidatures.php - Voir toutes mes candidatures (ÉTUDIANT)
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Vérifier que l'utilisateur est un étudiant
require_etudiant();

$errors = [];
$success = '';

// Traitement de l'annulation d'une candidature
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'annuler') {
    $candidature_id = intval($_POST['candidature_id'] ?? 0);
    
    try {
        // Vérifier que la candidature appartient à l'étudiant et est en attente
        $stmt = $pdo->prepare("
            SELECT id FROM candidatures 
            WHERE id = ? AND idEtudiant = ? AND statut = 'en_attente'
        ");
        $stmt->execute([$candidature_id, get_user_id()]);
        
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare("
                UPDATE candidatures 
                SET statut = 'annulee' 
                WHERE id = ?
            ");
            $stmt->execute([$candidature_id]);
            
            $success = "Candidature annulée avec succès.";
        } else {
            $errors[] = "Impossible d'annuler cette candidature.";
        }
    } catch (PDOException $e) {
        $errors[] = "Erreur : " . $e->getMessage();
    }
}

// Récupérer toutes les candidatures de l'étudiant
try {
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            a.id AS annonce_id,
            a.titre AS annonce_titre,
            a.ville AS annonce_ville,
            a.codePostal AS annonce_code_postal,
            a.prixMensuel AS annonce_prix,
            a.typeLogement AS annonce_type,
            a.statut AS annonce_statut,
            u.prenom AS loueur_prenom,
            u.nom AS loueur_nom,
            u.email AS loueur_email,
            u.telephone AS loueur_telephone
        FROM candidatures c
        JOIN annonces a ON c.idAnnonce = a.id
        JOIN utilisateurs u ON a.idLoueur = u.id
        WHERE c.idEtudiant = ?
        ORDER BY 
            CASE c.statut
                WHEN 'en_attente' THEN 1
                WHEN 'acceptee' THEN 2
                WHEN 'refusee' THEN 3
                WHEN 'annulee' THEN 4
            END,
            c.dateEnvoi DESC
    ");
    $stmt->execute([get_user_id()]);
    $candidatures = $stmt->fetchAll();
    
    // Statistiques
    $total = count($candidatures);
    $en_attente = count(array_filter($candidatures, fn($c) => $c['statut'] === 'en_attente'));
    $acceptees = count(array_filter($candidatures, fn($c) => $c['statut'] === 'acceptee'));
    $refusees = count(array_filter($candidatures, fn($c) => $c['statut'] === 'refusee'));
    $annulees = count(array_filter($candidatures, fn($c) => $c['statut'] === 'annulee'));
    
} catch (PDOException $e) {
    $candidatures = [];
    $errors[] = "Erreur lors de la récupération des candidatures.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes candidatures - DormQuest</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/candidatures.css">
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
                <a href="favoris.php" class="header__nav-link">Mes favoris</a>
                <a href="candidatures.php" class="header__nav-link header__nav-link--active">Mes candidatures</a>
                <a href="dashboard-etudiant.php" class="header__nav-link">Dashboard</a>
                <a href="logout.php" class="header__btn header__btn--logout">Déconnexion</a>
            </nav>
        </div>
    </header>

    <main class="candidatures-page">
        <div class="candidatures-page__container">
            
            <!-- En-tête -->
            <div class="candidatures-header">
                <div class="candidatures-header__content">
                    <h1 class="candidatures-header__title">Mes candidatures</h1>
                    <p class="candidatures-header__subtitle">
                        Suivez l'état de vos candidatures en temps réel
                    </p>
                </div>
                <a href="annonces.php" class="dashboard__btn dashboard__btn--primary">
                    <span class="dashboard__btn-icon">🔍</span>
                    Voir les annonces
                </a>
            </div>

            <!-- Messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert--error">
                    <strong>⚠️ Erreur :</strong>
                    <ul class="alert__list">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert--success">
                    <strong>✅ <?php echo htmlspecialchars($success); ?></strong>
                </div>
            <?php endif; ?>

            <!-- Statistiques -->
            <div class="candidatures-stats">
                <div class="stat-card">
                    <div class="stat-card__icon">📨</div>
                    <div class="stat-card__content">
                        <div class="stat-card__value"><?php echo $total; ?></div>
                        <div class="stat-card__label">Total</div>
                    </div>
                </div>
                <div class="stat-card stat-card--warning">
                    <div class="stat-card__icon">⏳</div>
                    <div class="stat-card__content">
                        <div class="stat-card__value"><?php echo $en_attente; ?></div>
                        <div class="stat-card__label">En attente</div>
                    </div>
                </div>
                <div class="stat-card stat-card--success">
                    <div class="stat-card__icon">✅</div>
                    <div class="stat-card__content">
                        <div class="stat-card__value"><?php echo $acceptees; ?></div>
                        <div class="stat-card__label">Acceptées</div>
                    </div>
                </div>
                <div class="stat-card stat-card--danger">
                    <div class="stat-card__icon">❌</div>
                    <div class="stat-card__content">
                        <div class="stat-card__value"><?php echo $refusees; ?></div>
                        <div class="stat-card__label">Refusées</div>
                    </div>
                </div>
            </div>

            <!-- Liste des candidatures -->
            <?php if (empty($candidatures)): ?>
                <div class="empty-state">
                    <div class="empty-state__icon">📭</div>
                    <h2 class="empty-state__title">Aucune candidature envoyée</h2>
                    <p class="empty-state__text">
                        Commencez par parcourir nos annonces et postulez aux logements qui vous intéressent !
                    </p>
                    <a href="annonces.php" class="empty-state__btn">
                        Voir les annonces
                    </a>
                </div>
            <?php else: ?>
                <!-- Filtres -->
                <div class="candidatures-filters">
                    <button class="filter-btn filter-btn--active" data-filter="all">
                        Toutes (<?php echo $total; ?>)
                    </button>
                    <button class="filter-btn" data-filter="en_attente">
                        En attente (<?php echo $en_attente; ?>)
                    </button>
                    <button class="filter-btn" data-filter="acceptee">
                        Acceptées (<?php echo $acceptees; ?>)
                    </button>
                    <button class="filter-btn" data-filter="refusee">
                        Refusées (<?php echo $refusees; ?>)
                    </button>
                </div>

                <div class="candidatures-list">
                    <?php foreach ($candidatures as $candidature): ?>
                        <div class="candidature-card-etudiant candidature-card-etudiant--<?php echo $candidature['statut']; ?>" 
                             data-status="<?php echo $candidature['statut']; ?>">
                            
                            <!-- Badge statut -->
                            <div class="candidature-card__badge candidature-card__badge--<?php echo $candidature['statut']; ?>">
                                <?php
                                $statuts = [
                                    'en_attente' => '⏳ En attente de réponse',
                                    'acceptee' => '✅ Candidature acceptée',
                                    'refusee' => '❌ Candidature refusée',
                                    'annulee' => '🚫 Candidature annulée'
                                ];
                                echo $statuts[$candidature['statut']] ?? $candidature['statut'];
                                ?>
                            </div>

                            <div class="candidature-card-etudiant__content">
                                
                                <!-- Infos annonce -->
                                <div class="candidature-annonce">
                                    <div class="candidature-annonce__header">
                                        <h3 class="candidature-annonce__title">
                                            <a href="annonce.php?id=<?php echo $candidature['annonce_id']; ?>">
                                                <?php echo htmlspecialchars($candidature['annonce_titre']); ?>
                                            </a>
                                        </h3>
                                        <span class="candidature-annonce__type">
                                            <?php
                                            $types = [
                                                'studio' => 'Studio',
                                                'colocation' => 'Colocation',
                                                'residence_etudiante' => 'Résidence',
                                                'chambre_habitant' => 'Chambre'
                                            ];
                                            echo $types[$candidature['annonce_type']] ?? $candidature['annonce_type'];
                                            ?>
                                        </span>
                                    </div>
                                    
                                    <div class="candidature-annonce__details">
                                        <span>📍 <?php echo htmlspecialchars($candidature['annonce_ville']); ?></span>
                                        <span>💰 <?php echo number_format($candidature['annonce_prix'], 0, ',', ' '); ?> €/mois</span>
                                    </div>

                                    <?php if ($candidature['annonce_statut'] !== 'active'): ?>
                                        <div class="candidature-annonce__warning">
                                            ⚠️ Cette annonce n'est plus active
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Mon message -->
                                <div class="candidature-message candidature-message--collapsed">
                                    <h4 class="candidature-message__title">
                                        💬 Mon message
                                        <button class="candidature-message__toggle" data-toggle>Voir</button>
                                    </h4>
                                    <div class="candidature-message__content" data-content>
                                        <?php echo nl2br(htmlspecialchars($candidature['message'])); ?>
                                    </div>
                                </div>

                                <!-- Contact loueur (si acceptée) -->
                                <?php if ($candidature['statut'] === 'acceptee'): ?>
                                    <div class="candidature-contact">
                                        <h4 class="candidature-contact__title">👤 Contact du loueur</h4>
                                        <div class="candidature-contact__info">
                                            <span><?php echo htmlspecialchars($candidature['loueur_prenom'] . ' ' . $candidature['loueur_nom']); ?></span>
                                            <a href="mailto:<?php echo htmlspecialchars($candidature['loueur_email']); ?>">
                                                📧 <?php echo htmlspecialchars($candidature['loueur_email']); ?>
                                            </a>
                                            <?php if ($candidature['loueur_telephone']): ?>
                                                <a href="tel:<?php echo htmlspecialchars($candidature['loueur_telephone']); ?>">
                                                    📞 <?php echo htmlspecialchars($candidature['loueur_telephone']); ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Dates -->
                                <div class="candidature-dates">
                                    <span class="candidature-dates__item">
                                        <strong>Envoyé le :</strong> 
                                        <?php echo date('d/m/Y à H:i', strtotime($candidature['dateEnvoi'])); ?>
                                    </span>
                                    <?php if ($candidature['dateReponse']): ?>
                                        <span class="candidature-dates__item">
                                            <strong>Réponse le :</strong> 
                                            <?php echo date('d/m/Y à H:i', strtotime($candidature['dateReponse'])); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Actions -->
                                <div class="candidature-actions">
                                    <a href="annonce.php?id=<?php echo $candidature['annonce_id']; ?>" 
                                       class="candidature-actions__btn candidature-actions__btn--view">
                                        👁️ Voir l'annonce
                                    </a>
                                    
                                    <?php if ($candidature['statut'] === 'en_attente'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="candidature_id" value="<?php echo $candidature['id']; ?>">
                                            <button type="submit" name="action" value="annuler" 
                                                    class="candidature-actions__btn candidature-actions__btn--cancel"
                                                    onclick="return confirm('⚠️ Voulez-vous vraiment annuler cette candidature ?')">
                                                🚫 Annuler ma candidature
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($candidature['statut'] === 'acceptee'): ?>
                                        <a href="mailto:<?php echo htmlspecialchars($candidature['loueur_email']); ?>" 
                                           class="candidature-actions__btn candidature-actions__btn--contact">
                                            📧 Contacter le loueur
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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

    <script src="js/candidatures.js"></script>
</body>
</html>