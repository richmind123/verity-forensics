<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Collection;

class RiskScoreCalculator
{
    /**
     * Known legitimate browser signatures. Anything NOT matching
     * one of these patterns in the payload's "browser" field is
     * treated as a suspicious User-Agent.
     */
    private const KNOWN_BROWSERS = ['Chrome', 'Firefox', 'Safari', 'Edge'];

    /**
     * Calculates a risk score (0-100) for every IP that has at
     * least one File Download event, based on correlated signals.
     */
    public static function calculate(): Collection
    {
        $ips = AuditLog::where('event', 'File Download')
            ->distinct()
            ->pluck('ip_address');

        return $ips->map(function ($ip) {
            $logsForIp = AuditLog::where('ip_address', $ip)->get();

            $downloads = $logsForIp->where('event', 'File Download');
            $suspiciousDownloads = $downloads->filter(fn ($log) => self::hasSuspiciousUserAgent($log));

            $score = 0;

            // Signal 1: suspicious User-Agent on downloads (heaviest weight)
            if ($suspiciousDownloads->isNotEmpty()) {
                $score += 50;
            }

            // Signal 2: volume of downloads (capped contribution)
            $score += min($downloads->count() * 5, 20);

            // Signal 3: any critical/fatal severity events from this IP
            $hasElevatedSeverity = $logsForIp->contains(
                fn ($log) => in_array($log->severity->value, ['critical', 'fatal'])
            );
            if ($hasElevatedSeverity) {
                $score += 30;
            }

            return (object) [
                'ip_address' => $ip,
                'score' => min($score, 100),
                'download_count' => $downloads->count(),
                'suspicious_download_count' => $suspiciousDownloads->count(),
                'has_elevated_severity' => $hasElevatedSeverity,
            ];
        })
        ->sortByDesc('score')
        ->values();
    }

    private static function hasSuspiciousUserAgent(AuditLog $log): bool
    {
        $browser = $log->payload['browser'] ?? null;

        if (! $browser) {
            return false; // no browser info at all — not enough signal alone
        }

        foreach (self::KNOWN_BROWSERS as $known) {
            if (str_contains($browser, $known)) {
                return false; // matches a known real browser
            }
        }

        return true; // doesn't match any known browser signature
    }
}