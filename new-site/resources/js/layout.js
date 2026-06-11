// Поведение перенесённой шапки/футера старого сайта без jQuery.
// Классы состояний (is-open, mobile-nav-open, is-visible) — те же,
// что ожидает custom.css, менять их нельзя.

document.addEventListener('DOMContentLoaded', () => {
    initMobileDrawer();
    initDesktopSubmenus();
    initCollapseToggles();
    initModals();
    initContactCard();
});

function initMobileDrawer() {
    const burger = document.getElementById('menu-icon');
    const overlay = document.getElementById('mobile-nav-overlay');
    const drawer = document.getElementById('mobile-nav-drawer');
    if (!burger || !drawer) return;

    let open = false;

    const closeAccordions = () => {
        drawer.querySelectorAll('.mobile-nav-toggle[aria-expanded="true"]').forEach((btn) => {
            btn.setAttribute('aria-expanded', 'false');
            const sub = btn.nextElementSibling;
            if (sub) sub.style.display = 'none';
            const icon = btn.querySelector('.material-icons');
            if (icon) icon.textContent = 'keyboard_arrow_down';
        });
    };

    const setOpen = (value) => {
        open = value;
        burger.setAttribute('aria-expanded', String(open));
        burger.setAttribute('aria-label', open ? 'Aizvērt izvēlni' : 'Atvērt izvēlni');
        burger.classList.toggle('is-active', open);
        if (overlay) {
            overlay.setAttribute('aria-hidden', String(!open));
            overlay.classList.toggle('is-visible', open);
        }
        drawer.setAttribute('aria-hidden', String(!open));
        drawer.classList.toggle('is-open', open);
        document.body.classList.toggle('mobile-nav-open', open);
        if (!open) closeAccordions();
    };

    burger.addEventListener('click', () => setOpen(!open));
    overlay?.addEventListener('click', () => setOpen(false));
    drawer.querySelector('.mobile-nav-drawer__close')?.addEventListener('click', () => setOpen(false));

    drawer.addEventListener('click', (event) => {
        const btn = event.target.closest('.mobile-nav-toggle');
        if (btn) {
            const expanded = btn.getAttribute('aria-expanded') === 'true';
            closeAccordions();
            if (!expanded) {
                btn.setAttribute('aria-expanded', 'true');
                const sub = btn.nextElementSibling;
                if (sub) sub.style.display = 'block';
                const icon = btn.querySelector('.material-icons');
                if (icon) icon.textContent = 'keyboard_arrow_up';
            }
            return;
        }
        if (event.target.closest('.mobile-nav-link')) setOpen(false);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && open) setOpen(false);
    });
}

function initDesktopSubmenus() {
    const menu = document.getElementById('_desktop_top_menu');
    if (!menu) return;

    menu.querySelectorAll(':scope .category').forEach((item) => {
        const submenu = item.querySelector(':scope > .sub-menu');
        if (!submenu) return;

        let hideTimer = null;

        item.addEventListener('mouseenter', () => {
            window.clearTimeout(hideTimer);
            menu.querySelectorAll('.category .sub-menu').forEach((s) => { s.style.display = 'none'; });
            submenu.style.display = 'block';
        });

        item.addEventListener('mouseleave', () => {
            hideTimer = window.setTimeout(() => { submenu.style.display = 'none'; }, 150);
        });
    });
}

// Мобильные «гармошки» футера и меню: data-toggle="collapse" + data-target="#id"
function initCollapseToggles() {
    document.querySelectorAll('[data-toggle="collapse"][data-target]').forEach((toggle) => {
        toggle.addEventListener('click', (event) => {
            const target = document.querySelector(toggle.dataset.target);
            if (!target) return;
            event.preventDefault();
            target.classList.toggle('show');
            target.style.display = target.classList.contains('show') ? 'block' : '';
        });
    });
}

// Минимальная замена bootstrap-модалки для разметки .modal.fade (карта в шапке)
function initModals() {
    document.querySelectorAll('[data-toggle="modal"][data-target]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const modal = document.querySelector(toggle.dataset.target);
            if (modal) showModal(modal);
        });
    });

    document.querySelectorAll('.modal [data-dismiss="modal"]').forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            hideModal(btn.closest('.modal'));
        });
    });

    document.querySelectorAll('.modal').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) hideModal(modal);
        });
    });
}

export function showModal(modal) {
    modal.style.display = 'block';
    modal.removeAttribute('aria-hidden');
    requestAnimationFrame(() => modal.classList.add('show'));
    document.body.classList.add('modal-open');
    modal.dispatchEvent(new CustomEvent('modal:shown', { bubbles: true }));
}

export function hideModal(modal) {
    if (!modal) return;
    modal.classList.remove('show');
    modal.setAttribute('aria-hidden', 'true');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
}

function initContactCard() {
    const toggle = document.getElementById('toggle-contacts');
    if (!toggle) return;
    const items = document.querySelector('.contact-card-items');
    const phone = document.getElementById('tc-phone');
    const close = document.getElementById('tc-close');

    toggle.addEventListener('click', () => {
        const visible = items && items.style.display === 'block';
        if (items) items.style.display = visible ? '' : 'block';
        if (phone) phone.style.display = visible ? '' : 'none';
        if (close) close.style.display = visible ? 'none' : 'inline';
    });
}
