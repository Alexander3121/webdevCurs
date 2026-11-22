/**
 * АвтоПрокат — клиентские скрипты.
 * 1) «Живой» калькулятор: при изменении полей запрашивает расчёт у сервера (api/calculate)
 * 2) Дата возврата не может быть раньше даты получения
 * 3) Подтверждение опасных действий (удаление, отмена)
 * 4) Мобильное меню
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ---------- 1. Калькулятор ---------- */
    document.querySelectorAll('form[data-calc]').forEach((form) => {
        const apiUrl = form.dataset.calc;
        const box = document.querySelector('[data-result]');
        const bookLink = document.querySelector('[data-book-link]');
        let timer = null;
        let lastRequest = 0;

        const recalc = async () => {
            const params = new URLSearchParams(new FormData(form));
            params.delete('_token');
            params.delete('comment');

            if (!params.get('car')) {
                box.innerHTML = '<p class="muted">Выберите автомобиль, чтобы увидеть расчёт.</p>';
                return;
            }

            const requestId = ++lastRequest;
            box.classList.add('is-loading');

            try {
                const response = await fetch(apiUrl + '?' + params.toString(), {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await response.json();
                if (requestId !== lastRequest) return; // пришёл устаревший ответ

                box.innerHTML = data.ok
                    ? data.html
                    : data.errors.map((e) => `<div class="flash flash--error">${escapeHtml(e)}</div>`).join('');

                if (bookLink) {
                    bookLink.hidden = !data.ok;
                    bookLink.href = bookLink.href.replace(/cars\/\d+$/, 'cars/' + params.get('car'));
                }
            } catch (err) {
                box.innerHTML = '<div class="flash flash--error">Не удалось связаться с сервером. Нажмите «Рассчитать».</div>';
            } finally {
                box.classList.remove('is-loading');
            }
        };

        // Небольшая задержка, чтобы не слать запрос на каждую цифру
        const schedule = () => {
            clearTimeout(timer);
            timer = setTimeout(recalc, 250);
        };

        form.addEventListener('input', schedule);
        form.addEventListener('change', schedule);
        recalc();
    });

    /* ---------- 2. Даты ---------- */
    document.querySelectorAll('[data-from]').forEach((from) => {
        const to = from.form.querySelector('[data-to]');
        const sync = () => {
            to.min = from.value;
            if (to.value && to.value < from.value) {
                to.value = from.value;
            }
        };
        from.addEventListener('change', sync);
        sync();
    });

    /* ---------- 3. Подтверждение ---------- */
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    /* ---------- 4. Мобильное меню ---------- */
    const burger = document.querySelector('[data-burger]');
    const nav = document.querySelector('[data-nav]');
    if (burger && nav) {
        burger.addEventListener('click', () => nav.classList.toggle('is-open'));
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
