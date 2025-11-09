# 🔐 DormQuest - Système de Connexion

## ✅ Fichiers créés

### 1. **login.php** - Page de connexion
- Formulaire de connexion avec email/mot de passe
- Option "Se souvenir de moi"
- Lien vers mot de passe oublié
- Liens rapides vers inscription étudiant/loueur
- Validation des identifiants
- Création de session
- Redirection selon le rôle (étudiant/loueur)

### 2. **includes/auth.php** - Gestion de l'authentification
Fonctions disponibles :
- `is_logged_in()` - Vérifie si connecté
- `has_role($role)` - Vérifie le rôle
- `is_etudiant()` - Vérifie si étudiant
- `is_loueur()` - Vérifie si loueur
- `require_login()` - Exige connexion
- `require_etudiant()` - Exige rôle étudiant
- `require_loueur()` - Exige rôle loueur
- `logout()` - Déconnexion
- `get_user_id()` - Récupère l'ID utilisateur
- `get_user_fullname()` - Récupère le nom complet
- `get_user_photo()` - Récupère la photo de profil
- `check_session_timeout()` - Vérifie expiration session

### 3. **logout.php** - Déconnexion
- Déconnecte l'utilisateur
- Détruit la session
- Supprime les cookies
- Redirige vers l'accueil

### 4. **dashboard-etudiant.php** - Dashboard étudiant (temporaire)
- Affiche les informations de l'étudiant
- Actions rapides (recherche, favoris, candidatures, profil)
- Utilise `require_etudiant()` pour protéger l'accès

### 5. **dashboard-loueur.php** - Dashboard loueur (temporaire)
- Affiche les informations du loueur
- Actions rapides (créer annonce, mes annonces, candidatures, profil)
- Utilise `require_loueur()` pour protéger l'accès

### 6. **js/login.js** - JavaScript de la page de connexion
- Toggle afficher/masquer mot de passe
- Validation en temps réel de l'email
- Détection Caps Lock
- Animations d'entrée
- Désactivation du bouton après soumission
- Persistance du "Se souvenir de moi"

### 7. **CSS ajouté dans forms.css**
- Styles pour le conteneur étroit
- Styles pour le toggle mot de passe
- Styles pour les checkboxes
- Styles pour le séparateur
- Styles pour les boutons de connexion rapide

---

## 🔄 Flux de connexion

```
1. Utilisateur va sur login.php
   ↓
2. Remplit email + mot de passe
   ↓
3. Validation côté serveur (login.php)
   ↓
4. Vérification dans la BDD (table utilisateurs)
   ↓
5. Si correct : Création de session
   ↓
6. Mise à jour derniereConnexion
   ↓
7. Redirection selon le rôle :
   - Étudiant → dashboard-etudiant.php
   - Loueur → dashboard-loueur.php
```

---

## 🧪 Tests à effectuer

### Test 1 : Connexion étudiant
1. Aller sur `http://localhost/dormquest/login.php`
2. Utiliser un compte étudiant créé via register.php
3. Vérifier redirection vers `dashboard-etudiant.php`
4. Vérifier affichage des informations (nom, email, ville, budget)

### Test 2 : Connexion loueur
1. Aller sur `http://localhost/dormquest/login.php`
2. Utiliser un compte loueur créé via register.php
3. Vérifier redirection vers `dashboard-loueur.php`
4. Vérifier affichage des informations (nom, email, type, téléphone)

### Test 3 : Mauvais identifiants
1. Entrer un email inexistant
2. Vérifier message d'erreur : "Email ou mot de passe incorrect"

### Test 4 : Déconnexion
1. Cliquer sur "Déconnexion"
2. Vérifier redirection vers `index.php`
3. Essayer d'accéder à `dashboard-etudiant.php`
4. Vérifier redirection automatique vers `login.php`

### Test 5 : Protection des pages
1. Se déconnecter
2. Tenter d'accéder directement à `dashboard-etudiant.php`
3. Vérifier redirection automatique vers `login.php`

### Test 6 : "Se souvenir de moi"
1. Cocher la case "Se souvenir de moi"
2. Se connecter
3. Fermer le navigateur
4. Rouvrir et vérifier que la case est toujours cochée

---

## 🔒 Sécurité implémentée

### ✅ Protection des mots de passe
- Hash avec `password_hash()` (bcrypt)
- Vérification avec `password_verify()`

### ✅ Protection des sessions
- Régénération d'ID de session après connexion
- Timeout d'inactivité (1 heure par défaut)
- Vérification automatique d'expiration

### ✅ Protection contre les attaques
- Protection CSRF (tokens)
- Validation des données côté serveur
- Échappement HTML avec `htmlspecialchars()`
- Requêtes préparées PDO

### ✅ Gestion des rôles
- Séparation étudiant/loueur
- Vérification du rôle avant chaque action
- Redirection automatique selon le rôle

---

## 📊 Données de session stockées

Après connexion, la session contient :
```php
$_SESSION['user_id']       // ID de l'utilisateur
$_SESSION['user_role']     // 'etudiant' ou 'loueur'
$_SESSION['user_prenom']   // Prénom
$_SESSION['user_nom']      // Nom
$_SESSION['user_email']    // Email
$_SESSION['user_photo']    // Chemin vers la photo
$_SESSION['login_time']    // Timestamp de connexion
```

---

## 🛡️ Utilisation dans les pages

### Protéger une page pour les utilisateurs connectés
```php
<?php
session_start();
require_once 'includes/auth.php';

// Exiger que l'utilisateur soit connecté
require_login();
?>
```

### Protéger une page pour les étudiants uniquement
```php
<?php
session_start();
require_once 'includes/auth.php';

// Exiger que l'utilisateur soit un étudiant
require_etudiant();
?>
```

### Protéger une page pour les loueurs uniquement
```php
<?php
session_start();
require_once 'includes/auth.php';

// Exiger que l'utilisateur soit un loueur
require_loueur();
?>
```

### Afficher du contenu selon l'état de connexion
```php
<?php if (is_logged_in()): ?>
    <p>Bienvenue <?php echo get_user_prenom(); ?> !</p>
<?php else: ?>
    <a href="login.php">Se connecter</a>
<?php endif; ?>
```

---

## 🚀 Prochaines étapes

### Pages à créer ensuite :
1. ✅ ~~login.php~~ (Fait !)
2. ✅ ~~dashboard-etudiant.php~~ (Temporaire)
3. ✅ ~~dashboard-loueur.php~~ (Temporaire)
4. 🔜 **annonces.php** - Liste des annonces
5. 🔜 **annonce.php** - Détail d'une annonce
6. 🔜 **create-annonce.php** - Créer une annonce (loueur)
7. 🔜 **favoris.php** - Favoris (étudiant)
8. 🔜 **candidatures.php** - Candidatures (étudiant)
9. 🔜 **mes-annonces.php** - Gérer mes annonces (loueur)
10. 🔜 **profil.php** - Modifier le profil
11. 🔜 **mot-de-passe-oublie.php** - Récupération mot de passe

---

## 🐛 Dépannage

### Problème : "Erreur de connexion à la base de données"
**Solution :** Vérifier `includes/db.php` et que MySQL est démarré

### Problème : Redirection infinie
**Solution :** Vérifier que les fichiers `dashboard-*.php` existent

### Problème : Session perdue après rafraîchissement
**Solution :** Vérifier que `session_start()` est appelé en début de fichier

### Problème : Mot de passe incorrect alors qu'il est bon
**Solution :** Vérifier que le mot de passe en BDD est bien hashé avec `password_hash()`

---

## 📝 Comptes de test

Si tu as importé `database_improvements.sql`, ces comptes existent :

**Étudiant :**
- Email : `jean.dupont@test.com`
- Mot de passe : `password`

**Loueur :**
- Email : `marie.martin@test.com`
- Mot de passe : `password`

---

## 🎉 Félicitations !

Le système de connexion est maintenant fonctionnel ! Tu peux :
- ✅ T'inscrire (register.php)
- ✅ Te connecter (login.php)
- ✅ Accéder à ton dashboard selon ton rôle
- ✅ Te déconnecter (logout.php)
- ✅ Être redirigé automatiquement selon ton rôle

**Prochaine étape recommandée :** Créer la page de liste des annonces ! 🏠