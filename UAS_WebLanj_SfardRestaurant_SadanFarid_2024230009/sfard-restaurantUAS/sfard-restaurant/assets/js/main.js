// ============================================================
// SFARD RESTAURANT — MAIN.JS
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // --- NAVBAR SCROLL EFFECT ---
    const nav = document.getElementById('mainNav');
    if (nav) {
        window.addEventListener('scroll', () => {
            nav.classList.toggle('scrolled', window.scrollY > 60);
        });
    }

    // --- SCROLL REVEAL ANIMATION ---
    const reveals = document.querySelectorAll('[data-reveal]');
    if (reveals.length) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        reveals.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(28px)';
            el.style.transition = 'opacity 0.7s ease, transform 0.7s ease';
            observer.observe(el);
        });
    }

    // REVEALED state
    document.addEventListener('scroll', function() {}, { passive: true });

    // Add revealed class style
    const style = document.createElement('style');
    style.textContent = '.revealed { opacity: 1 !important; transform: none !important; }';
    document.head.appendChild(style);

    // --- COUNTER ANIMATION ---
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length) {
        const countObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.getAttribute('data-count'));
                    let current = 0;
                    const step = Math.ceil(target / 60);
                    const timer = setInterval(() => {
                        current = Math.min(current + step, target);
                        el.textContent = current + (el.getAttribute('data-suffix') || '');
                        if (current >= target) clearInterval(timer);
                    }, 25);
                    countObserver.unobserve(el);
                }
            });
        }, { threshold: 0.5 });
        counters.forEach(el => countObserver.observe(el));
    }

    // --- MENU FILTER ---
    const filterBtns = document.querySelectorAll('[data-filter]');
    const menuCards  = document.querySelectorAll('[data-category]');
    if (filterBtns.length && menuCards.length) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const cat = btn.getAttribute('data-filter');
                menuCards.forEach(card => {
                    const show = cat === 'all' || card.getAttribute('data-category') === cat;
                    card.style.display = show ? 'block' : 'none';
                    if (show) {
                        card.style.animation = 'fadeUp 0.5s ease both';
                    }
                });
            });
        });
    }

    // --- GALLERY LIGHTBOX (simple) ---
    document.querySelectorAll('.gallery-item').forEach(item => {
        item.addEventListener('click', () => {
            const src = item.querySelector('img')?.src;
            if (!src) return;
            const modal = document.createElement('div');
            modal.style.cssText = `
                position:fixed;inset:0;background:rgba(0,0,0,0.9);
                display:flex;align-items:center;justify-content:center;z-index:9999;cursor:pointer;
            `;
            modal.innerHTML = `<img src="${src}" style="max-width:90vw;max-height:90vh;border-radius:4px;border:1px solid rgba(212,175,55,0.3);">`;
            modal.addEventListener('click', () => modal.remove());
            document.body.appendChild(modal);
        });
    });

    // --- AUTO DISMISS ALERTS ---
    document.querySelectorAll('.alert-auto-dismiss').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

});
