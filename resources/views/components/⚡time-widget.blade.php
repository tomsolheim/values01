<?php

use Livewire\Component;

new class extends Component {};
?>

<div class="card mb-3" data-card-toggle="time" data-local-time-widget>
    <div class="card-header bg-white border-bottom py-2" data-utility-card-header>
        <h6 class="mb-0 fw-semibold small"><i class="bi bi-clock me-1"></i>Time</h6>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between gap-3 small">
            <span class="text-muted">Local time</span>
            <span class="fw-semibold text-end" data-local-time-value>--</span>
        </div>
        <div class="d-flex justify-content-between gap-3 small mt-2">
            <span class="text-muted">UTC time</span>
            <span class="fw-semibold text-end" data-utc-time-value>--</span>
        </div>
    </div>

    <script>
        (() => {
            const widget = document.currentScript?.closest('[data-local-time-widget]');

            const formatDateTime = (date, timeZone) => {
                return new Intl.DateTimeFormat('en-CA', {
                    timeZone,
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false,
                }).format(date);
            };

            const update = () => {
                if (!widget) {
                    return;
                }

                const now = new Date();
                widget.querySelector('[data-local-time-value]').textContent = formatDateTime(now);
                widget.querySelector('[data-utc-time-value]').textContent = formatDateTime(now, 'UTC');
            };

            const init = () => {
                if (!widget || widget.dataset.initialized === '1') {
                    return;
                }

                widget.dataset.initialized = '1';
                update();
                setInterval(update, 60000);
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init, { once: true });
            } else {
                init();
            }
        })();
    </script>
</div>
