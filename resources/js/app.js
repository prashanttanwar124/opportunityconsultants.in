/**
 * Free counselling form: shows one step at a time, advances when an option
 * is picked, and mirrors answers onto the live boarding pass. Without JS the
 * form still works as one long page that posts to the server.
 */
function initApplyForm() {
    const form = document.getElementById('apply-form');
    if (!form) return;

    const steps = [...form.querySelectorAll('[data-step]')];
    const progress = document.querySelector('[data-progress]');
    const stepNumber = document.querySelector('[data-step-number]');
    const backButton = document.querySelector('[data-back]');
    const segments = [...document.querySelectorAll('[data-segment]')];
    const clientError = form.querySelector('[data-client-error]');
    const pass = (key) => document.querySelector(`[data-pass="${key}"]`);

    let current = Number(form.dataset.startStep) || 1;

    function show(step) {
        current = step;
        steps.forEach((el) => { el.hidden = Number(el.dataset.step) !== step; });
        stepNumber.textContent = step;
        backButton.hidden = step === 1;
        segments.forEach((seg, i) => {
            seg.classList.toggle('is-done', i + 1 < step);
            seg.classList.toggle('is-current', i + 1 === step);
        });
    }

    function syncPass() {
        const level = form.querySelector('input[name="level"]:checked');
        const country = form.querySelector('input[name="country"]:checked');
        const intake = form.querySelector('input[name="intake"]:checked');
        const name = form.querySelector('[data-pass-input="name"]').value.trim();
        const city = form.querySelector('[data-pass-input="city"]').value.trim();

        pass('level').textContent = level ? level.dataset.short : '—';
        pass('code').textContent = country ? country.dataset.code : '—';
        pass('country').textContent = country ? country.dataset.label : 'Destination';
        pass('intake').textContent = intake ? intake.dataset.short : '—';
        pass('name').textContent = name || 'Your name';
        pass('city').textContent = city || 'Your city';
    }

    form.addEventListener('change', (event) => {
        const input = event.target;
        syncPass();
        if (input.type === 'radio') {
            const step = Number(input.closest('[data-step]').dataset.step);
            if (step < 4) window.setTimeout(() => show(step + 1), 180);
        }
    });

    form.addEventListener('input', syncPass);

    backButton.addEventListener('click', () => {
        clientError.hidden = true;
        show(Math.max(1, current - 1));
    });

    form.addEventListener('submit', (event) => {
        const name = form.elements.name.value.trim();
        const digits = form.elements.phone.value.replace(/\D/g, '');
        const consent = form.elements.consent.checked;

        // Jump back to any step that was skipped.
        const missing = ['level', 'country', 'intake'].findIndex((key) => !form.querySelector(`input[name="${key}"]:checked`));
        if (missing !== -1) {
            event.preventDefault();
            show(missing + 1);
            return;
        }

        if (name.length < 2 || digits.length !== 10 || !consent) {
            event.preventDefault();
            clientError.hidden = false;
        }
    });

    progress.hidden = false;
    syncPass();
    show(current);
}

/** Close the mobile menu after a link inside it is tapped. */
function initMobileNav() {
    document.querySelectorAll('.mobile-nav').forEach((menu) => {
        menu.addEventListener('click', (event) => {
            if (event.target.closest('a')) menu.open = false;
        });
    });
    document.addEventListener('click', (event) => {
        document.querySelectorAll('.mobile-nav[open]').forEach((menu) => {
            if (!menu.contains(event.target)) menu.open = false;
        });
    });
}

initApplyForm();
initMobileNav();
