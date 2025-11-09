<?php
// login.php - Page de connexion DormQuest
session_start();

// Redirection si déjà connecté
if (isset($_SESSION['user_id'])) {
    // Rediriger selon le rôle
    if ($_SESSION['user_role'] === 'etudiant') {
        header('Location: dashboard-etudiant.php');
    } else {
        header('Location: dashboard-loueur.php');
    }
    exit();
}

// Connexion à la base de données (chemin absolu basé sur ce fichier)
require_once __DIR__ . '/includes/db.php';

// Variables
$errors = [];
$success = '';
$email = '';
$remember_me = false;

// Vérifier si un message de succès est passé (depuis register.php)
if (isset($_GET['success']) && $_GET['success'] === 'registered') {
    $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);
    
    // Validation
    if (empty($email)) {
        $errors[] = "L'email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide.";
    }
    
    if (empty($password)) {
        $errors[] = "Le mot de passe est obligatoire.";
    }
    
    // Si pas d'erreurs de validation, vérifier les identifiants
    if (empty($errors)) {
        try {
            // Récupérer l'utilisateur par email
            $stmt = $pdo->prepare("
                SELECT id, prenom, nom, email, motDePasse, role, photoDeProfil 
                FROM utilisateurs 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            // Vérifier si l'utilisateur existe et le mot de passe est correct
            if ($user && password_verify($password, $user['motDePasse'])) {
                // Connexion réussie
                
                // Régénérer l'ID de session pour la sécurité
                session_regenerate_id(true);
                
                // Stocker les informations dans la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_photo'] = $user['photoDeProfil'];
                $_SESSION['login_time'] = time();
                
                // Mettre à jour la dernière connexion
                $stmt = $pdo->prepare("
                    UPDATE utilisateurs 
                    SET derniereConnexion = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$user['id']]);
                
                // Gestion du "Se souvenir de moi"
                if ($remember_me) {
                    // Créer un token de connexion persistant
                    $token = bin2hex(random_bytes(32));
                    
                    // Stocker le token dans un cookie (30 jours)
                    setcookie(
                        'remember_token',
                        $token,
                        time() + (30 * 24 * 60 * 60), // 30 jours
                        '/',
                        '',
                        false, // HTTPS seulement en production
                        true   // HTTPOnly pour la sécurité
                    );
                    
                    // TODO: Stocker le token hashé en base de données
                    // pour vérifier lors des prochaines visites
                }
                
                // Redirection selon le rôle
                if ($user['role'] === 'etudiant') {
                    header('Location: dashboard-etudiant.php');
                } else {
                    header('Location: dashboard-loueur.php');
                }
                exit();
                
            } else {
                // Identifiants incorrects
                $errors[] = "Email ou mot de passe incorrect.";
            }
            
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la connexion : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - DormQuest</title>
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
                <a href="register.php" class="header__btn header__btn--register">Inscription</a>
            </nav>
        </div>
    </header>

    <!-- Formulaire de connexion -->
    <main class="form-page">
        <div class="form-container form-container--narrow">
            <div class="form-header">
                <h1 class="form-header__title">Connexion</h1>
                <p class="form-header__subtitle">Bienvenue sur DormQuest !</p>
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
                    <strong>✅ <?php echo htmlspecialchars($success); ?></strong>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="form">
                
                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($email); ?>" 
                        class="form-input" 
                        placeholder="votre.email@exemple.com"
                        required 
                        autofocus>
                </div>

                <!-- Mot de passe -->
                <div class="form-group">
                    <label for="password" class="form-label">Mot de passe</label>
                    <div class="form-input-group">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            placeholder="••••••••"
                            required>
                        <button 
                            type="button" 
                            class="form-input-toggle" 
                            id="toggle-password"
                            aria-label="Afficher le mot de passe">
                            👁️
                        </button>
                    </div>
                </div>

                <!-- Se souvenir de moi -->
                <div class="form-group form-group--checkbox">
                    <label class="form-checkbox">
                        <input 
                            type="checkbox" 
                            name="remember_me" 
                            id="remember_me" 
                            class="form-checkbox__input"
                            <?php echo $remember_me ? 'checked' : ''; ?>>
                        <span class="form-checkbox__label">Se souvenir de moi</span>
                    </label>
                    <a href="mot-de-passe-oublie.php" class="form-link form-link--right">
                        Mot de passe oublié ?
                    </a>
                </div>

                <!-- Bouton de connexion -->
                <div class="form-actions">
                    <button type="submit" class="form-btn form-btn--primary">
                        Se connecter
                    </button>
                </div>

                <!-- Lien vers inscription -->
                <p class="form-footer">
                    Vous n'avez pas encore de compte ? 
                    <a href="register.php" class="form-link">Créer un compte</a>
                </p>

                <!-- Séparateur -->
                <div class="form-separator">
                    <span class="form-separator__text">ou continuer avec</span>
                </div>

                <!-- Connexion rapide (optionnel) -->
                <div class="form-quick-login">
                    <a href="register.php?type=etudiant" class="form-quick-login__btn">
                        <span class="form-quick-login__icon">🎓</span>
                        <span class="form-quick-login__text">Je suis étudiant</span>
                    </a>
                    <a href="register.php?type=loueur" class="form-quick-login__btn">
                        <span class="form-quick-login__icon">🏠</span>
                        <span class="form-quick-login__text">Je suis loueur</span>
                    </a>
                </div>
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

    <script src="js/login.js"></script>
</body>
</html>