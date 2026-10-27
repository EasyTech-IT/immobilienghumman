/**
 * contact-captcha.js
 * Einfache, lokale Mathe-CAPTCHA ohne externe Drittanbieter-Skripte.
 * Wird fuer alle Kontaktformulare mit CSRF/Form-Typ eingebunden.
 */
(function () {
    var question = window.CONTACT_CAPTCHA_QUESTION || '';
    if (!question) return;

    function addCaptchaUi(form) {
        if (form.querySelector('input[name="captcha_answer"]')) return;

        var submitEl = form.querySelector('button[type="submit"], input[type="submit"]');
        var wrap = document.createElement('div');
        wrap.className = 'captcha-row';
        wrap.style.cssText = 'margin-top:0.75rem;display:flex;flex-direction:column;gap:0.4rem;';

        var label = document.createElement('label');
        label.style.cssText = 'font-size:0.9rem;font-weight:600;color:#1f2937;';
        label.textContent = 'Sicherheitsfrage: ' + question;

        var input = document.createElement('input');
        input.type = 'text';
        input.name = 'captcha_answer';
        input.required = true;
        input.inputMode = 'numeric';
        input.autocomplete = 'off';
        input.placeholder = 'Ergebnis eingeben';
        input.style.cssText = 'max-width:220px;padding:0.55rem 0.7rem;border:1px solid #cbd5e1;border-radius:8px;';

        var note = document.createElement('small');
        note.style.cssText = 'display:block;font-size:0.75rem;color:#6b7280;';
        note.textContent = 'Spam-Schutz aktiv: Bitte loesen Sie die Sicherheitsfrage.';

        wrap.appendChild(label);
        wrap.appendChild(input);
        wrap.appendChild(note);

        if (submitEl && submitEl.parentNode) {
            submitEl.parentNode.insertBefore(wrap, submitEl);
        } else {
            form.appendChild(wrap);
        }
    }

    function initContactForms() {
        document.querySelectorAll('form').forEach(function (form) {
            if (form.querySelector('input[name="csrf_token"], input[name="form_type"]')) {
                addCaptchaUi(form);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initContactForms);
    } else {
        initContactForms();
    }
})();

