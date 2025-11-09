# 📝 Création d'Annonce - Guide Complet

## ✅ Fichiers créés

### 1. **create-annonce.php**
Formulaire complet de création d'annonce avec :
- ✅ 5 sections organisées logiquement
- ✅ Validation côté serveur complète
- ✅ Gestion d'erreurs détaillée
- ✅ Insertion en BDD avec transaction
- ✅ Redirection avec message de succès
- ✅ Pré-remplissage des contacts (email, téléphone)

### 2. **js/create-annonce.js**
JavaScript avancé avec :
- ✅ Compteur de caractères pour la description
- ✅ Validation en temps réel
- ✅ Formatage automatique (code postal, téléphone)
- ✅ Calcul du prix au m²
- ✅ Sauvegarde automatique en brouillon (localStorage)
- ✅ Suggestions de titre selon le type
- ✅ Auto-capitalisation de la ville
- ✅ Animations au scroll

### 3. **CSS ajouté dans forms.css**
- ✅ Checkbox cards pour les critères
- ✅ Layout en grille responsive
- ✅ Boutons multiples (Annuler/Publier)
- ✅ Container large pour le formulaire

---

## 📋 Structure du formulaire

### Section 1 : Informations générales 📝
- **Titre** (min 10 caractères) *
- **Description** (min 50 caractères, compteur) *
- **Type de logement** (studio, colocation, résidence, chambre) *
- **Étiquette énergétique** (A-G, optionnel)

### Section 2 : Localisation 📍
- **Adresse complète** *
- **Ville** (auto-capitalisation) *
- **Code postal** (5 chiffres) *

### Section 3 : Caractéristiques 🏠
- **Prix mensuel** (€, avec calcul prix/m²) *
- **Superficie** (m²) *
- **Nombre de pièces** *
- **Date de disponibilité** (optionnel)

### Section 4 : Critères et équipements ✨
Checkbox cards visuelles :
- 🛋️ Meublé
- 👥 Colocation possible
- 🚗 Parking
- 💰 Éligible APL
- 🎓 Accepte boursiers
- ♿ Accès PMR
- 🐾 Animaux acceptés

### Section 5 : Contact 📞
- **Email de contact** (pré-rempli) *
- **Téléphone de contact** (pré-rempli) *

---

## 🎯 Fonctionnalités principales

### ✨ Validation en temps réel

#### Titre
- Bordure rouge si < 10 caractères
- Bordure verte si ≥ 10 caractères
- Suggestion selon le type de logement

#### Description
- Compteur de caractères en temps réel
- Couleur change selon la longueur :
  - Rouge : < 50 caractères
  - Orange : 50-99 caractères
  - Vert : ≥ 100 caractères

#### Code postal
- Accepte uniquement les chiffres
- Limite automatique à 5 chiffres
- Bordure verte quand valide

#### Téléphone
- Accepte uniquement les chiffres
- Limite automatique à 10 chiffres
- Bordure verte quand valide

#### Prix et superficie
- Bordure verte si > 0
- Calcul automatique du prix au m²
- Affichage : "💡 Prix au m² : XX.XX €/m²"

### 💾 Sauvegarde automatique

- **Brouillon auto** : Toutes les 30 secondes
- **Stockage** : localStorage (navigateur)
- **Récupération** : Proposition au chargement
- **Nettoyage** : Suppression après publication

### 🎨 Expérience utilisateur

- **Animations** : Sections apparaissent au scroll
- **Auto-capitalisation** : Ville formatée automatiquement
- **Tooltips** : Aides contextuelles
- **Indicateurs** : Sauvegarde discrète en bas à droite
- **Confirmation** : Désactivation du bouton après soumission

---

## 🔒 Validation côté serveur

### Champs obligatoires
- Titre (≥ 10 caractères)
- Description (≥ 50 caractères)
- Adresse
- Ville
- Code postal (format : 5 chiffres)
- Type de logement
- Prix mensuel (> 0)
- Superficie (> 0)
- Nombre de pièces (≥ 1)
- Email de contact (format valide)
- Téléphone (10 chiffres)

### Sécurité
- ✅ Protection contre injection SQL (requêtes préparées)
- ✅ Validation des types de données
- ✅ Échappement HTML
- ✅ Vérification du rôle (require_loueur)
- ✅ Transaction BDD (rollback en cas d'erreur)

---

## 💾 Base de données

### Tables modifiées

#### 1. annonces
```sql
INSERT INTO annonces (
    idLoueur, titre, description, adresse, ville, codePostal,
    typeLogement, prixMensuel, superficie, nombrePieces,
    colocationPossible, empreinteEnergie, dateDisponibilite,
    contactEmail, contactTelephone, statut
) VALUES (...)
```

#### 2. criteres_logement
```sql
INSERT INTO criteres_logement (
    idAnnonce, accesPMR, eligibleAPL, statutBoursier,
    animauxAcceptes, parkingDisponible, meuble
) VALUES (...)
```

### Transaction
```php
$pdo->beginTransaction();
// Insertion annonce
// Insertion critères
$pdo->commit();
// En cas d'erreur : $pdo->rollBack();
```

---

## 🚀 Tester la création

### Étape 1 : Accéder au formulaire

1. Connecte-toi en tant que loueur
2. Dashboard → **"Créer une annonce"**
3. Ou URL directe : `http://localhost/dormquest/create-annonce.php`

### Étape 2 : Remplir le formulaire

**Exemple de test rapide :**

```
Titre: Studio lumineux proche Sorbonne
Description: Charmant studio de 25m² situé au cœur du Quartier Latin, à deux pas de la Sorbonne. Entièrement meublé et équipé.
Adresse: 12 rue de la Sorbonne
Ville: Paris
Code postal: 75005
Type: Studio
Étiquette: D
Prix: 750
Superficie: 25
Pièces: 1
Email: ton-email@exemple.com
Téléphone: 0612345678

Critères: Meublé ✓, Éligible APL ✓
```

### Étape 3 : Publier

1. Clique sur **"Publier l'annonce"**
2. Le bouton affiche "Publication en cours..."
3. Redirection vers le dashboard
4. Message : "✅ Annonce créée avec succès !"
5. L'annonce apparaît dans la liste

---

## 🎨 Personnalisation

### Modifier les types de logements

Dans `create-annonce.php`, ligne ~170 :
```php
<option value="studio">Studio</option>
<option value="T1">T1</option>
<option value="T2">T2</option>
// Ajouter d'autres types ici
```

### Modifier les critères

Ajouter une checkbox card dans la section 4 :
```html
<label class="form-checkbox-card">
    <input type="checkbox" name="nouveau_critere" class="form-checkbox-card__input">
    <div class="form-checkbox-card__content">
        <span class="form-checkbox-card__icon">🎯</span>
        <span class="form-checkbox-card__label">Nouveau critère</span>
    </div>
</label>
```

### Changer la durée de sauvegarde auto

Dans `create-annonce.js`, ligne ~285 :
```javascript
}, 30000); // 30 secondes → Modifier ici
```

---

## 🐛 Résolution de problèmes

### Problème : Erreur "Erreur lors de la création"

**Solution :**
1. Vérifie les logs PHP
2. Vérifie que toutes les tables existent
3. Vérifie les types de données en BDD

### Problème : Brouillon ne se charge pas

**Solution :**
1. Vérifie la console (F12)
2. Vide le localStorage : `localStorage.clear()`
3. Recharge la page

### Problème : Téléphone ou code postal non valide

**Solution :**
- Le téléphone doit contenir EXACTEMENT 10 chiffres
- Le code postal doit contenir EXACTEMENT 5 chiffres
- Enlève les espaces

### Problème : Calcul prix/m² ne s'affiche pas

**Solution :**
1. Entre d'abord le prix
2. Puis la superficie
3. Le calcul apparaît automatiquement

---

## 📊 Statistiques du formulaire

- **5 sections** organisées logiquement
- **17 champs** au total (12 requis, 5 optionnels)
- **7 critères** en checkbox cards
- **2 validations** (client + serveur)
- **30 secondes** de sauvegarde automatique
- **~400 lignes** de code PHP
- **~300 lignes** de code JavaScript

---

## 🎓 Bonnes pratiques implémentées

### UX Design
- ✅ Labels clairs avec astérisques pour les champs obligatoires
- ✅ Placeholders avec exemples concrets
- ✅ Hints explicatifs sous les champs
- ✅ Feedback visuel instantané (couleurs)
- ✅ Animations douces et progressives

### Développement
- ✅ Code organisé et commenté
- ✅ Méthodologie BEM pour le CSS
- ✅ Validation double (client/serveur)
- ✅ Transactions BDD pour l'intégrité
- ✅ Gestion d'erreurs complète

### Sécurité
- ✅ Protection XSS (htmlspecialchars)
- ✅ Protection SQL injection (requêtes préparées)
- ✅ Vérification du rôle utilisateur
- ✅ Validation des types de données
- ✅ Nettoyage des entrées (trim)

---

## 🔜 Améliorations futures possibles

### Fonctionnalités
- [ ] Upload de photos (multiple)
- [ ] Aperçu en temps réel
- [ ] Validation d'adresse avec API
- [ ] Suggestion de prix basée sur la localisation
- [ ] Import depuis un fichier
- [ ] Duplication d'annonce existante

### UX
- [ ] Barre de progression multi-étapes
- [ ] Mode sombre
- [ ] Accessibilité WCAG AA
- [ ] Support multi-langues

---

## 🎉 Prochaines étapes

### Pages liées à créer :

1. **edit-annonce.php** ✏️
   - Même formulaire mais pré-rempli
   - Modification d'annonce existante

2. **annonce.php** 👁️
   - Affichage détaillé d'une annonce
   - Boutons d'action selon le rôle

3. **annonces.php** 📋
   - Liste publique des annonces
   - Système de recherche/filtres

4. **candidatures-annonce.php** 📬
   - Liste des candidatures reçues
   - Actions : Accepter/Refuser

---

## 📞 Support

Pour toute question :
- Email : contact@dormquest.fr
- Documentation : À venir

---

## 🎉 Félicitations !

Le formulaire de création d'annonce est maintenant **100% fonctionnel** !

Tu peux :
- ✅ Créer des annonces complètes
- ✅ Validation en temps réel
- ✅ Sauvegarde automatique
- ✅ Calculs automatiques
- ✅ Expérience utilisateur optimale

**Prochaine étape recommandée :** Créer la page d'affichage détaillé d'annonce ! 👁️