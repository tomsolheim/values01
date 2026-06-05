<?php

use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        return [
            'cards' => [
                ['id' => 'top08', 'label' => 'top08'],
                ['id' => 'top09', 'label' => 'Instance Info'],
                ['id' => 'time', 'label' => 'Time'],
                ['id' => 'git-status', 'label' => 'Git Status'],
                ['id' => 'system-status', 'label' => 'System Status'],
                ['id' => 'workbench', 'label' => 'Workbench'],
                ['id' => 'variables', 'label' => 'Variables'],
            ],
        ];
    }
};
?>

<div class="card mb-3" data-card-visibility-controller data-card-selector-position="side01">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center gap-2 py-2" data-utility-card-header>
        <h6 class="mb-0 fw-semibold small"><i class="bi bi-sliders me-1"></i>Card Selector</h6>
        <div class="btn-group btn-group-sm" role="group" aria-label="Card Selector actions">
            <button type="button" class="btn btn-outline-success" data-card-action="all-on">All on</button>
            <button type="button" class="btn btn-outline-secondary" data-card-action="all-off">All off</button>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="dropdown d-grid">
            <button class="btn btn-light dropdown-toggle rounded-0 text-start px-3 py-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Toggle cards
            </button>
            <ul class="dropdown-menu w-100">
                @foreach ($cards as $card)
                    <li>
                        <label class="dropdown-item d-flex align-items-center gap-2 mb-0">
                            <input class="form-check-input mt-0" type="checkbox" data-card-toggle-control="{{ $card['id'] }}" checked>
                            <span>{{ $card['label'] }}</span>
                        </label>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <script>
        (() => {
            const controller = document.currentScript?.closest('[data-card-visibility-controller]');

            const init = () => {
                if (!controller || controller.dataset.initialized === '1') {
                    return;
                }

                controller.dataset.initialized = '1';

                const controls = Array.from(controller.querySelectorAll('[data-card-toggle-control]'));
                const setControl = (id, visible) => {
                    const control = controller.querySelector(`[data-card-toggle-control="${id}"]`);

                    if (control) {
                        control.checked = visible;
                    }
                };
                const setCardVisible = (id, visible) => {
                    document.querySelectorAll(`[data-card-toggle="${id}"]`).forEach((card) => {
                        card.classList.toggle('d-none', !visible);
                    });

                    setControl(id, visible);
                };
                const setAllVisible = (visible) => controls.forEach((control) => setCardVisible(control.dataset.cardToggleControl, visible));

                controls.forEach((control) => {
                    control.addEventListener('change', () => setCardVisible(control.dataset.cardToggleControl, control.checked));
                });

                controller.querySelector('[data-card-action="all-on"]')?.addEventListener('click', () => setAllVisible(true));
                controller.querySelector('[data-card-action="all-off"]')?.addEventListener('click', () => setAllVisible(false));
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init, { once: true });
            } else {
                init();
            }
        })();
    </script>
</div>
