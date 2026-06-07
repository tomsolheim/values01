<?php

use App\Services\TransactionImportService;
use App\Models\Asset;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public $importFile;
    public string $importMode = 'all';
    public string $selectedIsin = '';
    public string $isinFilter = '';
    public bool $addAssets = false;
    public int $transactionCount = 0;
    public ?array $lastResult = null;
    public ?string $maintenanceMessage = null;

    public function mount(): void
    {
        $this->refreshTransactionCount();
    }

    public function import(TransactionImportService $importer): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt,tsv',
            'importMode' => 'required|in:all,one',
            'selectedIsin' => 'nullable|string|max:255',
            'isinFilter' => 'nullable|string|max:255',
            'addAssets' => 'boolean',
        ]);

        $selectedIsin = trim($this->selectedIsin);
        $manualIsin = trim($this->isinFilter);
        $filter = $this->importMode === 'one' ? ($selectedIsin ?: $manualIsin) : null;
        $this->lastResult = $importer->import($this->importFile->getRealPath(), $filter ?: null, $this->addAssets);
        $this->importFile = null;
        $this->refreshTransactionCount();
        $this->maintenanceMessage = null;
    }

    public function refreshTransactionCount(): void
    {
        $this->transactionCount = Transaction::query()->count();
    }

    public function deleteAllTransactions(): void
    {
        Transaction::query()->delete();
        $this->refreshTransactionCount();
        $this->lastResult = null;
        $this->maintenanceMessage = 'All transaction records were permanently deleted.';
    }

    public function assetIsinOptions(): array
    {
        return Asset::query()
            ->whereNotNull('isin')
            ->where('isin', '!=', '')
            ->orderBy('name')
            ->pluck('isin', 'name')
            ->all();
    }
};
?>

<div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0">Transaction Import</h5>
    </div>

    <div class="card mb-3">
        <div class="card-body d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 py-2">
            <div class="fw-semibold">Transactions: {{ $transactionCount }}</div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="refreshTransactionCount">Refresh</button>
                <button type="button" class="btn btn-outline-danger btn-sm" wire:click="deleteAllTransactions" wire:confirm="All transaction records will be permanently deleted. This cannot be undone. Continue?">Delete all transactions</button>
            </div>
        </div>
    </div>

    <form wire:submit="import" class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-lg-3">
                    <label for="transaction-import-file" class="form-label">Import file</label>
                    <input type="file" id="transaction-import-file" class="form-control @error('importFile') is-invalid @enderror" wire:model="importFile" accept=".csv,.txt,.tsv,text/tab-separated-values,text/plain">
                    @error('importFile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-sm-6 col-lg-2">
                    <label for="transaction-import-mode" class="form-label">Import mode</label>
                    <select id="transaction-import-mode" class="form-select" wire:model.live="importMode">
                        <option value="all">All ISINs</option>
                        <option value="one">One ISIN</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="transaction-selected-isin" class="form-label">Selected ISIN</label>
                    <select id="transaction-selected-isin" class="form-select" wire:model="selectedIsin">
                        <option value="">Choose existing asset ISIN</option>
                        @foreach ($this->assetIsinOptions() as $name => $isin)
                            <option value="{{ $isin }}">{{ $isin }} - {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-2">
                    <label for="transaction-isin-filter" class="form-label">ISIN filter</label>
                    <input type="text" id="transaction-isin-filter" class="form-control" wire:model="isinFilter" placeholder="Manual ISIN">
                    <div class="form-text">No ticker filter is used.</div>
                </div>
                <div class="col-12 col-sm-6 col-lg-2 d-flex align-items-end">
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

    @if ($maintenanceMessage)
        <div class="alert alert-success py-2 small mb-0">
            {{ $maintenanceMessage }}
        </div>
    @endif
</div>
