<?php
// create-annonce.php - Formulaire de création d'annonce
session_start();

// Inclure les fonctions d'authentification
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Vérifier que l'utilisateur est un loueur connecté
require_loueur();

$user_id = get_user_id();
$user = get_user_info($pdo);

// Variables pour pré-remplir le formulaire en cas d'erreur
$errors = [];
$success = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $code_postal = trim($_POST['code_postal'] ?? '');
    $type_logement = $_POST['type_logement'] ?? '';
    $prix_mensuel = trim($_POST['prix_mensuel'] ?? '');
    $superficie = trim($_POST['superficie'] ?? '');
    $nombre_pieces = trim($_POST['nombre_pieces'] ?? '');
    $colocation_possible = isset($_POST['colocation_possible']);
    $empreinte_energie = $_POST['empreinte_energie'] ?? '';
    $date_disponibilite = $_POST['date_disponibilite'] ?? '';
    $contact_email = trim($_POST['contact_email'] ?? $user['email']);
    $contact_telephone = trim($_POST['contact_telephone'] ?? $user['telephone']);
    
    // Critères de logement
    $acces_pmr = isset($_POST['acces_pmr']);
    $eligible_apl = isset($_POST['eligible_apl']);
    $statut_boursier = isset($_POST['statut_boursier']);
    $animaux_acceptes = isset($_POST['animaux_acceptes']);
    $parking_disponible = isset($_POST['parking_disponible']);
    $meuble = isset($_POST['meuble']);
    
    // Validation
    if (empty($titre)) {
        $errors[] = "Le titre est obligatoire.";
    } elseif (strlen($titre) < 10) {
        $errors[] = "Le titre doit contenir au moins 10 caractères.";
    }
    
    if (empty($description)) {
        $errors[] = "La description est obligatoire.";
    } elseif (strlen($description) < 50) {
        $errors[] = "La description doit contenir au moins 50 caractères.";
    }
    
    if (empty($adresse)) {
        $errors[] = "L'adresse est obligatoire.";
    }
    
    if (empty($ville)) {
        $errors[] = "La ville est obligatoire.";
    }
    
    if (empty($code_postal)) {
        $errors[] = "Le code postal est obligatoire.";
    } elseif (!preg_match('/^[0-9]{5}$/', $code_postal)) {
        $errors[] = "Le code postal doit contenir 5 chiffres.";
    }
    
    if (empty($type_logement)) {
        $errors[] = "Le type de logement est obligatoire.";
    }
    
    if (empty($prix_mensuel) || !is_numeric($prix_mensuel) || $prix_mensuel <= 0) {
        $errors[] = "Le prix mensuel doit être un nombre positif.";
    }
    
    if (empty($superficie) || !is_numeric($superficie) || $superficie <= 0) {
        $errors[] = "La superficie doit être un nombre positif.";
    }
    
    if (empty($nombre_pieces) || !is_numeric($nombre_pieces) || $nombre_pieces < 1) {
        $errors[] = "Le nombre de pièces doit être au moins 1.";
    }
    
    if (empty($contact_email) || !filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email de contact est invalide.";
    }
    
    if (empty($contact_telephone) || !preg_match('/^[0-9]{10}$/', str_replace(' ', '', $contact_telephone))) {
        $errors[] = "Le téléphone doit contenir 10 chiffres.";
    }
    
    // Si pas d'erreurs, insertion en BDD
    if (empty($errors)) {
        try {
            // Démarrer une transaction
            $pdo->beginTransaction();
            
            // Insérer l'annonce
            $stmt = $pdo->prepare("
                INSERT INTO annonces (
                    idLoueur, titre, description, adresse, ville, codePostal,
                    typeLogement, prixMensuel, superficie, nombrePieces,
                    colocationPossible, empreinteEnergie, dateDisponibilite,
                    contactEmail, contactTelephone, statut
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
            ");
            
            $stmt->execute([
                $user_id,
                $titre,
                $description,
                $adresse,
                $ville,
                $code_postal,
                $type_logement,
                $prix_mensuel,
                $superficie,
                $nombre_pieces,
                $colocation_possible ? 1 : 0,
                $empreinte_energie,
                $date_disponibilite ?: null,
                $contact_email,
                $contact_telephone
            ]);
            
            $annonce_id = $pdo->lastInsertId();
            
            // Insérer les critères de logement
            $stmt = $pdo->prepare("
                INSERT INTO criteres_logement (
                    idAnnonce, accesPMR, eligibleAPL, statutBoursier,
                    animauxAcceptes, parkingDisponible, meuble
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $annonce_id,
                $acces_pmr ? 1 : 0,
                $eligible_apl ? 1 : 0,
                $statut_boursier ? 1 : 0,
                $animaux_acceptes ? 1 : 0,
                $parking_disponible ? 1 : 0,
                $meuble ? 1 : 0
            ]);
            
            // Valider la transaction
            $pdo->commit();
            
            // Redirection vers le dashboard avec message de succès
            header('Location: dashboard-loueur.php?success=annonce_created');
            exit();
            
        } catch (PDOException $e) {
            // Annuler la transaction en cas d'erreur
            $pdo->rollBack();
            $errors[] = "Erreur lors de la création de l'annonce : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une annonce - DormQuest</title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/dashboard-loueur.css">
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
                <a href="dashboard-loueur.php" class="header__nav-link">Mes annonces</a>
                <a href="create-annonce.php" class="header__nav-link header__nav-link--active">Créer une annonce</a>
                <a href="profil.php" class="header__nav-link">Mon profil</a>
                <div class="header__user">
                    <img src="<?php echo htmlspecialchars(get_user_photo()); ?>" 
                         alt="Photo de profil" 
                         class="header__user-photo"
                         onerror="this.src='images/default-avatar.png'">
                    <span class="header__user-name"><?php echo htmlspecialchars(get_user_prenom()); ?></span>
                </div>
                <a href="logout.php" class="header__btn header__btn--logout">Déconnexion</a>
            </nav>
        </div>
    </header>

    <main class="form-page">
        <div class="form-container form-container--large">
            <div class="form-header">
                <h1 class="form-header__title">Créer une annonce</h1>
                <p class="form-header__subtitle">Proposez votre logement à des milliers d'étudiants</p>
            </div>

            <!-- Messages d'erreur -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert--error">
                    <strong>⚠️ Erreurs :</strong>
                    <ul class="alert__list">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="create-annonce.php" class="form" id="form-annonce">
                
                <!-- Section 1 : Informations générales -->
                <div class="form-section">
                    <h2 class="form-section__title">📝 Informations générales</h2>
                    
                    <div class="form-group">
                        <label for="titre" class="form-label">Titre de l'annonce *</label>
                        <input type="text" id="titre" name="titre" 
                               value="<?php echo htmlspecialchars($_POST['titre'] ?? ''); ?>" 
                               class="form-input" 
                               placeholder="Ex: Studio lumineux proche université"
                               required maxlength="200">
                        <small class="form-hint">Soyez clair et attractif (minimum 10 caractères)</small>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description *</label>
                        <textarea id="description" name="description" 
                                  class="form-input form-input--textarea" 
                                  placeholder="Décrivez votre logement en détail : équipements, proximités, ambiance..."
                                  required rows="8"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        <small class="form-hint">Minimum 50 caractères - <span id="char-count">0</span> caractères</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="type_logement" class="form-label">Type de logement *</label>
                            <select id="type_logement" name="type_logement" class="form-input" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="studio" <?php echo ($_POST['type_logement'] ?? '') === 'studio' ? 'selected' : ''; ?>>Studio</option>
                                <option value="colocation" <?php echo ($_POST['type_logement'] ?? '') === 'colocation' ? 'selected' : ''; ?>>Colocation</option>
                                <option value="residence_etudiante" <?php echo ($_POST['type_logement'] ?? '') === 'residence_etudiante' ? 'selected' : ''; ?>>Résidence étudiante</option>
                                <option value="chambre_habitant" <?php echo ($_POST['type_logement'] ?? '') === 'chambre_habitant' ? 'selected' : ''; ?>>Chambre chez l'habitant</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="empreinte_energie" class="form-label">Étiquette énergétique</label>
                            <select id="empreinte_energie" name="empreinte_energie" class="form-input">
                                <option value="">-- Non renseignée --</option>
                                <?php foreach(['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $lettre): ?>
                                    <option value="<?php echo $lettre; ?>" <?php echo ($_POST['empreinte_energie'] ?? '') === $lettre ? 'selected' : ''; ?>><?php echo $lettre; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2 : Localisation -->
                <div class="form-section">
                    <h2 class="form-section__title">📍 Localisation</h2>
                    
                    <div class="form-group">
                        <label for="adresse" class="form-label">Adresse complète *</label>
                        <input type="text" id="adresse" name="adresse" 
                               value="<?php echo htmlspecialchars($_POST['adresse'] ?? ''); ?>" 
                               class="form-input" 
                               placeholder="Ex: 12 rue de la République"
                               required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ville" class="form-label">Ville *</label>
                            <input type="text" id="ville" name="ville" 
                                   value="<?php echo htmlspecialchars($_POST['ville'] ?? ''); ?>" 
                                   class="form-input" 
                                   placeholder="Ex: Paris"
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="code_postal" class="form-label">Code postal *</label>
                            <input type="text" id="code_postal" name="code_postal" 
                                   value="<?php echo htmlspecialchars($_POST['code_postal'] ?? ''); ?>" 
                                   class="form-input" 
                                   placeholder="Ex: 75005"
                                   required maxlength="5" pattern="[0-9]{5}">
                        </div>
                    </div>
                </div>

                <!-- Section 3 : Caractéristiques -->
                <div class="form-section">
                    <h2 class="form-section__title">🏠 Caractéristiques</h2>
                    
                    <div class="form-row form-row--triple">
                        <div class="form-group">
                            <label for="prix_mensuel" class="form-label">Prix mensuel (€) *</label>
                            <input type="number" id="prix_mensuel" name="prix_mensuel" 
                                   value="<?php echo htmlspecialchars($_POST['prix_mensuel'] ?? ''); ?>" 
                                   class="form-input" 
                                   placeholder="Ex: 650"
                                   required min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="superficie" class="form-label">Superficie (m²) *</label>
                            <input type="number" id="superficie" name="superficie" 
                                   value="<?php echo htmlspecialchars($_POST['superficie'] ?? ''); ?>" 
                                   class="form-input" 
                                   placeholder="Ex: 25"
                                   required min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="nombre_pieces" class="form-label">Nombre de pièces *</label>
                            <input type="number" id="nombre_pieces" name="nombre_pieces" 
                                   value="<?php echo htmlspecialchars($_POST['nombre_pieces'] ?? '1'); ?>" 
                                   class="form-input" 
                                   placeholder="Ex: 1"
                                   required min="1">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="date_disponibilite" class="form-label">Date de disponibilité</label>
                        <input type="date" id="date_disponibilite" name="date_disponibilite" 
                               value="<?php echo htmlspecialchars($_POST['date_disponibilite'] ?? ''); ?>" 
                               class="form-input">
                        <small class="form-hint">Laissez vide si disponible immédiatement</small>
                    </div>
                </div>

                <!-- Section 4 : Critères -->
                <div class="form-section">
                    <h2 class="form-section__title">✨ Critères et équipements</h2>
                    
                    <div class="form-checkboxes">
                        <label class="form-checkbox-card">
                            <input type="checkbox" name="meuble" class="form-checkbox-card__input" 
                                   <?php echo isset($_POST['meuble']) ? 'checked' : ''; ?>>
                            <div class="form-checkbox-card__content">
                                <span class="form-checkbox-card__icon">🛋️</span>
                                <span class="form-checkbox-card__label">Meublé</span>
                            </div>
                        </label>

                        <label class="form-checkbox-card">
                            <input type="checkbox" name="colocation_possible" class="form-checkbox-card__input"
                                   <?php echo isset($_POST['colocation_possible']) ? 'checked' : ''; ?>>
                            <div class="form-checkbox-card__content">
                                <span class="form-checkbox-card__icon">👥</span>
                                <span class="form-checkbox-card__label">Colocation possible</span>
                            </div>
                        </label>

                        <label class="form-checkbox-card">
                            <input type="checkbox" name="parking_disponible" class="form-checkbox-card__input"
                                   <?php echo isset($_POST['parking_disponible']) ? 'checked' : ''; ?>>
                            <div class="form-checkbox-card__content">
                                <span class="form-checkbox-card__icon">🚗</span>
                                <span class="form-checkbox-card__label">Parking</span>
                            </div>
                        </label>

                        <label class="form-checkbox-card">
                            <input type="checkbox" name="eligible_apl" class="form-checkbox-card__input"
                                   <?php echo isset($_POST['eligible_apl']) ? 'checked' : ''; ?>>
                            <div class="form-checkbox-card__content">
                                <span class="form-checkbox-card__icon">💰</span>
                                <span class="form-checkbox-card__label">Éligible APL</span>
                            </div>
                        </label>

                        <label class="form-checkbox-card">
                            <input type="checkbox" name="statut_boursier" class="form-checkbox-card__input"
                                   <?php echo isset($_POST['statut_boursier']) ? 'checked' : ''; ?>>
                            <div class="form-checkbox-card__content">
                                <span class="form-checkbox-card__icon">🎓</span>
                                <span class="form-checkbox-card__label">Accepte boursiers</span>
                            </div>
                        </label>

                        <label class="form-checkbox-card">
                            <input type="checkbox" name="acces_pmr" class="form-checkbox-card__input"
                                   <?php echo isset($_POST['acces_pmr']) ? 'checked' : ''; ?>>
                            <div class="form-checkbox-card__content">
                                <span class="form-checkbox-card__icon">♿</span>
                                <span class="form-checkbox-card__label">Accès PMR</span>
                            </div>
                        </label>

                        <label class="form-checkbox-card">
                            <input type="checkbox" name="animaux_acceptes" class="form-checkbox-card__input"
                                   <?php echo isset($_POST['animaux_acceptes']) ? 'checked' : ''; ?>>
                            <div class="form-checkbox-card__content">
                                <span class="form-checkbox-card__icon">🐾</span>
                                <span class="form-checkbox-card__label">Animaux acceptés</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Section 5 : Contact -->
                <div class="form-section">
                    <h2 class="form-section__title">📞 Informations de contact</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_email" class="form-label">Email de contact *</label>
                            <input type="email" id="contact_email" name="contact_email" 
                                   value="<?php echo htmlspecialchars($_POST['contact_email'] ?? $user['email']); ?>" 
                                   class="form-input" 
                                   placeholder="votre.email@exemple.com"
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="contact_telephone" class="form-label">Téléphone de contact *</label>
                            <input type="tel" id="contact_telephone" name="contact_telephone" 
                                   value="<?php echo htmlspecialchars($_POST['contact_telephone'] ?? $user['telephone']); ?>" 
                                   class="form-input" 
                                   placeholder="0612345678"
                                   required>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="form-actions form-actions--multiple">
                    <a href="dashboard-loueur.php" class="form-btn form-btn--secondary">
                        Annuler
                    </a>
                    <button type="submit" class="form-btn form-btn--primary">
                        Publier l'annonce
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="footer footer--minimal">
        <div class="footer__container">
            <p class="footer__copyright">
                &copy; 2024 DormQuest by Nyzer. Tous droits réservés.
            </p>
        </div>
    </footer>

    <script src="js/create-annonce.js"></script>
</body>
</html>