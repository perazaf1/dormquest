<?php
// deposer-annonce.php – Création d’une annonce par un loueur
session_start();

require_once 'includes/auth.php';
require_once 'includes/db.php';

// Vérifie que l’utilisateur est un loueur
require_loueur();

// Récupération info loueur
$user = get_user_info($pdo);

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $codePostal = trim($_POST['codePostal'] ?? '');
    $typeLogement = trim($_POST['typeLogement'] ?? '');
    $prix = floatval($_POST['prix'] ?? 0);
    $superficie = floatval($_POST['superficie'] ?? 0);
    $nbPieces = intval($_POST['nbPieces'] ?? 0);
    $disponibilite = $_POST['disponibilite'] ?? null;
    $pmr = isset($_POST['pmr']) ? 1 : 0;
    $apl = isset($_POST['apl']) ? 1 : 0;

    // Validation basique
    if (!$titre || !$description || !$adresse || !$ville || !$codePostal || !$typeLogement || $prix <= 0 || $superficie <= 0 || $nbPieces <= 0 || !$disponibilite) {
        $error = "Tous les champs obligatoires doivent être remplis.";
    } elseif (empty($_FILES['photos']['name'][0]) || count(array_filter($_FILES['photos']['name'])) < 2) {
        $error = "Veuillez télécharger au moins deux photos.";
    } else {
        // Enregistrement de l’annonce
        $stmt = $pdo->prepare("INSERT INTO annonces (idLoueur, titre, description, adresse, ville, codePostal, typeLogement, loyer, surface, nbPieces, disponibilite, pmr, apl, dateCreation)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$user['id'], $titre, $description, $adresse, $ville, $codePostal, $typeLogement, $prix, $superficie, $nbPieces, $disponibilite, $pmr, $apl]);
        $idAnnonce = $pdo->lastInsertId();

        // Gestion upload
        $uploadDir = 'uploads/annonces/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        foreach ($_FILES['photos']['tmp_name'] as $i => $tmpName) {
            if (is_uploaded_file($tmpName)) {
                $fileName = uniqid('photo_') . '_' . basename($_FILES['photos']['name'][$i]);
                $target = $uploadDir . $fileName;
                move_uploaded_file($tmpName, $target);
                $stmtPhoto = $pdo->prepare("INSERT INTO photos_annonces (idAnnonce, chemin) VALUES (?, ?)");
                $stmtPhoto->execute([$idAnnonce, $target]);
            }
        }

        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déposer une annonce - DormQuest</title>
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
            <a href="dashboard-loueur.php" class="header__nav-link">Tableau de bord</a>
            <a href="logout.php" class="header__btn header__btn--logout">Déconnexion</a>
        </nav>
    </div>
</header>

<main class="form-page">
    <div class="form-page__container">
        <h1 class="form-page__title">📝 Déposer une nouvelle annonce</h1>
        <p class="form-page__subtitle">Remplissez tous les champs ci-dessous pour publier votre logement</p>

        <?php if ($success): ?>
            <div class="alert alert--success">
                <strong>✅ Annonce enregistrée !</strong>
                <p>Votre annonce a bien été créée.</p>
            </div>
        <?php elseif ($error): ?>
            <div class="alert alert--error">
                <strong>⚠️ Erreur :</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form class="form form--create-annonce" method="post" enctype="multipart/form-data" id="formAnnonce">
            <div class="form__group">
                <label for="titre">Titre *</label>
                <input type="text" id="titre" name="titre" required>
            </div>

            <div class="form__group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" rows="5" required></textarea>
            </div>

            <div class="form__grid">
                <div class="form__group">
                    <label for="adresse">Adresse *</label>
                    <input type="text" id="adresse" name="adresse" required>
                </div>
                <div class="form__group">
                    <label for="ville">Ville *</label>
                    <input type="text" id="ville" name="ville" required>
                </div>
                <div class="form__group">
                    <label for="codePostal">Code postal *</label>
                    <input type="text" id="codePostal" name="codePostal" pattern="\d{5}" required>
                </div>
            </div>

            <div class="form__grid">
                <div class="form__group">
                    <label for="typeLogement">Type de logement *</label>
                    <select id="typeLogement" name="typeLogement" required>
                        <option value="">Sélectionner</option>
                        <option value="Studio">Studio</option>
                        <option value="Appartement">Appartement</option>
                        <option value="Maison">Maison</option>
                        <option value="Colocation">Colocation</option>
                    </select>
                </div>
                <div class="form__group">
                    <label for="prix">Prix (€) *</label>
                    <input type="number" id="prix" name="prix" step="0.01" required>
                </div>
                <div class="form__group">
                    <label for="superficie">Superficie (m²) *</label>
                    <input type="number" id="superficie" name="superficie" required>
                </div>
                <div class="form__group">
                    <label for="nbPieces">Nombre de pièces *</label>
                    <input type="number" id="nbPieces" name="nbPieces" required>
                </div>
                <div class="form__group">
                    <label for="disponibilite">Disponible à partir du *</label>
                    <input type="date" id="disponibilite" name="disponibilite" required>
                </div>
            </div>

            <div class="form__group form__group--checkboxes">
                <label>Critères optionnels :</label>
                <label><input type="checkbox" name="pmr"> Accès PMR</label>
                <label><input type="checkbox" name="apl"> Éligible APL</label>
            </div>

            <div class="form__group">
                <label for="photos">Photos (min. 2) *</label>
                <input type="file" id="photos" name="photos[]" accept="image/*" multiple required>
            </div>

            <div class="form__actions">
                <button type="submit" class="btn btn--primary">Enregistrer</button>
                <a href="dashboard-loueur.php" class="btn btn--secondary">Annuler</a>
            </div>
        </form>
    </div>
</main>

<footer class="footer footer--minimal">
    <div class="footer__container">
        <p class="footer__copyright">&copy; 2025 DormQuest by Nyzer. Tous droits réservés.</p>
    </div>
</footer>

<script>
document.getElementById('formAnnonce').addEventListener('submit', function(e) {
    const photos = document.getElementById('photos').files;
    if (photos.length < 2) {
        e.preventDefault();
        alert("Veuillez sélectionner au moins deux photos.");
    }
});
</script>

<style>
.form-page {
    min-height: calc(100vh - 200px);
    background-color: #f3f4f6;
    padding: 3rem 0;
}
.form-page__container {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    padding: 2.5rem;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    animation: fadeIn 0.4s ease-in-out;
}
@keyframes fadeIn { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:none;} }
.form-page__title {
    color: var(--color-primary);
    font-size: 2rem;
    margin-bottom: 0.5rem;
    text-align: center;
}
.form-page__subtitle {
    text-align: center;
    color: var(--color-gray);
    margin-bottom: 2rem;
}
.form__group {
    margin-bottom: 1.5rem;
    display: flex;
    flex-direction: column;
}
.form__group label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #1f2937;
}
.form__group input, 
.form__group select, 
.form__group textarea {
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 1rem;
}
.form__group input:focus, 
.form__group select:focus, 
.form__group textarea:focus {
    border-color: var(--color-primary);
    outline: none;
}
.form__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
}
.form__actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
}
.alert--error {
    background-color: #fee2e2;
    color: #b91c1c;
}
.alert--success {
    background-color: #dcfce7;
    color: #166534;
}
.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s ease;
}
.btn--primary { background-color: var(--color-primary); color: white; }
.btn--primary:hover { background-color: var(--color-primary-dark); }
.btn--secondary { background-color: #e5e7eb; color: #111827; }
.btn--secondary:hover { background-color: #d1d5db; }
.header__btn--logout {
    background-color: #dc2626; color: white;
}
.header__btn--logout:hover {
    background-color: #991b1b;
}
</style>
</body>
</html>
