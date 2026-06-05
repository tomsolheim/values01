<?php

use App\Models\Bundle;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new class extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $name = '';
    public $comment = '';
    public $showForm = false;
    public $editId = null;
    public $search = '';
    public $csvImport;

    protected $rules = [
        'name' => 'required|string|max:255',
        'comment' => 'nullable|string',
    ];

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;

        if (!$this->showForm) {
            $this->resetForm();
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->comment = '';
        $this->editId = null;
        $this->resetValidation();
    }

    public function create()
    {
        $this->validate();

        Bundle::create([
            'name' => $this->name,
            'comment' => $this->comment,
        ]);

        $this->resetForm();
        $this->showForm = false;
    }

    public function edit($id)
    {
        $bundle = Bundle::findOrFail($id);
        $this->editId = $bundle->id;
        $this->name = $bundle->name;
        $this->comment = $bundle->comment;
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate();

        $bundle = Bundle::findOrFail($this->editId);
        $bundle->update([
            'name' => $this->name,
            'comment' => $this->comment,
        ]);

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Bundle::findOrFail($id)->delete();

        if ($this->editId === (int) $id) {
            $this->resetForm();
        }
    }

    public function importCsv()
    {
        $this->validate(['csvImport' => 'required|file|mimes:csv,txt']);

        $handle = fopen($this->csvImport->getRealPath(), 'r');
        $headers = fgetcsv($handle) ?: [];
        $headers = array_map(fn($header) => strtolower(trim($header)), $headers);

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);

            if (!$data || empty($data['name'])) {
                continue;
            }

            Bundle::updateOrCreate(
                ['name' => $data['name']],
                ['comment' => blank($data['comment'] ?? null) ? null : $data['comment']]
            );
        }

        fclose($handle);
        $this->csvImport = null;
    }

    public function exportCsv()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['name', 'comment']);

            Bundle::query()->orderBy('name')->each(function (Bundle $bundle) use ($out) {
                fputcsv($out, [$bundle->name, $bundle->comment]);
            });

            fclose($out);
        }, 'bundles.csv');
    }

    public function getBundlesProperty()
    {
        return Bundle::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('comment', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(10);
    }
};
?>

<div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0">Bundles</h5>
        <button class="btn btn-sm btn-outline-secondary" wire:click="toggleForm" type="button">
            {{ $showForm ? 'Hide form' : 'Show form' }}
        </button>
    </div>

    @if ($showForm)
        <form wire:submit="{{ $editId ? 'update' : 'create' }}" class="mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="bundle-name" class="form-label">Name</label>
                        <input type="text" id="bundle-name" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="bundle-comment" class="form-label">Comment</label>
                        <textarea id="bundle-comment" class="form-control @error('comment') is-invalid @enderror" wire:model="comment" rows="3"></textarea>
                        @error('comment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        {{ $editId ? 'Update' : 'Create' }}
                    </button>
                </div>
            </div>
        </form>
    @endif

    <div class="mb-3">
        <input type="text" class="form-control form-control-sm" placeholder="Search bundles..." wire:model.live="search">
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Comment</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->bundles as $bundle)
                    <tr>
                        <td>{{ $bundle->name }}</td>
                        <td class="text-muted">{{ $bundle->comment }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $bundle->id }})" title="Edit" data-bs-toggle="tooltip">
                                <i class="bi bi-pen"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $bundle->id }})" wire:confirm="Delete this bundle?" title="Delete" data-bs-toggle="tooltip">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-muted text-center">No bundles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($this->bundles->hasPages())
        <div class="mt-2">
            {{ $this->bundles->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif

    <div class="d-flex justify-content-end gap-2 mt-2">
        <input type="file" id="bundle-csv-import" class="d-none" accept=".csv,text/csv" wire:model="csvImport" wire:change="importCsv">
        <label for="bundle-csv-import" class="btn btn-sm btn-outline-secondary mb-0">Import CSV</label>
        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="exportCsv">Export CSV</button>
    </div>
</div>
