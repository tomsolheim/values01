<?php

use App\Models\Bundle;
use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        return [
            'bundles' => Bundle::query()
                ->withCount('assets')
                ->orderBy('name')
                ->get(),
        ];
    }
};
?>

<div data-status-widget>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0">Status</h5>
        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="$refresh" data-status-update-button>Update</button>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0" data-status-bundle-counts>
            <thead>
                <tr>
                    <th>Bundle</th>
                    <th class="text-end">Assets</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bundles as $bundle)
                    <tr>
                        <td>{{ $bundle->name }}</td>
                        <td class="text-end fw-semibold">{{ $bundle->assets_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-muted text-center">No bundles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
