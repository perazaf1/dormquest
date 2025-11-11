<?php
// candidatures-annonce.php - Voir les candidatures d'une annonce spécifique (LOUEUR)
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Vérifier que l'utilisateur est un loueur
require_loueur();

$annonce_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$errors = [];
$success = '';

if ($annonce_id <= 0) {
    header('Location: dashboard-loueur.php');
    exit();
}

// Traitement des actions (accepter/refuser)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $candidature_id = intval($_POST['candidature_id'] ?? 0);
    $action = $_POST['action'];
    
    try {
        // Vérifier que la candidature appartient bien à une annonce du loueur
        $stmt = $pdo->prepare("
            SELECT c.id 
            FROM candidatures c
            JOIN annonces a ON c.idAnnonce = a.id
            WHERE c.id = ? AND a.idLoueur = ?
        ");
        $stmt->execute([$candidature_id, get_user_id()]);
        
        if ($stmt->fetch()) {
            $new_statut = '';
            
            if ($action === 'accepter') {
                $new_statut = 'acceptee';
                $success = "Candidature acceptée avec succès !";
            } elseif ($action === 'refuser') {
                $new_statut = 'refusee';
                $success = "Candidature refusée.";
            }
            
            if ($new_statut) {
                $stmt = $pdo->prepare("
                    UPDATE candidatures 
                    SET statut = ?, dateReponse = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$new_statut, $candidature_id]);
            }
        } else {
            $errors[] = "Candidature introuvable.";
        }
    } catch (PDOException $e) {
        $errors[] = "Erreur : " . $e->getMessage();
    }
}

// Récupérer l'annonce et ses candidatures
try {
    // Vérifier que l'annonce appartient au loueur
    $stmt = $pdo->prepare("
        SELECT * FROM annonces 
        WHERE id = ? AND idLoueur = ?
    ");
    $stmt->execute([$annonce_id, get_user_id()]);
    $annonce = $stmt->fetch();
    
    if (!$annonce) {
        header('Location: dashboard-loueur.php');
        exit();
    }
    
    // Récupérer toutes les candidatures de cette annonce
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            u.prenom AS etudiant_prenom,
            u.nom AS etudiant_nom,
            u.email AS etudiant_email,
            u.telephone AS etudiant_telephone,
            u.photoDeProfil AS etudiant_photo,
            e.villeRecherche,
            e.budget
        FROM candidatures c
        JOIN utilisateurs u ON c.idEtudiant = u.id
        LEFT JOIN etudiants e ON e.user_id = u.id
        WHERE c.idAnnonce = ?
        ORDER BY 
            CASE c.statut
                WHEN 'en_attente' THEN 1
                WHEN 'acceptee' THEN 2
                WHEN 'refusee' THEN 3
                ELSE 4
            END,
            c.dateEnvoi DESC
    ");
    $stmt->execute([$annonce_id]);
    $candidatures = $stmt->fetchAll();
    
    // Statistiques
    $total = count($candidatures);
    $en_attente = count(array_filter($candidatures, fn($c) => $c['statut'] === 'en_attente'));
    $acceptees = count(array_filter($candidatures, fn($c) => $c['statut'] === 'acceptee'));
    $refusees = count(array_filter($candidatures, fn($c) => $c['statut'] === 'refusee'));
    
} catch (PDOException $e) {
    header('Location: dashboard-loueur.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatures - <?php echo htmlspecialchars($annonce['titre']); ?></title>
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
                <a href="dashboard-loueur.php" class="header__nav-link">← Mes annonces</a>
                <a href="annonce.php?id=<?php echo $annonce_id; ?>" class="header__nav-link">Voir l'annonce</a>
                <a href="logout.php" class="header__btn header__btn--logout">Déconnexion</a>
            </nav>
        </div>
    </header>

    <main class="candidatures-page">
        <div class="candidatures-page__container">
            
            <!-- En-tête -->
            <div class="candidatures-header">
                <div class="candidatures-header__content">
                    <h1 class="candidatures-header__title">Candidatures reçues</h1>
                    <p class="candidatures-header__subtitle">
                        Pour l'annonce : <strong><?php echo htmlspecialchars($annonce['titre']); ?></strong>
                    </p>
                </div>
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
                    <h2 class="empty-state__title">Aucune candidature pour le moment</h2>
                    <p class="empty-state__text">
                        Les étudiants intéressés pourront postuler à votre annonce.
                    </p>
                    <a href="annonce.php?id=<?php echo $annonce_id; ?>" class="empty-state__btn">
                        Voir l'annonce
                    </a>
                </div>
            <?php else: ?>
                <div class="candidatures-list">
                    <?php foreach ($candidatures as $candidature): ?>
                        <div class="candidature-card candidature-card--<?php echo $candidature['statut']; ?>">
                            
                            <!-- Badge statut -->
                            <div class="candidature-card__badge candidature-card__badge--<?php echo $candidature['statut']; ?>">
                                <?php
                                $statuts = [
                                    'en_attente' => '⏳ En attente',
                                    'acceptee' => '✅ Acceptée',
                                    'refusee' => '❌ Refusée',
                                    'annulee' => '🚫 Annulée'
                                ];
                                echo $statuts[$candidature['statut']] ?? $candidature['statut'];
                                ?>
                            </div>

                            <div class="candidature-card__content">
                                
                                <!-- Profil étudiant -->
                                <div class="candidature-profile">
                                    <img src="<?php echo htmlspecialchars($candidature['etudiant_photo'] ?? 'images/default-avatar.png'); ?>" 
                                         alt="Photo de profil" 
                                         class="candidature-profile__photo"
                                         onerror="this.src='images/default-avatar.png'">
                                    
                                    <div class="candidature-profile__info">
                                        <h3 class="candidature-profile__name">
                                            <?php echo htmlspecialchars($candidature['etudiant_prenom'] . ' ' . $candidature['etudiant_nom']); ?>
                                        </h3>
                                        <div class="candidature-profile__details">
                                            <span class="candidature-profile__detail">
                                                📧 <?php echo htmlspecialchars($candidature['etudiant_email']); ?>
                                            </span>
                                            <?php if ($candidature['etudiant_telephone']): ?>
                                                <span class="candidature-profile__detail">
                                                    📞 <?php echo htmlspecialchars($candidature['etudiant_telephone']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($candidature['villeRecherche']): ?>
                                                <span class="candidature-profile__detail">
                                                    📍 Recherche à <?php echo htmlspecialchars($candidature['villeRecherche']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($candidature['budget']): ?>
                                                <span class="candidature-profile__detail">
                                                    💰 Budget : <?php echo number_format($candidature['budget'], 0, ',', ' '); ?> €/mois
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Message de motivation -->
                                <div class="candidature-message">
                                    <h4 class="candidature-message__title">💬 Message de motivation</h4>
                                    <div class="candidature-message__content">
                                        <?php echo nl2br(htmlspecialchars($candidature['message'])); ?>
                                    </div>
                                </div>

                                <!-- Dates -->
                                <div class="candidature-dates">
                                    <span class="candidature-dates__item">
                                        <strong>Envoyé le :</strong> 
                                        <?php echo date('d/m/Y à H:i', strtotime($candidature['dateEnvoi'])); ?>
                                    </span>
                                    <?php if ($candidature['dateReponse']): ?>
                                        <span class="candidature-dates__item">
                                            <strong>Répondu le :</strong> 
                                            <?php echo date('d/m/Y à H:i', strtotime($candidature['dateReponse'])); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Actions (si en attente) -->
                                <?php if ($candidature['statut'] === 'en_attente'): ?>
                                    <div class="candidature-actions">
                                        <form method="POST" class="candidature-actions__form">
                                            <input type="hidden" name="candidature_id" value="<?php echo $candidature['id']; ?>">
                                            <button type="submit" name="action" value="accepter" 
                                                    class="candidature-actions__btn candidature-actions__btn--accept"
                                                    onclick="return confirm('✅ Accepter cette candidature ?')">
                                                ✅ Accepter
                                            </button>
                                            <button type="submit" name="action" value="refuser" 
                                                    class="candidature-actions__btn candidature-actions__btn--refuse"
                                                    onclick="return confirm('❌ Refuser cette candidature ?')">
                                                ❌ Refuser
                                            </button>
                                        </form>
                                        <a href="mailto:<?php echo htmlspecialchars($candidature['etudiant_email']); ?>" 
                                           class="candidature-actions__btn candidature-actions__btn--contact">
                                            📧 Contacter
                                        </a>
                                    </div>
                                <?php endif; ?>
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