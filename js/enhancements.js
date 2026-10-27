/**
 * enhancements.js - Immobilien Ghumman
 * Parallax . Particles . 3D-Tilt . Ripple . Magnetic . Carousel
 * Toast . Skeleton . Blur-up . Autocomplete . Process-Line . Lightbox
 */
(function () {
    'use strict';

    /* --- 1. Back-to-Top Button --- */
    function initBackToTop() {
        var btn = document.getElementById('backToTop');
        if (!btn) return;
        window.addEventListener('scroll', function () {
            btn.classList.toggle('btt-visible', window.scrollY > 400);
        }, { passive: true });
        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* --- 2. Parallax Hero --- */
    function initParallax() {
        var hero = document.querySelector('.hero-premium');
        if (!hero || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        window.addEventListener('scroll', function () {
            hero.style.backgroundPositionY = 'calc(50% + ' + (window.scrollY * 0.15) + 'px)';
        }, { passive: true });
    }

    /* --- 3. Hero Canvas Particles --- */
    function initParticles() {
        var canvas = document.getElementById('heroParticles');
        if (!canvas) return;
        var ctx = canvas.getContext('2d');
        var pts = [];
        var count = window.innerWidth < 640 ? 28 : 55;
        var raf;
        function resize() { canvas.width = canvas.offsetWidth; canvas.height = canvas.offsetHeight; }
        resize();
        window.addEventListener('resize', resize, { passive: true });
        for (var i = 0; i < count; i++) {
            pts.push({ x: Math.random() * canvas.width, y: Math.random() * canvas.height,
                r: Math.random() * 2.2 + 0.4, dx: (Math.random() - 0.5) * 0.38,
                dy: (Math.random() - 0.5) * 0.38, a: Math.random() * 0.38 + 0.07 });
        }
        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            pts.forEach(function (p) {
                ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(255,255,255,' + p.a + ')'; ctx.fill();
                p.x += p.dx; p.y += p.dy;
                if (p.x < 0 || p.x > canvas.width)  p.dx = -p.dx;
                if (p.y < 0 || p.y > canvas.height) p.dy = -p.dy;
            });
            raf = requestAnimationFrame(draw);
        }
        draw();
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) cancelAnimationFrame(raf); else draw();
        });
    }

    /* --- 4. 3D Card Tilt --- */
    function initCardTilt() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || window.innerWidth < 768) return;
        document.querySelectorAll('.service-card, .testimonial-card, .info-card').forEach(function (card) {
            card.addEventListener('mousemove', function (e) {
                var r  = card.getBoundingClientRect();
                var rx = ((e.clientY - r.top  - r.height / 2) / r.height) * -7;
                var ry = ((e.clientX - r.left - r.width  / 2) / r.width ) *  7;
                card.style.transform  = 'perspective(900px) rotateX(' + rx + 'deg) rotateY(' + ry + 'deg) translateZ(4px)';
                card.style.boxShadow  = '0 18px 50px rgba(0,0,0,0.18)';
                card.style.transition = 'transform 0.1s ease, box-shadow 0.1s ease';
            });
            card.addEventListener('mouseleave', function () {
                card.style.transition = 'transform 0.35s ease, box-shadow 0.35s ease';
                card.style.transform  = ''; card.style.boxShadow = '';
            });
        });
    }

    /* --- 5. Button Ripple --- */
    function initRipple() {
        document.querySelectorAll('.btn-premium, .btn-hero-primary, .btn-outline-premium, .btn-nav-cta').forEach(function (btn) {
            if (btn.dataset.ripple) return;
            btn.dataset.ripple = '1';
            btn.addEventListener('click', function (e) {
                var rect = btn.getBoundingClientRect();
                var span = document.createElement('span');
                span.className = 'btn-ripple';
                var size = Math.max(rect.width, rect.height) * 2;
                span.style.cssText = 'width:'+size+'px;height:'+size+'px;left:'+(e.clientX-rect.left-size/2)+'px;top:'+(e.clientY-rect.top-size/2)+'px';
                btn.appendChild(span);
                setTimeout(function () { span.remove(); }, 700);
            });
        });
    }

    /* --- 6. Magnetic Buttons --- */
    function initMagnetic() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || window.innerWidth < 1024) return;
        document.querySelectorAll('.btn-hero-primary, .btn-nav-cta').forEach(function (btn) {
            btn.addEventListener('mousemove', function (e) {
                var rect = btn.getBoundingClientRect();
                btn.style.transform = 'translate('+((e.clientX-rect.left-rect.width/2)*0.22)+'px,'+((e.clientY-rect.top-rect.height/2)*0.22)+'px)';
            });
            btn.addEventListener('mouseleave', function () { btn.style.transform = ''; });
        });
    }

    /* --- 7. Testimonial Auto-Carousel --- */
    function initTestimonialCarousel() {
        var grid = document.querySelector('.testimonials-grid');
        if (!grid) return;
        var cards = Array.from(grid.querySelectorAll('.testimonial-card'));
        if (cards.length < 2) return;
        var wrapper = document.createElement('div'); wrapper.className = 'tc-wrapper';
        var track   = document.createElement('div'); track.className   = 'tc-track';
        wrapper.appendChild(track);
        grid.parentNode.insertBefore(wrapper, grid);
        cards.forEach(function (c) { track.appendChild(c); });
        grid.remove();
        var dotsEl = document.createElement('div'); dotsEl.className = 'tc-dots';
        wrapper.appendChild(dotsEl);
        var cur = 0, auto;
        function pv() { return window.innerWidth >= 1024 ? 3 : window.innerWidth >= 640 ? 2 : 1; }
        function build() {
            var n = cards.length, p = pv(), slides = Math.ceil(n / p);
            track.style.width = (n * 100 / p) + '%';
            cards.forEach(function (c) { c.style.flex = '0 0 ' + (100 / n) + '%'; });
            dotsEl.innerHTML = '';
            for (var i = 0; i < slides; i++) {
                var dot = document.createElement('button');
                dot.className = 'tc-dot'; dot.setAttribute('aria-label', 'Slide ' + (i+1));
                (function (idx) { dot.addEventListener('click', function () { go(idx); }); })(i);
                dotsEl.appendChild(dot);
            }
            go(Math.min(cur, slides - 1));
        }
        function go(idx) {
            var p = pv(), n = cards.length, slides = Math.ceil(n / p);
            cur = ((idx % slides) + slides) % slides;
            track.style.transform = 'translateX(-' + (cur * p / n * 100) + '%)';
            dotsEl.querySelectorAll('.tc-dot').forEach(function (d, i) { d.classList.toggle('active', i === cur); });
        }
        build();
        window.addEventListener('resize', build, { passive: true });
        function startAuto() { auto = setInterval(function () { go(cur + 1); }, 5000); }
        startAuto();
        wrapper.addEventListener('mouseenter', function () { clearInterval(auto); });
        wrapper.addEventListener('mouseleave', startAuto);
        var sx;
        track.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
        track.addEventListener('touchend', function (e) {
            var diff = sx - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) go(cur + (diff > 0 ? 1 : -1));
        }, { passive: true });
    }

    /* --- 8. Toast Notifications --- */
    window.showToast = function (msg, type) {
        var wrap = document.getElementById('ig-toast-wrap');
        if (!wrap) { wrap = document.createElement('div'); wrap.id = 'ig-toast-wrap'; document.body.appendChild(wrap); }
        var t = document.createElement('div');
        t.className = 'ig-toast ig-toast-' + (type || 'success');
        t.innerHTML = '<span class="ig-toast-ico">'+(type==='error'?'&#x2715;':'&#x2713;')+'</span><span>'+msg+'</span>';
        wrap.appendChild(t);
        requestAnimationFrame(function () { t.classList.add('show'); });
        setTimeout(function () { t.classList.remove('show'); setTimeout(function () { t.remove(); }, 400); }, 4500);
    };

    /* --- 9. Skeleton Loaders --- */
    window.showSkeletons = function (id, count) {
        var el = typeof id === 'string' ? document.getElementById(id) : id;
        if (!el) return;
        el.innerHTML = Array(count || 6).fill(0).map(function () {
            return '<div class="prop-skeleton"><div class="skel skel-img"></div><div class="skel-body"><div class="skel skel-h"></div><div class="skel skel-h skel-short"></div><div class="skel skel-p"></div></div></div>';
        }).join('');
    };

    /* --- 10. Blur-up Image Loading --- */
    function initBlurUp() {
        if (!('IntersectionObserver' in window)) return;
        var obs = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (!e.isIntersecting) return;
                var img = e.target, src = img.dataset.src; if (!src) return;
                var tmp = new Image();
                tmp.onload = function () { img.src = src; img.classList.add('blur-up-loaded'); img.removeAttribute('data-src'); };
                tmp.src = src; obs.unobserve(img);
            });
        }, { rootMargin: '200px' });
        document.querySelectorAll('img[data-src]').forEach(function (img) { img.classList.add('blur-up-init'); obs.observe(img); });
    }

    /* --- 11. Location Autocomplete --- */
    function initAutocomplete() {
        var input = document.querySelector('.hero-search-box input[name="location"]');
        if (!input) return;
        var places = ['Bad Vilbel','Frankfurt am Main','Bad Homburg','Offenbach am Main','Wiesbaden','Mainz',
            'Darmstadt','Hanau','Friedberg (Hessen)','Nidderau','Karben','Buedingen','Langen','Dreieich',
            'Neu-Isenburg','Ruesselsheim','Gross-Gerau','Hofheim am Taunus','Eschborn','Bad Nauheim'];
        var list = document.createElement('ul');
        list.className = 'ac-list'; list.setAttribute('role', 'listbox');
        input.setAttribute('autocomplete', 'off');
        input.parentNode.style.position = 'relative';
        input.parentNode.appendChild(list);
        input.addEventListener('input', function () {
            var v = input.value.toLowerCase().trim(); list.innerHTML = '';
            if (v.length < 2) return;
            places.filter(function (p) { return p.toLowerCase().includes(v); }).slice(0, 6).forEach(function (p) {
                var li = document.createElement('li'); li.setAttribute('role', 'option');
                li.innerHTML = '<i class="fas fa-map-marker-alt"></i> ' + p;
                li.addEventListener('mousedown', function (e) { e.preventDefault(); input.value = p; list.innerHTML = ''; });
                list.appendChild(li);
            });
        });
        document.addEventListener('click', function (e) { if (!input.parentNode.contains(e.target)) list.innerHTML = ''; });
    }

    /* --- 12. Process Timeline Line --- */
    function initProcessLine() {
        var steps = document.querySelector('.process-steps');
        if (!steps || !('IntersectionObserver' in window)) return;
        var obs = new IntersectionObserver(function (entries) {
            if (entries[0].isIntersecting) { steps.classList.add('process-animated'); obs.disconnect(); }
        }, { threshold: 0.3 });
        obs.observe(steps);
    }

    /* --- 13. Hero Stat Counters --- */
    function initHeroCounters() {
        document.querySelectorAll('.hero-stat-num[data-count]').forEach(function (el) {
            var target = parseFloat(el.dataset.count);
            var suffix = el.dataset.suffix || '';
            var dec    = parseInt(el.dataset.dec || 0, 10);
            var dur = 1800, startTs = null;
            function step(ts) {
                if (!startTs) startTs = ts;
                var p = Math.min((ts - startTs) / dur, 1);
                el.textContent = ((1 - Math.pow(1 - p, 3)) * target).toFixed(dec) + suffix;
                if (p < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        });
    }

    /* --- 14. Lightbox --- */
    function initLightbox() {
        var imgs = document.querySelectorAll('[data-lightbox]');
        if (!imgs.length) return;
        var overlay = document.createElement('div');
        overlay.className = 'ig-lightbox';
        overlay.setAttribute('role', 'dialog'); overlay.setAttribute('aria-modal', 'true');
        overlay.innerHTML = '<button class="lb-close" aria-label="Schliessen">&#x2715;</button>' +
            '<button class="lb-prev" aria-label="Zurueck">&#x276E;</button>' +
            '<img class="lb-img" src="" alt="Immobilienbild">' +
            '<button class="lb-next" aria-label="Weiter">&#x276F;</button>';
        document.body.appendChild(overlay);
        var lbImg = overlay.querySelector('.lb-img');
        var srcs  = Array.from(imgs).map(function (i) { return i.dataset.lightbox; });
        var cur   = 0;
        function open(idx) { cur = ((idx % srcs.length) + srcs.length) % srcs.length; lbImg.src = srcs[cur]; overlay.classList.add('lb-open'); document.body.style.overflow = 'hidden'; }
        function close() { overlay.classList.remove('lb-open'); document.body.style.overflow = ''; }
        imgs.forEach(function (img, i) { img.style.cursor = 'zoom-in'; img.addEventListener('click', function () { open(i); }); });
        overlay.querySelector('.lb-close').addEventListener('click', close);
        overlay.querySelector('.lb-prev').addEventListener('click', function () { open(cur - 1); });
        overlay.querySelector('.lb-next').addEventListener('click', function () { open(cur + 1); });
        overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
        document.addEventListener('keydown', function (e) {
            if (!overlay.classList.contains('lb-open')) return;
            if (e.key === 'Escape') close();
            if (e.key === 'ArrowLeft')  open(cur - 1);
            if (e.key === 'ArrowRight') open(cur + 1);
        });
    }

    /* --- Scroll Reveal (IntersectionObserver) --- */
    function initScrollReveal() {
        var targets = document.querySelectorAll('.reveal,.reveal-left,.reveal-right,[data-reveal]');
        if (!targets.length) return;

        // Auto-assign subtle variants to common card blocks when no explicit variant is set.
        document.querySelectorAll('.analyse-benefit,.svc-card,.featured-card,.review-card,.ob-step-card').forEach(function (el, index) {
            if (el.hasAttribute('data-reveal')) return;
            var mode = (index % 3 === 0) ? 'up' : (index % 3 === 1 ? 'fade' : 'zoom');
            el.setAttribute('data-reveal', mode);
            if (!el.classList.contains('reveal')) {
                el.classList.add('reveal');
            }
        });

        var prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReducedMotion) {
            targets.forEach(function (el) {
                el.classList.add('revealed', 'is-visible', 'visible');
            });
            return;
        }

        if (!('IntersectionObserver' in window)) {
            targets.forEach(function (el) {
                el.classList.add('revealed', 'is-visible', 'visible');
            });
            return;
        }

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) {
                    e.target.classList.add('revealed', 'is-visible', 'visible');
                    io.unobserve(e.target);
                }
            });
        }, {
            threshold: 0.14,
            rootMargin: '0px 0px -8% 0px'
        });

        targets.forEach(function (el) {
            io.observe(el);
        });
    }

    /* --- Sticky Nav scrolled class --- */
    function initStickyNav() {
        var nav = document.querySelector('.nav-premium');
        if (!nav) return;
        window.addEventListener('scroll', function() {
            nav.classList.toggle('scrolled', window.scrollY > 60);
        }, { passive: true });
    }

    /* --- FAQ Accordion (if not already handled inline) --- */
    function initFaqAccordion() {
        document.querySelectorAll('.faq-question').forEach(function(q) {
            if (q.dataset.initDone) return;
            if (q.getAttribute('onclick')) return; // handled by global toggleFAQ
            q.dataset.initDone = '1';
            q.addEventListener('click', function() {
                var item = q.closest('.faq-item');
                if (!item) return;
                var ans = item.querySelector('.faq-answer');
                var isOpen = item.classList.contains('open');
                // close siblings in same container
                var siblings = item.parentElement ? item.parentElement.querySelectorAll('.faq-item.open') : [];
                siblings.forEach(function(s) {
                    s.classList.remove('open');
                    var sa = s.querySelector('.faq-answer');
                    if (sa) sa.style.display = 'none';
                });
                if (!isOpen) {
                    item.classList.add('open');
                    if (ans) ans.style.display = 'block';
                }
            });
            // init display
            var item = q.closest('.faq-item');
            var ans = item && item.querySelector('.faq-answer');
            if (ans && !item.classList.contains('open')) ans.style.display = 'none';
        });
    }

    /* --- Init --- */
    document.addEventListener('DOMContentLoaded', function () {
        initBackToTop(); initParallax(); initParticles(); initCardTilt();
        initRipple(); initMagnetic(); initTestimonialCarousel(); initBlurUp();
        initAutocomplete(); initProcessLine(); initHeroCounters(); initLightbox();
        initScrollReveal(); initStickyNav(); initFaqAccordion();
    });
})();

/* Global toggleFAQ — used by hausverwaltung subpages with onclick attributes.
   Uses CSS max-height animation via .active class. */
window.toggleFAQ = function (btn) {
    var item = btn.closest('.faq-item');
    if (!item) return;
    var isOpen = item.classList.contains('active');
    // close all open siblings
    var container = item.parentElement;
    if (container) {
        container.querySelectorAll('.faq-item.active').forEach(function (s) {
            s.classList.remove('active');
        });
    }
    if (!isOpen) { item.classList.add('active'); }
};
