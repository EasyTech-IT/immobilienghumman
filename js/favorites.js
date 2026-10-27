/**
 * js/favorites.js – Immobilien Favoriten (localStorage)
 * Automatisch auf allen Seiten eingebunden via footer.php
 */

(function () {
    'use strict';

    const STORAGE_KEY = 'immobilien_favorites';

    /* ── Public API ───────────────────────────────────────── */
    window.Favorites = {
        get() {
            try {
                return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
            } catch {
                return [];
            }
        },
        has(id) {
            return this.get().includes(String(id));
        },
        add(id) {
            const favs = this.get();
            id = String(id);
            if (!favs.includes(id)) {
                favs.push(id);
                localStorage.setItem(STORAGE_KEY, JSON.stringify(favs));
            }
            this._updateBadge();
        },
        remove(id) {
            id = String(id);
            const favs = this.get().filter(f => f !== id);
            localStorage.setItem(STORAGE_KEY, JSON.stringify(favs));
            this._updateBadge();
        },
        toggle(id) {
            if (this.has(id)) {
                this.remove(id);
                return false;
            } else {
                this.add(id);
                return true;
            }
        },
        count() {
            return this.get().length;
        },
        _updateBadge() {
            const n = this.count();
            document.querySelectorAll('.fav-badge').forEach(el => {
                el.textContent = n;
                el.style.display = n > 0 ? 'inline-flex' : 'none';
            });
        },
    };

    /* ── Init: alle Herz-Buttons ──────────────────────────── */
    function initFavButtons() {
        document.querySelectorAll('[data-fav-id]').forEach(btn => {
            const id = btn.dataset.favId;
            setFavState(btn, Favorites.has(id));

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const active = Favorites.toggle(id);
                setFavState(this, active);

                // Herzchen-Animation
                this.classList.add('fav-pulse');
                setTimeout(() => this.classList.remove('fav-pulse'), 400);

                // Toast-Feedback
                showToast(active
                    ? 'Zu Favoriten hinzugefügt ♥'
                    : 'Aus Favoriten entfernt');
            });
        });

        Favorites._updateBadge();
    }

    function setFavState(btn, active) {
        btn.classList.toggle('fav-active', active);
        btn.setAttribute('title', active ? 'Aus Favoriten entfernen' : 'Zu Favoriten hinzufügen');
        btn.setAttribute('aria-label', active ? 'Aus Favoriten entfernen' : 'Zu Favoriten hinzufügen');
        btn.querySelector('i')?.classList.replace(
            active ? 'far' : 'fas',
            active ? 'fas' : 'far'
        );
    }

    /* ── Toast Notification ───────────────────────────────── */
    let toastTimer;
    function showToast(msg) {
        let toast = document.getElementById('fav-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'fav-toast';
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            document.body.appendChild(toast);
        }
        toast.textContent = msg;
        toast.classList.add('visible');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('visible'), 2500);
    }

    /* ── Boot ─────────────────────────────────────────────── */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFavButtons);
    } else {
        initFavButtons();
    }

    // Re-init wenn neue Karten dynamisch gerendert werden
    document.addEventListener('propertiesRendered', initFavButtons);
})();
