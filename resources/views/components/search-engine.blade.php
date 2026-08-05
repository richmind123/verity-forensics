<?php

use App\Models\AuditLog;
use App\Services\BruteForceDetector;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\RiskScoreCalculator;

new class extends Component {
    use WithPagination;

    #[Url]
    public string $ipAddress = '';

    #[Url]
    public string $severity = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public bool $isDirty = false;

    public ?int $selectedLogId = null;

    public function updated($property): void
    {
        $this->isDirty = true;
    }

    public function runScan(): void
    {
        $this->isDirty = false;
        $this->resetPage();
    }

    public function selectLog(int $id): void
    {
        $this->selectedLogId = $id;
    }

    public function closeInspector(): void
    {
        $this->selectedLogId = null;
    }

    public function with(): array
    {
        $logs = AuditLog::query()
            ->when($this->ipAddress, fn($q) => $q->where('ip_address', 'like', "%{$this->ipAddress}%"))
            ->when($this->severity, fn($q) => $q->where('severity', $this->severity))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderByDesc('created_at')
            ->paginate(15);

        $selectedLog = $this->selectedLogId
            ? AuditLog::find($this->selectedLogId)
            : null;

        $bruteForceFlags = BruteForceDetector::detect(threshold: 50);

        $riskScores = RiskScoreCalculator::calculate()->take(5);

        return [
            'logs' => $logs,
            'selectedLog' => $selectedLog,
            'bruteForceFlags' => $bruteForceFlags,
            'riskScores' => $riskScores,
        ];
    }
};
?>

<div>
    @if ($bruteForceFlags->isNotEmpty())
        <div class="mb-4 p-3 bg-red-900/30 border border-red-700 rounded-lg">
            <p class="text-red-400 font-medium text-sm">⚠ Brute Force Activity Detected</p>
            <div class="mt-1 space-y-1">
                @foreach ($bruteForceFlags as $flag)
                    <button wire:click="$set('ipAddress', '{{ $flag->ip_address }}')" class="text-sm text-red-300 hover:underline block">
                        {{ $flag->ip_address }} — {{ $flag->attempts }} failed logins in a 5-minute window
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    @if ($riskScores->isNotEmpty())
        <div class="mb-4 p-3 bg-gray-800/50 border border-gray-700 rounded-lg">
            <p class="font-medium text-sm mb-2">Top Risk Scores</p>
            <div class="space-y-1">
                @foreach ($riskScores as $risk)
                    <button wire:click="$set('ipAddress', '{{ $risk->ip_address }}')" class="w-full flex justify-between items-center text-sm hover:bg-white/5 px-2 py-1 rounded">
                        <span>{{ $risk->ip_address }}</span>
                        <span class="flex items-center gap-2">
                            <span class="w-24 h-2 bg-gray-700 rounded-full overflow-hidden">
                                <span class="h-full block {{ $risk->score >= 80 ? 'bg-red-500' : ($risk->score >= 50 ? 'bg-orange-500' : 'bg-gray-500') }}" style="width: {{ $risk->score }}%"></span>
                            </span>
                            <span class="w-8 text-right">{{ $risk->score }}</span>
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
    @endif
    <div class="flex flex-wrap gap-4 items-end mb-4">
        <div>
            <label class="block text-sm font-medium mb-1">IP Address</label>
            <input type="text" wire:model.blur="ipAddress" placeholder="e.g. 185.220.101.47" class="border rounded px-3 py-2 bg-transparent">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Severity</label>
            <select wire:model.blur="severity" class="border rounded px-3 py-2 bg-transparent text-white [&>option]:bg-gray-800 [&>option]:text-white">
                <option value="">All</option>
                <option value="info">Info</option>
                <option value="warning">Warning</option>
                <option value="critical">Critical</option>
                <option value="fatal">Fatal</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">From</label>
            <input type="date" wire:model.blur="dateFrom" class="border rounded px-3 py-2 bg-transparent">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">To</label>
            <input type="date" wire:model.blur="dateTo" class="border rounded px-3 py-2 bg-transparent">
        </div>

        <flux:button wire:click="runScan" variant="primary" :class="$isDirty ? 'animate-pulse ring-2 ring-blue-400' : ''">
            {{ $isDirty ? 'Run Scan (filters changed)' : 'Run Scan' }}
        </flux:button>
    </div>

    <table class="w-full text-sm">
        <thead>
            <tr class="text-left border-b">
                <th class="p-2">Timestamp</th>
                <th class="p-2">Event</th>
                <th class="p-2">IP Address</th>
                <th class="p-2">Severity</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr wire:click="selectLog({{ $log->id }})" class="border-b hover:bg-white/5 cursor-pointer">
                    <td class="p-2">{{ $log->created_at }}</td>
                    <td class="p-2">{{ $log->event }}</td>
                    <td class="p-2">{{ $log->ip_address }}</td>
                    <td class="p-2">{{ $log->severity->value }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-400">No matching logs.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>

    @if ($selectedLog)
        <div class="fixed inset-0 bg-black/50 z-40" wire:click="closeInspector"></div>

        <div class="fixed top-0 right-0 h-full w-full max-w-md bg-gray-900 border-l border-gray-700 z-50 overflow-y-auto p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Forensic Inspector</h2>
                <button wire:click="closeInspector" class="text-gray-400 hover:text-white cursor-pointer">
                    ✕
                </button>
            </div>

            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-gray-400">Timestamp</span>
                    <p>{{ $selectedLog->created_at }}</p>
                </div>
                <div>
                    <span class="text-gray-400">Event</span>
                    <p>{{ $selectedLog->event }}</p>
                </div>
                <div>
                    <span class="text-gray-400">IP Address</span>
                    <p>{{ $selectedLog->ip_address }}</p>
                </div>
                <div>
                    <span class="text-gray-400">Severity</span>
                    <p>{{ $selectedLog->severity->value }}</p>
                </div>

                <div>
                    <span class="text-gray-400">Payload</span>
                    @if (empty($selectedLog->payload))
                        <p class="text-gray-500 italic">No additional data.</p>
                    @else
                        <div class="mt-1 space-y-1">
                            @foreach ($selectedLog->payload as $key => $value)
                                <div class="flex justify-between border-b border-gray-800 py-1">
                                    <span class="text-gray-400">{{ $key }}</span>
                                    <span>{{ is_null($value) ? '—' : $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>