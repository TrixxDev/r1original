// Страница /pieraksts: клик по свободному слоту → мягкая резервация (5 минут)
// → форма брони → отправка. Перенос логики simple-slot-lock.js + client.js
// старого сайта на fetch без jQuery; серверные эндпоинты — новые.

document.addEventListener('DOMContentLoaded', () => {
    const section = document.getElementById('mobile-main');
    const schedule = document.querySelector('.schedule-table');
    if (!section || !schedule) return;

    const form = document.getElementById('booking-form');
    const errorsBox = document.getElementById('booking-errors');
    const countdownEl = document.getElementById('reservation-countdown');
    const successBox = section.querySelector('.mobile-body-success');
    const formWrap = section.querySelector('.mobile-body');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    let current = null; // { slotId, reservedUntil }
    let countdownTimer = null;

    const post = async (url, body) => {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                Accept: 'application/json',
            },
            body: JSON.stringify(body),
        });
        return { status: response.status, data: await response.json().catch(() => ({})) };
    };

    const stopCountdown = () => {
        window.clearInterval(countdownTimer);
        countdownTimer = null;
        countdownEl.textContent = '';
    };

    const startCountdown = (untilIso) => {
        stopCountdown();
        const tick = () => {
            const left = Math.max(0, Math.floor((new Date(untilIso) - new Date()) / 1000));
            countdownEl.textContent = `Laiks rezervēts: ${Math.floor(left / 60)}:${String(left % 60).padStart(2, '0')}`;
            if (left <= 0) {
                stopCountdown();
                alert('Rezervācijas laiks ir beidzies. Lūdzu, izvēlieties laiku vēlreiz.');
                window.location.reload();
            }
        };
        tick();
        countdownTimer = window.setInterval(tick, 1000);
    };

    const showErrors = (errors, fallback) => {
        const messages = errors ? Object.values(errors).flat() : [fallback || 'Kļūda. Mēģiniet vēlreiz.'];
        errorsBox.innerHTML = messages.join('<br>');
        errorsBox.style.display = 'block';
        errorsBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    };

    // Клик по свободному слоту
    schedule.addEventListener('click', async (event) => {
        const btn = event.target.closest('.available-slot');
        if (!btn) return;

        const timeStatus = btn.closest('.time-status');
        const table = btn.closest('.table');
        const column = btn.closest('[data-date]');
        if (!timeStatus || !table || !column) return;

        const payload = {
            queue_id: Number(table.dataset.queueId),
            date: column.dataset.date,
            position: Number(timeStatus.dataset.iorder),
        };

        const { data } = await post(section.dataset.reserveUrl, payload);
        if (!data.success) {
            alert(data.message || 'Laiks vairs nav pieejams.');
            window.location.reload();
            return;
        }

        current = { ...payload, slotId: data.slot_id };
        form.elements.queue_id.value = payload.queue_id;
        form.elements.date.value = payload.date;
        form.elements.position.value = payload.position;
        form.elements.slot_id.value = data.slot_id;

        const time = timeStatus.querySelector('.time-slot')?.textContent ?? '';
        document.getElementById('mobile-modalTitle').textContent = `Pieraksts — ${formatDate(payload.date)} ${time}`;

        errorsBox.style.display = 'none';
        successBox.style.display = 'none';
        formWrap.style.display = '';
        // custom.css прячет внутреннюю форму по умолчанию (мобильный сценарий
        // старого сайта) — открываем инлайн-стилем, как делал старый client.js
        document.getElementById('mobile-reservation-form').style.display = 'block';
        section.style.display = 'block';
        startCountdown(data.reserved_until);
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    // Услуга №1 (riepu maiņa, riepas līdzi) — выбор «ar/bez diskiem» обязателен
    form.elements.service_id.addEventListener('change', (event) => {
        const rims = form.querySelector('.rims-with-mobile');
        rims.style.display = event.target.value === '1' ? 'block' : 'none';
    });
    form.elements.service_id.dispatchEvent(new Event('change'));

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!current) return;

        const body = {};
        new FormData(form).forEach((value, key) => {
            if (value !== '') body[key] = value;
        });

        const submitBtn = document.getElementById('mobile-submit-reservation');
        submitBtn.disabled = true;
        try {
            const { data } = await post(section.dataset.storeUrl, body);
            if (data.success) {
                stopCountdown();
                current = null;
                formWrap.style.display = 'none';
                successBox.querySelector('.alert-success').innerHTML = data.message;
                successBox.style.display = 'block';
            } else {
                showErrors(data.errors, data.message);
            }
        } finally {
            submitBtn.disabled = false;
        }
    });

    document.getElementById('mobile-close-modal').addEventListener('click', async () => {
        if (current?.slotId) {
            await post(section.dataset.releaseUrl, { slot_id: current.slotId });
        }
        stopCountdown();
        current = null;
        section.style.display = 'none';
    });

    const formatDate = (iso) => {
        const [y, m, d] = iso.split('-');
        return `${d}.${m}.${y}`;
    };
});
