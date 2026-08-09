@props(['severity'])

@php
    $styles = [
        'info' => 'text-verity-muted border-verity-muted/30 bg-verity-muted/10',
        'warning' => 'text-verity-alert border-verity-alert/30 bg-verity-alert/10',
        'critical' => 'text-verity-critical border-verity-critical/30 bg-verity-critical/10',
        'fatal' => 'text-verity-fatal border-verity-fatal/30 bg-verity-fatal/10',
    ];

    $classes = $styles[$severity] ?? 'text-verity-muted border-verity-muted/30 bg-verity-muted/10';
@endphp

<span {{ $attributes->merge(['class' => "inline-block px-2 py-0.5 text-xs uppercase tracking-wide border rounded-full {$classes}"]) }}>
    {{ $severity }}
</span>