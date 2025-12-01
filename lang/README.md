# Système d'internationalisation (i18n) - DormQuest

Ce dossier contient le système de traduction multilingue pour DormQuest.

## Structure des fichiers

- `lang.php` - Gestionnaire principal du système de langue
- `fr.php` - Traductions françaises
- `en.php` - Traductions anglaises

## Comment utiliser

### 1. Dans une page PHP

Au début de votre fichier PHP, incluez le système de langue :

```php
<?php
session_start();
require_once __DIR__ . '/lang/lang.php';
?>
```

### 2. Utiliser les traductions

Utilisez la fonction `t()` pour afficher une traduction :

```php
<h1><?= t('hero.title') ?></h1>
<p><?= t('hero.subtitle') ?></p>
```

### 3. Changer la langue

Pour changer la langue, ajoutez un paramètre `lang` dans l'URL :

- Français : `index.php?lang=fr`
- Anglais : `index.php?lang=en`

La langue est stockée dans la session et persiste lors de la navigation.

### 4. Obtenir la langue courante

```php
$current_lang = current_lang(); // Retourne 'fr' ou 'en'
```

### 5. Modifier l'attribut lang du HTML

```php
<html lang="<?= current_lang() ?>">
```

## Ajouter de nouvelles traductions

### 1. Éditer les fichiers de traduction

Ouvrez `fr.php` et `en.php` et ajoutez vos nouvelles clés :

**fr.php :**
```php
'nouvelle_section' => [
    'titre' => 'Mon nouveau titre',
    'texte' => 'Mon nouveau texte',
],
```

**en.php :**
```php
'nouvelle_section' => [
    'titre' => 'My new title',
    'texte' => 'My new text',
],
```

### 2. Utiliser dans votre code

```php
<h2><?= t('nouvelle_section.titre') ?></h2>
<p><?= t('nouvelle_section.texte') ?></p>
```

## Ajouter une nouvelle langue

1. Créez un nouveau fichier (ex: `es.php` pour l'espagnol)
2. Copiez la structure de `fr.php`
3. Traduisez toutes les valeurs
4. Modifiez `lang.php` pour autoriser cette langue :

```php
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en', 'es'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
```

5. Ajoutez le lien dans le sélecteur de langue

## Exemple complet

```php
<?php
session_start();
require_once __DIR__ . '/lang/lang.php';
?>
<!DOCTYPE html>
<html lang="<?= current_lang() ?>">
<head>
    <title><?= t('site.title') ?></title>
</head>
<body>
    <!-- Sélecteur de langue -->
    <a href="?lang=fr">FR</a>
    <a href="?lang=en">EN</a>

    <h1><?= t('hero.title') ?></h1>
    <p><?= t('hero.subtitle') ?></p>
</body>
</html>
```

## Notes importantes

- La fonction `t()` supporte la notation avec points (ex: `'header.login'`)
- Si une clé n'existe pas, la clé elle-même est retournée
- La langue par défaut est le français
- La langue est conservée en session, donc elle persiste lors de la navigation
