<?php

use App\Models\AuditLog;
use Livewire\Component;

new class extends Component {
    public array $timelineData;

    public function mount(): void
    {
        $this->timelineData = AuditLog::timelineData();
    }
};
?>

<div class="h-[400px] w-full">
    <canvas id="timelineChart" wire:ignore></canvas>
</div>

@script
<script>
    const data = @json($timelineData);

    const styles = getComputedStyle(document.documentElement);
    const colors = {
        info: styles.getPropertyValue('--color-verity-muted').trim(),
        warning: styles.getPropertyValue('--color-verity-alert').trim(),
        critical: styles.getPropertyValue('--color-verity-critical').trim(),
        fatal: styles.getPropertyValue('--color-verity-fatal').trim(),
    };

    const ctx = document.getElementById('timelineChart');

    if (ctx.chart) {
        ctx.chart.destroy();
    }

    ctx.chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Info',
                    data: data.datasets.info,
                    backgroundColor: colors.info,
                },
                {
                    label: 'Warning',
                    data: data.datasets.warning,
                    backgroundColor: colors.warning,
                },
                {
                    label: 'Critical',
                    data: data.datasets.critical,
                    backgroundColor: colors.critical,
                },
                {
                    label: 'Fatal',
                    data: data.datasets.fatal,
                    backgroundColor: colors.fatal,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { stacked: true },
                y: { stacked: true },
            },
        },
    });
</script>
@endscript