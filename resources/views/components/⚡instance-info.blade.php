<?php

use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        $hostname = gethostname() ?: 'Unknown';
        $ipAddress = gethostbyname($hostname);

        if (!$ipAddress || $ipAddress === $hostname || str_starts_with($ipAddress, '127.')) {
            $ipAddress = 'Unknown';
        }

        return [
            'hostname' => $hostname,
            'ipAddress' => $ipAddress,
            'projectName' => config('app.name', 'Values01'),
        ];
    }
};
?>

<div class="card h-100" data-card-toggle="top09">
    <div class="card-body">
        <h6 class="card-title text-secondary">Instance Info</h6>
        <div class="d-flex justify-content-between gap-3 small">
            <span class="text-muted">Project</span>
            <span class="fw-semibold text-end">{{ $projectName }}</span>
        </div>
        <div class="d-flex justify-content-between gap-3 small mt-2">
            <span class="text-muted">Hostname</span>
            <span class="fw-semibold text-end">{{ $hostname }}</span>
        </div>
        <div class="d-flex justify-content-between gap-3 small mt-2">
            <span class="text-muted">IP Address</span>
            <span class="fw-semibold text-end">{{ $ipAddress }}</span>
        </div>
    </div>
</div>
