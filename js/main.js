// main.js - DormQuest JavaScript
// Smooth scroll pour les ancres
document.addEventListener('DOMContentLoaded', function() {
    
    // === SMOOTH SCROLL ===
    const navLinks = document.querySelectorAll('a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                const headerHeight = document.querySelector('.header').offsetHeight;
                const targetPosition = targetElement.offsetTop - headerHeight - 20;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // === HEADER SCROLL EFFECT ===
    let lastScroll = 0;
    const header = document.querySelector('.header');
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            header.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
        } else {
            header.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.1)';
        }
        
        lastScroll = currentScroll;
    });
    
    // === FAQ ACCORDION (optionnel pour amélioration future) ===
    const faqItems = document.querySelectorAll('.faq__item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq__question');
        
        question.addEventListener('click', function() {
            item.classList.toggle('faq__item--active');
        });
    });
    
    // === ANIMATIONS ON SCROLL ===
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Sélectionner les éléments à animer
    const animatedElements = document.querySelectorAll('.avantages__card, .faq__item');
    
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
    
});

// === RESPONSIVE MENU (pour mobile - à développer si besoin) ===
function initMobileMenu() {
    const header = document.querySelector('.header__nav');
    
    // Création du bouton burger si écran < 768px
    if (window.innerWidth <= 768) {
        const burgerBtn = document.createElement('button');
        burgerBtn.className = 'header__burger';
        burgerBtn.innerHTML = '☰';
        burgerBtn.setAttribute('aria-label', 'Menu');
        
        // Insertion du bouton
        const headerContainer = document.querySelector('.header__container');
        headerContainer.appendChild(burgerBtn);
        
        // Toggle du menu
        burgerBtn.addEventListener('click', function() {
            header.classList.toggle('header__nav--active');
            burgerBtn.innerHTML = header.classList.contains('header__nav--active') ? '✕' : '☰';
        });
    }
}

// Initialiser au chargement et au redimensionnement
window.addEventListener('load', initMobileMenu);
window.addEventListener('resize', initMobileMenu);