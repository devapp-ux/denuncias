// ============================================
// COMPORTAMENTO GLOBAL DO PORTAL UNIROVUMA
// Usado em TODAS as páginas (cabeçalho, menu, âncoras)
// ============================================

document.addEventListener('DOMContentLoaded', () => {

    const header = document.querySelector('.ur-header');
    const menuToggle = document.querySelector('.ur-menu-toggle');
    const nav = document.querySelector('.ur-nav');

    if (!header) return;

    // ============================================
    // CONTROLO DO CABEÇALHO AO FAZER SCROLL
    // ============================================

    function handleScroll() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }

    window.addEventListener('scroll', handleScroll, { passive: true });

    // ============================================
    // MENU MOBILE
    // ============================================

    function toggleMenu() {
        const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
        menuToggle.setAttribute('aria-expanded', !isExpanded);
        menuToggle.classList.toggle('active');
        nav.classList.toggle('active');

        // Impedir scroll do body quando menu está aberto
        document.body.style.overflow = isExpanded ? '' : 'hidden';
    }

    if (menuToggle && nav) {
        menuToggle.addEventListener('click', toggleMenu);

        // Fechar menu ao clicar num link
        const navLinks = document.querySelectorAll('.ur-nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (nav.classList.contains('active')) {
                    toggleMenu();
                }
            });
        });

        // Fechar menu ao clicar fora
        document.addEventListener('click', (e) => {
            if (nav.classList.contains('active') &&
                !nav.contains(e.target) &&
                !menuToggle.contains(e.target)) {
                toggleMenu();
            }
        });

        // Tecla ESC para fechar menu
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && nav.classList.contains('active')) {
                toggleMenu();
            }
        });
    }

    // ============================================
    // SCROLL SUAVE PARA ÂNCORAS
    // ============================================

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();

                const headerHeight = header.offsetHeight;
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

});
