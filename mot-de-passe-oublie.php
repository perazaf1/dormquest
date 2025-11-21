<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/includes/db.php';

$errors = [];
$success = '';
$email = '';
$formDisabled = false;

// Protection contre les tentatives répétées (rate limiting basique)
if (!isset($_SESSION['reset_attempts'])) {
    $_SESSION['reset_attempts'] = 0;
    $_SESSION['reset_last_attempt'] = time();
}

// Réinitialiser le compteur après 15 minutes
if (time() - $_SESSION['reset_last_attempt'] > 900) {
    $_SESSION['reset_attempts'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier le nombre de tentatives
    if ($_SESSION['reset_attempts'] >= 3) {
        $timeLeft = 900 - time() - $_SESSION['reset_last_attempt'];
        $minutesLeft = ceil($timeLeft / 60);
        $errors[] = "Trop de tentatives. Veuillez réessayer dans $minutesLeft minute(s).";
        $formDisabled = true;
    } else {
        $email = trim($_POST['email'] ?? '');

        // Validation
        if ($email === '') {
            $errors[] = "Veuillez saisir votre email.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format de l'email incorrect.";
        }

        if (empty($errors)) {
            $_SESSION['reset_attempts']++;
            $_SESSION['reset_last_attempt'] = time();

            try {
                // Vérifier si l'email existe
                $stmt = $pdo->prepare("SELECT id, prenom, email FROM utilisateurs WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user) {
                    // Vérifier s'il n'y a pas déjà un token récent (moins de 5 minutes)
                    // Note: Utilise expires_at comme approximation si created_at n'existe pas
                    try {
                        $stmt = $pdo->prepare("
                            SELECT expires_at FROM password_resets
                            WHERE user_id = ? AND expires_at > DATE_SUB(NOW(), INTERVAL 55 MINUTE)
                        ");
                        $stmt->execute([$user['id']]);
                        $recentToken = $stmt->fetch();
                    } catch (PDOException $e) {
                        $recentToken = false;
                    }

                    if ($recentToken) {
                        $success = "✅ Un email de réinitialisation a déjà été envoyé récemment. Veuillez vérifier votre boîte mail.";
                    } else {
                        // Générer un token sécurisé
                        $token = bin2hex(random_bytes(32));
                        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                        // Supprimer les anciens tokens pour cet utilisateur
                        $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?")->execute([$user['id']]);

                        // Insérer le nouveau token
                        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
                        $stmt->execute([$user['id'], hash('sha256', $token), $expiry]);

                        // Construire le lien de réinitialisation avec protocole HTTPS si disponible
                        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                        $resetLink = $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reinitialiser-mdp.php?token=" . $token;

                        // Envoyer l'email
                        $to = $user['email'];
                        $subject = "DormQuest - Réinitialisation de votre mot de passe";
                        $message = "
                        <html>
                        <head>
                            <title>Réinitialisation de mot de passe</title>
                        </head>
                        <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                            <div style='background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); padding: 20px; border-radius: 10px 10px 0 0;'>
                                <h2 style='color: white; margin: 0;'>🔐 Réinitialisation de mot de passe</h2>
                            </div>
                            <div style='background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;'>
                                <p style='font-size: 16px;'>Bonjour <strong>" . htmlspecialchars($user['prenom']) . "</strong>,</p>
                                <p style='font-size: 14px; color: #666;'>Vous avez demandé la réinitialisation de votre mot de passe sur DormQuest.</p>
                                <p style='text-align: center; margin: 30px 0;'>
                                    <a href='" . $resetLink . "' style='background: #2563eb; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold;'>Réinitialiser mon mot de passe</a>
                                </p>
                                <p style='font-size: 13px; color: #999;'>Ou copiez ce lien dans votre navigateur :<br><span style='color: #2563eb;'>" . $resetLink . "</span></p>
                                <p style='font-size: 13px; color: #d97706; background: #fef3c7; padding: 10px; border-radius: 5px;'>⚠️ <strong>Ce lien expire dans 1 heure.</strong></p>
                                <p style='font-size: 13px; color: #666;'>Si vous n'avez pas fait cette demande, ignorez cet email. Votre mot de passe restera inchangé.</p>
                                <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                                <p style='font-size: 12px; color: #999; text-align: center;'>L'équipe DormQuest<br>&copy; 2025 DormQuest by Nyzer</p>
                            </div>
                        </body>
                        </html>
                        ";

                        $headers = "MIME-Version: 1.0\r\n";
                        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
                        $headers .= "From: DormQuest <noreply@dormquest.com>\r\n";
                        $headers .= "Reply-To: support@dormquest.com\r\n";

                        if (mail($to, $subject, $message, $headers)) {
                            $success = "✅ Un email de réinitialisation a été envoyé à votre adresse.";
                        } else {
                            $errors[] = "Erreur lors de l'envoi de l'email. Veuillez réessayer.";
                        }
                    }
                } else {
                    // Message identique pour éviter l'énumération des emails
                    $success = "✅ Si cette adresse existe dans notre base, un email de réinitialisation a été envoyé.";
                }
            } catch (PDOException $e) {
                error_log("Erreur réinitialisation mot de passe : " . $e->getMessage());
                $errors[] = "Une erreur est survenue. Veuillez réessayer plus tard.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - DormQuest</title>
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
                <h1 class="form-header__title">🔐 Mot de passe oublié</h1>
                <p class="form-header__subtitle">Entrez votre email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
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
            <?php endif; ?>

            <form method="POST" action="" class="form">

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Adresse email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo htmlspecialchars($email); ?>"
                        class="form-input"
                        placeholder="votre.email@exemple.com"
                        required
                        autofocus
                        <?php echo $formDisabled ? 'disabled' : ''; ?>>
                    <span class="form-hint">Vous recevrez un email avec un lien valide pendant 1 heure.</span>
                </div>

                <!-- Bouton d'envoi -->
                <div class="form-actions">
                    <button type="submit" class="form-btn form-btn--primary" <?php echo $formDisabled ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''; ?>>
                        Envoyer le lien de réinitialisation
                    </button>
                </div>

                <!-- Lien de retour -->
                <p class="form-footer">
                    <a href="login.php" class="form-link">← Retour à la connexion</a>
                </p>
            </form>
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

</body>
</html>