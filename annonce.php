<?php
// annonce.php - Page de détail d'une annonce
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Récupérer l'ID de l'annonce
$annonce_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($annonce_id <= 0) {
    header('Location: annonces.php');
    exit();
}

$errors = [];
$success = '';

// Traitement de la candidature
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'candidater') {
    if (!is_logged_in() || !is_etudiant()) {
        $errors[] = "Vous devez être connecté en tant qu'étudiant pour candidater.";
    } else {
        $message = trim($_POST['message'] ?? '');
        
        if (empty($message)) {
            $errors[] = "Le message de motivation est obligatoire.";
        } elseif (strlen($message) < 20) {
            $errors[] = "Le message doit contenir au moins 20 caractères.";
        } else {
            try {
                // Vérifier si déjà candidaté
                $stmt = $pdo->prepare("SELECT id FROM candidatures WHERE idEtudiant = ? AND idAnnonce = ?");
                $stmt->execute([get_user_id(), $annonce_id]);
                
                if ($stmt->fetch()) {
                    $errors[] = "Vous avez déjà candidaté à cette annonce.";
                } else {
                    // Insérer la candidature
                    $stmt = $pdo->prepare("
                        INSERT INTO candidatures (idEtudiant, idAnnonce, message, statut, dateEnvoi)
                        VALUES (?, ?, ?, 'en_attente', NOW())
                    ");
                    $stmt->execute([get_user_id(), $annonce_id, $message]);
                    
                    $success = "Votre candidature a été envoyée avec succès !";
                }
            } catch (PDOException $e) {
                $errors[] = "Erreur lors de l'envoi de la candidature : " . $e->getMessage();
            }
        }
    }
}

// Récupérer l'annonce complète
try {
    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            u.id AS loueur_id,
            u.prenom AS loueur_prenom,
            u.nom AS loueur_nom,
            u.email AS loueur_email,
            u.telephone AS loueur_telephone,
            u.typeLoueur,
            u.photoDeProfil AS loueur_photo,
            c.accesPMR,
            c.eligibleAPL,
            c.statutBoursier,
            c.animauxAcceptes,
            c.parkingDisponible,
            c.meuble
        FROM annonces a
        JOIN utilisateurs u ON a.idLoueur = u.id
        LEFT JOIN criteres_logement c ON c.idAnnonce = a.id
        WHERE a.id = ? AND a.statut = 'active'
    ");
    $stmt->execute([$annonce_id]);
    $annonce = $stmt->fetch();
    
    if (!$annonce) {
        header('Location: annonces.php');
        exit();
    }
    
    // Vérifier si l'utilisateur connecté est le propriétaire
    $is_owner = is_logged_in() && get_user_id() == $annonce['loueur_id'];
    
    // Vérifier si en favoris (pour étudiants)
    $is_favori = false;
    if (is_logged_in() && is_etudiant()) {
        $stmt = $pdo->prepare("SELECT id FROM favoris WHERE idEtudiant = ? AND idAnnonce = ?");
        $stmt->execute([get_user_id(), $annonce_id]);
        $is_favori = $stmt->fetch() !== false;
    }
    
    // Vérifier si déjà candidaté
    $has_candidated = false;
    if (is_logged_in() && is_etudiant()) {
        $stmt = $pdo->prepare("SELECT id FROM candidatures WHERE idEtudiant = ? AND idAnnonce = ?");
        $stmt->execute([get_user_id(), $annonce_id]);
        $has_candidated = $stmt->fetch() !== false;
    }
    
    // Récupérer les photos (si table existe)
    $photos = [];
    // TODO: Implémenter quand la gestion de photos sera ajoutée
    
} catch (PDOException $e) {
    header('Location: annonces.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($annonce['titre']); ?> - DormQuest</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/annonce-detail.css">
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
                <a href="annonces.php" class="header__nav-link">← Retour aux annonces</a>
                <?php if (is_logged_in()): ?>
                    <?php if (is_etudiant()): ?>
                        <a href="favoris.php" class="header__nav-link">Mes favoris</a>
                        <a href="dashboard-etudiant.php" class="header__nav-link">Mon dashboard</a>
                    <?php else: ?>
                        <a href="dashboard-loueur.php" class="header__nav-link">Mes annonces</a>
                    <?php endif; ?>
                    <a href="logout.php" class="header__btn header__btn--logout">Déconnexion</a>
                <?php else: ?>
                    <a href="login.php" class="header__btn header__btn--login">Connexion</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="annonce-detail">
        <div class="annonce-detail__container">
            
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

            <!-- Contenu principal -->
            <div class="annonce-detail__content">
                
                <!-- Colonne gauche : Photos + Infos -->
                <div class="annonce-detail__main">
                    
                    <!-- Galerie de photos -->
                    <div class="photo-gallery">
                        <div class="photo-gallery__main">
                            <img src="https://via.placeholder.com/800x500/2563eb/ffffff?text=<?php echo urlencode($annonce['typeLogement']); ?>" 
                                 alt="<?php echo htmlspecialchars($annonce['titre']); ?>"
                                 class="photo-gallery__main-img"
                                 id="main-photo">
                            
                            <!-- Badge type -->
                            <span class="photo-gallery__badge">
                                <?php 
                                $types_labels = [
                                    'studio' => 'Studio',
                                    'colocation' => 'Colocation',
                                    'residence_etudiante' => 'Résidence étudiante',
                                    'chambre_habitant' => 'Chambre chez l\'habitant'
                                ];
                                echo $types_labels[$annonce['typeLogement']] ?? $annonce['typeLogement'];
                                ?>
                            </span>
                        </div>
                        
                        <!-- Thumbnails (si plusieurs photos) -->
                        <div class="photo-gallery__thumbnails">
                            <img src="https://via.placeholder.com/150x100/60a5fa/ffffff?text=Photo+1" 
                                 alt="Photo 1" 
                                 class="photo-gallery__thumb photo-gallery__thumb--active"
                                 data-full="https://via.placeholder.com/800x500/2563eb/ffffff?text=<?php echo urlencode($annonce['typeLogement']); ?>">
                            <img src="https://via.placeholder.com/150x100/60a5fa/ffffff?text=Photo+2" 
                                 alt="Photo 2" 
                                 class="photo-gallery__thumb"
                                 data-full="https://via.placeholder.com/800x500/1e40af/ffffff?text=Cuisine">
                            <img src="https://via.placeholder.com/150x100/60a5fa/ffffff?text=Photo+3" 
                                 alt="Photo 3" 
                                 class="photo-gallery__thumb"
                                 data-full="https://via.placeholder.com/800x500/3b82f6/ffffff?text=Salle+de+bain">
                        </div>
                    </div>

                    <!-- En-tête de l'annonce -->
                    <div class="annonce-header">
                        <div class="annonce-header__top">
                            <h1 class="annonce-header__title"><?php echo htmlspecialchars($annonce['titre']); ?></h1>
                            
                            <!-- Actions propriétaire -->
                            <?php if ($is_owner): ?>
                                <div class="annonce-header__actions">
                                    <a href="edit-annonce.php?id=<?php echo $annonce['id']; ?>" class="btn btn--edit">
                                        ✏️ Modifier
                                    </a>
                                    <a href="dashboard-loueur.php?action=delete&id=<?php echo $annonce['id']; ?>" 
                                       class="btn btn--delete"
                                       onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette annonce ?')">
                                        🗑️ Supprimer
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Bouton favori (étudiants) -->
                            <?php if (!$is_owner && is_logged_in() && is_etudiant()): ?>
                                <button class="btn-favori <?php echo $is_favori ? 'btn-favori--active' : ''; ?>" 
                                        data-annonce-id="<?php echo $annonce['id']; ?>"
                                        id="btn-favori">
                                    ⭐ <?php echo $is_favori ? 'En favoris' : 'Ajouter aux favoris'; ?>
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="annonce-header__location">
                            📍 <?php echo htmlspecialchars($annonce['adresse']); ?>, 
                            <?php echo htmlspecialchars($annonce['codePostal']); ?> 
                            <?php echo htmlspecialchars($annonce['ville']); ?>
                        </div>
                        
                        <div class="annonce-header__price">
                            <?php echo number_format($annonce['prixMensuel'], 0, ',', ' '); ?> €
                            <span class="annonce-header__price-label">/mois</span>
                        </div>
                    </div>

                    <!-- Caractéristiques -->
                    <div class="annonce-section">
                        <h2 class="annonce-section__title">🏠 Caractéristiques</h2>
                        <div class="characteristics">
                            <div class="characteristic">
                                <span class="characteristic__icon">📐</span>
                                <div class="characteristic__content">
                                    <strong><?php echo $annonce['superficie']; ?> m²</strong>
                                    <span>Superficie</span>
                                </div>
                            </div>
                            <div class="characteristic">
                                <span class="characteristic__icon">🚪</span>
                                <div class="characteristic__content">
                                    <strong><?php echo $annonce['nombrePieces']; ?> pièce<?php echo $annonce['nombrePieces'] > 1 ? 's' : ''; ?></strong>
                                    <span>Nombre de pièces</span>
                                </div>
                            </div>
                            <?php if ($annonce['empreinteEnergie']): ?>
                                <div class="characteristic">
                                    <span class="characteristic__icon">⚡</span>
                                    <div class="characteristic__content">
                                        <strong>Classe <?php echo $annonce['empreinteEnergie']; ?></strong>
                                        <span>Énergie</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($annonce['dateDisponibilite']): ?>
                                <div class="characteristic">
                                    <span class="characteristic__icon">📅</span>
                                    <div class="characteristic__content">
                                        <strong><?php echo date('d/m/Y', strtotime($annonce['dateDisponibilite'])); ?></strong>
                                        <span>Disponible le</span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="characteristic">
                                    <span class="characteristic__icon">✅</span>
                                    <div class="characteristic__content">
                                        <strong>Immédiatement</strong>
                                        <span>Disponibilité</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="annonce-section">
                        <h2 class="annonce-section__title">📝 Description</h2>
                        <div class="annonce-description">
                            <?php echo nl2br(htmlspecialchars($annonce['description'])); ?>
                        </div>
                    </div>

                    <!-- Équipements et critères -->
                    <div class="annonce-section">
                        <h2 class="annonce-section__title">✨ Équipements et critères</h2>
                        <div class="criteres-grid">
                            <?php if ($annonce['meuble']): ?>
                                <div class="critere-item critere-item--active">
                                    <span class="critere-item__icon">🛋️</span>
                                    <span class="critere-item__label">Meublé</span>
                                </div>
                            <?php else: ?>
                                <div class="critere-item">
                                    <span class="critere-item__icon">🛋️</span>
                                    <span class="critere-item__label">Non meublé</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($annonce['eligibleAPL']): ?>
                                <div class="critere-item critere-item--active">
                                    <span class="critere-item__icon">💰</span>
                                    <span class="critere-item__label">Éligible APL</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($annonce['statutBoursier']): ?>
                                <div class="critere-item critere-item--active">
                                    <span class="critere-item__icon">🎓</span>
                                    <span class="critere-item__label">Accepte boursiers</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($annonce['parkingDisponible']): ?>
                                <div class="critere-item critere-item--active">
                                    <span class="critere-item__icon">🚗</span>
                                    <span class="critere-item__label">Parking disponible</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($annonce['accesPMR']): ?>
                                <div class="critere-item critere-item--active">
                                    <span class="critere-item__icon">♿</span>
                                    <span class="critere-item__label">Accès PMR</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($annonce['animauxAcceptes']): ?>
                                <div class="critere-item critere-item--active">
                                    <span class="critere-item__icon">🐾</span>
                                    <span class="critere-item__label">Animaux acceptés</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($annonce['colocationPossible']): ?>
                                <div class="critere-item critere-item--active">
                                    <span class="critere-item__icon">👥</span>
                                    <span class="critere-item__label">Colocation possible</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite : Contact + Candidature -->
                <aside class="annonce-detail__sidebar">
                    
                    <!-- Carte de contact du loueur -->
                    <div class="contact-card">
                        <h3 class="contact-card__title">👤 Contact</h3>
                        
                        <div class="contact-card__profile">
                            <img src="<?php echo htmlspecialchars($annonce['loueur_photo'] ?? 'images/default-avatar.png'); ?>" 
                                 alt="Photo de profil" 
                                 class="contact-card__photo"
                                 onerror="this.src='images/default-avatar.png'">
                            <div class="contact-card__info">
                                <div class="contact-card__name">
                                    <?php echo htmlspecialchars($annonce['loueur_prenom'] . ' ' . $annonce['loueur_nom']); ?>
                                </div>
                                <div class="contact-card__type">
                                    <?php
                                    $types_loueur = [
                                        'particulier' => 'Particulier',
                                        'agence' => 'Agence immobilière',
                                        'organisme' => 'Organisme',
                                        'crous' => 'CROUS'
                                    ];
                                    echo $types_loueur[$annonce['typeLoueur']] ?? 'Loueur';
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="contact-card__details">
                            <a href="mailto:<?php echo htmlspecialchars($annonce['contactEmail']); ?>" 
                               class="contact-card__link">
                                📧 <?php echo htmlspecialchars($annonce['contactEmail']); ?>
                            </a>
                            <a href="tel:<?php echo htmlspecialchars($annonce['contactTelephone']); ?>" 
                               class="contact-card__link">
                                📞 <?php echo htmlspecialchars($annonce['contactTelephone']); ?>
                            </a>
                        </div>
                    </div>

                    <!-- Formulaire de candidature (étudiants non propriétaires) -->
                    <?php if (!$is_owner && is_logged_in() && is_etudiant()): ?>
                        <?php if ($has_candidated): ?>
                            <div class="candidature-sent">
                                <div class="candidature-sent__icon">✅</div>
                                <h3 class="candidature-sent__title">Candidature envoyée</h3>
                                <p class="candidature-sent__text">
                                    Vous avez déjà postulé à cette annonce. Le loueur vous contactera prochainement.
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="candidature-form">
                                <h3 class="candidature-form__title">📨 Postuler</h3>
                                <p class="candidature-form__desc">
                                    Présentez-vous brièvement et expliquez pourquoi ce logement vous intéresse.
                                </p>
                                
                                <form method="POST" action="annonce.php?id=<?php echo $annonce_id; ?>">
                                    <input type="hidden" name="action" value="candidater">
                                    
                                    <textarea name="message" 
                                              class="candidature-form__textarea" 
                                              placeholder="Bonjour, je suis étudiant(e) en... Je suis intéressé(e) par votre logement car..."
                                              required
                                              minlength="20"
                                              rows="6"></textarea>
                                    
                                    <small class="candidature-form__hint">Minimum 20 caractères</small>
                                    
                                    <button type="submit" class="candidature-form__btn">
                                        Envoyer ma candidature
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    <?php elseif (!is_logged_in()): ?>
                        <div class="login-prompt">
                            <div class="login-prompt__icon">🔒</div>
                            <h3 class="login-prompt__title">Connectez-vous</h3>
                            <p class="login-prompt__text">
                                Vous devez être connecté en tant qu'étudiant pour postuler à cette annonce.
                            </p>
                            <a href="login.php?redirect=annonce.php?id=<?php echo $annonce_id; ?>" 
                               class="login-prompt__btn">
                                Se connecter
                            </a>
                            <a href="register.php?type=etudiant" class="login-prompt__link">
                                Créer un compte étudiant
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Informations complémentaires -->
                    <div class="info-box">
                        <h4 class="info-box__title">ℹ️ Informations</h4>
                        <div class="info-box__item">
                            <strong>Publié le :</strong>
                            <?php echo date('d/m/Y', strtotime($annonce['dateCreation'])); ?>
                        </div>
                        <?php if ($annonce['dateModification']): ?>
                            <div class="info-box__item">
                                <strong>Modifié le :</strong>
                                <?php echo date('d/m/Y', strtotime($annonce['dateModification'])); ?>
                            </div>
                        <?php endif; ?>
                        <div class="info-box__item">
                            <strong>Référence :</strong>
                            #<?php echo str_pad($annonce['id'], 6, '0', STR_PAD_LEFT); ?>
                        </div>
                    </div>
                </aside>
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

    <script src="js/annonce-detail.js"></script>
</body>
</html>