<?php

use App\Models\Area;
use App\Models\Asset;
use App\Models\Bundle;
use App\Models\Variable;
use App\Services\AssetLookupService;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new class extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $type = '';
    public $isin = '';
    public $ticker = '';
    public $country = '';
    public $name = '';
    public $bundle_id = '';
    public $area_id = '';
    public $comment = '';
    public $showForm = false;
    public $editId = null;
    public $search = '';
    public $csvImport;
    public ?string $lookupMessage = null;
    public ?string $lookupPopupMessage = null;
    public array $lookupChoices = [];

    public array $typeOptions = ['Stock', 'Bank', 'Fund', 'Other'];
    public array $countryOptions = ['NO', 'SE', 'DK', 'DE', 'F', 'ES', 'US', 'UK', 'Other'];

    protected function rules(): array
    {
        return [
            'type' => ['required', Rule::in($this->typeOptions)],
            'isin' => ['nullable', 'string', 'max:255', Rule::unique('assets', 'isin')->ignore($this->editId)],
            'ticker' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', Rule::in($this->countryOptions)],
            'name' => ['required', 'string', 'max:255'],
            'bundle_id' => ['nullable', 'exists:bundles,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'comment' => ['nullable', 'string'],
        ];
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;

        if (!$this->showForm) {
            $this->resetForm();
        }
    }

    public function resetForm()
    {
        $this->type = '';
        $this->isin = '';
        $this->ticker = '';
        $this->country = '';
        $this->name = '';
        $this->bundle_id = '';
        $this->area_id = '';
        $this->comment = '';
        $this->editId = null;
        $this->lookupMessage = null;
        $this->lookupPopupMessage = null;
        $this->lookupChoices = [];
        $this->resetValidation();
    }

    public function create()
    {
        $this->validate();

        Asset::create($this->payload());

        $this->resetForm();
        $this->showForm = false;
    }

    public function edit($id)
    {
        $asset = Asset::findOrFail($id);

        $this->editId = $asset->id;
        $this->type = $asset->type;
        $this->isin = $asset->isin ?? '';
        $this->ticker = $asset->ticker ?? '';
        $this->country = $asset->country ?? '';
        $this->name = $asset->name;
        $this->bundle_id = $asset->bundle_id ? (string) $asset->bundle_id : '';
        $this->area_id = $asset->area_id ? (string) $asset->area_id : '';
        $this->comment = $asset->comment ?? '';
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate();

        Asset::findOrFail($this->editId)->update($this->payload());

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Asset::findOrFail($id)->delete();

        if ($this->editId === (int) $id) {
            $this->resetForm();
        }
    }

    public function lookupIsin(AssetLookupService $lookup)
    {
        $this->lookupMessage = null;
        $this->lookupPopupMessage = null;
        $this->lookupChoices = [];

        if (blank($this->isin)) {
            $this->lookupMessage = 'Enter an ISIN before lookup.';
            return;
        }

        $counter = Variable::query()->where('name', 'isin_counter')->first();

        if (!$counter || !is_numeric($counter->value)) {
            $this->lookupMessage = 'ISIN lookup counter is not configured.';
            return;
        }

        if ((int) $counter->value <= 0) {
            $this->lookupPopupMessage = 'todays lookup quota is used';
            return;
        }

        if (!config('services.eodhd.token')) {
            $this->lookupMessage = 'ISIN lookup needs EODHD_API_TOKEN to be configured.';
            return;
        }

        $counter->update(['value' => (string) ((int) $counter->value - 1)]);

        $result = $lookup->search($this->isin);

        if ($result['status'] === 'error') {
            $this->lookupMessage = 'ISIN lookup failed. Try again later.';
            return;
        }

        if ($result['status'] === 'not-found') {
            $this->lookupMessage = 'No match found.';
            return;
        }

        $choice = collect($result['results'])->firstWhere('isPrimary', true)
            ?? collect($result['results'])->firstWhere('country', $this->country);

        if (!$choice && count($result['results']) > 1) {
            $this->lookupChoices = $result['results'];
            $this->lookupMessage = 'Choose a lookup result.';
            return;
        }

        $this->applyLookupChoice($choice ?? $result['results'][0]);
    }

    public function useLookupChoice(int $index)
    {
        if (!isset($this->lookupChoices[$index])) {
            return;
        }

        $this->applyLookupChoice($this->lookupChoices[$index]);
        $this->lookupChoices = [];
    }

    private function applyLookupChoice(array $choice): void
    {
        $this->ticker = $this->ticker ?: ($choice['ticker'] ?? '');
        $this->country = $this->country ?: ($choice['country'] ?? '');
        $this->name = $this->name ?: ($choice['name'] ?? '');
        $this->type = $this->type ?: ($choice['type'] ?? '');

        if (!$this->area_id && ($choice['country'] ?? null)) {
            $this->area_id = Area::query()->where('name', $choice['country'])->value('id')
                ?: Area::query()->where('name', 'Unknown')->value('id')
                ?: '';
        }

        $this->lookupMessage = 'Lookup values applied. Save to persist changes.';
    }

    public function importCsv()
    {
        $this->validate(['csvImport' => 'required|file|mimes:csv,txt']);

        $handle = fopen($this->csvImport->getRealPath(), 'r');
        $headers = fgetcsv($handle) ?: [];
        $headers = array_map(fn($header) => strtolower(trim($header)), $headers);

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);

            if (!$data || empty($data['type']) || empty($data['name'])) {
                continue;
            }

            $payload = [
                'type' => $data['type'],
                'isin' => $this->nullIfEmpty($data['isin'] ?? null),
                'ticker' => $this->nullIfEmpty(strtoupper($data['ticker'] ?? '')),
                'country' => $this->nullIfEmpty($data['country'] ?? null),
                'name' => $data['name'],
                'bundle_id' => $this->lookupId(Bundle::class, $data['bundle'] ?? null),
                'area_id' => $this->lookupId(Area::class, $data['area'] ?? null),
                'comment' => $this->nullIfEmpty($data['comment'] ?? null),
            ];

            $payload['isin']
                ? Asset::updateOrCreate(['isin' => $payload['isin']], $payload)
                : Asset::create($payload);
        }

        fclose($handle);
        $this->csvImport = null;
    }

    public function exportCsv()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['type', 'isin', 'ticker', 'country', 'name', 'bundle', 'area', 'comment']);

            Asset::with(['bundle', 'area'])->orderBy('name')->each(function (Asset $asset) use ($out) {
                fputcsv($out, [
                    $asset->type,
                    $asset->isin,
                    $asset->ticker,
                    $asset->country,
                    $asset->name,
                    $asset->bundle?->name,
                    $asset->area?->name,
                    $asset->comment,
                ]);
            });

            fclose($out);
        }, 'assets.csv');
    }

    public function getAssetsProperty()
    {
        return Asset::query()
            ->with(['bundle', 'area'])
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('type', 'like', "%{$this->search}%")
                        ->orWhere('isin', 'like', "%{$this->search}%")
                        ->orWhere('ticker', 'like', "%{$this->search}%")
                        ->orWhere('country', 'like', "%{$this->search}%")
                        ->orWhere('name', 'like', "%{$this->search}%")
                        ->orWhere('comment', 'like', "%{$this->search}%")
                        ->orWhereHas('bundle', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('area', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->orderBy('name')
            ->paginate(10);
    }

    public function getBundlesProperty()
    {
        return Bundle::query()->orderBy('name')->get();
    }

    public function getAreasProperty()
    {
        return Area::query()->orderBy('name')->get();
    }

    private function payload(): array
    {
        return [
            'type' => $this->type,
            'isin' => $this->nullIfEmpty($this->isin),
            'ticker' => $this->nullIfEmpty(strtoupper($this->ticker)),
            'country' => $this->nullIfEmpty($this->country),
            'name' => $this->name,
            'bundle_id' => $this->nullIfEmpty($this->bundle_id),
            'area_id' => $this->nullIfEmpty($this->area_id),
            'comment' => $this->nullIfEmpty($this->comment),
        ];
    }

    private function nullIfEmpty($value)
    {
        return blank($value) ? null : $value;
    }

    private function lookupId(string $model, $name)
    {
        if (blank($name)) {
            return null;
        }

        return $model::query()->where('name', $name)->value('id');
    }
};
?>

<div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0">Assets</h5>
        <button class="btn btn-sm btn-outline-secondary" wire:click="toggleForm" type="button">
            {{ $showForm ? 'Hide form' : 'Show form' }}
        </button>
    </div>

    @if ($showForm)
        <form wire:submit="{{ $editId ? 'update' : 'create' }}" class="mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-3">
                            <label for="asset-type" class="form-label">Type</label>
                            <select id="asset-type" class="form-select @error('type') is-invalid @enderror" wire:model="type">
                                <option value="">Select type</option>
                                @foreach ($typeOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="asset-isin" class="form-label">ISIN</label>
                            <input type="text" id="asset-isin" class="form-control @error('isin') is-invalid @enderror" wire:model="isin">
                            @error('isin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="asset-ticker" class="form-label">Tic</label>
                            <input type="text" id="asset-ticker" class="form-control @error('ticker') is-invalid @enderror" wire:model="ticker">
                            @error('ticker') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="asset-country" class="form-label">Country</label>
                            <select id="asset-country" class="form-select @error('country') is-invalid @enderror" wire:model="country">
                                <option value="">Select country</option>
                                @foreach ($countryOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="asset-name" class="form-label">Name</label>
                            <input type="text" id="asset-name" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="asset-bundle" class="form-label">Bundle</label>
                            <select id="asset-bundle" class="form-select @error('bundle_id') is-invalid @enderror" wire:model="bundle_id">
                                <option value="">No bundle</option>
                                @foreach ($this->bundles as $bundle)
                                    <option value="{{ $bundle->id }}">{{ $bundle->name }}</option>
                                @endforeach
                            </select>
                            @error('bundle_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="asset-area" class="form-label">Area</label>
                            <select id="asset-area" class="form-select @error('area_id') is-invalid @enderror" wire:model="area_id">
                                <option value="">No area</option>
                                @foreach ($this->areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                            @error('area_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label for="asset-comment" class="form-label">Comment</label>
                            <textarea id="asset-comment" class="form-control @error('comment') is-invalid @enderror" wire:model="comment" rows="3"></textarea>
                            @error('comment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">{{ $editId ? 'Update' : 'Create' }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="lookupIsin" @disabled(blank($isin))>Lookup ISIN</button>
                        @if ($editId)
                            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="resetForm">Cancel</button>
                        @endif
                    </div>

                    @if ($lookupMessage)
                        <div class="alert alert-info py-2 mt-3 mb-0 small">{{ $lookupMessage }}</div>
                    @endif

                    @if ($lookupPopupMessage)
                        <div class="alert alert-warning py-2 mt-3 mb-0 small" data-popup-message>{{ $lookupPopupMessage }}</div>
                    @endif

                    @if ($lookupChoices)
                        <div class="mt-3 border rounded p-2" data-isin-lookup-choices>
                            <div class="small text-muted mb-2">Lookup choices</div>
                            @foreach ($lookupChoices as $index => $choice)
                                <button type="button" class="btn btn-sm btn-outline-secondary me-2 mb-2" wire:click="useLookupChoice({{ $index }})">
                                    {{ $choice['name'] ?? 'Unknown' }} {{ $choice['ticker'] ? '('.$choice['ticker'].')' : '' }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </form>
    @endif

    <div class="mb-3">
        <input type="text" class="form-control form-control-sm" placeholder="Search assets..." wire:model.live="search">
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>ISIN</th>
                    <th>Tic</th>
                    <th>Country</th>
                    <th>Name</th>
                    <th>Bundle</th>
                    <th>Area</th>
                    <th>Comment</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->assets as $asset)
                    <tr>
                        <td>{{ $asset->type }}</td>
                        <td>{{ $asset->isin }}</td>
                        <td>{{ $asset->ticker }}</td>
                        <td>{{ $asset->country }}</td>
                        <td>{{ $asset->name }}</td>
                        <td>{{ $asset->bundle?->name }}</td>
                        <td>{{ $asset->area?->name }}</td>
                        <td class="text-muted">{{ $asset->comment }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $asset->id }})" title="Edit" data-bs-toggle="tooltip">
                                <i class="bi bi-pen"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $asset->id }})" wire:confirm="Delete this asset?" title="Delete" data-bs-toggle="tooltip">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-muted text-center">No assets found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($this->assets->hasPages())
        <div class="mt-2">
            {{ $this->assets->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif

    <div class="d-flex justify-content-end gap-2 mt-2">
        <input type="file" id="asset-csv-import" class="d-none" accept=".csv,text/csv" wire:model="csvImport" wire:change="importCsv">
        <label for="asset-csv-import" class="btn btn-sm btn-outline-secondary mb-0">Import CSV</label>
        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="exportCsv">Export CSV</button>
    </div>
</div>
