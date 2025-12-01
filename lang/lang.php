<?php
// lang/lang.php - Gestionnaire de langue

// Démarre la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définir la langue par défaut
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'fr';
}

// Changer la langue si demandé
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Charger le fichier de traduction approprié
$lang = [];
$lang_file = __DIR__ . '/' . $_SESSION['lang'] . '.php';

if (file_exists($lang_file)) {
    $lang = require $lang_file;
} else {
    // Fallback sur le français si le fichier n'existe pas
    $lang = require __DIR__ . '/fr.php';
}

/**
 * Fonction helper pour obtenir une traduction
 * @param string $key La clé de traduction (ex: 'header.login')
 * @param mixed $default Valeur par défaut si la clé n'existe pas
 * @return string La traduction ou la valeur par défaut
 */
function t($key, $default = '') {
    global $lang;

    // Supporter la notation avec points (ex: 'header.login')
    $keys = explode('.', $key);
    $value = $lang;

    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $default ?: $key;
        }
    }

    return $value;
}

/**
 * Retourne la langue courante
 * @return string Code de la langue (fr, en, etc.)
 */
function current_lang() {
    return $_SESSION['lang'] ?? 'fr';
}
