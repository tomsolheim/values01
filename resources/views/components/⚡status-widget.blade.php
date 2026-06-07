<?php

use App\Models\Bundle;
use Livewire\Component;

new class extends Component
{
    public function selectBundle(int $bundleId): void
    {
        $this->dispatch('status-bundle-selected', bundleId: (string) $bundleId);
    }

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

    <div class="table-responsive mx-md-5">
        <table class="table table-sm table-hover mb-0" data-status-bundle-counts>
            <thead>
                <tr>
                    <th>Bundle</th>
                    <th class="text-center">Assets</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bundles as $bundle)
                    <tr>
                        <td>
                            <button type="button" class="btn btn-link btn-sm p-0 align-baseline" wire:click="selectBundle({{ $bundle->id }})" onclick="window.values01OpenAssetsTabFromStatus && window.values01OpenAssetsTabFromStatus()" data-status-bundle-link="{{ $bundle->id }}">
                                {{ $bundle->name }}
                            </button>
                        </td>
                        <td class="text-center fw-semibold">{{ $bundle->assets_count }}</td>
                        <td class="text-muted">{{ $bundle->comment }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-muted text-center">No bundles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
