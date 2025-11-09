<?php
// dashboard-loueur.php - Tableau de bord loueur
session_start();

require_once 'includes/auth.php';
require_once 'includes/db.php';

// Vérifie que l'utilisateur est un loueur connecté
require_loueur();

// Récupération des infos complètes du loueur
$user = get_user_info($pdo);

// Récupération des annonces créées par ce loueur
$stmt = $pdo->prepare("SELECT * FROM annonces WHERE idLoueur = ?");
$stmt->execute([$user['id']]);
$annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Loueur - DormQuest</title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/forms.css">
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
                <a href="dashboard-loueur.php" class="header__nav-link">Tableau de bord</a>
                <a href="deposer-annonce.php" class="header__nav-link">Déposer une annonce</a>
                <a href="profil.php" class="header__nav-link">Mon profil</a>
                <a href="logout.php" class="header__btn header__btn--logout">Déconnexion</a>
            </nav>
        </div>
    </header>

    <main class="dashboard">
        <div class="dashboard__container">
            <div class="dashboard__header">
                <h1 class="dashboard__title">
                    Bienvenue, <?php echo htmlspecialchars(get_user_prenom()); ?> 👋
                </h1>
                <p class="dashboard__subtitle">
                    Gérez vos annonces et vos candidatures
                </p>
            </div>

            <!-- Message de succès connexion -->
            <div class="alert alert--success">
                <strong>✅ Connexion réussie !</strong>
                <p>Vous êtes maintenant connecté en tant que loueur.</p>
            </div>

            <!-- Informations du compte -->
            <div class="dashboard__section">
                <h2 class="dashboard__section-title">📋 Mes informations</h2>
                <div class="dashboard__card">
                    <div class="user-info">
                        <div class="user-info__photo">
                            <img src="<?php echo htmlspecialchars(get_user_photo()); ?>" 
                                 alt="Photo de profil" 
                                 onerror="this.src='images/default-avatar.png'">
                        </div>
                        <div class="user-info__details">
                            <p><strong>Nom :</strong> <?php echo htmlspecialchars(get_user_fullname()); ?></p>
                            <p><strong>Email :</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                            <p><strong>Rôle :</strong> Loueur</p>
                            <?php if ($user): ?>
                                <p><strong>Entreprise / Nom du bailleur :</strong> <?php echo htmlspecialchars($user['nomEntreprise'] ?? 'Non renseigné'); ?></p>
                                <p><strong>Ville principale :</strong> <?php echo htmlspecialchars($user['ville'] ?? 'Non renseignée'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des annonces -->
            <div class="dashboard__section">
                <h2 class="dashboard__section-title">🏘️ Mes annonces</h2>

                <div class="dashboard__actions">
                    <a href="deposer-annonce.php" class="dashboard__action-card">
                        <span class="dashboard__action-icon">➕</span>
                        <h3 class="dashboard__action-title">Déposer une annonce</h3>
                        <p class="dashboard__action-desc">Publiez une nouvelle offre de logement</p>
                    </a>
                </div>

                <?php if (count($annonces) > 0): ?>
                    <div class="dashboard__list">
                        <?php foreach ($annonces as $annonce): ?>
                            <div class="dashboard__card annonce-card">
                                <div class="annonce-card__content">
                                    <h3 class="annonce-card__title"><?php echo htmlspecialchars($annonce['titre']); ?></h3>
                                    <p class="annonce-card__desc"><?php echo htmlspecialchars(substr($annonce['description'], 0, 150)); ?>...</p>
                                    <p class="annonce-card__info">
                                        <strong>Ville :</strong> <?php echo htmlspecialchars($annonce['ville']); ?> |
                                        <strong>Loyer :</strong> <?php echo number_format($annonce['loyer'], 2, ',', ' '); ?> €
                                    </p>
                                </div>
                                <div class="annonce-card__actions">
                                    <a href="modifier-annonce.php?id=<?php echo $annonce['id']; ?>" class="btn btn--primary">Modifier</a>
                                    <a href="supprimer-annonce.php?id=<?php echo $annonce['id']; ?>" class="btn btn--danger" onclick="return confirm('Supprimer cette annonce ?')">Supprimer</a>
                                    <a href="archiver-annonce.php?id=<?php echo $annonce['id']; ?>" class="btn btn--secondary">Archiver</a>
                                </div>

                                <!-- Candidatures reçues -->
                                <?php
                                $stmtC = $pdo->prepare("SELECT COUNT(*) FROM candidatures WHERE idAnnonce = ?");
                                $stmtC->execute([$annonce['id']]);
                                $nbCandidatures = $stmtC->fetchColumn();
                                ?>
                                <div class="annonce-card__footer">
                                    <p><strong>📨 Candidatures reçues :</strong> <?php echo $nbCandidatures; ?></p>
                                    <?php if ($nbCandidatures > 0): ?>
                                        <a href="candidatures-annonce.php?id=<?php echo $annonce['id']; ?>" class="btn btn--small btn--info">Voir les candidatures</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert--info">
                        <strong>Aucune annonce publiée.</strong>
                        <p>Commencez dès maintenant en déposant votre première annonce.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Note de développement -->
            <div class="dashboard__section">
                <div class="alert alert--info">
                    <strong>ℹ️ En développement</strong>
                    <p>Des fonctionnalités supplémentaires seront bientôt disponibles pour les loueurs (statistiques, messagerie, gestion avancée des candidatures).</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer footer--minimal">
        <div class="footer__container">
            <p class="footer__copyright">
                &copy; 2025 DormQuest by Nyzer. Tous droits réservés.
            </p>
        </div>
    </footer>
</body>
</html>

<style>
.dashboard {
    min-height: calc(100vh - 200px);
    padding: 3rem 0;
    background-color: #f3f4f6;
}

.dashboard__container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.dashboard__header {
    text-align: center;
    margin-bottom: 3rem;
}

.dashboard__title {
    font-size: 2.5rem;
    color: var(--color-primary);
    margin-bottom: 0.5rem;
}

.dashboard__subtitle {
    font-size: 1.125rem;
    color: var(--color-gray);
}

.dashboard__section {
    margin-bottom: 3rem;
}

.dashboard__section-title {
    font-size: 1.5rem;
    color: var(--color-primary);
    margin-bottom: 1.5rem;
}

.dashboard__card {
    background-color: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 1.5rem;
}

.annonce-card__content {
    margin-bottom: 1rem;
}

.annonce-card__title {
    font-size: 1.25rem;
    color: var(--color-primary-dark);
    margin-bottom: 0.5rem;
}

.annonce-card__desc {
    color: var(--color-gray-dark);
    margin-bottom: 0.75rem;
}

.annonce-card__actions {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn--primary { background-color: var(--color-primary); color: white; }
.btn--primary:hover { background-color: var(--color-primary-dark); }

.btn--danger { background-color: #dc2626; color: white; }
.btn--danger:hover { background-color: #991b1b; }

.btn--secondary { background-color: #e5e7eb; color: #111827; }
.btn--secondary:hover { background-color: #d1d5db; }

.btn--info { background-color: #2563eb; color: white; }
.btn--info:hover { background-color: #1e40af; }

.btn--small { font-size: 0.875rem; padding: 0.4rem 0.8rem; }

.annonce-card__footer {
    margin-top: 1rem;
    border-top: 1px solid #e5e7eb;
    padding-top: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.alert--info {
    background-color: #dbeafe;
    border-color: var(--color-primary);
    color: #1e40af;
}

.header__btn--logout {
    background-color: #dc2626;
    color: white;
}

.header__btn--logout:hover {
    background-color: #991b1b;
}
</style>
