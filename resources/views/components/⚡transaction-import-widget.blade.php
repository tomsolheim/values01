<?php

use App\Services\TransactionImportService;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public $importFile;
    public string $importMode = 'all';
    public string $isinFilter = '';
    public bool $addAssets = false;
    public ?array $lastResult = null;

    public function import(TransactionImportService $importer): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt,tsv',
            'importMode' => 'required|in:all,one',
            'isinFilter' => 'nullable|string|max:255',
            'addAssets' => 'boolean',
        ]);

        $filter = $this->importMode === 'one' ? trim($this->isinFilter) : null;
        $this->lastResult = $importer->import($this->importFile->getRealPath(), $filter ?: null, $this->addAssets);
        $this->importFile = null;
    }
};
?>

<div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0">Transaction Import</h5>
    </div>

    <form wire:submit="import" class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="transaction-import-file" class="form-label">Import file</label>
                    <input type="file" id="transaction-import-file" class="form-control @error('importFile') is-invalid @enderror" wire:model="importFile" accept=".csv,.txt,.tsv,text/tab-separated-values,text/plain">
                    @error('importFile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-md-3">
                    <label for="transaction-import-mode" class="form-label">Import mode</label>
                    <select id="transaction-import-mode" class="form-select" wire:model.live="importMode">
                        <option value="all">All ISINs</option>
                        <option value="one">One ISIN</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label for="transaction-isin-filter" class="form-label">ISIN filter</label>
                    <input type="text" id="transaction-isin-filter" class="form-control" wire:model="isinFilter" placeholder="Manual ISIN">
                    <div class="form-text">No ticker filter is used.</div>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input type="checkbox" id="transaction-add-assets" class="form-check-input" wire:model="addAssets">
                        <label for="transaction-add-assets" class="form-check-label">Add assets</label>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary btn-sm">Import transactions</button>
            </div>
        </div>
    </form>

    @if ($lastResult)
        <div class="alert alert-info py-2 small mb-0">
            Imported {{ $lastResult['imported'] }} rows, skipped {{ $lastResult['skipped'] }} rows, created {{ $lastResult['assetsCreated'] }} assets.
        </div>
    @endif
</div>
