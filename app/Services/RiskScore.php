<?php

namespace App\Services;

class RiskScore
{
    public function __construct(
        public readonly string $ip_address,
        public readonly int $score,
        public readonly int $download_count,
        public readonly int $suspicious_download_count,
        public readonly bool $has_elevated_severity,
    ) {}
}
