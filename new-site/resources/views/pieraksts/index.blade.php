<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pieraksts — R1 Riepas</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; padding: 1rem; background: #f5f5f5; }
        h1 { font-size: 1.4rem; }
        .flash { padding: .75rem 1rem; border-radius: 6px; margin-bottom: 1rem; }
        .flash-success { background: #d1fae5; color: #065f46; }
        .flash-warning, .flash-danger { background: #fee2e2; color: #991b1b; }
        .day { background: #fff; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; }
        .day h2 { font-size: 1.05rem; margin: 0 0 .5rem; }
        .queue { margin-bottom: .75rem; }
        .queue h3 { font-size: .9rem; margin: 0 0 .25rem; color: #555; }
        .slots { display: flex; flex-wrap: wrap; gap: 4px; }
        .slot { border: 0; border-radius: 4px; padding: .35rem .55rem; font-size: .8rem; cursor: pointer; }
        .slot-free { background: #bbf7d0; }
        .slot-reserved { background: #fde68a; cursor: not-allowed; }
        .slot-taken { background: #e5e7eb; color: #9ca3af; cursor: not-allowed; }
        dialog { border: 1px solid #ddd; border-radius: 8px; max-width: 420px; width: 100%; }
        dialog form { display: flex; flex-direction: column; gap: .5rem; }
        dialog label { font-size: .85rem; }
        dialog input, dialog select, dialog textarea { width: 100%; padding: .4rem; box-sizing: border-box; }
        .errors { color: #b91c1c; font-size: .85rem; }
        .countdown { font-size: .8rem; color: #92400e; }
    </style>
</head>
<body>
<h1>Pieraksts riepu servisam</h1>

@foreach (['success', 'warning', 'danger'] as $flash)
    @if (session($flash))
        <div class="flash flash-{{ $flash }}">{!! session($flash) !!}</div>
    @endif
@endforeach

@foreach ($days as $date => $queues)
    @continue(empty($queues))
    <section class="day">
        <h2>{{ \Carbon\Carbon::parse($date)->translatedFormat('d.m.Y') }}</h2>
        @foreach ($queues as $queue)
            <div class="queue">
                <h3>{{ $offices->firstWhere('id', $queue['office_id'])?->title }} — {{ $queue['title'] ?? ('Rinda '.$queue['queue_id']) }}</h3>
                <div class="slots">
                    @foreach ($queue['slots'] as $slot)
                        <button
                            class="slot slot-{{ $slot['status'] }}"
                            @if ($slot['status'] !== 'free') disabled @endif
                            data-queue="{{ $queue['queue_id'] }}"
                            data-date="{{ $date }}"
                            data-position="{{ $slot['position'] }}"
                            data-time="{{ $slot['time'] }}"
                            data-office="{{ $queue['office_id'] }}"
                        >{{ $slot['time'] }}</button>
                    @endforeach
                </div>
            </div>
        @endforeach
    </section>
@endforeach

<dialog id="bookingDialog">
    <form id="bookingForm">
        <p><b id="dlgTitle"></b> <span class="countdown" id="countdown"></span></p>
        <div class="errors" id="formErrors"></div>
        <label>Pakalpojums*
            <select name="service_id" required>
                <option value="">— izvēlieties —</option>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}">{{ $service->title }}</option>
                @endforeach
            </select>
        </label>
        <label id="rimsRow" hidden>Riepas*
            <select name="rims_with">
                <option value="">— izvēlieties —</option>
                <option value="1">Bez diskiem</option>
                <option value="2">Ar diskiem</option>
            </select>
        </label>
        <label>Auto marka* <input name="car_brand" required></label>
        <label>Auto modelis* <input name="car_model" required></label>
        <label>Reģ. numurs* <input name="license_plate" required></label>
        <label>Telefons* <input name="phone_number" required inputmode="numeric"></label>
        <label>E-pasts <input name="email" type="email"></label>
        <label>Komentārs <textarea name="customer_comment" rows="2"></textarea></label>
        <button type="submit">Pierakstīties</button>
        <button type="button" id="dlgClose">Aizvērt</button>
    </form>
</dialog>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;
const dialog = document.getElementById('bookingDialog');
const form = document.getElementById('bookingForm');
const errorsBox = document.getElementById('formErrors');
const countdownEl = document.getElementById('countdown');
let current = null;       // {queue, date, position, slotId}
let countdownTimer = null;

async function post(url, body) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'},
        body: JSON.stringify(body),
    });
    return {status: res.status, data: await res.json()};
}

function startCountdown(until) {
    clearInterval(countdownTimer);
    countdownTimer = setInterval(() => {
        const left = Math.max(0, Math.floor((new Date(until) - new Date()) / 1000));
        countdownEl.textContent = 'Rezervēts: ' + Math.floor(left / 60) + ':' + String(left % 60).padStart(2, '0');
        if (left <= 0) clearInterval(countdownTimer);
    }, 1000);
}

document.querySelectorAll('.slot-free').forEach(btn => btn.addEventListener('click', async () => {
    const payload = {queue_id: +btn.dataset.queue, date: btn.dataset.date, position: +btn.dataset.position};
    const {data} = await post('{{ route('pieraksts.reserve') }}', payload);
    if (!data.success) { alert(data.message); location.reload(); return; }
    current = {...payload, slotId: data.slot_id};
    document.getElementById('dlgTitle').textContent = btn.dataset.date + ' ' + btn.dataset.time;
    errorsBox.textContent = '';
    form.reset();
    startCountdown(data.reserved_until);
    dialog.showModal();
}));

form.querySelector('[name="service_id"]').addEventListener('change', e => {
    document.getElementById('rimsRow').hidden = e.target.value !== '1';
});

form.addEventListener('submit', async e => {
    e.preventDefault();
    const body = Object.fromEntries(new FormData(form).entries());
    Object.assign(body, current, {queue_id: current.queue_id ?? current.queue, position: current.position, date: current.date});
    const {status, data} = await post('{{ route('pieraksts.store') }}', body);
    if (data.success) {
        dialog.close();
        alert(data.message.replace(/<[^>]+>/g, ''));
        location.reload();
    } else if (data.errors) {
        errorsBox.innerHTML = Object.values(data.errors).flat().join('<br>');
    } else {
        errorsBox.textContent = data.message || 'Kļūda. Mēģiniet vēlreiz.';
    }
});

document.getElementById('dlgClose').addEventListener('click', async () => {
    if (current?.slotId) await post('{{ route('pieraksts.release') }}', {slot_id: current.slotId});
    clearInterval(countdownTimer);
    dialog.close();
});
</script>
</body>
</html>
