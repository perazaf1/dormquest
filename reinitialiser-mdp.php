<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/includes/db.php';

$errors = [];
$success = '';
$token = '';
$validToken = false;

// Récupérer le token depuis l'URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Vérifier si le token est valide
    try {
        $stmt = $pdo->prepare("
            SELECT pr.user_id, pr.expires_at, u.email, u.prenom
            FROM password_resets pr
            JOIN utilisateurs u ON pr.user_id = u.id
            WHERE pr.token = ? AND pr.expires_at > NOW()
        ");
        $stmt->execute([hash('sha256', $token)]);
        $resetData = $stmt->fetch();

        if ($resetData) {
            $validToken = true;
        } else {
            $errors[] = "Ce lien de réinitialisation est invalide ou a expiré.";
        }
    } catch (PDOException $e) {
        $errors[] = "Erreur lors de la vérification du token.";
    }
} else {
    $errors[] = "Aucun token de réinitialisation fourni.";
}

// Traitement du formulaire de réinitialisation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($password)) {
        $errors[] = "Le mot de passe est obligatoire.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une majuscule.";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une minuscule.";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins un chiffre.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Si pas d'erreurs, mettre à jour le mot de passe
    if (empty($errors)) {
        try {
            // Hasher le nouveau mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Mettre à jour le mot de passe
            $stmt = $pdo->prepare("UPDATE utilisateurs SET motDePasse = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $resetData['user_id']]);

            // Supprimer le token utilisé
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
            $stmt->execute([$resetData['user_id']]);

            $success = "✅ Votre mot de passe a été réinitialisé avec succès ! Vous pouvez maintenant vous connecter.";
            $validToken = false; // Empêcher une nouvelle soumission

        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la réinitialisation : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe - DormQuest</title>
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
                <a href="index.php" class="header__nav-link">Accueil</a>
                <a href="login.php" class="header__btn header__btn--login">Connexion</a>
                <a href="register.php" class="header__btn header__btn--register">Inscription</a>
            </nav>
        </div>
    </header>

    <!-- Formulaire de réinitialisation -->
    <main class="form-page">
        <div class="form-container form-container--narrow">
            <div class="form-header">
                <h1 class="form-header__title">🔑 Nouveau mot de passe</h1>
                <p class="form-header__subtitle">Choisissez un nouveau mot de passe sécurisé pour votre compte.</p>
            </div>

            <!-- Messages d'erreur -->
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

            <!-- Message de succès -->
            <?php if ($success): ?>
                <div class="alert alert--success">
                    <strong><?php echo htmlspecialchars($success); ?></strong>
                </div>
                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="login.php" class="form-btn form-btn--primary" style="display: inline-block; max-width: 300px;">
                        Se connecter maintenant
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($validToken && !$success): ?>
                <form method="POST" action="" class="form">

                    <!-- Nouveau mot de passe -->
                    <div class="form-group">
                        <label for="password" class="form-label">Nouveau mot de passe</label>
                        <div class="form-input-group">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                placeholder="••••••••"
                                required
                                autofocus>
                            <button
                                type="button"
                                class="form-input-toggle"
                                id="toggle-password"
                                aria-label="Afficher le mot de passe">
                                👁️
                            </button>
                        </div>
                        <span class="form-hint">Au moins 8 caractères avec majuscule, minuscule et chiffre.</span>
                    </div>

                    <!-- Confirmation du mot de passe -->
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                        <div class="form-input-group">
                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                class="form-input"
                                placeholder="••••••••"
                                required>
                            <button
                                type="button"
                                class="form-input-toggle"
                                id="toggle-confirm-password"
                                aria-label="Afficher le mot de passe">
                                👁️
                            </button>
                        </div>
                    </div>

                    <!-- Bouton de soumission -->
                    <div class="form-actions">
                        <button type="submit" class="form-btn form-btn--primary">
                            Réinitialiser mon mot de passe
                        </button>
                    </div>

                    <!-- Lien de retour -->
                    <p class="form-footer">
                        <a href="login.php" class="form-link">← Retour à la connexion</a>
                    </p>
                </form>
            <?php elseif (!$validToken && !$success): ?>
                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="mot-de-passe-oublie.php" class="form-btn form-btn--primary" style="display: inline-block; max-width: 350px;">
                        Demander un nouveau lien
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer footer--minimal">
        <div class="footer__container">
            <p class="footer__copyright">
                &copy; 2025 DormQuest by Nyzer. Tous droits réservés.
            </p>
        </div>
    </footer>

    <script src="js/login.js"></script>
</body>
</html>
