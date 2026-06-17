<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data->code }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #111; background: #fff; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; }
        .invoice-title { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 4px; }
        .invoice-code { font-size: 20px; font-weight: 700; font-family: monospace; margin-bottom: 6px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-draft { background: #f3f4f6; color: #374151; }
        .badge-waiting { background: #fef3c7; color: #92400e; }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-partial { background: #dbeafe; color: #1e40af; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .company-info { text-align: right; }
        .company-name { font-size: 13px; font-weight: 700; margin-bottom: 2px; }
        .company-detail { font-size: 11px; color: #6b7280; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 20px 0; }
        .meta-grid { display: flex; gap: 24px; margin-bottom: 8px; }
        .meta-col { flex: 1; }
        .meta-label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .meta-value { font-size: 12px; font-weight: 500; }
        .meta-subvalue { font-size: 11px; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { border-bottom: 1px solid #e5e7eb; }
        th { padding: 8px 6px; text-align: left; font-size: 10px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
        th.text-right { text-align: right; }
        td { padding: 8px 6px; font-size: 12px; border-bottom: 1px solid #f3f4f6; }
        td.text-right { text-align: right; }
        td.muted { color: #6b7280; }
        td.mono { font-family: monospace; font-size: 11px; }
        .totals { margin-top: 16px; display: flex; justify-content: flex-end; }
        .totals-table { width: 260px; }
        .totals-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 12px; }
        .totals-label { color: #6b7280; }
        .totals-total { font-size: 14px; font-weight: 700; padding-top: 8px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; }
        .notes-section { margin-top: 24px; }
        .notes-title { font-size: 12px; font-weight: 600; margin-bottom: 4px; }
        .notes-text { font-size: 11px; color: #6b7280; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="invoice-title">Invoice</div>
            <div class="invoice-code">{{ $data->code }}</div>
            @php
                $badgeClass = match($data->status->value) {
                    'DRAFT'               => 'badge-draft',
                    'WAITING_FOR_PAYMENT' => 'badge-waiting',
                    'PAID'                => 'badge-paid',
                    'PARTIALLY_PAID'      => 'badge-partial',
                    'CANCELLED'           => 'badge-cancelled',
                    default               => 'badge-draft',
                };
            @endphp
            <span class="badge {{ $badgeClass }}">{{ $data->status->label() }}</span>
        </div>
        <div class="company-info">
            <div class="company-name">{{ config('app.name') }}</div>
            @if($data->salesperson)
                <div class="company-detail">Sales: {{ $data->salesperson->name }}</div>
            @endif
        </div>
    </div>

    <hr class="divider">

    {{-- Meta --}}
    <div class="meta-grid">
        <div class="meta-col">
            <div class="meta-label">Tagih ke</div>
            <div class="meta-value">{{ $data->customer_snapshot['name'] ?? $data->customer?->name ?? '-' }}</div>
            @if(!empty($data->customer_snapshot['company_name']))
                <div class="meta-subvalue">{{ $data->customer_snapshot['company_name'] }}</div>
            @endif
            @if(!empty($data->customer_snapshot['address']))
                <div class="meta-subvalue">{{ $data->customer_snapshot['address'] }}</div>
            @endif
            @if(!empty($data->customer_snapshot['phone']))
                <div class="meta-subvalue">{{ $data->customer_snapshot['phone'] }}</div>
            @endif
            @if(!empty($data->customer_snapshot['email']))
                <div class="meta-subvalue">{{ $data->customer_snapshot['email'] }}</div>
            @endif
        </div>
        <div class="meta-col">
            <div class="meta-label">Tanggal Invoice</div>
            <div class="meta-value">{{ $data->invoice_date->translatedFormat('d F Y') }}</div>
            <div class="meta-label" style="margin-top:8px;">Jatuh Tempo</div>
            <div class="meta-value">{{ $data->due_date ? $data->due_date->translatedFormat('d F Y') : '-' }}</div>
        </div>
        <div class="meta-col">
            <div class="meta-label">Sudah Dibayar</div>
            <div class="meta-value">Rp {{ number_format($data->paid_amount, 0, ',', '.') }}</div>
            @if(!empty($data->customer_snapshot['tax_number']))
                <div class="meta-label" style="margin-top:8px;">NPWP</div>
                <div class="meta-value">{{ $data->customer_snapshot['tax_number'] }}</div>
            @endif
        </div>
    </div>

    <hr class="divider">

    {{-- Line Items --}}
    <table>
        <thead>
            <tr>
                <th style="width:24px;">#</th>
                <th>Produk</th>
                <th>SKU</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Diskon</th>
                <th class="text-right">Pajak</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data->details as $i => $detail)
                <tr>
                    <td class="muted">{{ $i + 1 }}</td>
                    <td><strong>{{ $detail->product_snapshot['name'] ?? '-' }}</strong></td>
                    <td class="muted mono">{{ $detail->product_snapshot['sku'] ?? '-' }}</td>
                    <td class="text-right">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right muted">{{ $detail->discount_amount ? 'Rp ' . number_format($detail->discount_amount, 0, ',', '.') : '-' }}</td>
                    <td class="text-right muted">{{ $detail->tax_amount ? 'Rp ' . number_format($detail->tax_amount, 0, ',', '.') : '-' }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($detail->amount, 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals">
        <div class="totals-table">
            <div class="totals-row">
                <span class="totals-label">Subtotal</span>
                <span>Rp {{ number_format($data->subtotal_amount, 0, ',', '.') }}</span>
            </div>
            @if($data->discount_amount)
                <div class="totals-row">
                    <span class="totals-label">Diskon{{ $data->discount_percent ? ' (' . $data->discount_percent . '%)' : '' }}</span>
                    <span>- Rp {{ number_format($data->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($data->tax_amount)
                <div class="totals-row">
                    <span class="totals-label">Pajak{{ $data->tax_percent ? ' (' . $data->tax_percent . '%)' : '' }}</span>
                    <span>Rp {{ number_format($data->tax_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="totals-total">
                <span>Total</span>
                <span>Rp {{ number_format($data->amount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    @if($data->notes)
        <div class="notes-section">
            <div class="notes-title">Catatan</div>
            <div class="notes-text">{{ $data->notes }}</div>
        </div>
    @endif

    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh {{ config('app.name') }}
    </div>

</body>
</html>
