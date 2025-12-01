<?php
// index.php - Landing Page DormQuest
session_start();

// Charger le système de langue
require_once __DIR__ . '/lang/lang.php';

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']);
$userType = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
?>
<!DOCTYPE html>
<html lang="<?= current_lang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= t('site.description') ?>">
    <title><?= t('site.title') ?></title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/lang-switcher.css">
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
                <a href="#avantages" class="header__nav-link"><?= t('header.advantages') ?></a>
                <a href="#annonces" class="header__nav-link"><?= t('header.listings') ?></a>

                <a href="#faq" class="header__nav-link"><?= t('header.faq') ?></a>
                <a href="#apropos" class="header__nav-link"><?= t('header.about') ?></a>

                <!-- Sélecteur de langue -->
                <div class="header__lang-switcher">
                    <a href="?lang=fr" class="header__lang-link <?= current_lang() === 'fr' ? 'active' : '' ?>">FR</a>
                    <span class="header__lang-separator">|</span>
                    <a href="?lang=en" class="header__lang-link <?= current_lang() === 'en' ? 'active' : '' ?>">EN</a>
                </div>

                <?php if ($isLoggedIn): ?>
                    <!-- Si l'utilisateur est connecté -->
                    <?php if ($userType === 'loueur'): ?>
                        <a href="dashboard-loueur.php" class="header__btn header__btn--login"><?= t('header.dashboard') ?></a>
                    <?php else: ?>
                        <a href="dashboard-etudiant.php" class="header__btn header__btn--login"><?= t('header.dashboard') ?></a>
                    <?php endif; ?>
                    <a href="logout.php" class="header__btn header__btn--register"><?= t('header.logout') ?></a>
                <?php else: ?>
                    <!-- Si l'utilisateur n'est pas connecté -->
                    <a href="login.php" class="header__btn header__btn--login"><?= t('header.login') ?></a>
                    <a href="register.php" class="header__btn header__btn--register"><?= t('header.register') ?></a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero__container">
            <h1 class="hero__title"><?= t('hero.title') ?></h1>
            <p class="hero__subtitle">
                <?= t('hero.subtitle') ?>
            </p>
            <div class="hero__cta">
                <a href="register.php?type=etudiant" class="hero__btn hero__btn--primary">
                    <?= t('hero.search_btn') ?>
                </a>
                <a href="register.php?type=loueur" class="hero__btn hero__btn--secondary">
                    <?= t('hero.offer_btn') ?>
                </a>
            </div>
            <div class="hero__image">
                <img src="images/hero-illustration2.jpg" alt="<?= t('hero.alt_image') ?>" class="hero__image-img">
            </div>
        </div>
    </section>

    <!-- Avantages Section -->
    <section class="avantages" id="avantages">
        <div class="avantages__container">
            <h2 class="avantages__title"><?= t('advantages.title') ?></h2>

            <!-- Pour les étudiants -->
            <div class="avantages__group">
                <h3 class="avantages__subtitle"><?= t('advantages.students_title') ?></h3>
                <div class="avantages__cards">
                    <div class="avantages__card">
                        <div class="avantages__card-icon">🔍</div>
                        <h4 class="avantages__card-title"><?= t('advantages.search_title') ?></h4>
                        <p class="avantages__card-text">
                            <?= t('advantages.search_text') ?>
                        </p>
                    </div>
                    <div class="avantages__card">
                        <div class="avantages__card-icon">⭐</div>
                        <h4 class="avantages__card-title"><?= t('advantages.favorites_title') ?></h4>
                        <p class="avantages__card-text">
                            <?= t('advantages.favorites_text') ?>
                        </p>
                    </div>
                    <div class="avantages__card">
                        <div class="avantages__card-icon">✉️</div>
                        <h4 class="avantages__card-title"><?= t('advantages.contact_title') ?></h4>
                        <p class="avantages__card-text">
                            <?= t('advantages.contact_text') ?>
                        </p>
                    </div>
                    <div class="avantages__card">
                        <a class="avantages__card-icon" href="https://www.dossierfacile.logement.gouv.fr/" target="_blank" rel="noopener noreferrer">📂</a>
                        <h4 class="avantages__card-title"><?= t('advantages.file_title') ?>
                            <span class="external-icon" aria-hidden="true">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M14 3h7v7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10 14L21 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 21H3V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </h4>
                        <p class="avantages__card-text">
                            <?= t('advantages.file_text') ?>
                        </p>
                    </div>
                     <div class="avantages__card">
                        <a class="avantages__card-icon" href="https://wwwd.caf.fr/wps/portal/caffr/aidesetdemarches/mesdemarches/faireunesimulation/lelogement#/preparation" target="_blank" rel="noopener noreferrer">💱</a>
                        <h4 class="avantages__card-title"><?= t('advantages.apl_title') ?>
                            <span class="external-icon" aria-hidden="true">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M14 3h7v7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10 14L21 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 21H3V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </h4>
                        <p class="avantages__card-text">
                            <?= t('advantages.apl_text') ?>
                        </p>
                    </div>
                    <div class="avantages__card">
                        <a class="avantages__card-icon" href="https://www.visale.fr/" target="_blank" rel="noopener noreferrer">🏡</a>
                        <h4 class="avantages__card-title"><?= t('advantages.guarantor_title') ?>
                            <span class="external-icon" aria-hidden="true">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M14 3h7v7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10 14L21 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 21H3V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </h4>
                        <p class="avantages__card-text">
                            <?= t('advantages.guarantor_text') ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pour les loueurs -->
            <div class="avantages__group">
                <h3 class="avantages__subtitle"><?= t('advantages.landlords_title') ?></h3>
                <div class="avantages__cards">
                    <div class="avantages__card">
                        <div class="avantages__card-icon">📝</div>
                        <h4 class="avantages__card-title"><?= t('advantages.management_title') ?></h4>
                        <p class="avantages__card-text">
                            <?= t('advantages.management_text') ?>
                        </p>
                    </div>
                    <div class="avantages__card">
                        <div class="avantages__card-icon">👥</div>
                        <h4 class="avantages__card-title"><?= t('advantages.audience_title') ?></h4>
                        <p class="avantages__card-text">
                            <?= t('advantages.audience_text') ?>
                        </p>
                    </div>
                    <div class="avantages__card">
                        <div class="avantages__card-icon">🛡️</div>
                        <h4 class="avantages__card-title"><?= t('advantages.secure_title') ?></h4>
                        <p class="avantages__card-text">
                            <?= t('advantages.secure_text') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Aperçu Annonces -->
    <section class="apercu-annonces" id="annonces">
        <div class="apercu-annonces__container">
            <h2 class="apercu-annonces__title"><?= t('listings.title') ?></h2>
            <p class="apercu-annonces__subtitle">
                <?= t('listings.subtitle') ?>
            </p>
            <div class="apercu-annonces__cta">
                <a href="annonces.php" class="apercu-annonces__btn">
                    <?= t('listings.view_all') ?>
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq" id="faq">
        <div class="faq__container">
            <h2 class="faq__title"><?= t('faq.title') ?></h2>
            <div class="faq__items">
                <div class="faq__item">
                    <h3 class="faq__question"><?= t('faq.q1_question') ?></h3>
                    <p class="faq__answer">
                        <?= t('faq.q1_answer') ?>
                    </p>
                </div>
                <div class="faq__item">
                    <h3 class="faq__question"><?= t('faq.q2_question') ?></h3>
                    <p class="faq__answer">
                        <?= t('faq.q2_answer') ?>
                    </p>
                </div>
                <div class="faq__item">
                    <h3 class="faq__question"><?= t('faq.q3_question') ?></h3>
                    <p class="faq__answer">
                        <?= t('faq.q3_answer') ?>
                    </p>
                </div>
                <div class="faq__item">
                    <h3 class="faq__question"><?= t('faq.q4_question') ?></h3>
                    <p class="faq__answer">
                        <?= t('faq.q4_answer') ?>
                    </p>
                </div>
                <div class="faq__item">
                    <h3 class="faq__question"><?= t('faq.q5_question') ?></h3>
                    <p class="faq__answer">
                        <?= t('faq.q5_answer') ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- À propos -->
    <section class="apropos" id="apropos">
        <div class="apropos__container">
            <h2 class="apropos__title"><?= t('about.title') ?></h2>
            <p class="apropos__text">
                <?= t('about.text') ?>
            </p>
            <div class="apropos__logo">
                <img src="images/logo-nyzer.png" alt="<?= t('about.logo_alt') ?>" class="apropos__logo-img">
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer__container">
            <div class="footer__section">
                <h4 class="footer__title"><?= t('footer.title') ?></h4>
                <p class="footer__text">
                    <?= t('footer.description') ?>
                </p>
            </div>
            <div class="footer__section">
                <h4 class="footer__title"><?= t('footer.navigation') ?></h4>
                <ul class="footer__list">
                    <li class="footer__item"><a href="#annonces" class="footer__link"><?= t('footer.listings') ?></a></li>
                    <li class="footer__item"><a href="#avantages" class="footer__link"><?= t('footer.advantages') ?></a></li>
                    <li class="footer__item"><a href="#apropos" class="footer__link"><?= t('footer.about') ?></a></li>
                    <li class="footer__item"><a href="#faq" class="footer__link"><?= t('footer.faq') ?></a></li>
                </ul>
            </div>
            <div class="footer__section">
                <h4 class="footer__title"><?= t('footer.useful_links') ?></h4>
                <ul class="footer__list">
                    <li class="footer__item"><a href="register.php" class="footer__link"><?= t('footer.register') ?></a></li>
                    <li class="footer__item"><a href="login.php" class="footer__link"><?= t('footer.login') ?></a></li>
                    <li class="footer__item"><a href="contact.php" class="footer__link"><?= t('footer.contact') ?></a></li>
                    <li class="footer__item"><a href="CGU.php" class="footer__link" target="blank"><?= t('footer.terms') ?></a></li>
                    <li class="footer__item"><a href="mentions-legales.php" class="footer__link" target="blank"><?= t('footer.legal') ?></a></li>
                </ul>
            </div>
            <div class="footer__section">
                <h4 class="footer__title"><?= t('footer.powered_by') ?></h4>
                <img src="images/logo-nyzer.png" alt="Nyzer" class="footer__nyzer-logo">
            </div>
        </div>
        <div class="footer__bottom">
            <p class="footer__copyright">
                <?= t('footer.copyright') ?>
            </p>
        </div>
    </footer>

<div class="index__arrow">
    <img src="images/arrow-up.svg" alt="<?= t('footer.back_to_top') ?>" class="index__arrow-img" />
</div>

    <script src="js/main.js"></script>
    <script src="js/register.js"></script>
    <script src="js/login.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</body>
</html>