<?php

use App\Models\Link;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new class extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $name = '';
    public string $group = '';
    public string $url = '';
    public string $comment = '';
    public bool $showForm = false;
    public $editId = null;
    public string $search = '';
    public string $groupFilter = '';
    public $csvImport;

    protected array $rules = [
        'name' => 'required|string|max:255',
        'group' => 'nullable|string|max:255',
        'url' => ['required', 'url', 'regex:/^https?:\/\//i'],
        'comment' => 'nullable|string',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedGroupFilter(): void
    {
        $this->resetPage();
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;

        if (!$this->showForm) {
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->group = '';
        $this->url = '';
        $this->comment = '';
        $this->editId = null;
        $this->resetValidation();
    }

    public function create(): void
    {
        $this->validate();

        Link::create($this->payload());

        $this->resetForm();
        $this->showForm = false;
    }

    public function edit($id): void
    {
        $link = Link::findOrFail($id);
        $this->editId = $link->id;
        $this->name = $link->name;
        $this->group = (string) $link->group;
        $this->url = $link->url;
        $this->comment = (string) $link->comment;
        $this->showForm = true;
    }

    public function update(): void
    {
        $this->validate();

        Link::findOrFail($this->editId)->update($this->payload());

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id): void
    {
        Link::findOrFail($id)->delete();

        if ($this->editId === (int) $id) {
            $this->resetForm();
        }
    }

    public function resetSearch(): void
    {
        $this->search = '';
        $this->groupFilter = '';
        $this->resetPage();
    }

    public function importCsv(): void
    {
        $this->validate(['csvImport' => 'required|file|mimes:csv,txt']);

        $handle = fopen($this->csvImport->getRealPath(), 'r');
        $headers = fgetcsv($handle) ?: [];
        $headers = array_map(fn($header) => strtolower(trim($header)), $headers);

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, array_pad($row, count($headers), null));

            if (!$data || empty($data['name']) || empty($data['url']) || !$this->isSupportedUrl($data['url'])) {
                continue;
            }

            Link::updateOrCreate(
                ['name' => $data['name']],
                [
                    'group' => blank($data['group'] ?? null) ? null : $data['group'],
                    'url' => $data['url'],
                    'comment' => blank($data['comment'] ?? null) ? null : $data['comment'],
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
            fputcsv($out, ['name', 'group', 'url', 'comment']);

            Link::query()->orderBy('name')->each(function (Link $link) use ($out) {
                fputcsv($out, [$link->name, $link->group, $link->url, $link->comment]);
            });

            fclose($out);
        }, 'links.csv');
    }

    public function getLinksProperty()
    {
        return Link::query()
            ->when($this->search, function ($query) {
                $search = $this->search;

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('group', 'like', "%{$search}%")
                        ->orWhere('url', 'like', "%{$search}%")
                        ->orWhere('comment', 'like', "%{$search}%");
                });
            })
            ->when($this->groupFilter, fn($query) => $query->where('group', $this->groupFilter))
            ->orderBy('name')
            ->paginate(10);
    }

    public function getGroupOptionsProperty(): array
    {
        return Link::query()
            ->whereNotNull('group')
            ->where('group', '!=', '')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->all();
    }

    private function payload(): array
    {
        return [
            'name' => $this->name,
            'group' => blank($this->group) ? null : $this->group,
            'url' => $this->url,
            'comment' => blank($this->comment) ? null : $this->comment,
        ];
    }

    private function isSupportedUrl(?string $url): bool
    {
        return is_string($url) && filter_var($url, FILTER_VALIDATE_URL) && preg_match('/^https?:\/\//i', $url);
    }
};
?>

<div data-links-widget>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0">Links</h5>
        <button class="btn btn-sm btn-outline-secondary" wire:click="toggleForm" type="button">
            {{ $showForm ? 'Hide form' : 'Show form' }}
        </button>
    </div>

    @if ($showForm)
        <form wire:submit="{{ $editId ? 'update' : 'create' }}" class="mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="link-name" class="form-label">Name</label>
                            <input type="text" id="link-name" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="link-group" class="form-label">Group</label>
                            <input type="text" id="link-group" class="form-control @error('group') is-invalid @enderror" wire:model="group">
                            @error('group') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label for="link-url" class="form-label">URL</label>
                            <input type="url" id="link-url" class="form-control @error('url') is-invalid @enderror" wire:model="url" placeholder="https://example.com">
                            @error('url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label for="link-comment" class="form-label">Comment</label>
                            <textarea id="link-comment" class="form-control @error('comment') is-invalid @enderror" wire:model="comment" rows="3"></textarea>
                            @error('comment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">{{ $editId ? 'Update' : 'Create' }}</button>
                        @if ($editId)
                            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="resetForm">Cancel</button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    @endif

    <div class="row g-2 align-items-end mb-3">
        <div class="col-12 col-md-7">
            <label for="links-search" class="form-label small mb-1">Search links</label>
            <input type="text" id="links-search" class="form-control form-control-sm" placeholder="Search links..." wire:model.live="search">
        </div>
        <div class="col-12 col-md-3">
            <label for="links-group-filter" class="form-label small mb-1">Group filter</label>
            <select id="links-group-filter" class="form-select form-select-sm" wire:model.live="groupFilter">
                <option value="">All groups</option>
                @foreach ($this->groupOptions as $groupOption)
                    <option value="{{ $groupOption }}">{{ $groupOption }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2">
            <button type="button" class="btn btn-sm btn-outline-secondary w-100" wire:click="resetSearch">Reset Search</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Group</th>
                    <th>URL</th>
                    <th>Comment</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->links as $link)
                    <tr>
                        <td>{{ $link->name }}</td>
                        <td class="text-muted">{{ $link->group }}</td>
                        <td class="text-break" style="max-width: 18rem;">
                            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer">{{ $link->url }}</a>
                        </td>
                        <td class="text-muted">{{ $link->comment }}</td>
                        <td class="text-end text-nowrap">
                            <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $link->id }})" title="Edit" data-bs-toggle="tooltip" aria-label="Edit link">
                                <i class="bi bi-pen"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $link->id }})" wire:confirm="Delete this link?" title="Delete" data-bs-toggle="tooltip" aria-label="Delete link">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted text-center">No links found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($this->links->hasPages())
        <div class="mt-2">
            {{ $this->links->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif

    <div class="d-flex justify-content-end gap-2 mt-2">
        <input type="file" id="links-csv-import" class="d-none" accept=".csv,text/csv" wire:model="csvImport" wire:change="importCsv">
        <label for="links-csv-import" class="btn btn-sm btn-outline-secondary mb-0">Import CSV</label>
        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="exportCsv">Export CSV</button>
    </div>
</div>
