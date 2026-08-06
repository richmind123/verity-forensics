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
                    backgroundColor: '#6b7280',
                },
                {
                    label: 'Warning',
                    data: data.datasets.warning,
                    backgroundColor: '#f59e0b',
                },
                {
                    label: 'Critical',
                    data: data.datasets.critical,
                    backgroundColor: '#ea580c',
                },
                {
                    label: 'Fatal',
                    data: data.datasets.fatal,
                    backgroundColor: '#dc2626',
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