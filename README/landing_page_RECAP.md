# 🏠 DormQuest - Guide d'installation et d'utilisation

## 📋 Vue d'ensemble

**DormQuest** est une plateforme web de gestion de logements étudiants permettant de mettre en relation étudiants et loueurs.

**Slogan** : *Trouvez le logement parfait pour vos études !*

**Développé par** : Nyzer

---

## 🛠️ Technologies utilisées

- **Frontend** : HTML5, CSS3 (Méthodologie BEM), JavaScript (Vanilla)
- **Backend** : PHP 7.4+
- **Base de données** : MySQL 5.7+ / MariaDB
- **Serveur local** : XAMPP
- **Hébergement futur** : OVH Cloud

---

## 📁 Structure du projet

```
dormquest/
│
├── index.php                    # Landing page
├── register.php                 # Page d'inscription
├── login.php                    # Page de connexion (à créer)
├── dashboard.php                # Tableau de bord (à créer)
│
├── css/
│   ├── styles.css              # Styles globaux
│   └── forms.css               # Styles des formulaires
│
├── js/
│   ├── main.js                 # JavaScript principal
│   └── register.js             # JavaScript inscription
│
├── images/
│   ├── logo-dormquest.png      # Logo principal
│   ├── logo-nyzer.png          # Logo Nyzer
│   └── hero-illustration.png   # Illustration hero
│
├── includes/
│   ├── db.php                  # Connexion base de données
│   ├── config.php              # Configuration globale
│   ├── header.php              # En-tête réutilisable (à créer)
│   └── footer.php              # Pied de page réutilisable (à créer)
│
├── uploads/
│   ├── profiles/               # Photos de profil
│   └── annonces/               # Photos d'annonces
│
└── sql/
    ├── database.sql            # Structure initiale
    └── improvements.sql        # Améliorations et vues
```

---

## 🚀 Installation

### Étape 1 : Prérequis

- **XAMPP** installé (Apache + MySQL + PHP)
- Navigateur web moderne (Chrome, Firefox, Edge)
- Éditeur de code (VS Code recommandé)

### Étape 2 : Configuration de XAMPP

1. Démarrez **Apache** et **MySQL** depuis le panneau XAMPP
2. Vérifiez que les services sont bien lancés (indicateurs verts)

### Étape 3 : Création du projet

1. Copiez le dossier `dormquest` dans :
   ```
   C:\xampp\htdocs\dormquest\
   ```

2. Vérifiez que tous les fichiers sont bien présents

### Étape 4 : Configuration de la base de données

1. Ouvrez **phpMyAdmin** : `http://localhost/phpmyadmin`

2. Créez la base de données :
   - Cliquez sur "Nouvelle base de données"
   - Nom : `dormquest`
   - Interclassement : `utf8mb4_unicode_ci`
   - Cliquez sur "Créer"

3. Importez les tables :
   - Sélectionnez la base `dormquest`
   - Onglet "SQL"
   - Copiez-collez le contenu de votre fichier SQL existant
   - Cliquez sur "Exécuter"

4. (Optionnel) Importez les améliorations :
   - Onglet "SQL"
   - Copiez-collez le contenu de `database_improvements.sql`
   - Cliquez sur "Exécuter"

### Étape 5 : Configuration de la connexion

Vérifiez le fichier `includes/db.php` :

```php
$host = 'localhost';
$dbname = 'dormquest';  // Nom de votre BDD
$username = 'root';      // Par défaut sur XAMPP
$password = '';          // Vide par défaut sur XAMPP
```

### Étape 6 : Permissions des dossiers

Assurez-vous que les dossiers suivants ont les bonnes permissions :
- `uploads/profiles/` (lecture/écriture)
- `uploads/annonces/` (lecture/écriture)

Sur Windows, les permissions sont généralement correctes par défaut.

---

## 🎨 Charte graphique

### Couleurs principales

- **Bleu principal** : `#2563eb`
- **Bleu foncé** : `#1e40af`
- **Bleu clair** : `#60a5fa`
- **Jaune accent** : `#fbbf24`
- **Blanc** : `#ffffff`
- **Gris clair** : `#f3f4f6`
- **Gris** : `#6b7280`
- **Gris foncé** : `#374151`

### Police

- **Principale** : Segoe UI, Tahoma, Geneva, Verdana, sans-serif

---

## 🧪 Tests

### Tester la landing page

1. Ouvrez votre navigateur
2. Allez sur : `http://localhost/dormquest/`
3. Vérifiez que la page s'affiche correctement

### Tester l'inscription

1. Allez sur : `http://localhost/dormquest/register.php`
2. Remplissez le formulaire
3. Vérifiez dans phpMyAdmin que l'utilisateur est bien créé

### Comptes de test

**Étudiant :**
- Email : `jean.dupont@test.com`
- Mot de passe : `password`

**Loueur :**
- Email : `marie.martin@test.com`
- Mot de passe : `password`

---

## 📊 Base de données

### Tables principales

1. **utilisateurs** : Informations des utilisateurs (étudiants et loueurs)
2. **annonces** : Annonces de logements
3. **photos_annonces** : Photos des annonces
4. **criteres_logement** : Critères spécifiques des logements
5. **favoris** : Annonces favorites des étudiants
6. **candidatures** : Candidatures des étudiants aux annonces

### Vues disponibles

- `vue_annonces_completes` : Annonces avec infos loueur
- `vue_candidatures_completes` : Candidatures avec détails
- `vue_favoris_complets` : Favoris avec détails
- `vue_stats_loueurs` : Statistiques des loueurs

### Procédures stockées

- `rechercher_annonces(ville, prix_max, type_logement)`
- `nb_candidatures_etudiant(etudiant_id)`

---

## 🔧 Configuration avancée

### Mode Debug

Dans `includes/config.php`, modifiez :

```php
define('DEBUG_MODE', true);  // Activer en développement
define('DEBUG_MODE', false); // Désactiver en production
```

### Taille maximale des uploads

Dans `includes/config.php` :

```php
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
```

Dans `php.ini` (XAMPP) :

```ini
upload_max_filesize = 2M
post_max_size = 2M
```

---

## 🐛 Résolution des problèmes

### Erreur : "Erreur de connexion à la base de données"

**Solution :**
1. Vérifiez que MySQL est démarré dans XAMPP
2. Vérifiez les identifiants dans `includes/db.php`
3. Vérifiez que la base `dormquest` existe dans phpMyAdmin

### Erreur : "Upload impossible"

**Solution :**
1. Vérifiez que le dossier `uploads/` existe
2. Vérifiez les permissions du dossier
3. Vérifiez la taille du fichier (max 2MB)

### Page blanche

**Solution :**
1. Activez le mode debug dans `config.php`
2. Consultez les logs Apache : `xampp/apache/logs/error.log`
3. Vérifiez la syntaxe PHP

### Erreur : "Email déjà utilisé"

**Solution :**
- Utilisez un autre email
- Ou supprimez l'utilisateur existant dans phpMyAdmin

---

## 📝 Fonctionnalités implémentées

### ✅ MVP - Version actuelle

- [x] Landing page complète
- [x] Page d'inscription avec choix du rôle
- [x] Validation des formulaires
- [x] Upload de photos
- [x] Structure de base de données complète
- [x] Design responsive

### 🚧 À développer

- [ ] Page de connexion
- [ ] Tableau de bord utilisateur
- [ ] Liste des annonces
- [ ] Détail d'une annonce
- [ ] Création d'annonce (loueur)
- [ ] Système de favoris
- [ ] Système de candidatures
- [ ] Messagerie interne
- [ ] Recherche avancée
- [ ] Filtres dynamiques

---

## 🎯 Prochaines étapes

1. Créer la page de **connexion** (`login.php`)
2. Créer le **tableau de bord** (`dashboard.php`)
3. Créer la page de **liste des annonces** (`annonces.php`)
4. Créer la page de **détail d'annonce** (`annonce.php`)
5. Créer la page de **création d'annonce** (`create-annonce.php`)

---

## 📞 Support

Pour toute question ou problème :
- Email : contact@dormquest.fr
- Documentation : À venir

---

## 📄 Licence

© 2024 DormQuest by Nyzer. Tous droits réservés.

---

## 🙏 Remerciements

Projet développé avec passion pour faciliter la vie des étudiants ! 🎓🏠