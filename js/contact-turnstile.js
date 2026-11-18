/**
 * contact-turnstile.js
 * Bindet Cloudflare Turnstile in Kontaktformulare ein.
 */
(function () {
    var siteKey = (window.TURNSTILE_SITE_KEY || '').trim();
    if (!siteKey) return;

    function setSubmitState(form, enabled) {
        form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (btn) {
            btn.disabled = !enabled;
        });
    }

    function bindSubmitGate(form) {
        // Default locked until Turnstile has produced a token.
        setSubmitState(form, false);

        function refreshFromToken() {
            var tokenInput = form.querySelector('input[name="cf-turnstile-response"]');
            var solved = !!(tokenInput && tokenInput.value && tokenInput.value.trim().length > 0);
            setSubmitState(form, solved);
        }

        var checks = 0;
        var maxChecks = 180; // 90s @ 500ms
        var timer = setInterval(function () {
            checks += 1;
            refreshFromToken();
            if (checks >= maxChecks) {
                clearInterval(timer);
            }
        }, 500);

        form.addEventListener('submit', function () {
            clearInterval(timer);
        });

        form.addEventListener('input', refreshFromToken, true);
        form.addEventListener('change', refreshFromToken, true);
        refreshFromToken();
    }

    function hasTurnstile(form) {
        return !!form.querySelector('.cf-turnstile') || !!form.querySelector('input[name="cf-turnstile-response"]');
    }

    function ensureTurnstile(form) {
        var submitEl = form.querySelector('button[type="submit"], input[type="submit"]');

        // Existing widget in markup (e.g., homepage form)
        if (hasTurnstile(form)) {
            bindSubmitGate(form);
            var existing = form.querySelector('.cf-turnstile');
            if (existing) {
                existing.setAttribute('data-sitekey', siteKey);
                existing.setAttribute('data-callback', 'javascriptCallback');
            }
            return;
        }

        var wrap = document.createElement('div');
        wrap.className = 'turnstile-wrap';
        wrap.style.cssText = 'margin-top:0.9rem;display:flex;justify-content:flex-start;';

        var widget = document.createElement('div');
        widget.className = 'cf-turnstile';
        widget.setAttribute('data-sitekey', siteKey);
        widget.setAttribute('data-theme', 'light');
        widget.setAttribute('data-size', 'normal');
        widget.setAttribute('data-callback', 'javascriptCallback');

        wrap.appendChild(widget);

        if (submitEl && submitEl.parentNode) {
            submitEl.parentNode.insertBefore(wrap, submitEl);
        } else {
            form.appendChild(wrap);
        }

        bindSubmitGate(form);

        try {
            window.turnstile.render(widget, {
                sitekey: siteKey,
                theme: 'light',
                callback: function () {
                    setSubmitState(form, true);
                },
                'expired-callback': function () {
                    setSubmitState(form, false);
                },
                'error-callback': function () {
                    setSubmitState(form, false);
                }
            });
        } catch (err) {
            console.error('Turnstile konnte nicht initialisiert werden.', err);
        }
    }

    // Global callback for markup-based widgets using data-callback="javascriptCallback".
    window.javascriptCallback = function () {
        document.querySelectorAll('form').forEach(function (form) {
            var tokenInput = form.querySelector('input[name="cf-turnstile-response"]');
            if (tokenInput && tokenInput.value && tokenInput.value.trim().length > 0) {
                setSubmitState(form, true);
            }
        });
    };

    function init() {
        if (typeof window.turnstile === 'undefined') return false;
        document.querySelectorAll('form').forEach(function (form) {
            if (form.querySelector('input[name="csrf_token"], input[name="form_type"]')) {
                ensureTurnstile(form);
            }
        });
        return true;
    }

    function boot() {
        var needsTurnstile = Array.prototype.some.call(document.querySelectorAll('form'), function (form) {
            return hasTurnstile(form) || !!form.querySelector('input[name="csrf_token"], input[name="form_type"]');
        });
        if (!needsTurnstile) return;
        if (init()) return;

        var script = document.createElement('script');
        script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js';
        script.async = true;
        script.addEventListener('load', init);
        script.addEventListener('error', function () {
            console.error('Cloudflare Turnstile konnte nicht geladen werden.');
        });
        document.head.appendChild(script);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
