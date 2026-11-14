(function () {
    'use strict';

    var tabs = Array.from(document.querySelectorAll('.analysis-tab'));
    function activateTab(tab) {
        tabs.forEach(function (item) {
            var selected = item === tab;
            item.classList.toggle('is-active', selected);
            item.setAttribute('aria-selected', String(selected));
            item.tabIndex = selected ? 0 : -1;
            document.getElementById(item.getAttribute('aria-controls')).hidden = !selected;
        });
    }
    tabs.forEach(function (tab, index) {
        tab.addEventListener('click', function () { activateTab(tab); });
        tab.addEventListener('keydown', function (event) {
            var next = index;
            if (event.key === 'ArrowRight' || event.key === 'ArrowDown') next = (index + 1) % tabs.length;
            else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') next = (index + tabs.length - 1) % tabs.length;
            else if (event.key === 'Home') next = 0;
            else if (event.key === 'End') next = tabs.length - 1;
            else return;
            event.preventDefault();
            activateTab(tabs[next]);
            tabs[next].focus();
        });
    });
    var categories = {
        immobilie: 'analyse_immobilie',
        finanzierung: 'analyse_finanzierung',
        hausverwaltung: 'analyse_hausverwaltung'
    };
    var selectedCategory = new URLSearchParams(window.location.search).get('bereich');
    if (categories[selectedCategory]) {
        activateTab(document.getElementById('tab-' + categories[selectedCategory]));
    }
    document.querySelectorAll('[data-analysis-target]').forEach(function (link) {
        link.addEventListener('click', function (event) {
            var tab = document.getElementById('tab-' + categories[link.dataset.analysisTarget]);
            if (!tab) return;
            event.preventDefault();
            activateTab(tab);
            document.getElementById('analyser').scrollIntoView({ behavior: 'auto' });
            tab.focus({ preventScroll: true });
        });
    });

    document.querySelectorAll('.analysis-form').forEach(function (form) {
        var panel = form.closest('.analysis-panel');
        var steps = Array.from(form.querySelectorAll('.analysis-step'));
        var back = form.querySelector('.analysis-back');
        var next = form.querySelector('.analysis-next');
        var submit = form.querySelector('.analysis-submit');
        var error = form.querySelector('.analysis-error');
        var result = panel.querySelector('.analysis-result');
        var current = 0;
        var returnToReview = false;
        var submitting = false;

        function showError(message) {
            error.textContent = message;
            error.hidden = false;
        }

        function validateStep(step) {
            if (step.dataset.multiple === 'true' && !step.querySelector('input:checked')) {
                showError('Bitte wählen Sie mindestens eine Antwort aus.');
                step.querySelector('input').focus();
                return false;
            }
            var controls = Array.from(step.querySelectorAll('input[required]'));
            var invalid = controls.find(function (input) { return !input.checkValidity(); });
            if (invalid) {
                showError(invalid.type === 'radio' ? 'Bitte wählen Sie eine Antwort aus.' : 'Bitte prüfen Sie Ihre Angaben und füllen Sie alle Pflichtfelder aus.');
                invalid.focus();
                return false;
            }
            if (form.elements.form_type.value === 'analyse_finanzierung' && step.dataset.step === '5') {
                var price = form.querySelector('input[name="analyse_kaufpreis"]:checked');
                var equity = form.querySelector('input[name="analyse_eigenkapital"]:checked');
                if (price && equity && price.dataset.amountBound !== undefined && equity.dataset.amountBound !== undefined &&
                    Number(equity.dataset.amountBound) > Number(price.dataset.amountBound)) {
                    showError('Das Eigenkapital liegt über dem Budget. Bitte prüfen Sie Ihre Angaben.');
                    equity.focus();
                    return false;
                }
            }
            if (form.elements.form_type.value === 'analyse_hausverwaltung' && step.classList.contains('analysis-contact') &&
                ['Telefonisch', 'Beides'].includes(form.elements.analyse_kontaktwunsch.value) && !form.elements.phone.value.trim()) {
                showError('Bitte geben Sie für den gewünschten Rückruf Ihre Telefonnummer an.');
                form.elements.phone.focus();
                return false;
            }
            return true;
        }

        function updateReview() {
            var list = form.querySelector('.analysis-review-list');
            list.replaceChildren();
            steps.slice(0, 12).forEach(function (step, index) {
                var row = document.createElement('div');
                var question = document.createElement('dt');
                var answer = document.createElement('dd');
                var edit = document.createElement('button');
                question.textContent = step.querySelector('legend').textContent;
                var selected = Array.from(step.querySelectorAll('input:checked'));
                answer.textContent = selected.length ? selected.map(function (input) { return input.value; }).join(', ') : 'Nicht beantwortet';
                edit.type = 'button';
                edit.textContent = 'Ändern';
                edit.setAttribute('aria-label', question.textContent + ' ändern');
                edit.addEventListener('click', function () {
                    returnToReview = true;
                    showStep(index);
                });
                row.append(question, answer, edit);
                list.appendChild(row);
            });
        }

        function showStep(index) {
            current = index;
            steps.forEach(function (step, i) { step.hidden = i !== index; });
            back.hidden = index === 0 && !returnToReview;
            next.hidden = index === steps.length - 1;
            submit.hidden = index !== steps.length - 1;
            panel.querySelector('progress').value = Math.min(index + 1, 12);
            panel.querySelector('.analysis-progress-text').textContent = index === 12 ? 'Ihre Antworten · Übersicht' :
                index === 13 ? 'Kontakt · letzter Schritt' : 'Frage ' + (index + 1) + ' von 12';
            error.hidden = true;
            if (index === 12) updateReview();
            steps[index].querySelector('legend').focus();
        }

        next.addEventListener('click', function () {
            error.hidden = true;
            if (validateStep(steps[current])) {
                var target = returnToReview ? 12 : current + 1;
                returnToReview = false;
                if (target === 12 && form.elements.form_type.value === 'analyse_finanzierung' && !validateStep(steps[4])) {
                    returnToReview = true;
                    showStep(4);
                    validateStep(steps[4]);
                    return;
                }
                showStep(target);
            }
        });
        form.addEventListener('click', function (event) {
            var input = event.target;
            if (!input.matches('.analysis-step input[type="radio"]')) return;
            next.click();
        });
        form.addEventListener('change', function (event) {
            var input = event.target;
            if (!input.matches('.analysis-step[data-multiple] input[type="checkbox"]') || !input.checked) return;
            var step = input.closest('.analysis-step');
            step.querySelectorAll('input[type="checkbox"]').forEach(function (other) {
                if (other !== input && (input.dataset.exclusive || other.dataset.exclusive)) other.checked = false;
            });
        });
        back.addEventListener('click', function () {
            if (returnToReview) {
                returnToReview = false;
                showStep(12);
            } else {
                showStep(current - 1);
            }
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            if (submitting) return;
            error.hidden = true;
            for (var i = 0; i < steps.length; i++) {
                if (!validateStep(steps[i])) {
                    if (i !== current) {
                        showStep(i);
                        validateStep(steps[i]);
                    }
                    return;
                }
            }
            submitting = true;
            submit.disabled = true;
            submit.textContent = 'Wird gesendet …';
            fetch(form.action, { method: 'POST', body: new FormData(form) })
                .then(function (response) {
                    return response.text().then(function (text) {
                        var data;
                        try { data = JSON.parse(text.replace(/^\uFEFF/, '')); }
                        catch (e) { throw new Error('Ungültige Serverantwort'); }
                        return { ok: response.ok, data: data };
                    });
                })
                .then(function (response) {
                    if (response.ok && response.data.success) {
                        form.hidden = true;
                        panel.querySelector('.analysis-progress').hidden = true;
                        result.textContent = response.data.message;
                        result.hidden = false;
                    } else if (response.data.stored) {
                        form.hidden = true;
                        panel.querySelector('.analysis-progress').hidden = true;
                        result.textContent = response.data.message;
                        result.hidden = false;
                    } else {
                        submitting = false;
                        showError(response.data.message || 'Die Anfrage konnte nicht gesendet werden. Bitte versuchen Sie es erneut.');
                        submit.disabled = false;
                        submit.textContent = 'Anfrage senden';
                    }
                })
                .catch(function () {
                    submitting = false;
                    showError('Die Verbindung wurde unterbrochen. Ihre Anfrage wurde möglicherweise gespeichert. Bitte rufen Sie uns an, bevor Sie sie erneut senden.');
                    submit.disabled = false;
                    submit.textContent = 'Anfrage senden';
                });
        });
    });
})();
