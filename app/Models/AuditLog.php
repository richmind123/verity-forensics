<?php

namespace App\Models;

use App\Enums\Severity;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['event', 'ip_address', 'severity', 'payload', 'created_at', 'updated_at'];

    protected $casts = [
        'severity' => Severity::class,
        'payload'  => 'array',
    ];

    public static function timelineData(): array
{
    $rows = self::selectRaw('DATE(created_at) as date, severity, COUNT(*) as count')
        ->groupBy('date', 'severity')
        ->orderBy('date')
        ->get();

    $labels = $rows->pluck('date')->unique()->sort()->values()->all();

    $severities = ['info', 'warning', 'critical', 'fatal'];

    $datasets = [];
    foreach ($severities as $severity) {
        $datasets[$severity] = [];
        foreach ($labels as $date) {
            $match = $rows->first(fn ($row) => $row->date === $date && $row->severity->value === $severity);
            $datasets[$severity][] = $match ? $match->count : 0;
        }
    }

    return [
        'labels' => $labels,
        'datasets' => $datasets,
    ];
}
}