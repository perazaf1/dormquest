<?php
// index.php - Landing Page DormQuest
session_start();

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']);
$userType = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DormQuest - Trouvez le logement parfait pour vos études ! Plateforme de mise en relation entre étudiants et loueurs.">
    <title>DormQuest - Trouvez le logement parfait pour vos études !</title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/dashboard-loueur.css.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
</head>
<body>

    

    <!-- Header -->
    <header class="header">
        <div class="header__container">
            <div class="header__logo">
                <a href="index.php" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                    <img src="images/logo-dormquest.png" alt="DormQuest Logo" class="header__logo-img">
                    <span class="header__logo-text">DormQuest</span>
                </a>
            </div>
            <nav class="header__nav">
                <a href="#avantages" class="header__nav-link">Avantages</a>
                <a href="#annonces" class="header__nav-link">Annonces</a>
                
                <a href="#faq" class="header__nav-link">FAQ</a>
                <a href="#apropos" class="header__nav-link">À propos</a>
                
                <?php if ($isLoggedIn): ?>
                    <!-- Si l'utilisateur est connecté -->
                    <?php if ($userType === 'loueur'): ?>
                        <a href="dashboard-loueur.php" class="header__btn header__btn--login">Mon tableau de bord</a>
                    <?php else: ?>
                        <a href="dashboard-etudiant.php" class="header__btn header__btn--login">Mon tableau de bord</a>
                    <?php endif; ?>
                    <a href="logout.php" class="header__btn header__btn--register">Déconnexion</a>
                <?php else: ?>
                    <!-- Si l'utilisateur n'est pas connecté -->
                    <a href="login.php" class="header__btn header__btn--login">Connexion</a>
                    <a href="register.php" class="header__btn header__btn--register">Inscription</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero__container">
            <h1 class="hero__title">Trouvez le logement parfait pour vos études !</h1>
            <p class="hero__subtitle">
                DormQuest connecte les étudiants avec les meilleurs logements. 
                Simple, rapide et sécurisé.
            </p>
            <div class="hero__cta">
                <a href="register.php?type=etudiant" class="hero__btn hero__btn--primary">
                    Je cherche un logement
                </a>
                <a href="register.php?type=loueur" class="hero__btn hero__btn--secondary">
                    Je propose un logement
                </a>
            </div>
            <div class="hero__image">
                <img src="images/hero-illustration2.jpg" alt="Étudiante cherchant un logement" class="hero__image-img">
            </div>
        </div>
    </section>

    <!-- Avantages Section -->
    <section class="avantages" id="avantages">
        <div class="avantages__container">
            <h2 class="avantages__title">Pourquoi choisir DormQuest ?</h2>
            
            <!-- Pour les étudiants -->
            <div class="avantages__group">
                <h3 class="avantages__subtitle">Pour les étudiants</h3>
                <div class="avantages__cards">
                    <div class="avantages__card">
                        <div class="avantages__card-icon">🔍</div>
                        <h4 class="avantages__card-title">Recherche simplifiée</h4>
                        <p class="avantages__card-text">
                            Trouvez rapidement des logements adaptés à vos critères et votre budget.
                        </p>
                    </div>
                    <div class="avantages__card">
                        <div class="avantages__card-icon">⭐</div>
                        <h4 class="avantages__card-title">Liste de favoris</h4>
                        <p class="avantages__card-text">
                            Sauvegardez vos annonces préférées et comparez-les facilement.
                        </p>
                    </div>
                    <div class="avantages__card">
                        <div class="avantages__card-icon">✉️</div>
                        <h4 class="avantages__card-title">Contact direct</h4>
                        <p class="avantages__card-text">
                            Candidatez en un clic et communiquez directement avec les loueurs.
                        </p>
                    </div>
                    <div class="avantages__card">
                        <a class="avantages__card-icon" href="https://www.dossierfacile.logement.gouv.fr/" target="_blank" rel="noopener noreferrer">📂</a>
                        <h4 class="avantages__card-title">Dossier facile
                            <span class="external-icon" aria-hidden="true">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M14 3h7v7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10 14L21 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 21H3V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </h4>
                        <p class="avantages__card-text">
                            Créez facilement votre dossier grâce à des formulaires du gouvernement.
                        </p>
                    </div>
                     <div class="avantages__card">
                        <a class="avantages__card-icon" href="https://wwwd.caf.fr/wps/portal/caffr/aidesetdemarches/mesdemarches/faireunesimulation/lelogement#/preparation" target="_blank" rel="noopener noreferrer">💱</a>
                        <h4 class="avantages__card-title">Calculateur d'APL
                            <span class="external-icon" aria-hidden="true">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M14 3h7v7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10 14L21 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 21H3V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </h4>
                        <p class="avantages__card-text">
                            Calculez rapidement vos aides au logement avec l'outil officiel de la CAF.
                        </p>
                    </div>
                    <div class="avantages__card">
                        <a class="avantages__card-icon" href="https://www.visale.fr/" target="_blank" rel="noopener noreferrer">🏡</a>
                        <h4 class="avantages__card-title">Obtenez un garant
                            <span class="external-icon" aria-hidden="true">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M14 3h7v7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10 14L21 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 21H3V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </h4>
                        <p class="avantages__card-text">
                            Testez votre éligibilité d'un garant locatif via le dispositif Visale.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pour les loueurs -->
            <div class="avantages__group">
                <h3 class="avantages__subtitle">Pour les loueurs</h3>
                <div class="avantages__cards">
                    <div class="avantages__card">
                        <div class="avantages__card-icon">📝</div>
                        <h4 class="avantages__card-title">Gestion facile</h4>
                        <p class="avantages__card-text">
                            Créez, modifiez et gérez vos annonces en quelques clics.
                        </p>
                    </div>
                    <div class="avantages__card">
                        <div class="avantages__card-icon">👥</div>
                        <h4 class="avantages__card-title">Large audience</h4>
                        <p class="avantages__card-text">
                            Touchez des milliers d'étudiants en recherche de logement.
                        </p>
                    </div>
                    <div class="avantages__card">
                        <div class="avantages__card-icon">🛡️</div>
                        <h4 class="avantages__card-title">Sécurisé</h4>
                        <p class="avantages__card-text">
                            Profils vérifiés et système de candidatures organisé.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Aperçu Annonces -->
    <section class="apercu-annonces" id="annonces">
        <div class="apercu-annonces__container">
            <h2 class="apercu-annonces__title">Découvrez nos annonces</h2>
            <p class="apercu-annonces__subtitle">
                Des centaines de logements disponibles pour vous
            </p>
            <div class="apercu-annonces__cta">
                <a href="annonces.php" class="apercu-annonces__btn">
                    Voir toutes les annonces
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq" id="faq">
        <div class="faq__container">
            <h2 class="faq__title">Questions fréquentes</h2>
            <div class="faq__items">
                <div class="faq__item">
                    <h3 class="faq__question">Comment créer un compte ?</h3>
                    <p class="faq__answer">
                        Cliquez sur "Inscription" et choisissez votre profil (étudiant ou loueur). 
                        Remplissez le formulaire et validez votre email.
                    </p>
                </div>
                <div class="faq__item">
                    <h3 class="faq__question">Est-ce que le service est gratuit ?</h3>
                    <p class="faq__answer">
                        Oui ! DormQuest est entièrement gratuit pour les étudiants. 
                        Les loueurs peuvent publier leurs annonces gratuitement.
                    </p>
                </div>
                <div class="faq__item">
                    <h3 class="faq__question">Comment candidater à une annonce ?</h3>
                    <p class="faq__answer">
                        Connectez-vous à votre compte étudiant, consultez une annonce et cliquez 
                        sur "Candidater". Vous pouvez ajouter un message personnalisé.
                    </p>
                </div>
                <div class="faq__item">
                    <h3 class="faq__question">Puis-je modifier mon annonce après publication ?</h3>
                    <p class="faq__answer">
                        Oui ! Depuis votre espace loueur, vous pouvez modifier ou supprimer 
                        vos annonces à tout moment.
                    </p>
                </div>
                <div class="faq__item">
                    <h3 class="faq__question">Combien de temps pour obtenir une réponse ?</h3>
                    <p class="faq__answer">
                        Les propriétaires s'engagent à répondre sous 48 heures. Cependant, 
                        le délai peut varier en fonction de la demande.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- À propos -->
    <section class="apropos" id="apropos">
        <div class="apropos__container">
            <h2 class="apropos__title">À propos de DormQuest</h2>
            <p class="apropos__text">
                DormQuest est une initiative de <strong>Nyzer</strong>, une startup innovante 
                dédiée à faciliter la vie des étudiants. Notre mission est de rendre 
                la recherche de logement simple, rapide et accessible à tous.
            </p>
            <div class="apropos__logo">
                <img src="images/logo-nyzer.png" alt="Nyzer Logo" class="apropos__logo-img">
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer__container">
            <div class="footer__section">
                <h4 class="footer__title">DormQuest</h4>
                <p class="footer__text">
                    Trouvez le logement parfait pour vos études !
                </p>
            </div>
            <div class="footer__section">
                <h4 class="footer__title">Navigation</h4>
                <ul class="footer__list">
                    <li class="footer__item"><a href="#annonces" class="footer__link">Annonces</a></li>
                    <li class="footer__item"><a href="#avantages" class="footer__link">Avantages</a></li>
                    <li class="footer__item"><a href="#apropos" class="footer__link">À propos</a></li>
                    <li class="footer__item"><a href="#faq" class="footer__link">FAQ</a></li>
                </ul>
            </div>
            <div class="footer__section">
                <h4 class="footer__title">Liens utiles</h4>
                <ul class="footer__list">
                    <li class="footer__item"><a href="register.php" class="footer__link">Inscription</a></li>
                    <li class="footer__item"><a href="login.php" class="footer__link">Connexion</a></li>
                    <li class="footer__item"><a href="contact.php" class="footer__link">Contact</a></li>
                    <li class="footer__item"><a href="CGU.php" class="footer__link" target="blank">CGU</a></li>
                    <li class="footer__item"><a href="mentions-legales.php" class="footer__link" target="blank">Mentions légales</a></li>
                </ul>
            </div>
            <div class="footer__section">
                <h4 class="footer__title">Powered by</h4>
                <img src="images/logo-nyzer.png" alt="Nyzer" class="footer__nyzer-logo">
            </div>
        </div>
        <div class="footer__bottom">
            <p class="footer__copyright">
                &copy; 2025 DormQuest by Nyzer. Tous droits réservés.
            </p>
        </div>
    </footer>

<div class="index__arrow">
    <img src="images/arrow-up.svg" alt="flèche de retour en haut" class="index__arrow-img" />
</div>

    <script src="js/main.js"></script>
    <script src="js/register.js"></script>
    <script src="js/login.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</body>
</html>