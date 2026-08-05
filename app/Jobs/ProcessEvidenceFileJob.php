<?php

namespace App\Jobs;

use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessEvidenceFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $filePath)
    {
    }

    public function handle(): void
    {
        $handle = fopen($this->filePath, 'r');

        $header = fgetcsv($handle, escape: '');

        while (($row = fgetcsv($handle, escape: '')) !== false) {
            $data = array_combine($header, $row);

            AuditLog::create([
                'event' => $data['event'],
                'ip_address' => $data['ip_address'],
                'severity' => $data['severity'],
                'payload' => json_decode($data['payload'], true),
                'created_at' => $data['timestamp'],
                'updated_at' => $data['timestamp'],
            ]);
        }

        fclose($handle);
    }
}