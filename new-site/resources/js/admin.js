// Админка записи: клик по ячейке → панель деталей с действиями
// (отмена брони, блокировка, скидка-комментарий).

document.addEventListener('DOMContentLoaded', () => {
    const gridEl = document.querySelector('.day-grid');
    const panel = document.getElementById('slot-panel');
    if (!gridEl || !panel) return;

    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    let cell = null; // активная ячейка (DOM)

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
        return response.json().catch(() => ({}));
    };

    const act = async (url, body) => {
        const data = await post(url, body);
        if (!data.success) {
            alert(data.message || 'Neizdevās. Atsvaidziniet lapu.');
        }
        window.location.reload();
    };

    document.getElementById('date-picker').addEventListener('change', (e) => {
        if (e.target.value) window.location = `?date=${e.target.value}`;
    });

    const show = (id, visible) => { document.getElementById(id).hidden = !visible; };
    const text = (id, value) => { document.getElementById(id).textContent = value || '—'; };

    gridEl.addEventListener('click', (event) => {
        const target = event.target.closest('.cell');
        if (!target || target.classList.contains('cell--closed')) return;

        cell = target;
        const state = cell.dataset.state;
        document.getElementById('sp-title').textContent = `${cell.dataset.time} — ${state === 'booked' ? 'pieraksts' : state === 'blocked' ? 'bloķēts' : 'brīvs laiks'}`;

        show('sp-booking', state === 'booked');
        show('sp-free', state === 'free' || state === 'discount' || state === 'reserved');
        show('sp-blocked', state === 'blocked');

        if (state === 'booked') {
            const b = JSON.parse(cell.dataset.booking);
            text('sp-car', b.car);
            text('sp-plate', b.plate);
            text('sp-name', b.name);
            text('sp-phone', b.phone);
            text('sp-email', b.email);
            text('sp-service', b.service + (b.rims_with ? (b.rims_with === 1 || b.rims_with === '1' ? ' (bez diskiem)' : ' (ar diskiem)') : ''));
            text('sp-comment', b.comment);
            text('sp-created', b.created_at);
            panel.dataset.bookingId = b.id;
        } else {
            document.getElementById('sp-discount').value = cell.dataset.comment || '';
        }

        panel.hidden = false;
    });

    panel.querySelector('.slot-panel__close').addEventListener('click', () => { panel.hidden = true; });

    const slotPayload = () => ({
        queue_id: Number(cell.dataset.queueId),
        date: gridEl.dataset.date,
        position: Number(cell.dataset.position),
    });

    document.getElementById('sp-cancel-booking').addEventListener('click', () => {
        if (!confirm('Atcelt šo pierakstu? Klients saņems paziņojumu.')) return;
        act(gridEl.dataset.cancelUrl, { booking_id: Number(panel.dataset.bookingId) });
    });

    document.getElementById('sp-block').addEventListener('click', () => {
        act(gridEl.dataset.blockUrl, slotPayload());
    });

    document.getElementById('sp-unblock').addEventListener('click', () => {
        act(gridEl.dataset.unblockUrl, { slot_id: Number(cell.dataset.slotId) });
    });

    document.getElementById('sp-save-discount').addEventListener('click', () => {
        act(gridEl.dataset.commentUrl, {
            ...slotPayload(),
            comment: document.getElementById('sp-discount').value,
        });
    });
});
