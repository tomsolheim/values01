<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        return [
            'counts' => [
                'Assets' => $this->count('assets'),
                'Bundles' => $this->count('bundles'),
                'Areas' => $this->count('areas'),
                'Transactions' => $this->count('transactions'),
                'Variables' => $this->count('variables'),
            ],
        ];
    }

    private function count(string $table): int|string
    {
        if (!Schema::hasTable($table)) {
            return 0;
        }

        return DB::table($table)->count();
    }
};
?>

<div class="card h-100" data-table-size-widget>
    <div class="card-header bg-white border-bottom py-2" data-utility-card-header>
        <h6 class="mb-0 fw-semibold small"><i class="bi bi-hdd me-1"></i>Table Size</h6>
    </div>
    <div class="card-body py-2">
        <div class="row g-2 small" data-table-size-counts>
            @foreach ($counts as $label => $count)
                <div class="col-6 d-flex justify-content-between gap-2">
                    <span class="text-muted">{{ $label }}</span>
                    <span class="fw-semibold text-end">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
