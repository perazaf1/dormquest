# ⭐ Page Favoris - Guide Complet

## ✅ Fichiers créés

### 1. **favoris.php**
Page complète des favoris avec :
- ✅ Liste de tous les favoris de l'étudiant
- ✅ Statistiques (nombre, prix moyen, superficie moyenne)
- ✅ Actions : Voir, Postuler, Comparer, Retirer
- ✅ Système de comparaison avec modal
- ✅ État vide si aucun favori
- ✅ Badge "Candidature envoyée"

### 2. **css/favoris.css**
Styles complets avec :
- ✅ Cartes de favoris modernes
- ✅ Modal de comparaison responsive
- ✅ Animations au scroll
- ✅ Badge de date d'ajout
- ✅ Design cohérent avec le reste du site

### 3. **js/favoris.js**
JavaScript avancé avec :
- ✅ Système de comparaison (sélection + modal)
- ✅ Tableau comparatif dynamique
- ✅ Compteurs animés pour les stats
- ✅ Confirmation améliorée de suppression
- ✅ Auto-masquage des alertes
- ✅ Export PDF (préparé)

---

## 🎯 Fonctionnalités principales

### ⭐ **Affichage des favoris**

**Chaque carte contient :**
- Date d'ajout (badge en haut à droite)
- Photo du logement
- Badge du type
- Titre + Localisation
- Prix mis en évidence
- Détails (superficie, pièces)
- Tags des critères (meublé, APL, parking)
- Badge "Candidature envoyée" (si applicable)
- 4 boutons d'action

### 📊 **Statistiques**

**3 cartes en haut :**
1. **Nombre de favoris** ⭐
2. **Prix moyen** 💰
3. **Superficie moyenne** 📐

Avec compteurs animés au chargement !

### 🔍 **Système de comparaison**

**Workflow :**
```
1. Clique sur "📊 Comparer" sur 2-3 annonces
2. Les boutons deviennent actifs (bleus)
3. Le bouton principal affiche "📊 Comparer (2)"
4. Clique sur le bouton principal
5. Modal s'ouvre avec tableau comparatif
6. Compare prix, superficie, critères, etc.
```

**Critères comparés :**
- 📍 Ville
- 💰 Prix mensuel
- 📐 Superficie
- 💵 Prix au m²
- 🚪 Nombre de pièces
- 🏠 Type de logement
- 🛋️ Meublé
- 💰 Éligible APL
- 🚗 Parking
- ♿ Accès PMR

### 🎬 **Actions disponibles**

1. **👁️ Voir l'annonce** : Redirige vers la page de détail
2. **📨 Postuler** : Redirige vers le formulaire de candidature
3. **📊 Comparer** : Sélectionne pour la comparaison
4. **❌ Retirer** : Supprime des favoris (avec confirmation)

---

## 💾 **Base de données**

### Requête SQL principale

```sql
SELECT 
    f.id AS favori_id,
    f.dateAjout,
    a.*,
    u.prenom, u.nom, u.typeLoueur,
    c.meuble, c.eligibleAPL, c.parkingDisponible, c.accesPMR,
    (SELECT COUNT(*) FROM candidatures 
     WHERE idAnnonce = a.id AND idEtudiant = ?) as has_candidated
FROM favoris f
JOIN annonces a ON f.idAnnonce = a.id
JOIN utilisateurs u ON a.idLoueur = u.id
LEFT JOIN criteres_logement c ON c.idAnnonce = a.id
WHERE f.idEtudiant = ? AND a.statut = 'active'
ORDER BY f.dateAjout DESC
```

### Actions BDD

**Retirer un favori :**
```sql
DELETE FROM favoris 
WHERE idEtudiant = ? AND idAnnonce = ?
```

---

## 🎨 **Design**

### Carte de favori

```
┌─────────────────────────────┐
│ [Date ajout]          [Type]│
│                              │
│        [PHOTO]               │
│                              │
├─────────────────────────────┤
│ Titre de l'annonce          │
│ 📍 Ville (75000)            │
│                              │
│ 750 € /mois                 │
│                              │
│ 📐 25 m²  🚪 1 pièce        │
│ ───────────────────────────│
│ 🛋️ Meublé  💰 APL          │
│                              │
│ ✅ Candidature envoyée      │
├─────────────────────────────┤
│ [Voir] [Postuler]           │
│ [Comparer] [Retirer]        │
└─────────────────────────────┘
```

### Modal de comparaison

```
┌──────────────────────────────────────┐
│  📊 Comparaison des annonces    [✕] │
├──────────────────────────────────────┤
│ Critère      │ Annonce 1│ Annonce 2 │
├──────────────┼──────────┼───────────┤
│ 📍 Ville     │ Paris    │ Lyon      │
│ 💰 Prix      │ 750 €    │ 600 €     │
│ 📐 Superficie│ 25 m²    │ 30 m²     │
│ 💵 Prix/m²   │ 30 €/m²  │ 20 €/m²   │
│ ...          │ ...      │ ...       │
└──────────────────────────────────────┘
```

---

## 🚀 **Tester la page**

### Étape 1 : Ajouter des favoris

```
1. Va sur annonces.php
2. Connecte-toi en tant qu'étudiant
3. Clique sur ⭐ sur 3-4 annonces
4. Les étoiles deviennent jaunes
```

### Étape 2 : Voir les favoris

```
1. Clique sur "Mes favoris" dans le header
2. Ou va sur : http://localhost/dormquest/favoris.php
3. Tu verras toutes tes annonces favorites
```

### Étape 3 : Comparer

```
1. Clique sur "📊 Comparer" sur 2 annonces
2. Les boutons deviennent bleus
3. Clique sur le bouton "📊 Comparer (2)" en haut
4. La modal s'ouvre avec le tableau
```

### Étape 4 : Retirer

```
1. Clique sur "❌ Retirer"
2. Confirme dans l'alerte
3. L'annonce disparaît
4. Message de succès s'affiche
```

---

## 📊 **Statistiques calculées**

### Prix moyen
```php
$prix_moyen = array_sum(array_column($favoris, 'prixMensuel')) / $total_favoris;
```

### Superficie moyenne
```php
$superficie_moyenne = array_sum(array_column($favoris, 'superficie')) / $total_favoris;
```

---

## 🔧 **Personnalisation**

### Changer le nombre max d'annonces comparables

Dans `favoris.js`, ligne ~28 :
```javascript
if (selectedForComparison.length >= 3) { // Change 3 ici
    alert('Vous ne pouvez comparer que 3 annonces maximum');
    return;
}
```

### Ajouter un critère de comparaison

Dans `favoris.js`, après les critères existants :
```javascript
// Nouveau critère
html += '<tr>';
html += '<td class="comparison-table__label">🎯 Nouveau critère</td>';
selectedAnnonces.forEach(a => {
    html += `<td class="comparison-table__value">${a.nouveau_critere}</td>`;
});
html += '</tr>';
```

### Modifier les stats affichées

Dans `favoris.php`, après `$superficie_moyenne` :
```php
// Nouvelle stat
$nb_meubles = count(array_filter($favoris, fn($f) => $f['meuble']));
```

Puis dans le HTML :
```html
<div class="stat-card">
    <div class="stat-card__icon">🛋️</div>
    <div class="stat-card__content">
        <div class="stat-card__value"><?php echo $nb_meubles; ?></div>
        <div class="stat-card__label">Meublés</div>
    </div>
</div>
```

---

## 🐛 **Résolution de problèmes**

### Problème : Aucun favori n'apparaît

**Solution :**
1. Vérifie que tu es connecté en tant qu'étudiant
2. Vérifie dans phpMyAdmin :
   ```sql
   SELECT * FROM favoris WHERE idEtudiant = TON_ID;
   ```
3. Ajoute des favoris depuis annonces.php

### Problème : La comparaison ne fonctionne pas

**Solution :**
1. Ouvre la console (F12)
2. Vérifie que `favorisData` est défini
3. Vérifie qu'il y a au moins 2 annonces
4. Vérifie que les boutons ont bien l'attribut `data-annonce-id`

### Problème : Le modal ne s'ouvre pas

**Solution :**
1. Vérifie que `comparison-modal` existe dans le DOM
2. Ouvre la console et cherche les erreurs JavaScript
3. Vérifie que tu as bien sélectionné 2+ annonces

### Problème : Les stats ne s'animent pas

**Solution :**
1. Vérifie que `favoris.js` est bien chargé
2. Vérifie la console pour des erreurs
3. Vérifie que les `.stat-card__value` existent

---

## 💡 **Fonctionnalités bonus implémentées**

### 1. Badge "Candidature envoyée"
Si l'étudiant a déjà postulé, un badge vert s'affiche avec "✅ Candidature envoyée"

### 2. Compteurs animés
Les statistiques s'animent de 0 à la valeur réelle au chargement

### 3. Confirmation améliorée
La suppression affiche le titre de l'annonce dans la confirmation

### 4. Animations au scroll
Les cartes apparaissent progressivement au scroll

### 5. Export PDF (préparé)
Bouton "📥 Exporter en PDF" prêt pour intégration future

---

## 🔜 **Améliorations possibles**

### Fonctionnalités
- [ ] Tri (date, prix, superficie)
- [ ] Filtres (ville, type, budget)
- [ ] Notes personnelles sur chaque favori
- [ ] Partage de favoris par email
- [ ] Export PDF réel (jsPDF)
- [ ] Graphiques de comparaison (Chart.js)
- [ ] Alertes email (baisse de prix)

### UX
- [ ] Drag & drop pour réorganiser
- [ ] Vue grille / liste (toggle)
- [ ] Favoris partagés (famille)
- [ ] Historique des favoris retirés

---

## 📈 **Statistiques**

### Code
- **350+ lignes** de PHP
- **450+ lignes** de CSS
- **400+ lignes** de JavaScript
- **3 fichiers** créés

### Fonctionnalités
- **4 actions** par favori
- **10 critères** comparables
- **3 statistiques** affichées
- **1 modal** de comparaison
- **Max 3** annonces comparables

---

## 🎉 **Félicitations !**

La page de favoris est maintenant **100% fonctionnelle** !

Tu peux :
- ✅ Voir tous tes favoris
- ✅ Comparer jusqu'à 3 annonces
- ✅ Voir les stats (prix, superficie)
- ✅ Retirer des favoris
- ✅ Postuler directement
- ✅ Interface moderne et responsive

**Prochaine page recommandée :** La page des candidatures ! 📨