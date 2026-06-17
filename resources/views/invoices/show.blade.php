@extends('layouts.main', ['title' => $data->code])

@section('content')
<div class="page-content">

    <div class="page-header">
        <h1>{{ $data->code }}</h1>
        <p>@lang('general.invoice_detail')</p>
    </div>

    {{-- Action buttons --}}
    <div class="flex items-center gap-2 mb-6">
        <button onclick="window.print()" class="btn btn-secondary btn-sm">
            <x-icon name="print" class="size-3.5" />
            @lang('general.print')
        </button>
        <a href="mailto:{{ $data->customer_snapshot['email'] ?? '' }}?subject={{ urlencode(__('general.send_invoice_subject', ['code' => $data->code, 'company' => config('app.name')])) }}"
           class="btn btn-secondary btn-sm">
            <x-icon name="mail" class="size-3.5" />
            @lang('general.send')
        </a>
        <a href="{{ route('invoices.pdf', ['encryptedId' => $encryptedId]) }}"
           target="_blank"
           class="btn btn-secondary btn-sm">
            <x-icon name="download" class="size-3.5" />
            @lang('general.download_pdf')
        </a>
    </div>

    {{-- Invoice Card --}}
    <div class="rounded-lg border bg-card text-card-foreground shadow-xs" id="invoice-card">
        <div class="p-6 space-y-6">

            {{-- Top: Invoice number + status / Company info --}}
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">@lang('general.invoice')</p>
                    <h2 class="text-xl font-semibold font-mono">{{ $data->code }}</h2>
                    <span class="badge {{ $data->status->badgeClass() }} mt-1">{{ $data->status->label() }}</span>
                </div>
                <div class="text-right text-sm">
                    <p class="font-semibold text-sm">{{ config('app.name') }}</p>
                    @if($data->salesperson)
                        <p class="text-xs text-muted-foreground">{{ __('general.salesperson') }}: {{ $data->salesperson->name }}</p>
                    @endif
                </div>
            </div>

            <hr class="border-border">

            {{-- Bill To / Invoice Date / Due Date --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 text-sm">
                <div class="space-y-1">
                    <p class="text-xs text-muted-foreground">@lang('general.bill_to')</p>
                    <p class="font-medium">{{ $data->customer_snapshot['name'] ?? $data->customer?->name ?? '-' }}</p>
                    @if(!empty($data->customer_snapshot['company_name']))
                        <p class="text-xs text-muted-foreground">{{ $data->customer_snapshot['company_name'] }}</p>
                    @endif
                    @if(!empty($data->customer_snapshot['address']))
                        <p class="text-xs text-muted-foreground">{{ $data->customer_snapshot['address'] }}</p>
                    @endif
                    @if(!empty($data->customer_snapshot['phone']))
                        <p class="text-xs text-muted-foreground">{{ $data->customer_snapshot['phone'] }}</p>
                    @endif
                    @if(!empty($data->customer_snapshot['email']))
                        <p class="text-xs text-muted-foreground">{{ $data->customer_snapshot['email'] }}</p>
                    @endif
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-muted-foreground">@lang('general.invoice_date')</p>
                    <p class="font-medium">{{ $data->invoice_date->translatedFormat('d F Y') }}</p>
                    <p class="text-xs text-muted-foreground mt-2">@lang('general.due_date')</p>
                    <p class="font-medium">{{ $data->due_date ? $data->due_date->translatedFormat('d F Y') : '-' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-muted-foreground">@lang('general.paid_amount')</p>
                    <p class="font-medium">Rp {{ number_format($data->paid_amount, 0, ',', '.') }}</p>
                    @if($data->tax_number ?? $data->customer_snapshot['tax_number'] ?? null)
                        <p class="text-xs text-muted-foreground mt-2">NPWP</p>
                        <p class="font-medium">{{ $data->customer_snapshot['tax_number'] }}</p>
                    @endif
                </div>
            </div>

            <hr class="border-border my-2">

            {{-- Line Items --}}
            <div class="overflow-x-auto mt-6">
                <table class="w-full text-sm" data-no-sort>
                    <thead>
                        <tr class="border-b">
                            <th class="pb-3 text-left text-xs font-medium text-muted-foreground">#</th>
                            <th class="pb-3 text-left text-xs font-medium text-muted-foreground">@lang('general.product')</th>
                            <th class="pb-3 text-left text-xs font-medium text-muted-foreground">@lang('general.sku')</th>
                            <th class="pb-3 text-right text-xs font-medium text-muted-foreground">@lang('general.qty')</th>
                            <th class="pb-3 text-right text-xs font-medium text-muted-foreground">@lang('general.unit_price')</th>
                            <th class="pb-3 text-right text-xs font-medium text-muted-foreground">@lang('general.discount')</th>
                            <th class="pb-3 text-right text-xs font-medium text-muted-foreground">@lang('general.tax')</th>
                            <th class="pb-3 text-right text-xs font-medium text-muted-foreground">@lang('general.amount')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data->details as $i => $detail)
                            <tr class="border-b last:border-0">
                                <td class="py-3 text-muted-foreground">{{ $i + 1 }}</td>
                                <td class="py-3 font-medium">{{ $detail->product_snapshot['name'] ?? '-' }}</td>
                                <td class="py-3 text-muted-foreground font-mono text-xs">{{ $detail->product_snapshot['sku'] ?? '-' }}</td>
                                <td class="py-3 text-right">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                                <td class="py-3 text-right">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                                <td class="py-3 text-right text-muted-foreground">
                                    {{ $detail->discount_amount ? 'Rp ' . number_format($detail->discount_amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3 text-right text-muted-foreground">
                                    {{ $detail->tax_amount ? 'Rp ' . number_format($detail->tax_amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3 text-right font-medium">Rp {{ number_format($detail->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="flex justify-end">
                <div class="w-full max-w-xs space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">@lang('general.subtotal')</span>
                        <span>Rp {{ number_format($data->subtotal_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($data->discount_amount)
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">
                                @lang('general.discount')
                                @if($data->discount_percent) ({{ $data->discount_percent }}%) @endif
                            </span>
                            <span>- Rp {{ number_format($data->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($data->tax_amount)
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">
                                @lang('general.tax')
                                @if($data->tax_percent) ({{ $data->tax_percent }}%) @endif
                            </span>
                            <span>Rp {{ number_format($data->tax_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <hr class="border-border">
                    <div class="flex justify-between text-base font-semibold">
                        <span>@lang('general.total')</span>
                        <span>Rp {{ number_format($data->amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            @if($data->notes)
                <hr class="border-border">
                <div class="space-y-1">
                    <p class="text-sm font-medium">@lang('general.notes')</p>
                    <p class="text-xs text-muted-foreground">{{ $data->notes }}</p>
                </div>
            @endif

        </div>
    </div>

    {{-- Footer Actions --}}
    <div class="mt-4 flex gap-2">
        @if($data->status->canEdit())
            <a href="{{ route('invoices.edit', ['encryptedId' => $encryptedId]) }}"
               class="btn btn-ghost btn-sm">
                <x-icon name="edit" class="size-3.5" />
                @lang('general.edit')
            </a>
        @endif

        @if($data->status->canCancel())
            <form action="{{ route('invoices.cancel', ['encryptedId' => $encryptedId]) }}"
                  method="POST"
                  id="cancel-form">
                @csrf
                @method('PATCH')
                <button type="button"
                        onclick="confirmCancel()"
                        class="btn btn-ghost btn-sm text-destructive hover:bg-destructive/10">
                    <x-icon name="x-circle" class="size-3.5" />
                    @lang('general.cancel_invoice')
                </button>
            </form>
        @endif
    </div>

</div>
@endsection

@push('styles')
<style>
@media print {
    body * { visibility: hidden; }
    #invoice-card, #invoice-card * { visibility: visible; }
    #invoice-card { position: absolute; inset: 0; border: none; box-shadow: none; }
    .badge { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
}
</style>
@endpush

@push('scripts')
<script>
function confirmCancel() {
    if (confirm('{{ __('general.confirm_cancel_invoice') }}')) {
        document.getElementById('cancel-form').submit();
    }
}
</script>
@endpush
