<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $data->code }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }

        body {
            font-size: 11px;
            color: #000;
            background: #fff;
            padding: 56px 52px;
            line-height: 1.6;
        }

        .clearfix::after { content: ''; display: table; clear: both; }

        /* ── TOP BAR ── */
        .top-code  { float: left; }
        .top-logo  { float: right; text-align: right; }

        .code-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 4px; }
        .code-value { font-size: 26px; font-weight: 700; letter-spacing: -0.5px; }

        .logo { max-height: 64px; max-width: 220px; }

        /* ── THIN LINE ── */
        .line       { border: none; border-top: 1px solid #d0d0d0; margin: 24px 0; clear: both; }
        .line-light { border: none; border-top: 1px solid #d0d0d0; margin: 20px 0; clear: both; }

        /* ── META ROW: tanggal, jatuh tempo, salesperson ── */
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { font-size: 11px; padding: 0; width: 33.33%; vertical-align: top; }
        .meta-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2px; }
        .meta-value { font-size: 11px; }

        /* ── TWO COLUMNS: company / bill to ── */
        .col-table { width: 100%; border-collapse: collapse; }
        .col-table td { vertical-align: top; width: 50%; padding: 0; }
        .col-table td + td { padding-left: 32px; }

        .col-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
        .col-name  { font-size: 13px; font-weight: 700; margin-bottom: 5px; }
        .col-line  { font-size: 11px; margin-bottom: 2px; }

        /* ── ITEMS TABLE ── */
        .items { width: 100%; border-collapse: collapse; }
        .items thead th {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0 0 8px 0;
            text-align: left;
            border-bottom: 1px solid #d0d0d0;
        }
        .items thead th.r { text-align: right; }
        .items tbody td {
            padding: 10px 0;
            font-size: 11px;
            border-bottom: 1px solid #e8e8e8;
            vertical-align: middle;
        }
        .items tbody tr:last-child td { border-bottom: 1px solid #d0d0d0; }
        .items tbody td.r    { text-align: right; }
        .items tbody td.bold { font-weight: 700; }
        .items tbody td.num  { text-align: right; font-weight: 700; }

        /* ── TOTALS ── */
        .totals-outer { width: 100%; margin-top: 20px; }
        .totals-inner { float: right; width: 220px; }
        .t-row  { width: 100%; border-collapse: collapse; }
        .t-row td { padding: 4px 0; font-size: 11px; }
        .t-row td.tr { text-align: right; }
        .t-sep  { border: none; border-top: 1px solid #d0d0d0; margin: 8px 0; }
        .t-grand { width: 100%; border-collapse: collapse; }
        .t-grand td { padding: 6px 0; font-size: 13px; font-weight: 700; }
        .t-grand td.tr { text-align: right; }
        .t-sub  { width: 100%; border-collapse: collapse; }
        .t-sub td { padding: 3px 0; font-size: 11px; }
        .t-sub td.tr { text-align: right; }

        /* ── PAYMENT INFO ── */
        .payment-wrap { width: 100%; margin-top: 28px; clear: both; }
        .payment-left  { float: left; width: 48%; }
        .payment-right { float: right; width: 48%; }
        .payment-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
        .payment-table { width: 100%; border-collapse: collapse; }
        .payment-table td { font-size: 11px; padding: 3px 0; vertical-align: top; }
        .payment-table td.pk { width: 130px; }

        /* ── NOTES ── */
        .notes-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px; }
        .notes-text  { font-size: 11px; line-height: 1.7; }

        /* ── FOOTER ── */
        .footer { margin-top: 52px; text-align: center; font-size: 9px; color: #aaa; clear: both; }
    </style>
</head>
<body>

    @php
        $remaining = $data->amount - $data->paid_amount;
    @endphp

    {{-- Top: Kode Invoice kiri, Logo kanan --}}
    <div class="clearfix">
        <div class="top-code">
            <div class="code-label">{{ __('general.invoice') }}</div>
            <div class="code-value">{{ $data->code }}</div>
        </div>
        <div class="top-logo">
            <img src="{{ public_path('src/img/logo-dark.png') }}" class="logo" alt="{{ config('app.name') }}">
        </div>
    </div>

    <hr class="line">

    {{-- Meta: tanggal, jatuh tempo, salesperson --}}
    <table class="meta-table">
        <tr>
            <td>
                <div class="meta-label">{{ __('general.invoice_date') }}</div>
                <div class="meta-value">{{ $data->invoice_date->translatedFormat('d F Y') }}</div>
            </td>
            <td>
                <div class="meta-label">{{ __('general.due_date') }}</div>
                <div class="meta-value">{{ $data->due_date ? $data->due_date->translatedFormat('d F Y') : '-' }}</div>
            </td>
            <td>
                @if($data->salesperson)
                    <div class="meta-label">{{ __('general.salesperson') }}</div>
                    <div class="meta-value">{{ $data->salesperson->name }}</div>
                @endif
            </td>
        </tr>
    </table>

    <hr class="line-light">

    {{-- Company Info & Bill To --}}
    <table class="col-table">
        <tr>
            <td>
                <div class="col-label">{{ __('general.company_information') }}</div>
                <div class="col-name">{{ config('app.name') }}</div>
                <div class="col-line">Jl. Contoh Alamat No. 1</div>
                <div class="col-line">Surabaya, Jawa Timur 60111</div>
                <div class="col-line">Telp: (031) 000-0000</div>
                <div class="col-line">Email: info@surya-makmur.com</div>
            </td>
            <td>
                <div class="col-label">{{ __('general.bill_to_header') }}</div>
                <div class="col-name">{{ $data->customer_snapshot['name'] ?? $data->customer?->name ?? '-' }}</div>
                @if(!empty($data->customer_snapshot['company_name']))
                    <div class="col-line">{{ $data->customer_snapshot['company_name'] }}</div>
                @endif
                @if(!empty($data->customer_snapshot['address']))
                    <div class="col-line">{{ $data->customer_snapshot['address'] }}</div>
                @endif
                @if(!empty($data->customer_snapshot['phone']))
                    <div class="col-line">Telp: {{ $data->customer_snapshot['phone'] }}</div>
                @endif
                @if(!empty($data->customer_snapshot['email']))
                    <div class="col-line">Email: {{ $data->customer_snapshot['email'] }}</div>
                @endif
                @if(!empty($data->customer_snapshot['tax_number']))
                    <div class="col-line">{{ __('general.tax_number') }}: {{ $data->customer_snapshot['tax_number'] }}</div>
                @endif
            </td>
        </tr>
    </table>

    <hr class="line">

    {{-- Line Items --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:22px;">#</th>
                <th>{{ __('general.product') }}</th>
                <th class="r" style="width:40px;">{{ __('general.qty') }}</th>
                <th class="r" style="width:90px;">{{ __('general.price') }}</th>
                <th class="r" style="width:80px;">{{ __('general.discount') }}</th>
                <th class="r" style="width:70px;">{{ __('general.tax') }}</th>
                <th class="r" style="width:90px;">{{ __('general.amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data->details as $i => $detail)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="bold">{{ $detail->product_snapshot['name'] ?? '-' }}</td>
                    <td class="r">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                    <td class="r">Rp{{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                    <td class="r">{{ $detail->discount_amount ? 'Rp' . number_format($detail->discount_amount, 0, ',', '.') : '-' }}</td>
                    <td class="r">{{ $detail->tax_amount ? 'Rp' . number_format($detail->tax_amount, 0, ',', '.') : '-' }}</td>
                    <td class="num">Rp{{ number_format($detail->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Bottom: Notes kiri, Totals kanan --}}
    <div class="clearfix" style="margin-top: 20px;">

        {{-- Kiri: Notes --}}
        <div style="float: left; width: 68%;">
            @if($data->notes)
                <div class="notes-label">{{ __('general.notes') }}</div>
                <div class="notes-text" style="max-width: 70%;">{{ $data->notes }}</div>
            @endif
        </div>

        {{-- Kanan: Totals --}}
        <div style="float: right; width: 30%;">
            <table class="t-row">
                <tr>
                    <td>{{ __('general.subtotal') }}</td>
                    <td class="tr">Rp{{ number_format($data->subtotal_amount, 0, ',', '.') }}</td>
                </tr>
                @if($data->discount_amount)
                <tr>
                    <td>{{ __('general.discount') }}{{ $data->discount_percent ? ' ('.$data->discount_percent.'%)' : '' }}</td>
                    <td class="tr">- Rp{{ number_format($data->discount_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($data->tax_amount)
                <tr>
                    <td>{{ __('general.tax') }}{{ $data->tax_percent ? ' ('.$data->tax_percent.'%)' : '' }}</td>
                    <td class="tr">Rp{{ number_format($data->tax_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
            </table>
            <hr class="t-sep">
            <table class="t-grand">
                <tr>
                    <td>{{ __('general.total') }}</td>
                    <td class="tr">Rp{{ number_format($data->amount, 0, ',', '.') }}</td>
                </tr>
            </table>
            <hr class="t-sep">
            <table class="t-sub">
                <tr>
                    <td>{{ __('general.payment_received') }}</td>
                    <td class="tr">Rp{{ number_format($data->paid_amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('general.remaining_bill') }}</strong></td>
                    <td class="tr"><strong>Rp{{ number_format($remaining, 0, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>

    </div>

    {{-- Bank Info --}}
    <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid #d0d0d0; clear: both;">
        <div class="payment-label">{{ __('general.bank_information') }}</div>
        <table class="payment-table">
            <tr>
                <td class="pk">{{ __('general.bank_name') }}</td>
                <td>: Bank Central Asia (BCA)</td>
            </tr>
            <tr>
                <td class="pk">{{ __('general.account_number') }}</td>
                <td>: 1234567890</td>
            </tr>
            <tr>
                <td class="pk">{{ __('general.account_holder_name') }}</td>
                <td>: CV. Surya Makmur</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        {{ config('app.name') }} &mdash; {{ now()->translatedFormat('d F Y') }}
    </div>

</body>
</html>
