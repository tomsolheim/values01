<?php

use Livewire\Component;

new class extends Component
{
    private function git(string $arguments): string
    {
        return trim((string) shell_exec('git -C '.escapeshellarg(base_path())." {$arguments} 2>/dev/null"));
    }

    public function refreshRemote(): void
    {
        $upstream = $this->git('rev-parse --abbrev-ref --symbolic-full-name @{u}');
        $remoteName = $upstream ? strtok($upstream, '/') : '';

        if ($remoteName) {
            $this->git('fetch --quiet --prune '.escapeshellarg($remoteName));
        }
    }

    public function with(): array
    {
        $upstream = $this->git('rev-parse --abbrev-ref --symbolic-full-name @{u}');
        $remoteName = $upstream ? strtok($upstream, '/') : '';
        $counts = $upstream ? preg_split('/\s+/', $this->git('rev-list --left-right --count HEAD...@{u}')) : [];

        return [
            'commitHash' => $this->git('rev-parse --short=6 HEAD') ?: 'N/A',
            'commitDate' => $this->git("log -1 --format='%cd' --date=format:'%Y-%m-%d %H:%M'") ?: 'Unknown',
            'changes' => count(array_filter(explode("\n", $this->git('status --porcelain')))),
            'branch' => $this->git('branch --show-current') ?: 'Unknown',
            'upstream' => $upstream ?: 'No upstream',
            'remoteCommit' => $upstream ? ($this->git('rev-parse --short=6 '.escapeshellarg($upstream)) ?: 'N/A') : 'N/A',
            'ahead' => isset($counts[0]) && is_numeric($counts[0]) ? (int) $counts[0] : 0,
            'behind' => isset($counts[1]) && is_numeric($counts[1]) ? (int) $counts[1] : 0,
        ];
    }
};
?>

<div class="card mb-3" data-card-toggle="git-status" wire:poll.30s>
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="card-title text-secondary mb-0">Git Status</h6>
            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1" wire:click="refreshRemote" title="Refresh Git status">
                <i class="bi bi-cloud-arrow-down"></i>
            </button>
        </div>
        <div class="d-flex justify-content-between gap-3 small">
            <span class="text-muted">Current commit hash</span>
            <span class="fw-semibold text-end font-monospace">{{ $commitHash }}</span>
        </div>
        <div class="d-flex justify-content-between gap-3 small mt-2">
            <span class="text-muted">Last commit date</span>
            <span class="fw-semibold text-end">{{ $commitDate }}</span>
        </div>
        <div class="d-flex justify-content-between gap-3 small mt-2">
            <span class="text-muted">Local pending changes</span>
            <span class="fw-semibold text-end">{{ $changes }}</span>
        </div>
        <div class="d-flex justify-content-between gap-3 small mt-2">
            <span class="text-muted">Branch</span>
            <span class="fw-semibold text-end">{{ $branch }}</span>
        </div>
        <div class="d-flex justify-content-between gap-3 small mt-2">
            <span class="text-muted">Upstream</span>
            <span class="fw-semibold text-end">{{ $upstream }}</span>
        </div>
        <div class="d-flex justify-content-between gap-3 small mt-2">
            <span class="text-muted">Remote commit</span>
            <span class="fw-semibold text-end font-monospace">{{ $remoteCommit }}</span>
        </div>
        <div class="d-flex justify-content-between gap-3 small mt-2">
            <span class="text-muted">Ahead/behind sync status</span>
            <span class="fw-semibold text-end">ahead {{ $ahead }} / behind {{ $behind }}</span>
        </div>
    </div>
</div>
