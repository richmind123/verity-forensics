<?php

use App\Jobs\ProcessEvidenceFileJob;
use App\Models\EvidenceUpload;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $file;

    public function updatedFile()
    {
        $this->resetErrorBag('file');
    }

    public function save()
    {
        $this->validate([
            'file' => 'required|mimes:csv,txt|max:51200',
        ]);

        $hash = hash_file('sha256', $this->file->getRealPath());

        if (EvidenceUpload::where('file_hash', $hash)->exists()) {
            $this->addError('file', 'This file has already been processed.');
            return;
        }

        $path = $this->file->store('samples');

        ProcessEvidenceFileJob::dispatch(
            Storage::path($path),
            $hash,
            $this->file->getClientOriginalName()
        );

        session()->flash('message', 'File uploaded — processing in the background.');

        $this->reset('file');
    }


    public function getTimelineData(): array
    {
        $raw = \App\Models\AuditLog::selectRaw('DATE(created_at) as date, severity, COUNT(*) as count')
            ->groupBy('date', 'severity')
            ->orderBy('date')
            ->get();

        $dates = $raw->pluck('date')->unique()->sort()->values();
        $severities = ['info', 'warning', 'critical', 'fatal'];

        $colors = [
            'info' => '#9CA3AF',
            'warning' => '#FACC15',
            'critical' => '#FB923C',
            'fatal' => '#EF4444',
        ];

        $datasets = [];
        foreach ($severities as $severity) {
            $data = $dates->map(function ($date) use ($raw, $severity) {
                $match = $raw->first(fn($row) => $row->date === $date && $row->severity === $severity);
                return $match ? $match->count : 0;
            });

            $datasets[] = [
                'label' => ucfirst($severity),
                'data' => $data->values(),
                'backgroundColor' => $colors[$severity],
            ];
        }

        return [
            'labels' => $dates->values(),
            'datasets' => $datasets,
        ];
    }
};

?>


<div>
    <form wire:submit="save">
        <label class="block text-sm font-medium mb-1">Evidence CSV</label>

        <div x-data="{ isDragging: false, uploadProgress: 0 }" x-on:livewire-upload-start="uploadProgress = 0" x-on:livewire-upload-finish="uploadProgress = 100" x-on:livewire-upload-cancel="uploadProgress = 0" x-on:livewire-upload-error="uploadProgress = 0" x-on:livewire-upload-progress="uploadProgress = $event.detail.progress" x-on:dragover.prevent="isDragging = true" x-on:dragleave.prevent="isDragging = false" x-on:drop.prevent="
                isDragging = false;
                if ($event.dataTransfer.files.length > 0) {
                    $wire.upload('file', $event.dataTransfer.files[0]);
                }
            " x-on:click="$refs.fileInput.click()" :class="isDragging ? 'border-blue-500 bg-blue-500/10' : 'border-gray-600'" class="border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-colors">
            <input type="file" wire:model="file" accept=".csv" x-ref="fileInput" class="hidden">

            <p class="text-sm text-gray-400">
                Drag and drop your CSV here, or click to browse
            </p>

            <template x-if="uploadProgress > 0 && uploadProgress < 100">
                <div class="mt-4">
                    <div class="w-full bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full transition-all" :style="`width: ${uploadProgress}%`"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1" x-text="`${uploadProgress}%`"></p>
                </div>
            </template>
        </div>

        @if ($file)
            <p class="text-sm text-gray-300 mt-2">Selected: {{ $file->getClientOriginalName() }}</p>
        @endif

        @error('file') <span class="text-red-500">{{ $message }}</span> @enderror

        <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save" class="mt-4">
            <span wire:loading.remove wire:target="save">Upload Evidence</span>
            <span wire:loading wire:target="save">Processing...</span>
        </flux:button>
    </form>

    @if (session('message'))
        <div class="text-green-500 mt-2">{{ session('message') }}</div>
    @endif
</div>