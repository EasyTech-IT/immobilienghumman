/* Bewertungs-Wizard: eine Frage pro Schritt, meist nur Klicks. Muss vor dem Absende-Skript der Seite laufen. */
(function () {
    var form = document.getElementById('bewertungsForm');
    if (!form) return;
    form.classList.add('wz-js');

    var steps = [].slice.call(form.querySelectorAll('.wz-step'));
    var count = document.getElementById('wzCount');
    var bar = document.getElementById('wzBar');
    var barWrap = form.querySelector('.wz-bar');
    var back = document.getElementById('wzBack');
    var next = document.getElementById('wzNext');
    var err = document.getElementById('wzError');
    var nav = form.querySelector('.wz-nav');
    var top = form.querySelector('.wz-top');
    var stepsBox = form.querySelector('.wz-steps');
    var success = document.getElementById('wzSuccess');
    var area = document.getElementById('wzArea');
    var areaOut = document.getElementById('wzAreaOut');
    var areaQ = document.getElementById('wzAreaQ');
    var cur = steps[0];
    var timer = null;

    function isLand() {
        var c = form.querySelector('input[name=property_type]:checked');
        return !!c && c.value === 'Grundstueck';
    }
    function list() {
        var land = isLand();
        return steps.filter(function (s) { return !(land && s.hasAttribute('data-skip')); });
    }
    function check(step, silent) {
        var need = step.getAttribute('data-need');
        var msg = '';
        if (need === 'radio' && !step.querySelector('input:checked')) msg = 'Bitte wählen Sie eine Antwort aus.';
        if (need === 'address') {
            var a = document.getElementById('address');
            if (a.value.trim().length < 5) msg = 'Bitte geben Sie Straße und Ort an.';
        }
        if (need === 'contact') {
            var n = document.getElementById('name'), m = document.getElementById('email'), d = document.getElementById('datenschutz');
            if (!n.value.trim()) msg = 'Bitte geben Sie Ihren Namen an.';
            else if (!m.value.trim() || !m.checkValidity()) msg = 'Bitte geben Sie eine gültige E-Mail-Adresse an.';
            else if (!d.checked) msg = 'Bitte stimmen Sie der Datenschutzerklärung zu.';
        }
        if (!silent) { err.hidden = !msg; err.textContent = msg; }
        return !msg;
    }
    function render(focus) {
        var l = list();
        var i = l.indexOf(cur);
        steps.forEach(function (s) { s.hidden = s !== cur; });
        var pct = Math.round(((i + 1) / l.length) * 100);
        count.textContent = 'Frage ' + (i + 1) + ' von ' + l.length;
        bar.style.width = pct + '%';
        barWrap.setAttribute('aria-valuenow', pct);
        back.style.visibility = i === 0 ? 'hidden' : 'visible';
        var last = i === l.length - 1;
        next.hidden = last;
        next.disabled = false;
        areaQ.textContent = isLand() ? 'Wie groß ist das Grundstück?' : 'Wie groß ist die Wohnfläche?';
        err.hidden = true;
        if (focus) {
            var q = cur.querySelector('.wz-q');
            if (q) q.focus({ preventScroll: true });
            var r = form.getBoundingClientRect();
            if (r.top < 0) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    function go(d) {
        clearTimeout(timer);
        var l = list();
        var i = l.indexOf(cur) + d;
        if (i < 0 || i >= l.length) return;
        if (d > 0 && !check(cur)) return;
        cur = l[i];
        render(true);
    }

    form.addEventListener('change', function (e) {
        var t = e.target;
        if (t.type === 'radio' && cur.hasAttribute('data-auto') && cur.contains(t)) {
            err.hidden = true;
            clearTimeout(timer);
            var step = cur;
            timer = setTimeout(function () { if (cur === step) go(1); }, 320);
        }
        if (t.name === 'property_type') render(false);
    });

    function showArea() {
        var v = +area.value;
        areaOut.textContent = (v >= +area.max ? v + '+' : v) + ' m²';
        area.style.setProperty('--p', ((v - area.min) / (area.max - area.min) * 100) + '%');
    }
    area.addEventListener('input', showArea);
    [].forEach.call(form.querySelectorAll('.wz-step-btn'), function (b) {
        b.addEventListener('click', function () {
            area.value = Math.min(+area.max, Math.max(+area.min, +area.value + (+b.getAttribute('data-d'))));
            showArea();
        });
    });
    showArea();

    next.addEventListener('click', function () { go(1); });
    back.addEventListener('click', function () { go(-1); });

    form.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'BUTTON') return;
        var l = list();
        if (cur !== l[l.length - 1]) { e.preventDefault(); go(1); }
    });

    function pick(name) {
        return [].map.call(form.querySelectorAll('input[name=' + name + ']:checked'), function (i) { return i.value; }).join(', ');
    }
    form.addEventListener('submit', function (e) {
        if (!check(cur)) { e.preventDefault(); e.stopImmediatePropagation(); return; }
        if (isLand()) {
            steps.filter(function (s) { return s.hasAttribute('data-skip'); }).forEach(function (s) {
                [].forEach.call(s.querySelectorAll('input'), function (i) { i.checked = false; });
            });
        }
        var lines = [];
        [['Ziel', 'goal'], ['Zeitrahmen', 'timeframe'], ['Heizung', 'heating'], ['Ausstattung', 'amenity'], ['Aktuelle Nutzung', 'usage']].forEach(function (p) {
            var v = pick(p[1]);
            if (v) lines.push(p[0] + ': ' + v);
        });
        if (isLand()) lines.push('Grundstücksfläche: ' + area.value + ' m²');
        document.getElementById('wzMessage').value = lines.join('\n');
    });

    window.wzDone = function () {
        var m = document.getElementById('formMessage');
        if (m) m.style.display = 'none';
        stepsBox.hidden = true; nav.hidden = true; top.hidden = true; err.hidden = true;
        success.hidden = false;
        success.focus({ preventScroll: true });
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };
    form.addEventListener('reset', function () { setTimeout(function () { showArea(); }, 0); });
    document.getElementById('wzAgain').addEventListener('click', function () {
        success.hidden = true; stepsBox.hidden = false; nav.hidden = false; top.hidden = false;
        cur = steps[0];
        render(true);
    });

    render(false);
})();
