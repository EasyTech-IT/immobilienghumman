/**
 * Property Detail Page Loader
 * Lädt eine Immobilie per Slug, Legacy-Objektnummer oder ID aus der API.
 * Datenquelle: ausschließlich API/DB – es werden keine Objektdaten erfunden.
 */
(function () {
    'use strict';

    const SITE_BASE = window.SITE_BASE || '';
    const API_BASE = window.API_BASE_URL || (SITE_BASE + '/api');
    const FETCH_TIMEOUT_MS = 15000;
    const PHONE_TEL = '+4916098787878';
    const PHONE_LABEL = '+49 160 98787878';
    const EMAIL = 'info@immobilienghumman.de';
    const WA_BASE = 'https://wa.me/4916098787878';
    const AGENT_IMG = 'https://res.cloudinary.com/dlpdbr0ey/image/upload/v1755051245/7ae1aa7d-b239-4ee5-8090-6b8339e15d4e-removebg-preview_m6fipk.png';

    let images = [];
    let current = 0;
    let lightboxImages = [];
    let lightboxCurrent = 0;
    let property = null;
    let lastFocus = null;
    let printExpanded = [];

    /* ───────────── Helpers ───────────── */
    const $ = (sel, root) => (root || document).querySelector(sel);
    const $$ = (sel, root) => Array.from((root || document).querySelectorAll(sel));

    function esc(v) {
        return String(v == null ? '' : v).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }

    function parseNum(v) {
        if (v == null || v === '') return null;
        if (typeof v === 'number') return isFinite(v) ? v : null;
        let s = String(v).trim().replace(/[^\d,.\-]/g, '');
        if (!s) return null;
        if (s.includes(',')) s = s.replace(/\./g, '').replace(',', '.');
        const n = parseFloat(s);
        return isFinite(n) ? n : null;
    }

    const moneyFmt = new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR', minimumFractionDigits: 0, maximumFractionDigits: 2 });
    const numFmt = new Intl.NumberFormat('de-DE', { maximumFractionDigits: 2 });
    const money = v => { const n = parseNum(v); return n == null ? '' : moneyFmt.format(n); };
    const fmtNum = v => { const n = parseNum(v); return n == null ? '' : numFmt.format(n); };
    const hasVal = v => v !== null && v !== undefined && String(v).trim() !== '' && v !== 0 && v !== '0' && v !== '0.00';
    const publicPropertyPath = p => `${SITE_BASE}/immobilie/${encodeURIComponent(p.slug || '')}`;

    function parseDate(v) {
        if (!v) return null;
        const s = String(v).trim();
        let m = s.match(/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/);
        if (m) return new Date(+m[3], +m[2] - 1, +m[1]);
        m = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
        if (m) return new Date(+m[1], +m[2] - 1, +m[3]);
        return null;
    }

    function fmtDate(v) {
        if (!v) return '';
        const d = parseDate(v);
        return d && !isNaN(d) ? d.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) : String(v);
    }

    function assetUrl(u) {
        if (!u) return '';
        u = String(u).trim();
        if (/^(https?:)?\/\//i.test(u)) return u;
        if (/^[a-z][a-z0-9+.\-]*:/i.test(u)) return '';
        if (u.startsWith('/')) return (SITE_BASE && !u.startsWith(SITE_BASE + '/') ? SITE_BASE : '') + u;
        return SITE_BASE + '/' + u;
    }

    const TYPE_LABELS = { wohnung: 'Wohnung', haus: 'Haus', gewerbe: 'Gewerbe', 'grundstück': 'Grundstück', grundstueck: 'Grundstück' };
    const typeLabel = t => { if (!t) return ''; const k = String(t).toLowerCase(); return TYPE_LABELS[k] || (String(t).charAt(0).toUpperCase() + String(t).slice(1)); };
    const isRent = p => /miet/i.test(p.offer_type || '');
    const offerLabel = p => (isRent(p) ? 'Miete' : (p.offer_type ? 'Kauf' : ''));
    const STATUS = { available: 'Verfügbar', reserved: 'Reserviert', sold: 'Verkauft', rented: 'Vermietet' };

    function toast(text) {
        let t = document.getElementById('fav-toast');
        if (!t) {
            t = document.createElement('div');
            t.id = 'fav-toast';
            t.setAttribute('role', 'status');
            document.body.appendChild(t);
        }
        t.textContent = text;
        t.classList.add('visible');
        clearTimeout(toast._t);
        toast._t = setTimeout(() => t.classList.remove('visible'), 2500);
    }

    /* ───────────── State handling ───────────── */
    function setState(state) {
        const loading = $('#pd-loading'), error = $('#pd-error'), content = $('#pd-content'), root = $('#pd-root');
        if (loading) loading.hidden = state !== 'loading';
        if (error) error.hidden = state !== 'error';
        if (content) content.hidden = state !== 'ready';
        if (root) root.setAttribute('aria-busy', state === 'loading' ? 'true' : 'false');
    }

    function showError(code, detail) {
        const box = $('#pd-error');
        const messages = {
            not_found: ['Diese Immobilie ist nicht verfügbar', 'Das Objekt wurde nicht gefunden oder ist nicht mehr online. Schauen Sie sich gern unsere aktuellen Angebote an.'],
            invalid: ['Ungültiger Link', 'Es wurde keine gültige Immobilie angegeben. Bitte wählen Sie ein Objekt aus unseren Angeboten.'],
            timeout: ['Die Anfrage hat zu lange gedauert', 'Der Server antwortet gerade nicht rechtzeitig. Bitte versuchen Sie es erneut oder kontaktieren Sie uns direkt.'],
            network: ['Keine Verbindung', 'Die Immobilie konnte nicht geladen werden. Bitte prüfen Sie Ihre Internetverbindung oder kontaktieren Sie uns direkt.'],
            render: ['Anzeige nicht möglich', 'Die Objektdaten konnten nicht vollständig angezeigt werden. Bitte laden Sie die Seite neu oder kontaktieren Sie uns direkt.'],
            server: ['Vorübergehend nicht erreichbar', 'Die Objektdaten konnten nicht geladen werden. Bitte versuchen Sie es später erneut oder kontaktieren Sie uns direkt.']
        };
        const m = messages[code] || messages.server;
        if (box) {
            const canRetry = code !== 'not_found' && code !== 'invalid';
            box.innerHTML = `
                <div class="pd-error-icon"><i class="fas fa-house-circle-exclamation" aria-hidden="true"></i></div>
                <h2>${esc(m[0])}</h2>
                <p>${esc(m[1])}</p>
                <div class="pd-error-actions">
                    ${canRetry ? '<button type="button" class="pd-btn pd-btn-primary" data-retry><i class="fas fa-rotate-right"></i> Erneut versuchen</button>' : ''}
                    <a class="pd-btn pd-btn-outline" href="${esc(SITE_BASE)}/immobilien-angebote"><i class="fas fa-building"></i> Alle Immobilien</a>
                    <a class="pd-btn pd-btn-outline" href="tel:${PHONE_TEL}"><i class="fas fa-phone"></i> ${PHONE_LABEL}</a>
                    <a class="pd-btn pd-btn-wa" href="${WA_BASE}" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                    <a class="pd-btn pd-btn-outline" href="mailto:${EMAIL}"><i class="fas fa-envelope"></i> E-Mail</a>
                </div>`;
            const retry = $('[data-retry]', box);
            if (retry) retry.addEventListener('click', loadPropertyDetails);
        }
        setState('error');
        console.error('Property detail error:', code, detail || '');
    }

    /* ───────────── Data loading ───────────── */
    async function fetchProperty(ref) {
        const url = ref.number
            ? `${API_BASE}/properties.php?number=${encodeURIComponent(ref.number)}`
            : ref.slug
            ? `${API_BASE}/properties.php?slug=${encodeURIComponent(ref.slug)}`
            : `${API_BASE}/properties.php?id=${encodeURIComponent(ref.id)}`;
        const ctrl = new AbortController();
        const timer = setTimeout(() => ctrl.abort(), FETCH_TIMEOUT_MS);
        const fail = (code, msg) => Object.assign(new Error(msg || code), { code });
        try {
            const res = await fetch(url, { headers: { Accept: 'application/json' }, signal: ctrl.signal, credentials: 'same-origin' });
            const raw = (await res.text()).replace(/^\uFEFF+/, '').trim();
            let data = null;
            try { data = raw ? JSON.parse(raw) : null; } catch (e) { data = null; }
            if (res.status === 404) throw fail('not_found');
            if (!res.ok || !data) throw fail('server', `HTTP ${res.status}`);
            if (!data.property || typeof data.property !== 'object') throw fail('not_found');
            return data.property;
        } catch (e) {
            if (e.name === 'AbortError') throw fail('timeout');
            if (!e.code) e.code = 'network';
            throw e;
        } finally {
            clearTimeout(timer);
        }
    }

    async function loadPropertyDetails() {
        const params = new URLSearchParams(window.location.search);
        let slug = params.get('slug');
        const id = params.get('id');
        if (!slug) {
            const m = window.location.pathname.match(/\/immobilie\/([^/?#]+)/i);
            if (m && m[1]) {
                try { slug = decodeURIComponent(m[1]); } catch (e) { slug = m[1]; }
            }
        }
        const number = slug && /^\d+$/.test(slug) ? slug : '';
        if (number) slug = '';
        if (!slug && !id && !number) { showError('invalid'); return; }

        setState('loading');
        let data = null;
        let lastErr = null;
        for (let attempt = 0; attempt < 2 && !data; attempt++) {
            try {
                data = await fetchProperty({ number, slug, id });
            } catch (e) {
                lastErr = e;
                if (e.code === 'not_found') break;
            }
        }
        if (!data) { showError(lastErr ? lastErr.code : 'server', lastErr); return; }

        try {
            property = data;
            const canonicalPath = data.slug
                ? `${SITE_BASE}/immobilie/${encodeURIComponent(data.slug)}${window.location.search || ''}${window.location.hash || ''}`
                : '';
            if (canonicalPath && window.location.pathname !== canonicalPath && window.history && window.history.replaceState) {
                window.history.replaceState(null, '', canonicalPath);
            }
            displayProperty(data);
            setState('ready');
        } catch (e) {
            showError('render', e);
        }
    }

    /* ───────────── Rendering ───────────── */
    function displayProperty(p) {
        const place = [p.zip, p.location || p.city].filter(Boolean).join(' ');
        const inquiryForm = $('#propertyInquiryForm');
        document.title = `${p.title} – ${p.location || p.city || ''} | Immobilien Ghumman`.replace(' –  |', ' |');
        const meta = $('meta[name="description"]');
        const desc = (p.short_description || p.description || '').toString().replace(/\s+/g, ' ').trim().slice(0, 155);
        if (meta && desc) meta.setAttribute('content', desc);
        const crumb = $('#pd-crumb-title');
        if (crumb) crumb.textContent = p.title || 'Objekt';

        initImages(p);

        const parts = [
            safe(() => galleryHtml(p)),
            safe(() => headHtml(p, place)),
            safe(thumbnailsHtml),
            `<div class="pd-layout"><div class="pd-main">` +
            [descriptionHtml, detailsHtml, costsHtml, energyHtml, featuresHtml, locationHtml, mediaHtml].map(fn => safe(() => fn(p))).join('') +
            `</div><aside class="pd-aside">${safe(() => contactHtml(p))}</aside></div>`,
            safe(() => mobileBarHtml(p))
        ];
        $('#pd-content').innerHTML = parts.join('');

        const inquirySlot = $('#propertyInquirySlot');
        if (inquiryForm && inquirySlot) {
            inquirySlot.appendChild(inquiryForm);
            $('#propertyInquiryFormSource')?.remove();
        }

        bindGallery(p);
        bindMediaGallery(p);
        bindActions(p);
        syncInquiryForm(p);
        safe(() => initPropertyMap(p));
        safe(() => updateStructuredData(p));
    }

    function safe(fn) {
        try { return fn() || ''; } catch (e) { console.error('Section render failed:', e); return ''; }
    }

    function section(id, icon, title, body) {
        return `<section class="pd-card" id="${id}"><h2 class="pd-h2"><i class="fas ${icon}" aria-hidden="true"></i> ${esc(title)}</h2>${body}</section>`;
    }

    function rows(list, totalLabel) {
        const items = list.filter(r => r && r.value !== '' && r.value != null);
        if (!items.length) return '';
        return `<dl class="pd-dl">${items.map(r => `<div${r.label === totalLabel ? ' class="is-total"' : ''}><dt>${esc(r.label)}</dt><dd>${esc(r.value)}</dd></div>`).join('')}</dl>`;
    }

    const extra = p => (p.extra_data && typeof p.extra_data === 'object' && !Array.isArray(p.extra_data)) ? p.extra_data : {};

    /* — Gallery — */
    function initImages(p) {
        images = [];
        const raw = Array.isArray(p.images) ? p.images : [];
        raw.forEach(img => {
            const url = typeof img === 'string' ? img : (img && (img.url || img.image_url));
            const u = assetUrl(url);
            if (u) {
                const label = (img && typeof img === 'object' && img.label) ? String(img.label) : '';
                const staged = !!(img && typeof img === 'object' && img.virtual_staging);
                images.push({ url: u, label: [staged ? 'Visualisierung / Einrichtungsbeispiel' : '', label].filter(Boolean).join(' · '), staged });
            }
        });
        if (!images.length && p.main_image) {
            const u = assetUrl(p.main_image);
            if (u) images.push({ url: u, label: '' });
        }
        const main = assetUrl(p.main_image);
        const idx = main ? images.findIndex(i => i.url === main) : -1;
        current = idx > 0 ? idx : 0;
    }

    function galleryHtml(p) {
        const multi = images.length > 1;
        const empty = images.length === 0;
        return `
        <section class="pd-gallery" aria-label="Bildergalerie">
            <div class="pd-gallery-grid">
                <div class="pd-stage${empty ? ' is-empty' : ''}" id="pd-stage">
                    <div class="pd-stage-bg" id="pd-stage-bg"></div>
                    <img id="main-image" class="pd-stage-img" ${empty ? '' : `src="${esc(images[current].url)}"`} alt="${esc(p.title)}" width="1600" height="1000" decoding="async" fetchpriority="high">
                    <div class="pd-empty-msg"><i class="far fa-image" aria-hidden="true"></i>Für dieses Objekt sind noch keine Bilder hinterlegt.</div>
                    ${empty ? '' : `
                    ${multi ? `<button type="button" class="pd-nav pd-prev" aria-label="Vorheriges Bild"><i class="fas fa-chevron-left"></i></button>
                    <button type="button" class="pd-nav pd-next" aria-label="Nächstes Bild"><i class="fas fa-chevron-right"></i></button>` : ''}
                    <button type="button" class="pd-fs" aria-label="Galerie im Vollbild öffnen"><i class="fas fa-expand"></i><span>Vollbild</span></button>
                    <div class="pd-label" id="pd-label"></div>
                    ${multi ? '<div class="pd-counter" id="pd-counter"></div>' : ''}`}
                </div>
                ${multi ? `<button type="button" class="pd-gallery-preview" data-gallery-next aria-label="Nächstes Bild${images[(current + 1) % images.length].staged ? ': Visualisierung / Einrichtungsbeispiel' : ''}">
                    <img data-preview-image src="${esc(images[(current + 1) % images.length].url)}" alt="" loading="lazy" decoding="async">
                    <span><i class="fas fa-camera" aria-hidden="true"></i> ${images.length} Bilder <span data-preview-staging>${images[(current + 1) % images.length].staged ? ' · Visualisierung' : ''}</span></span>
                </button>` : ''}
            </div>
        </section>`;
    }

    function thumbnailsHtml() {
        if (images.length < 2) return '';
        return `<div class="pd-thumbs" id="gallery-thumbnails" role="group" aria-label="Miniaturbilder">
            ${images.map((img, i) => `<button type="button" class="pd-thumb${i === current ? ' active' : ''}" data-index="${i}" aria-label="Bild ${i + 1}${img.label ? ': ' + esc(img.label) : ''}"><img src="${esc(img.url)}" alt="" loading="${i < 6 ? 'eager' : 'lazy'}" decoding="async" width="96" height="68">${img.staged ? '<span class="pd-thumb-visualization">Visualisierung</span>' : ''}</button>`).join('')}
        </div>`;
    }

    function bindGallery(p) {
        const stage = $('#pd-stage');
        if (!stage || !images.length) return;
        const img = $('#main-image');
        img.addEventListener('error', () => { stage.classList.add('is-empty'); });
        img.addEventListener('load', () => { stage.classList.remove('is-empty'); });
        img.addEventListener('click', () => openLightbox(current));
        $('.pd-prev', stage)?.addEventListener('click', () => showImage(current - 1));
        $('.pd-next', stage)?.addEventListener('click', () => showImage(current + 1));
        $('.pd-fs', stage)?.addEventListener('click', () => openLightbox(current));
        $('[data-gallery-next]')?.addEventListener('click', () => showImage(current + 1));
        const thumbs = $('#gallery-thumbnails');
        if (thumbs) {
            thumbs.addEventListener('click', e => {
                const t = e.target.closest('.pd-thumb');
                if (t) showImage(parseInt(t.dataset.index, 10));
            });
        }
        let startX = 0;
        stage.addEventListener('touchstart', e => { startX = e.changedTouches[0].clientX; }, { passive: true });
        stage.addEventListener('touchend', e => {
            const dx = e.changedTouches[0].clientX - startX;
            if (Math.abs(dx) > 50) showImage(current + (dx < 0 ? 1 : -1));
        }, { passive: true });
        paintStage();
    }

    function paintStage() {
        const item = images[current];
        if (!item) return;
        const img = $('#main-image');
        if (img && img.getAttribute('src') !== item.url) img.src = item.url;
        const preview = $('[data-preview-image]');
        if (preview && images.length > 1) preview.src = images[(current + 1) % images.length].url;
        const previewButton = $('[data-gallery-next]');
        if (previewButton) previewButton.setAttribute('aria-label', 'Nächstes Bild' + (images[(current + 1) % images.length].staged ? ': Visualisierung / Einrichtungsbeispiel' : ''));
        const previewStaging = $('[data-preview-staging]');
        if (previewStaging) previewStaging.textContent = images[(current + 1) % images.length].staged ? ' · Visualisierung' : '';
        const bg = $('#pd-stage-bg');
        if (bg) bg.style.backgroundImage = `url("${item.url.replace(/"/g, '%22')}")`;
        const label = $('#pd-label');
        if (label) { label.textContent = item.label; label.style.display = item.label ? '' : 'none'; }
        const counter = $('#pd-counter');
        if (counter) counter.textContent = `${current + 1} / ${images.length}`;
        $$('.pd-thumb').forEach((t, i) => {
            const on = i === current;
            t.classList.toggle('active', on);
            if (on) t.setAttribute('aria-current', 'true'); else t.removeAttribute('aria-current');
        });
        const active = $('.pd-thumb.active');
        const thumbs = $('#gallery-thumbnails');
        if (active && thumbs) {
            const itemBox = active.getBoundingClientRect();
            const stripBox = thumbs.getBoundingClientRect();
            if (itemBox.left < stripBox.left) thumbs.scrollLeft += itemBox.left - stripBox.left;
            else if (itemBox.right > stripBox.right) thumbs.scrollLeft += itemBox.right - stripBox.right;
        }
        [current + 1, current - 1].forEach(i => {
            const n = images[(i + images.length) % images.length];
            if (n) { const pre = new Image(); pre.src = n.url; }
        });
    }

    function showImage(index) {
        if (!images.length) return;
        current = (index + images.length) % images.length;
        paintStage();
    }
    const nextImage = () => showImage(current + 1);
    const previousImage = () => showImage(current - 1);

    /* — Lightbox — */
    function paintLightbox(lb) {
        const item = lightboxImages[lightboxCurrent];
        if (!item) return;
        const img = $('.lightbox-img', lb);
        img.src = item.url;
        img.alt = item.label || (property && property.title) || '';
        const label = $('.lightbox-label', lb);
        label.textContent = item.label || '';
        label.style.display = item.label ? 'block' : 'none';
        $('.lightbox-counter', lb).textContent = `${lightboxCurrent + 1} / ${lightboxImages.length}`;
    }

    function showLightboxImage(index) {
        lightboxCurrent = (index + lightboxImages.length) % lightboxImages.length;
        paintLightbox(document.getElementById('gallery-lightbox'));
        if (lightboxImages === images) showImage(lightboxCurrent);
    }

    function openLightbox(startIndex, items = images) {
        if (!items.length) return;
        let lb = document.getElementById('gallery-lightbox');
        if (!lb) {
            lb = document.createElement('div');
            lb.id = 'gallery-lightbox';
            lb.setAttribute('role', 'dialog');
            lb.setAttribute('aria-modal', 'true');
            lb.setAttribute('aria-label', 'Bildergalerie');
            lb.innerHTML = `
                <div class="lightbox-backdrop"></div>
                <button type="button" class="lightbox-close" aria-label="Schließen"><i class="fas fa-times"></i></button>
                <button type="button" class="lightbox-nav lightbox-prev" aria-label="Vorheriges Bild"><i class="fas fa-chevron-left"></i></button>
                <div class="lightbox-content">
                    <img class="lightbox-img" src="" alt="">
                    <div class="lightbox-label"></div>
                    <div class="lightbox-counter"></div>
                </div>
                <button type="button" class="lightbox-nav lightbox-next" aria-label="Nächstes Bild"><i class="fas fa-chevron-right"></i></button>`;
            document.body.appendChild(lb);
            $('.lightbox-close', lb).addEventListener('click', closeLightbox);
            $('.lightbox-backdrop', lb).addEventListener('click', closeLightbox);
            $('.lightbox-prev', lb).addEventListener('click', () => showLightboxImage(lightboxCurrent - 1));
            $('.lightbox-next', lb).addEventListener('click', () => showLightboxImage(lightboxCurrent + 1));
            let x = 0;
            lb.addEventListener('touchstart', e => { x = e.changedTouches[0].clientX; }, { passive: true });
            lb.addEventListener('touchend', e => {
                const dx = e.changedTouches[0].clientX - x;
                if (Math.abs(dx) > 50) showLightboxImage(lightboxCurrent + (dx < 0 ? 1 : -1));
            }, { passive: true });
        }
        lastFocus = document.activeElement;
        lightboxImages = items;
        lightboxCurrent = ((startIndex % items.length) + items.length) % items.length;
        paintLightbox(lb);
        lb.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        $('.lightbox-close', lb).focus();
    }

    function closeLightbox() {
        const lb = document.getElementById('gallery-lightbox');
        if (lb) lb.style.display = 'none';
        document.body.style.overflow = '';
        if (lastFocus && lastFocus.focus) lastFocus.focus();
    }

    /* — Head / facts — */
    function priceInfo(p) {
        const rent = isRent(p);
        const extras = extra(p);
        const sub = [];
        if (rent && hasVal(p.warm_rent)) sub.push(`Warmmiete ${money(p.warm_rent)}`);
        if (rent && hasVal(p.deposit)) sub.push(`Kaution ${money(p.deposit)}`);
        if (!rent && hasVal(extras.house_fee)) sub.push(`Hausgeld ${money(extras.house_fee)}`);
        const hasCosts = hasVal(p.extra_costs) || hasVal(p.warm_rent);
        return {
            label: rent ? (hasCosts ? 'Kaltmiete / Monat' : 'Miete / Monat') : 'Kaufpreis',
            value: hasVal(p.price) ? money(p.price) : 'Preis auf Anfrage',
            sub: sub.join(' · ')
        };
    }

    function headHtml(p, place) {
        const pr = priceInfo(p);
        const status = p.status && p.status !== 'available' ? (STATUS[p.status] || p.status) : '';
        const loc = place;
        return `
        <header class="pd-head">
            <div>
                <div class="pd-badges">
                    ${offerLabel(p) ? `<span class="pd-badge is-offer">${esc(offerLabel(p))}</span>` : ''}
                    ${p.type ? `<span class="pd-badge">${esc(typeLabel(p.type))}</span>` : ''}
                    ${status ? `<span class="pd-badge is-warn">${esc(status)}</span>` : '<span class="pd-badge is-ok">Verfügbar</span>'}
                </div>
                <h1 class="pd-title">${esc(p.title)}</h1>
                ${loc ? `<p class="pd-loc"><i class="fas fa-location-dot" aria-hidden="true"></i><span>${esc(loc)}</span></p>` : ''}
            </div>
            <div class="pd-price-box">
                <span class="pd-price-label">${esc(pr.label)}</span>
                <span class="pd-price property-price">${esc(pr.value)}</span>
                ${pr.sub ? `<span class="pd-price-sub">${esc(pr.sub)}</span>` : ''}
            </div>
            ${factsHtml(p)}
            <div class="pd-actions">
                <button type="button" class="pd-icon-btn" id="detail-fav-btn" aria-pressed="false"><i class="far fa-heart"></i> <span>Merken</span></button>
                <a class="pd-icon-btn" href="#contact-form"><i class="far fa-envelope"></i> <span>Exposé anfordern</span></a>
                <button type="button" class="pd-icon-btn" id="detail-print-btn"><i class="fas fa-print"></i> <span>Seite drucken</span></button>
                <button type="button" class="pd-icon-btn" id="detail-share-btn"><i class="fas fa-share-alt"></i> <span>Teilen</span></button>
            </div>
        </header>`;
    }

    function factsHtml(p) {
        const f = [];
        const e = extra(p);
        const area = parseNum(p.area != null ? p.area : (p.size != null ? p.size : p.living_area));
        if (area) f.push(['fa-ruler-combined', `${fmtNum(area)} m²`, 'Wohnfläche']);
        const rooms = parseNum(p.rooms);
        if (rooms) f.push(['fa-door-open', fmtNum(rooms), 'Zimmer']);
        const baths = parseNum(p.bathrooms);
        if (baths) f.push(['fa-bath', fmtNum(baths), 'Badezimmer']);
        if (hasVal(e.bedrooms)) f.push(['fa-bed', fmtNum(e.bedrooms), 'Schlafzimmer']);
        else if (hasVal(e.parking_spaces)) f.push(['fa-car', fmtNum(e.parking_spaces), 'Stellplätze']);
        if (!f.length) return '';
        return `<section class="pd-facts" aria-label="Eckdaten">${f.map(x => `
            <div class="pd-fact"><span class="pd-fact-icon"><i class="fas ${x[0]}" aria-hidden="true"></i></span><div><strong>${esc(x[1])}</strong><span>${esc(x[2])}</span></div></div>`).join('')}</section>`;
    }

    /* — Sections — */
    function textParagraphs(value) {
        return value.split(/\n+/).map(s => s.trim()).filter(Boolean).map(s => `<p>${esc(s)}</p>`).join('');
    }

    function expandableText(body, previewSource = body) {
        if (body.length <= 520) return `<div class="pd-text">${textParagraphs(body)}</div>`;
        const cutoff = body.lastIndexOf(' ', 520);
        const previewText = previewSource === body
            ? body.slice(0, cutoff > 200 ? cutoff : 520).trim()
            : previewSource;
        const remainder = previewSource === body ? body.slice(previewText.length).trim() : body;
        const preview = previewText + (previewSource === body ? '…' : '');
        return `<div class="pd-text"><p>${esc(preview)}</p><details class="pd-description-more"><summary>Mehr anzeigen</summary><div class="pd-text pd-description-full">${textParagraphs(remainder)}</div></details></div>`;
    }

    function descriptionHtml(p) {
        const text = String(p.description || '').trim();
        const short = String(p.short_description || '').trim();
        const body = text || short;
        if (!body) return '';
        if (body.length <= 520) {
            const intro = text && short && short !== text ? `<p><strong>${esc(short)}</strong></p>` : '';
            return section('pd-description', 'fa-align-left', 'Beschreibung', `<div class="pd-text">${intro}${textParagraphs(body)}</div>`);
        }
        const previewSource = short && short !== body ? short : body;
        return section('pd-description', 'fa-align-left', 'Beschreibung', expandableText(body, previewSource));
    }

    function costsHtml(p) {
        const e = extra(p);
        let list;
        if (isRent(p)) {
            const hasCosts = hasVal(p.extra_costs) || hasVal(p.warm_rent);
            list = [
                { label: hasCosts ? 'Kaltmiete' : 'Miete', value: money(p.price) },
                { label: 'Nebenkosten', value: hasVal(p.extra_costs) ? money(p.extra_costs) : '' },
                { label: 'Heizkosten', value: hasVal(p.heating_costs) ? money(p.heating_costs) : '' },
                { label: 'Warmmiete', value: hasVal(p.warm_rent) ? money(p.warm_rent) : '' },
                { label: 'Kaution', value: hasVal(p.deposit) ? money(p.deposit) : '' }
            ];
        } else {
            const comm = e.commission_required && hasVal(e.commission_amount)
                ? (e.commission_unit === 'percent' ? `${fmtNum(e.commission_amount)} %` : money(e.commission_amount)) + (e.commission_payer ? ` (${e.commission_payer})` : '') + (e.commission_note ? ` · ${e.commission_note}` : '')
                : '';
            list = [
                { label: 'Kaufpreis', value: money(p.price) },
                { label: 'Kaufpreis pro m²', value: hasVal(p.area) && hasVal(p.price) ? money(Math.round(parseNum(p.price) / parseNum(p.area))) : '' },
                { label: 'Hausgeld', value: hasVal(e.house_fee) ? money(e.house_fee) : '' },
                { label: 'Käuferprovision', value: comm }
            ];
        }
        const filled = list.filter(r => r.value);
        if (filled.length < 2) return '';
        return section('pd-costs', 'fa-euro-sign', 'Preise & Kosten', rows(list, 'Warmmiete'));
    }

    function featuresHtml(p) {
        const list = (Array.isArray(p.features) ? p.features : []).map(x => String(x).trim()).filter(Boolean);
        if (!list.length) return '';
        const iconForFeature = label => {
            const value = label.toLowerCase();
            if (/erstbezug|neubau/.test(value)) return 'fa-building';
            if (/balkon|terrasse/.test(value)) return 'fa-umbrella-beach';
            if (/einbauk[uü]che|k[uü]che/.test(value)) return 'fa-kitchen-set';
            if (/garage|stellplatz|parkplatz/.test(value)) return 'fa-car';
            if (/aufzug|lift/.test(value)) return 'fa-elevator';
            if (/keller/.test(value)) return 'fa-box-archive';
            if (/garten/.test(value)) return 'fa-tree';
            if (/bad|dusche|wanne/.test(value)) return 'fa-bath';
            if (/heizung|w[aä]rmepumpe/.test(value)) return 'fa-temperature-half';
            if (/barrierefrei|rollstuhl/.test(value)) return 'fa-wheelchair';
            return 'fa-circle-check';
        };
        return section('pd-features', 'fa-couch', 'Ausstattung', `<ul class="pd-chips">${list.map(f => `<li><i class="fas ${iconForFeature(f)}" aria-hidden="true"></i>${esc(f)}</li>`).join('')}</ul>`);
    }

    function locationHtml(p) {
        const place = [p.zip, p.location || p.city].filter(Boolean).join(' ');
        const list = [
            { label: 'Ort', value: p.location || p.city || '' },
            { label: 'PLZ', value: p.zip || '' },
        ];
        const locationText = String(extra(p).location_description || '').trim();
        const body = rows(list) +
            (locationText ? expandableText(locationText) : '') +
            '<p class="pd-note"><i class="fas fa-circle-info"></i> Die genaue Adresse erhalten Sie auf Anfrage.</p>' +
            `<div id="property-map-section" hidden><div id="property-map" class="pd-map" role="img" aria-label="Kartenansicht der ungefähren Lage"></div><a id="property-map-fallback" class="pd-map-fallback" hidden target="_blank" rel="noopener noreferrer">Karte auf OpenStreetMap öffnen</a><p class="pd-note">Kartenansicht ist ungefähr.</p></div>`;
        if (!place) return '';
        return section('pd-location', 'fa-map-location-dot', 'Lage', body);
    }

    const ENERGY_COLORS = { 'A+': '#00a651', A: '#4cb847', B: '#8dc63f', C: '#c8d300', D: '#ffd500', E: '#fbb034', F: '#f58220', G: '#ee3124', H: '#c1272d' };

    function energyHtml(p) {
        const e = extra(p);
        const cert = { verbrauchsausweis: 'Verbrauchsausweis', bedarfsausweis: 'Bedarfsausweis' }[String(e.energy_certificate_type || '').toLowerCase()] || e.energy_certificate_type || '';
        const kind = { bedarf: 'Endenergiebedarf', verbrauch: 'Endenergieverbrauch' }[String(e.energy_value_kind || '').toLowerCase()] || 'Energiekennwert';
        const cls = String(e.energy_efficiency_class || '').trim().toUpperCase();
        const hasElectricityValue = e.energy_value_electricity !== null && e.energy_value_electricity !== undefined && String(e.energy_value_electricity).trim() !== '';
        const list = [
            { label: 'Energieausweis', value: cert },
            { label: hasElectricityValue ? `${kind} Wärme` : kind, value: e.energy_value !== null && e.energy_value !== undefined && String(e.energy_value).trim() !== '' ? `${fmtNum(e.energy_value)} kWh/(m²·a)` : '' },
            { label: `${kind} Strom`, value: hasElectricityValue ? `${fmtNum(e.energy_value_electricity)} kWh/(m²·a)` : '' },
            { label: 'Energieträger', value: e.energy_source || '' },
            { label: 'Effizienzklasse', value: cls },
            { label: 'Heizungsart', value: e.heating_type || '' },
            { label: 'Baujahr Heizung', value: e.heating_year || '' },
            { label: 'Baujahr lt. Energieausweis', value: e.energy_building_year || '' },
            { label: 'Gültig bis', value: fmtDate(e.energy_valid_until) }
        ];
        const hasEnergyData = [
            cert, e.energy_value, e.energy_value_electricity, e.energy_source, cls,
            e.heating_type, e.heating_year, e.energy_building_year, e.energy_valid_until
        ].some(value => value != null && String(value).trim() !== '');
        if (!hasEnergyData) return '';
        const scale = ENERGY_COLORS[cls] ? `<div class="pd-scale" aria-hidden="true">${Object.keys(ENERGY_COLORS).map(k => `<span class="${k === cls ? 'on' : ''}" style="background:${ENERGY_COLORS[k]}">${k}</span>`).join('')}</div>` : '';
        return section('pd-energy', 'fa-leaf', 'Energieinformationen', `<div class="pd-energy">${scale}${rows(list)}</div>`);
    }

    function detailsHtml(p) {
        const e = extra(p);
        const list = [
            { label: 'Baujahr', value: e.building_year || p.year_built || '' },
            { label: 'Berechnungsgrundlage Fläche', value: e.area_calculation_basis || '' },
            { label: 'Grundstücksfläche', value: hasVal(e.plot_area) ? `${fmtNum(e.plot_area)} m²` : '' },
            { label: 'Wohneinheiten', value: hasVal(e.residential_units) ? fmtNum(e.residential_units) : '' },
            { label: 'Gäste-WCs', value: hasVal(e.guest_toilets) ? fmtNum(e.guest_toilets) : '' },
            { label: 'Etagen', value: e.floors_total || '' },
            { label: 'Etage', value: hasVal(e.floor) ? e.floor : '' },
            { label: 'Schlafzimmer', value: hasVal(e.bedrooms) ? e.bedrooms : '' },
            { label: 'Balkon/Terrasse', value: e.has_balcony_terrace ? 'Ja' : '' },
            { label: 'Balkon-/Terrassenfläche', value: hasVal(e.balcony_area) ? `${fmtNum(e.balcony_area)} m²` : '' },
            { label: 'Einbauküche', value: e.has_builtin_kitchen ? 'Ja' : '' },
            { label: 'Aufzug', value: e.has_elevator === true ? 'Ja' : '' },
            { label: 'Keller', value: e.has_basement ? 'Ja' : '' },
            { label: 'Bad', value: e.bath_features || '' },
            { label: 'Bodenbelag', value: e.flooring || '' },
            { label: 'Zustand', value: e.condition || '' },
            { label: 'Stellplätze', value: hasVal(e.parking_spaces) ? e.parking_spaces : '' },
            { label: 'Stellplatztyp', value: e.parking_type || '' },
            { label: 'Weitere Stellplatzangaben', value: e.parking_info || '' },
            { label: 'Letzte Modernisierung', value: e.last_modernization_year || '' },
            { label: 'Verfügbar ab', value: fmtDate(p.available_from) }
        ];
        const html = list.filter(item => item.value != null && String(item.value).trim() !== '').map(item =>
            `<div class="pd-detail-fact"><span>${esc(item.label)}</span><strong>${esc(item.value)}</strong></div>`
        ).join('');
        return html ? section('pd-details', 'fa-list-check', 'Fakten', `<div class="pd-detail-facts">${html}</div>`) : '';
    }

    function listAssets(arr) {
        return (Array.isArray(arr) ? arr : []).map(x => {
            if (typeof x === 'string') return { url: assetUrl(x), name: '' };
            if (!x || typeof x !== 'object') return null;
            return { url: assetUrl(x.url || x.file || x.path || x.image_url), name: x.label || x.name || x.title || x.filename || '' };
        }).filter(x => x && x.url);
    }

    const isImageAsset = url => /\.(jpe?g|png|webp|gif|avif)([?#]|$)/i.test(url);

    function mediaHtml(p) {
        const e = extra(p);
        const plans = listAssets(e.floorplans);
        const docs = listAssets(e.documents);
        const video = assetUrl(p.video_url);
        const imageItems = [...plans, ...docs].filter(x => isImageAsset(x.url));
        const imageLink = (x, label) => `<a href="${esc(x.url)}" data-media-index="${imageItems.indexOf(x)}"><img src="${esc(x.url)}" alt="${esc(label)}" loading="lazy" width="180" height="150"></a>`;
        let body = '';
        if (plans.length) {
            body += `<h3 style="margin:0 0 .75rem;font-size:1.05rem">Grundrisse</h3><div class="pd-plans">${plans.map((x, i) => isImageAsset(x.url)
                ? imageLink(x, x.name || 'Grundriss ' + (i + 1))
                : `<a class="pd-btn pd-btn-outline" href="${esc(x.url)}"><i class="fas fa-file-lines"></i> ${esc(x.name || 'Grundriss ' + (i + 1))}</a>`).join('')}</div>`;
        }
        if (docs.length) {
            body += `<h3 style="margin:1rem 0 .75rem;font-size:1.05rem">Dokumente</h3><ul class="pd-docs">${docs.map((x, i) => `<li>${isImageAsset(x.url)
                ? imageLink(x, x.name || 'Dokument ' + (i + 1))
                : `<a href="${esc(x.url)}"><i class="fas fa-file-pdf"></i> ${esc(x.name || 'Dokument ' + (i + 1))}</a>`}</li>`).join('')}</ul>`;
        }
        if (video) {
            body += /\.(mp4|webm|mov)(\?|$)/i.test(video)
                ? `<video class="pd-video" controls preload="none" src="${esc(video)}"></video>`
                : `<p><a class="pd-btn pd-btn-outline" href="${esc(video)}" target="_blank" rel="noopener"><i class="fas fa-play"></i> Video ansehen</a></p>`;
        }
        return body ? section('pd-media', 'fa-folder-open', 'Grundrisse, Dokumente & Video', body) : '';
    }

    function bindMediaGallery(p) {
        const container = $('#pd-media');
        if (!container) return;
        const e = extra(p);
        const items = [...listAssets(e.floorplans), ...listAssets(e.documents)]
            .filter(x => isImageAsset(x.url))
            .map(x => ({ url: x.url, label: x.name }));
        container.addEventListener('click', event => {
            const link = event.target.closest('[data-media-index]');
            if (!link) return;
            event.preventDefault();
            openLightbox(Number(link.dataset.mediaIndex), items);
        });
    }

    /* — Contact — */
    function waLink(p) {
        const link = (window.location.origin || '') + publicPropertyPath(p);
        return `${WA_BASE}?text=${encodeURIComponent(`Guten Tag, ich interessiere mich für die Immobilie „${p.title}“: ${link}`)}`;
    }

    function contactHtml(p) {
        return `
        <div class="pd-card pd-contact" id="contact-form">
            <h2 class="pd-h2">Kontaktformular</h2>
            <p class="pd-contact-intro">Senden Sie uns Ihre Frage oder fordern Sie das persönliche Exposé per E-Mail an.</p>
            <div id="propertyInquirySlot"></div>
            <div class="pd-contact-actions">
                <a class="pd-btn pd-btn-outline pd-btn-block" href="tel:${PHONE_TEL}"><i class="fas fa-phone"></i> ${PHONE_LABEL}</a>
                <a class="pd-btn pd-btn-wa pd-btn-block" href="${esc(waLink(p))}" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp schreiben</a>
            </div>
            <div class="pd-agent">
                <img src="${AGENT_IMG}" alt="Immobilien Ghumman" width="52" height="52" loading="lazy">
                <div><strong>Immobilien Ghumman</strong><span>Mo–Fr 09:00–18:00</span></div>
            </div>
        </div>`;
    }

    function mobileBarHtml(p) {
        return `
        <div class="pd-mobile-bar" role="region" aria-label="Kontakt">
            <a class="pd-btn pd-btn-primary" href="#contact-form"><i class="far fa-envelope"></i> Kontaktformular</a>
            <a class="pd-btn pd-btn-outline pd-btn-sq" href="tel:${PHONE_TEL}" aria-label="Anrufen"><i class="fas fa-phone"></i></a>
            <a class="pd-btn pd-btn-wa pd-btn-sq" href="${esc(waLink(p))}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
        </div>`;
    }

    /* ───────────── Actions (favorite, print, modal) ───────────── */
    function favHas(id) {
        if (window.Favorites) return window.Favorites.has(id);
        try { return (JSON.parse(localStorage.getItem('immobilien_favorites') || '[]')).includes(String(id)); } catch (e) { return false; }
    }
    function favToggle(id) {
        if (window.Favorites) return window.Favorites.toggle(id);
        let list = [];
        try { list = JSON.parse(localStorage.getItem('immobilien_favorites') || '[]'); } catch (e) { list = []; }
        const sid = String(id);
        const on = !list.includes(sid);
        list = on ? list.concat(sid) : list.filter(x => x !== sid);
        localStorage.setItem('immobilien_favorites', JSON.stringify(list));
        return on;
    }

    function bindActions(p) {
        $('#detail-print-btn')?.addEventListener('click', () => window.print());
        const fav = $('#detail-fav-btn');
        if (fav && p.id) {
            const paint = on => {
                fav.classList.toggle('fav-active', on);
                fav.setAttribute('aria-pressed', on ? 'true' : 'false');
                fav.innerHTML = `<i class="${on ? 'fas' : 'far'} fa-heart"></i> <span>${on ? 'Gemerkt' : 'Merken'}</span>`;
            };
            paint(favHas(p.id));
            fav.addEventListener('click', () => {
                const on = favToggle(p.id);
                paint(on);
                toast(on ? 'Zu Favoriten hinzugefügt ♥' : 'Aus Favoriten entfernt');
            });
        }
        const share = $('#detail-share-btn');
        if (share) share.addEventListener('click', async () => {
            try {
                if (navigator.share) {
                    await navigator.share({ title: document.title, url: location.href });
                } else if (navigator.clipboard) {
                    await navigator.clipboard.writeText(location.href);
                    toast('Link kopiert');
                } else {
                    toast('Teilen ist in diesem Browser nicht verfügbar.');
                }
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error('Immobilie konnte nicht geteilt werden:', error);
                    toast('Teilen fehlgeschlagen. Bitte kopieren Sie den Link aus der Adressleiste.');
                }
            }
        });
    }

    function syncInquiryForm(p) {
        const idInput = $('#inquiry_property_id'), titleInput = $('#inquiry_property_title'), linkInput = $('#inquiry_property_link');
        if (!idInput || !titleInput || !linkInput) return;
        idInput.value = p.id || '';
        titleInput.value = p.title || '';
        linkInput.value = (window.location.origin || 'https://www.immobilienghumman.de') + publicPropertyPath(p);
    }

    document.addEventListener('keydown', e => {
        const lb = document.getElementById('gallery-lightbox');
        const lbOpen = lb && lb.style.display === 'flex';
        if (e.key === 'Escape') {
            if (lbOpen) closeLightbox();
            return;
        }
        const tag = (e.target && e.target.tagName) || '';
        if (/INPUT|TEXTAREA|SELECT/.test(tag)) return;
        if (lbOpen) {
            if (e.key === 'ArrowRight') showLightboxImage(lightboxCurrent + 1);
            if (e.key === 'ArrowLeft') showLightboxImage(lightboxCurrent - 1);
        } else if (e.target && e.target.closest && e.target.closest('.pd-gallery')) {
            if (e.key === 'ArrowRight') showImage(current + 1);
            if (e.key === 'ArrowLeft') showImage(current - 1);
        }
    });

    window.addEventListener('beforeprint', () => {
        printExpanded = $$('#pd-content details:not([open])');
        printExpanded.forEach(detail => { detail.open = true; });
    });
    window.addEventListener('afterprint', () => {
        printExpanded.forEach(detail => { detail.open = false; });
        printExpanded = [];
    });

    /* ───────────── Map (Leaflet + Nominatim) ───────────── */
    function initPropertyMap(p) {
        const wrap = $('#property-map-section'), mapEl = $('#property-map');
        if (!wrap || !mapEl) return;
        const query = [p.zip, p.location || p.city, 'Deutschland'].filter(Boolean).join(', ');
        if (!p.zip && !p.location && !p.city) return;

        const fallback = $('#property-map-fallback');
        const showFallback = error => {
            console.error('Kartenansicht konnte nicht geladen werden:', error);
            wrap.hidden = false;
            mapEl.hidden = true;
            if (fallback) {
                fallback.href = `https://www.openstreetmap.org/search?query=${encodeURIComponent(query)}`;
                fallback.hidden = false;
            }
        };
        if (typeof L === 'undefined') {
            showFallback(new Error('Leaflet ist nicht verfügbar'));
            return;
        }

        fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=1`, { headers: { 'Accept-Language': 'de' } })
            .then(r => {
                if (!r.ok) throw new Error(`Geocoding HTTP ${r.status}`);
                return r.json();
            })
            .then(data => {
                if (!data || !data.length) throw new Error('Keine passende Kartenposition gefunden');
                const lat = parseFloat(data[0].lat), lon = parseFloat(data[0].lon);
                if (!isFinite(lat) || !isFinite(lon)) throw new Error('Ungültige Kartenkoordinaten');
                wrap.hidden = false;
                mapEl.hidden = false;
                if (fallback) fallback.hidden = true;
                const map = L.map(mapEl, { scrollWheelZoom: false }).setView([lat, lon], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    maxZoom: 19
                }).addTo(map);
                L.circle([lat, lon], { radius: 900, color: '#3070a0', fillColor: '#3070a0', fillOpacity: 0.18, weight: 2 }).addTo(map);
                setTimeout(() => map.invalidateSize(), 50);
            })
            .catch(showFallback);
    }

    /* ───────────── SEO ───────────── */
    function updateStructuredData(p) {
        let script = document.getElementById('pd-jsonld');
        if (!script) {
            script = document.createElement('script');
            script.type = 'application/ld+json';
            script.id = 'pd-jsonld';
            document.head.appendChild(script);
        }
        const data = {
            '@context': 'https://schema.org',
            '@type': 'RealEstateListing',
            name: p.title,
            url: (window.location.origin || '') + window.location.pathname,
            description: String(p.description || p.short_description || '').slice(0, 500),
            datePosted: p.created_at || undefined,
            image: images.map(i => i.url)
        };
        if (hasVal(p.price)) data.offers = { '@type': 'Offer', price: parseNum(p.price), priceCurrency: 'EUR' };
        script.textContent = JSON.stringify(data);
    }

    /* ───────────── Boot ───────────── */
    window.showImage = showImage;
    window.openLightbox = openLightbox;
    window.closeLightbox = closeLightbox;
    window.nextImage = nextImage;
    window.previousImage = previousImage;
    window.loadPropertyDetails = loadPropertyDetails;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadPropertyDetails);
    } else {
        loadPropertyDetails();
    }
})();
