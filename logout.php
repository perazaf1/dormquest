<?php
// logout.php - Déconnexion de l'utilisateur
session_start();

// Inclure les fonctions d'authentification (chemin absolu basé sur ce fichier)
require_once __DIR__ . '/includes/auth.php';

// Déconnexion
logout();

// Redirection vers la page d'accueil avec message
header('Location: index.php?logout=success');
exit();
?>