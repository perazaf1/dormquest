<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Récupérer les annonces filtrées si applicable
$ville = $_GET['ville'] ?? '';
$typeLogement = $_GET['type'] ?? '';
$budget_max = $_GET['budget'] ?? '';
$annonces = [];
$error = null;

try {
    $query = "SELECT id, titre, ville, prixMensuel AS prixMensuel FROM annonces WHERE 1=1";
    $params = [];

    if ($ville) {
        $query .= " AND ville LIKE ?";
        $params[] = "%$ville%";
    }
    if ($typeLogement) {
        $query .= " AND typeLogement = ?";
        $params[] = $typeLogement;
    }
    if ($budget_max) {
        $query .= " AND prixMensuel <= ?";
        $params[] = $budget_max;
    }

    $query .= " ORDER BY dateCreation DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Annonces - DormQuest</title>
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="css/styles.css">
<link rel="stylesheet" href="css/forms.css">
</head>
<body>
<header class="header">
    <div class="header__container">
        <a href="index.php" class="header__logo">
            <img src="images/logo-dormquest.png" alt="DormQuest Logo" class="header__logo-img">
            <span class="header__logo-text">DormQuest</span>
        </a>
        <nav class="header__nav">
            <a href="dashboard-loueur.php" class="header__nav-link">Dashboard Loueur</a>
            <a href="dashboard-etudiant.php" class="header__nav-link">Dashboard Étudiant</a>
            <a href="profil.php" class="header__nav-link">Mon profil</a>
            <a href="logout.php" class="header__btn header__btn--login">Déconnexion</a>
        </nav>
    </div>
</header>

<main class="apercu-annonces">
    <div class="apercu-annonces__container" style="padding:2rem 0;">
        <h1 class="apercu-annonces__title">Liste des Annonces</h1>
        <p class="apercu-annonces__subtitle">Trouvez le logement qui vous convient.</p>

        <!-- Formulaire de recherche et filtres -->
        <form method="get" class="form form--filters" style="margin:2rem 0; display:flex; gap:1rem; flex-wrap:wrap;">
            <input type="text" name="ville" placeholder="Ville ou code postal" class="form__input" value="<?php echo htmlspecialchars($ville); ?>">
            <select name="type" class="form__select">
                <option value="">Type de logement</option>
                <option value="studio" <?php if($typeLogement==='studio') echo 'selected'; ?>>Studio</option>
                <option value="T1" <?php if($typeLogement==='T1') echo 'selected'; ?>>T1</option>
                <option value="T2" <?php if($typeLogement==='T2') echo 'selected'; ?>>T2</option>
            </select>
            <input type="number" name="budget" placeholder="Budget max (€)" class="form__input" value="<?php echo htmlspecialchars($budget_max); ?>">
            <button type="submit" class="hero__btn hero__btn--primary">Filtrer</button>
        </form>

        <?php if ($error): ?>
            <div class="alert alert--error annonce-card">
                <strong>Erreur :</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="apercu-annonces__cards" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:1.5rem;">
            <?php if (empty($annonces)): ?>
                <p>Aucune annonce trouvée pour vos critères.</p>
            <?php else: ?>
                <?php foreach($annonces as $ann): ?>
                    <article class="apercu-annonces__card annonce-card" style="background:var(--color-white); border-radius:var(--border-radius); box-shadow:0 4px 12px rgba(0,0,0,0.08); overflow:hidden; transition:var(--transition);">
                        <?php if($ann['photo_principale']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($ann['photo_principale']); ?>" alt="<?php echo htmlspecialchars($ann['titre']); ?>" class="apercu-annonces__card-img" style="width:100%; height:180px; object-fit:cover;">
                        <?php endif; ?>
                        <div class="apercu-annonces__card-content" style="padding:1rem;">
                            <h3 class="apercu-annonces__card-title" style="font-size:1.125rem; color:var(--color-primary); margin-bottom:0.5rem;"><?php echo htmlspecialchars($ann['titre']); ?></h3>
                            <p class="apercu-annonces__card-price" style="margin-bottom:0.5rem; color:var(--color-gray-dark); font-weight:600;"><?php echo number_format($ann['prixMensuel'],2,',',' '); ?> €/mois</p>
                            <p class="apercu-annonces__card-city" style="margin-bottom:0.5rem; color:var(--color-gray);"><?php echo htmlspecialchars($ann['ville']); ?></p>
                            <a href="detail-annonce.php?id=<?php echo urlencode($ann['id']); ?>" class="hero__btn hero__btn--secondary" style="display:inline-block;">Voir plus</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="footer__container">
        <p class="footer__copyright">&copy; <?php echo date('Y'); ?> DormQuest. Tous droits réservés.</p>
    </div>
</footer>

<script>
// Effet hover sur cartes annonces
document.querySelectorAll('.apercu-annonces__card').forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-4px)';
        card.style.boxShadow = '0 8px 20px rgba(0,0,0,0.1)';
    });
    card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0)';
        card.style.boxShadow = '0 4px 12px rgba(0,0,0,0.08)';
    });
});
</script>

</body>
</html>