<?php

namespace App\Helpers;

use App\Models\Config;

class CodeGenerator
{
    /**
     * Token yang didukung di format template:
     *   {Y}   → tahun 4 digit  (2026)
     *   {y}   → tahun 2 digit  (26)
     *   {m}   → bulan 2 digit  (06)
     *   {d}   → hari 2 digit   (17)
     *   {seq} → nomor urut (diisi otomatis)
     *
     * Contoh: INV-{Y}{m}-{seq}  →  INV-202606-0001
     *         INV/{Y}/{m}/{d}/{seq}  →  INV/2026/06/17/001
     */
    private static function generateFromTemplate(string $modelClass, string $template, int $padding): string
    {
        // Pisah template menjadi bagian sebelum {seq} (prefix) dan sesudahnya (suffix)
        if (! str_contains($template, '{seq}')) {
            $template .= '{seq}';
        }

        [$prefixTemplate, $suffix] = explode('{seq}', $template, 2);

        $basePrefix = self::resolveDateTokens($prefixTemplate);

        $last = $modelClass::withTrashed()
            ->where('code', 'like', $basePrefix . '%')
            ->orderByDesc('code')
            ->value('code');

        if ($last) {
            // Ambil bagian setelah prefix, sebelum suffix (jika ada suffix)
            $inner = substr($last, strlen($basePrefix));
            if ($suffix) $inner = rtrim($inner, $suffix);
            $next = ((int) $inner) + 1;
        } else {
            $next = 1;
        }

        return $basePrefix . str_pad($next, max(1, $padding), '0', STR_PAD_LEFT) . $suffix;
    }

    private static function resolveDateTokens(string $template, ?\DateTimeInterface $date = null): string
    {
        $date = $date ?? now();
        return str_replace(
            ['{Y}', '{y}', '{m}', '{d}'],
            [$date->format('Y'), $date->format('y'), $date->format('m'), $date->format('d')],
            $template
        );
    }

    public static function invoice(): string
    {
        $template = Config::get('invoice_format', 'INV-{Y}{m}-{seq}');
        $padding  = (int) Config::get('invoice_padding', '4');
        return self::generateFromTemplate(\App\Models\Invoice::class, $template, $padding);
    }

    public static function bill(): string
    {
        $template = Config::get('bill_format', 'BILL-{Y}{m}-{seq}');
        $padding  = (int) Config::get('bill_padding', '4');
        return self::generateFromTemplate(\App\Models\Bill::class, $template, $padding);
    }

    public static function vendor(): string
    {
        $prefix = 'VND-';
        $last   = \App\Models\Vendor::withTrashed()
            ->where('code', 'like', $prefix . '%')
            ->orderByDesc('code')
            ->value('code');
        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public static function receipt(): string
    {
        $prefix = 'RCP-' . now()->format('Ym') . '-';
        $last   = \App\Models\Receipt::withTrashed()
            ->where('code', 'like', $prefix . '%')
            ->orderByDesc('code')
            ->value('code');
        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public static function salesReturn(): string
    {
        $prefix = 'RTN-' . now()->format('Ym') . '-';
        $last   = \App\Models\SalesReturn::withTrashed()
            ->where('code', 'like', $prefix . '%')
            ->orderByDesc('code')
            ->value('code');
        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    /** Preview untuk settings page (tanpa query DB) */
    public static function preview(string $template, int $padding): string
    {
        if (! str_contains($template, '{seq}')) {
            $template .= '{seq}';
        }
        [$prefixTemplate, $suffix] = explode('{seq}', $template, 2);
        $basePrefix = self::resolveDateTokens($prefixTemplate);
        return $basePrefix . str_pad(1, max(1, $padding), '0', STR_PAD_LEFT) . $suffix;
    }
}
