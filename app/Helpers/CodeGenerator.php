<?php

namespace App\Helpers;

class CodeGenerator
{
    public static function vendor(): string
    {
        $prefix = 'VND-';

        $last = \App\Models\Vendor::withTrashed()
            ->where('code', 'like', $prefix . '%')
            ->orderByDesc('code')
            ->value('code');

        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public static function invoice(): string
    {
        $prefix = 'INV-' . now()->format('Ym') . '-';

        $last = \App\Models\Invoice::withTrashed()
            ->where('code', 'like', $prefix . '%')
            ->orderByDesc('code')
            ->value('code');

        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
