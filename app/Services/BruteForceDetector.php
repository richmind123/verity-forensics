<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Collection;

class BruteForceDetector
{
    /**
     * Returns IPs with more than $threshold failed login attempts
     * within any 5-minute window.
     */
    public static function detect(int $threshold = 50): Collection
    {
        return AuditLog::query()
            ->where('event', 'Login Failed')
            ->selectRaw("ip_address, CAST(strftime('%s', created_at) / 300 AS INTEGER) as time_bucket, COUNT(*) as attempts")
            ->groupBy('ip_address', 'time_bucket')
            ->havingRaw('COUNT(*) > ?', [$threshold])
            ->orderByDesc('attempts')
            ->get();
    }
}
