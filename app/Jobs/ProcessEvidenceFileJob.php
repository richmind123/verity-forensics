<?php

namespace App\Jobs;

use App\Models\AuditLog;
use App\Models\EvidenceUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ProcessEvidenceFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $filePath,
        public string $fileHash,
        public string $originalFilename,
        public int $userId
    ) {
    }

    public function handle(): void
    {
        // Guard against duplicate/queued-twice uploads: if this exact file
        // was already processed BY THIS USER (matched by hash), skip entirely
        // rather than re-parsing the CSV and inserting duplicate audit log rows.
        if (EvidenceUpload::where('user_id', $this->userId)->where('file_hash', $this->fileHash)->exists()) {
            Log::info("Skipped duplicate evidence file: {$this->originalFilename} (hash already processed for user {$this->userId}).");
            return;
        }

        $handle = fopen($this->filePath, 'r');

        if ($handle === false) {
            throw new RuntimeException("Unable to open evidence file: {$this->filePath}");
        }

        $header = fgetcsv($handle, escape: '');

        if ($header === false) {
            throw new RuntimeException("Evidence file is empty or unreadable: {$this->filePath}");
        }

        $header = array_map('strval', $header);

        $rowCount = 0;

        while (($row = fgetcsv($handle, escape: '')) !== false) {
            $data = array_combine($header, $row);

            AuditLog::create([
                'user_id' => $this->userId,
                'event' => $data['event'],
                'ip_address' => $data['ip_address'],
                'severity' => $data['severity'],
                'payload' => json_decode($data['payload'], true),
                'created_at' => $data['timestamp'],
                'updated_at' => $data['timestamp'],
            ]);

            $rowCount++;
        }

        fclose($handle);

        try {
            EvidenceUpload::create([
                'user_id' => $this->userId,
                'filename' => $this->originalFilename,
                'file_hash' => $this->fileHash,
                'row_count' => $rowCount,
            ]);
        } catch (UniqueConstraintViolationException $e) {
            Log::warning("Duplicate evidence upload race for {$this->originalFilename} (user {$this->userId}) — file_hash already recorded.");
        }
    }
}