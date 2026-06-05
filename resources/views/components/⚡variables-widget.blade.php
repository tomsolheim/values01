<?php

use App\Models\Variable;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new class extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $name = '';
    public $value = '';
    public $group = '';
    public $comment = '';
    public $showForm = false;
    public $editId = null;
    public $search = '';
    public $csvImport;

    protected $rules = [
        'name' => 'required|string|max:255',
        'value' => 'required|string',
        'group' => 'required|string|max:255',
        'comment' => 'required|string',
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
        $this->value = '';
        $this->group = '';
        $this->comment = '';
        $this->editId = null;
        $this->resetValidation();
    }

    public function create()
    {
        $this->validate();

        Variable::create($this->payload());

        $this->resetForm();
        $this->showForm = false;
    }

    public function edit($id)
    {
        $variable = Variable::findOrFail($id);

        $this->editId = $variable->id;
        $this->name = $variable->name;
        $this->value = $variable->value;
        $this->group = $variable->group;
        $this->comment = $variable->comment;
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate();

        Variable::findOrFail($this->editId)->update($this->payload());

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Variable::findOrFail($id)->delete();

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

            if (!$data || empty($data['name']) || empty($data['value']) || empty($data['group']) || empty($data['comment'])) {
                continue;
            }

            Variable::updateOrCreate(
                ['name' => $data['name']],
                [
                    'value' => $data['value'],
                    'group' => $data['group'],
                    'comment' => $data['comment'],
                ]
            );
        }

        fclose($handle);
        $this->csvImport = null;
    }

    public function exportCsv()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['name', 'value', 'group', 'comment']);

            Variable::query()->orderBy('name')->each(function (Variable $variable) use ($out) {
                fputcsv($out, [$variable->name, $variable->value, $variable->group, $variable->comment]);
            });

            fclose($out);
        }, 'variables.csv');
    }

    public function getVariablesProperty()
    {
        return Variable::query()
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('value', 'like', "%{$this->search}%")
                        ->orWhere('group', 'like', "%{$this->search}%")
                        ->orWhere('comment', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10);
    }

    private function payload(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'group' => $this->group,
            'comment' => $this->comment,
        ];
    }
};
?>

<div class="card" data-variables-widget>
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0">Variables</h5>
            <button class="btn btn-sm btn-outline-secondary" wire:click="toggleForm" type="button">
                {{ $showForm ? 'Hide form' : 'Show form' }}
            </button>
        </div>

        @if ($showForm)
            <form wire:submit="{{ $editId ? 'update' : 'create' }}" class="mb-3">
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <label for="variable-name" class="form-label">Name</label>
                        <input type="text" id="variable-name" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="variable-value" class="form-label">Value</label>
                        <input type="text" id="variable-value" class="form-control @error('value') is-invalid @enderror" wire:model="value">
                        @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="variable-group" class="form-label">Group</label>
                        <input type="text" id="variable-group" class="form-control @error('group') is-invalid @enderror" wire:model="group">
                        @error('group') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="variable-comment" class="form-label">Comment</label>
                        <input type="text" id="variable-comment" class="form-control @error('comment') is-invalid @enderror" wire:model="comment">
                        @error('comment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">{{ $editId ? 'Update' : 'Create' }}</button>
                    @if ($editId)
                        <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="resetForm">Cancel</button>
                    @endif
                </div>
            </form>
        @endif

        <div class="mb-3">
            <input type="text" class="form-control form-control-sm" placeholder="Search variables..." wire:model.live="search">
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Value</th>
                        <th>Group</th>
                        <th>Comment</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->variables as $variable)
                        <tr>
                            <td>{{ $variable->name }}</td>
                            <td>{{ $variable->value }}</td>
                            <td>{{ $variable->group }}</td>
                            <td class="text-muted">{{ $variable->comment }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $variable->id }})" title="Edit" data-bs-toggle="tooltip">
                                    <i class="bi bi-pen"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $variable->id }})" wire:confirm="Delete this variable?" title="Delete" data-bs-toggle="tooltip">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center">No variables found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->variables->hasPages())
            <div class="mt-2">
                {{ $this->variables->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif

        <div class="d-flex justify-content-end gap-2 mt-2">
            <input type="file" id="variables-csv-import" class="d-none" accept=".csv,text/csv" wire:model="csvImport" wire:change="importCsv">
            <label for="variables-csv-import" class="btn btn-sm btn-outline-secondary mb-0">Import CSV</label>
            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="exportCsv">Export CSV</button>
        </div>
    </div>
</div>
