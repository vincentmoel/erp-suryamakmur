{{-- Drawer preview --}}
@php
    $remaining      = $data->amount - $data->paid_amount;
    $companyName    = \App\Models\Config::get('company_name', config('app.name'));
    $companyAddress = \App\Models\Config::get('company_address');
    $companyPhone   = \App\Models\Config::get('company_phone');
    $companyEmail   = \App\Models\Config::get('company_email');
@endphp

<div class="space-y-6 text-sm">

    {{-- Meta: tanggal, jatuh tempo, salesperson --}}
    <div class="grid grid-cols-3 gap-4">
        <div>
            <p class="text-xs text-muted-foreground">{{ __('general.invoice_date') }}</p>
            <p class="font-medium mt-0.5">{{ $data->invoice_date->translatedFormat('d F Y') }}</p>
        </div>
        <div>
            <p class="text-xs text-muted-foreground">{{ __('general.due_date') }}</p>
            <p class="font-medium mt-0.5">{{ $data->due_date ? $data->due_date->translatedFormat('d F Y') : '-' }}</p>
        </div>
        @if($data->salesperson)
        <div>
            <p class="text-xs text-muted-foreground">{{ __('general.salesperson') }}</p>
            <p class="font-medium mt-0.5">{{ $data->salesperson->name }}</p>
        </div>
        @endif
    </div>

    <hr class="border-border">

    {{-- Company info + Bill To --}}
    <div class="grid grid-cols-2 gap-6">
        <div>
            <p class="text-xs text-muted-foreground uppercase tracking-wider mb-2">{{ __('general.company_information') }}</p>
            <p class="font-semibold">{{ $companyName }}</p>
            @if($companyAddress)
                @foreach(explode("\n", $companyAddress) as $line)
                    <p class="text-xs text-muted-foreground">{{ trim($line) }}</p>
                @endforeach
            @endif
            @if($companyPhone) <p class="text-xs text-muted-foreground">Telp: {{ $companyPhone }}</p> @endif
            @if($companyEmail) <p class="text-xs text-muted-foreground">Email: {{ $companyEmail }}</p> @endif
        </div>
        <div>
            <p class="text-xs text-muted-foreground uppercase tracking-wider mb-2">{{ __('general.bill_to_header') }}</p>
            <p class="font-semibold">{{ $data->customer_snapshot['name'] ?? $data->customer?->name ?? '-' }}</p>
            @if(!empty($data->customer_snapshot['company_name']))
                <p class="text-xs text-muted-foreground">{{ $data->customer_snapshot['company_name'] }}</p>
            @endif
            @if(!empty($data->customer_snapshot['address']))
                <p class="text-xs text-muted-foreground">{{ $data->customer_snapshot['address'] }}</p>
            @endif
            @if(!empty($data->customer_snapshot['phone']))
                <p class="text-xs text-muted-foreground">Telp: {{ $data->customer_snapshot['phone'] }}</p>
            @endif
            @if(!empty($data->customer_snapshot['email']))
                <p class="text-xs text-muted-foreground">Email: {{ $data->customer_snapshot['email'] }}</p>
            @endif
            @if(!empty($data->customer_snapshot['tax_number']))
                <p class="text-xs text-muted-foreground">{{ __('general.tax_number') }}: {{ $data->customer_snapshot['tax_number'] }}</p>
            @endif
        </div>
    </div>

    <hr class="border-border">

    {{-- Line Items --}}
    <div class="overflow-x-auto">
        <table class="invoice-table w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="col-numeric text-left text-xs font-medium text-muted-foreground">#</th>
                    <th class="text-left text-xs font-medium text-muted-foreground">{{ __('general.product') }}</th>
                    <th class="col-numeric text-right text-xs font-medium text-muted-foreground">{{ __('general.qty') }}</th>
                    <th class="col-numeric text-right text-xs font-medium text-muted-foreground">{{ __('general.price') }}</th>
                    <th class="col-numeric text-right text-xs font-medium text-muted-foreground">{{ __('general.discount') }}</th>
                    <th class="col-numeric text-right text-xs font-medium text-muted-foreground">{{ __('general.tax') }}</th>
                    <th class="col-numeric text-right text-xs font-medium text-muted-foreground">{{ __('general.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data->details as $i => $detail)
                    <tr class="border-b last:border-0">
                        <td class="col-numeric text-muted-foreground">{{ $i + 1 }}</td>
                        <td class="font-medium">{{ $detail->product_snapshot['name'] ?? '-' }}</td>
                        <td class="col-numeric text-right">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                        <td class="col-numeric text-right">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                        <td class="col-numeric text-right text-muted-foreground">
                            {{ $detail->discount_amount ? 'Rp ' . number_format($detail->discount_amount, 0, ',', '.') : '-' }}
                        </td>
                        <td class="col-numeric text-right text-muted-foreground">
                            {{ $detail->tax_amount ? 'Rp ' . number_format($detail->tax_amount, 0, ',', '.') : '-' }}
                        </td>
                        <td class="col-numeric text-right font-semibold">Rp {{ number_format($detail->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
    <div class="flex justify-end">
        <div class="w-full max-w-xs space-y-2">
            <div class="flex justify-between">
                <span class="text-muted-foreground">{{ __('general.subtotal') }}</span>
                <span>Rp {{ number_format($data->subtotal_amount, 0, ',', '.') }}</span>
            </div>
            @if($data->discount_amount)
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{ __('general.discount') }}{{ $data->discount_percent ? ' ('.$data->discount_percent.'%)' : '' }}</span>
                    <span>- Rp {{ number_format($data->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($data->tax_amount)
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{ __('general.tax') }}{{ $data->tax_percent ? ' ('.$data->tax_percent.'%)' : '' }}</span>
                    <span>Rp {{ number_format($data->tax_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            <hr class="border-border">
            <div class="flex justify-between font-semibold text-base">
                <span>{{ __('general.total') }}</span>
                <span>Rp {{ number_format($data->amount, 0, ',', '.') }}</span>
            </div>
            <hr class="border-border">
            <div class="flex justify-between">
                <span class="text-muted-foreground">{{ __('general.payment_received') }}</span>
                <span>Rp {{ number_format($data->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-semibold">
                <span>{{ __('general.remaining_bill') }}</span>
                <span>Rp {{ number_format($remaining, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    @if($data->notes)
        <hr class="border-border">
        <div>
            <p class="text-xs text-muted-foreground uppercase tracking-wider mb-1">{{ __('general.notes') }}</p>
            <p class="text-xs">{{ $data->notes }}</p>
        </div>
    @endif

</div>
