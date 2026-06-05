<?php

use App\Services\SystemStatusVariables;
use Carbon\Carbon;
use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        return ['stats' => $this->stats()];
    }

    private function command(string $command): string
    {
        return function_exists('shell_exec') ? trim((string) shell_exec($command.' 2>/dev/null')) : '';
    }

    private function cpuCount(): int
    {
        $cpuCount = PHP_OS_FAMILY === 'Darwin'
            ? (int) $this->command('sysctl -n hw.ncpu')
            : (int) $this->command('nproc');

        return $cpuCount > 0 ? $cpuCount : 1;
    }

    private function memory(): array
    {
        if (PHP_OS_FAMILY === 'Darwin') {
            $total = (int) $this->command('sysctl -n hw.memsize');
            return ['total' => $total, 'free' => 0];
        }

        if (is_readable('/proc/meminfo')) {
            $meminfo = file_get_contents('/proc/meminfo');
            preg_match('/MemTotal:\s+(\d+) kB/', $meminfo, $total);
            preg_match('/MemAvailable:\s+(\d+) kB/', $meminfo, $free);

            return [
                'total' => isset($total[1]) ? (int) $total[1] * 1024 : 0,
                'free' => isset($free[1]) ? (int) $free[1] * 1024 : 0,
            ];
        }

        return ['total' => 0, 'free' => 0];
    }

    private function bootTime(): string
    {
        if (PHP_OS_FAMILY === 'Darwin') {
            $bootTime = $this->command('sysctl -n kern.boottime');

            if (preg_match('/sec = (\d+)/', $bootTime, $matches)) {
                return Carbon::createFromTimestamp((int) $matches[1])->format('Y-m-d H:i:s');
            }

            return 'Unknown';
        }

        return $this->command('uptime -s') ?: 'Unknown';
    }

    private function formatBytes(int|float $bytes, string $unit): string
    {
        $divisor = $unit === 'GB' ? 1073741824 : 1048576;

        return number_format($bytes / $divisor, 2).' '.$unit;
    }

    private function stats(): array
    {
        $cpuCount = $this->cpuCount();
        $reserved = app(SystemStatusVariables::class)->vmwareCores();
        $available = max(1, $cpuCount - $reserved);
        $load = sys_getloadavg();
        $memory = $this->memory();
        $diskTotal = disk_total_space('/') ?: 0;
        $diskFree = disk_free_space('/') ?: 0;
        $diskUsed = max(0, $diskTotal - $diskFree);

        return [
            'cpus' => $cpuCount.' host / '.$available.' available',
            'reserved' => $reserved.' reserved',
            'cpu_load' => is_array($load) ? round(($load[0] / $available) * 100).' %' : 'N/A',
            'memory' => $this->formatBytes($memory['total'], 'MB'),
            'free_memory' => $memory['free'] > 0 ? $this->formatBytes($memory['free'], 'MB') : 'Unknown',
            'disk' => $this->formatBytes($diskTotal, 'GB'),
            'free_disk' => $this->formatBytes($diskFree, 'GB'),
            'used_disk' => $this->formatBytes($diskUsed, 'GB'),
            'last_boot' => $this->bootTime(),
        ];
    }
};
?>

<div class="card mb-3" data-card-toggle="system-status" wire:poll.30s>
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between gap-2 py-2" data-utility-card-header>
        <h6 class="mb-0 fw-semibold small"><i class="bi bi-server me-1"></i>System Status</h6>
        <button class="btn btn-sm btn-outline-secondary py-0 px-1" wire:click="$refresh" title="Refresh system status">
            <i class="bi bi-arrow-repeat"></i>
        </button>
    </div>
    <div class="card-body">
        @foreach ([
            'CPUs' => $stats['cpus'],
            'VM CPUs' => $stats['reserved'],
            'CPU Load' => $stats['cpu_load'],
            'Memory' => $stats['memory'],
            'Free Memory' => $stats['free_memory'],
            'Disk' => $stats['disk'],
            'Free Disk' => $stats['free_disk'],
            'Used Disk' => $stats['used_disk'],
            'Last Boot' => $stats['last_boot'],
        ] as $label => $value)
            <div class="d-flex justify-content-between gap-3 small mt-2">
                <span class="text-muted">{{ $label }}</span>
                <span class="fw-semibold text-end">{{ $value }}</span>
            </div>
        @endforeach
    </div>
</div>
