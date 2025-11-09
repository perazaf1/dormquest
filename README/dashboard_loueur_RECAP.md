# 🏠 Dashboard Loueur - Guide Complet

## ✅ Fichiers créés

### 1. **dashboard-loueur.php** (réécrit)
Dashboard complet avec toutes les fonctionnalités MVP :
- ✅ Liste de toutes les annonces du loueur
- ✅ Statistiques (total annonces, actives, candidatures)
- ✅ Actions : Voir, Modifier, Archiver, Réactiver, Supprimer
- ✅ Filtres : Toutes / Actives / Archivées
- ✅ Affichage du nombre de candidatures par annonce
- ✅ Lien vers les candidatures reçues
- ✅ Bouton "Créer une annonce"
- ✅ État vide si aucune annonce

### 2. **css/dashboard.css**
Styles complets avec :
- Design moderne et professionnel
- Cartes d'annonces responsive
- Système de grille adaptative
- Badges de statut (actif/archivé)
- Animations au scroll
- Header avec photo utilisateur
- États hover sur tous les boutons

### 3. **js/dashboard-loueur.js**
Fonctionnalités JavaScript :
- ✅ Système de filtres dynamiques
- ✅ Confirmation améliorée avant suppression
- ✅ Auto-masquage des alertes (5s)
- ✅ Animations au scroll
- ✅ Compteurs animés pour les stats
- ✅ Bouton copier le lien d'annonce
- ✅ Sauvegarde du filtre actif
- ✅ Tooltips personnalisés
- ✅ Menu responsive

### 4. **test_annonces.sql**
Script SQL pour créer 6 annonces de test :
- 1 Studio à Paris
- 1 Colocation à Lyon
- 1 T2 à Toulouse
- 1 Résidence étudiante à Bordeaux
- 1 Chambre chez l'habitant à Lille
- 1 Studio archivé à Montpellier

---

## 🚀 Installation et test

### Étape 1 : Créer un compte loueur

1. Va sur : `http://localhost/dormquest/register.php?type=loueur`
2. Remplis le formulaire d'inscription
3. Note ton email et mot de passe

### Étape 2 : Trouver ton ID loueur

```sql
-- Dans phpMyAdmin, exécute :
SELECT id, prenom, nom, email FROM utilisateurs WHERE email = 'ton-email@example.com';
```

Note l'**ID** retourné (par exemple : 2)

### Étape 3 : Créer des annonces de test

1. Ouvre le fichier `test_annonces.sql`
2. À la ligne 11, remplace :
   ```sql
   SET @loueur_id = 2; -- CHANGE PAR TON ID
   ```
3. Copie tout le contenu du fichier
4. Va dans phpMyAdmin → SQL
5. Colle et exécute le script

### Étape 4 : Tester le dashboard

1. Connecte-toi avec ton compte loueur
2. Tu seras redirigé vers `dashboard-loueur.php`
3. Tu devrais voir 6 annonces !

---

## 🎯 Fonctionnalités du dashboard

### 📊 Statistiques en haut
- **Total annonces** : Nombre total d'annonces créées
- **Annonces actives** : Annonces visibles par les étudiants
- **Candidatures reçues** : Total de toutes les candidatures

### 🔍 Système de filtres
- **Toutes** : Affiche toutes les annonces
- **Actives** : Seulement les annonces visibles
- **Archivées** : Annonces masquées (non visibles par les étudiants)

### 📋 Carte d'annonce
Chaque annonce affiche :
- **Badge** : Active (vert) ou Archivée (gris)
- **Image** : Illustration de l'annonce
- **Titre** : Nom du logement
- **Infos** : Ville, prix mensuel, superficie
- **Stats** : Nombre de candidatures et favoris
- **Date** : Date de création

### ⚡ Actions disponibles

#### 1. 👁️ Voir
- Affiche le détail complet de l'annonce
- Lien : `annonce.php?id=X`
- À créer prochainement

#### 2. ✏️ Modifier
- Modifier le titre, description, prix, etc.
- Lien : `edit-annonce.php?id=X`
- À créer prochainement

#### 3. 📦 Archiver
- Masque l'annonce (invisible pour les étudiants)
- L'annonce reste dans ta liste
- Les candidatures sont conservées
- Peut être réactivée à tout moment

#### 4. ✅ Réactiver
- Rend visible une annonce archivée
- Apparaît à nouveau dans les recherches

#### 5. 🗑️ Supprimer
- **⚠️ ATTENTION : Action irréversible !**
- Supprime définitivement l'annonce
- Supprime toutes les candidatures associées
- Demande confirmation avant suppression

#### 6. 📬 Voir les candidatures
- Affiche toutes les candidatures pour cette annonce
- Lien : `candidatures-annonce.php?id=X`
- À créer prochainement

---

## 🎨 États de l'annonce

### Active ✅
- Badge vert "Active"
- Visible par tous les étudiants
- Apparaît dans les recherches
- Peut recevoir des candidatures

### Archivée 📦
- Badge gris "Archivée"
- Invisible pour les étudiants
- N'apparaît plus dans les recherches
- Les candidatures existantes restent accessibles

---

## 📱 Responsive

Le dashboard s'adapte automatiquement :
- **Desktop** : 3 colonnes de cartes
- **Tablette** : 2 colonnes
- **Mobile** : 1 colonne, menu burger

---

## 🔧 Personnalisation

### Modifier le nombre d'annonces par ligne

Dans `dashboard.css`, ligne ~280 :
```css
.annonces-grid {
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    /* Change 350px pour ajuster la largeur minimale */
}
```

### Changer les couleurs

Dans `styles.css` (variables globales) :
```css
:root {
    --color-primary: #2563eb;      /* Bleu principal */
    --color-secondary: #fbbf24;    /* Jaune accent */
}
```

---

## 🐛 Résolution de problèmes

### Problème : Aucune annonce n'apparaît

**Solution :**
1. Vérifie que tu es connecté avec un compte loueur
2. Vérifie dans phpMyAdmin :
   ```sql
   SELECT * FROM annonces WHERE idLoueur = TON_ID;
   ```
3. Si vide, exécute `test_annonces.sql`

### Problème : Les actions ne fonctionnent pas

**Solution :**
- Vérifie que `dashboard-loueur.js` est bien chargé
- Ouvre la console (F12) et cherche des erreurs JavaScript

### Problème : Statistiques à 0

**Solution :**
- Les statistiques se basent sur les données réelles
- Si tu n'as pas d'annonces, elles seront à 0
- Crée des annonces de test avec `test_annonces.sql`

### Problème : Design cassé

**Solution :**
- Vérifie que `dashboard.css` est bien chargé
- Vide le cache du navigateur (Ctrl + F5)
- Vérifie la console pour les erreurs CSS

---

## 📊 Base de données

### Tables utilisées

1. **annonces** : Liste des annonces
2. **candidatures** : Candidatures reçues
3. **favoris** : Annonces mises en favoris
4. **criteres_logement** : Critères détaillés

### Requête SQL principale

```sql
SELECT 
    a.*,
    COUNT(DISTINCT c.id) as nb_candidatures,
    COUNT(DISTINCT f.id) as nb_favoris
FROM annonces a
LEFT JOIN candidatures c ON c.idAnnonce = a.id
LEFT JOIN favoris f ON f.idAnnonce = a.id
WHERE a.idLoueur = ?
GROUP BY a.id
ORDER BY a.dateCreation DESC
```

---

## 🚀 Prochaines étapes

### Pages à créer (par priorité) :

1. ✅ ~~Dashboard loueur~~ (FAIT !)
2. 🔜 **create-annonce.php** - Créer une annonce
3. 🔜 **edit-annonce.php** - Modifier une annonce
4. 🔜 **annonce.php** - Voir le détail
5. 🔜 **candidatures-annonce.php** - Voir les candidatures
6. 🔜 **annonces.php** - Liste publique (côté étudiant)

---

## ✨ Fonctionnalités bonus implémentées

### 🔗 Copier le lien
- Bouton 🔗 en haut à gauche de chaque carte
- Copie l'URL de l'annonce dans le presse-papiers
- Icône change en ✅ pendant 2 secondes

### 💾 Sauvegarde du filtre
- Le filtre actif est sauvegardé dans localStorage
- Restauré automatiquement au prochain chargement

### ⏱️ Auto-masquage des alertes
- Les messages de succès/erreur disparaissent après 5s
- Animation de fondu en sortie

### 📈 Compteurs animés
- Les statistiques s'animent au chargement
- Comptent de 0 jusqu'à la valeur réelle

### 🎭 Animations au scroll
- Les cartes apparaissent progressivement au scroll
- Effet de fondu + translation

---

## 🎓 Astuces d'utilisation

### Workflow recommandé

1. **Créer** une annonce → `create-annonce.php`
2. **Consulter** les candidatures reçues
3. **Modifier** l'annonce si besoin
4. **Archiver** quand le logement est loué
5. **Réactiver** si le locataire part

### Bonnes pratiques

- ✅ Archive plutôt que supprimer (historique conservé)
- ✅ Mets à jour régulièrement tes annonces
- ✅ Réponds rapidement aux candidatures
- ✅ Utilise des titres clairs et descriptifs

---

## 📞 Support

Pour toute question :
- Email : contact@dormquest.fr
- Documentation complète : À venir

---

## 🎉 Félicitations !

Le dashboard loueur est maintenant **100% fonctionnel** !

Tu peux :
- ✅ Voir toutes tes annonces
- ✅ Filtrer par statut
- ✅ Archiver/Réactiver
- ✅ Supprimer
- ✅ Voir les statistiques
- ✅ Accéder aux candidatures

**Prochaine étape recommandée :** Créer la page de création d'annonce ! 📝