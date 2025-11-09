<?php
// dashboard-etudiant.php - Tableau de bord étudiant (page temporaire)
session_start();

// Inclure les fonctions d'authentification
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Vérifier que l'utilisateur est un étudiant connecté
require_etudiant();

// Récupérer les informations complètes de l'étudiant
$user = get_user_info($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Étudiant - DormQuest</title>
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
                <a href="annonces.php" class="header__nav-link">Annonces</a>
                <a href="favoris.php" class="header__nav-link">Mes favoris</a>
                <a href="candidatures.php" class="header__nav-link">Mes candidatures</a>
                <a href="profil.php" class="header__nav-link">Mon profil</a>
                <a href="logout.php" class="header__btn header__btn--logout">Déconnexion</a>
            </nav>
        </div>
    </header>

    <main class="dashboard">
        <div class="dashboard__container">
            <div class="dashboard__header">
                <h1 class="dashboard__title">
                    Bienvenue, <?php echo htmlspecialchars(get_user_prenom()); ?> ! 🎓
                </h1>
                <p class="dashboard__subtitle">
                    Voici votre tableau de bord étudiant
                </p>
            </div>

            <!-- Message de succès connexion -->
            <div class="alert alert--success">
                <strong>✅ Connexion réussie !</strong>
                <p>Vous êtes maintenant connecté en tant qu'étudiant.</p>
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
                            <p><strong>Rôle :</strong> Étudiant</p>
                            <?php if ($user): ?>
                                <p><strong>Ville recherchée :</strong> <?php echo htmlspecialchars($user['villeRecherche'] ?? 'Non renseignée'); ?></p>
                                <p><strong>Budget :</strong> <?php echo number_format($user['budget'] ?? 0, 2, ',', ' '); ?> €</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="dashboard__section">
                <h2 class="dashboard__section-title">⚡ Actions rapides</h2>
                <div class="dashboard__actions">
                    <a href="annonces.php" class="dashboard__action-card">
                        <span class="dashboard__action-icon">🔍</span>
                        <h3 class="dashboard__action-title">Rechercher un logement</h3>
                        <p class="dashboard__action-desc">Parcourez les annonces disponibles</p>
                    </a>
                    <a href="favoris.php" class="dashboard__action-card">
                        <span class="dashboard__action-icon">⭐</span>
                        <h3 class="dashboard__action-title">Mes favoris</h3>
                        <p class="dashboard__action-desc">Consultez vos annonces favorites</p>
                    </a>
                    <a href="candidatures.php" class="dashboard__action-card">
                        <span class="dashboard__action-icon">📨</span>
                        <h3 class="dashboard__action-title">Mes candidatures</h3>
                        <p class="dashboard__action-desc">Suivez vos candidatures en cours</p>
                    </a>
                    <a href="profil.php" class="dashboard__action-card">
                        <span class="dashboard__action-icon">⚙️</span>
                        <h3 class="dashboard__action-title">Mon profil</h3>
                        <p class="dashboard__action-desc">Modifiez vos informations</p>
                    </a>
                </div>
            </div>

            <!-- Note de développement -->
            <div class="dashboard__section">
                <div class="alert alert--info">
                    <strong>ℹ️ En développement</strong>
                    <p>Cette page est temporaire. Les fonctionnalités complètes seront bientôt disponibles.</p>
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
/* Styles temporaires pour le dashboard */
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
}

.user-info {
    display: flex;
    gap: 2rem;
    align-items: center;
}

.user-info__photo img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--color-primary);
}

.user-info__details p {
    margin-bottom: 0.75rem;
    color: var(--color-gray-dark);
}

.dashboard__actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.dashboard__action-card {
    background-color: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    text-align: center;
    transition: all 0.3s ease;
}

.dashboard__action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.2);
}

.dashboard__action-icon {
    font-size: 3rem;
    display: block;
    margin-bottom: 1rem;
}

.dashboard__action-title {
    font-size: 1.25rem;
    color: var(--color-primary);
    margin-bottom: 0.5rem;
}

.dashboard__action-desc {
    font-size: 0.9375rem;
    color: var(--color-gray);
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