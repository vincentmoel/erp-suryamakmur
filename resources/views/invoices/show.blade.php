@extends('layouts.main', ['title' => $data->code])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.invoice_detail')</h1>
            <p>{{ $data->code }}</p>
        </div>

        {{-- Invoice Header --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">

            <div class="flex items-center justify-between gap-3 border-b px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                        <x-icon name="invoice" class="size-4 text-primary" />
                    </div>
                    <h3 class="text-sm font-semibold">@lang('general.invoice_information')</h3>
                </div>
                <span class="badge {{ $data->status->badgeClass() }}">{{ $data->status->label() }}</span>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.invoice_code')</span>
                    <span class="text-sm font-medium font-mono">{{ $data->code }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.customer')</span>
                    <span class="text-sm font-medium">{{ $data->customer->name ?? '-' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.salesperson')</span>
                    <span class="text-sm font-medium">{{ $data->salesperson->name ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.invoice_date')</span>
                    <span class="text-sm font-medium">{{ $data->invoice_date->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.due_date')</span>
                    <span class="text-sm font-medium">{{ $data->due_date ? $data->due_date->translatedFormat('d F Y') : '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.paid_amount')</span>
                    <span class="text-sm font-medium">Rp {{ number_format($data->paid_amount, 0, ',', '.') }}</span>
                </div>
            </div>

        </div>

        {{-- Line Items --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">
            <div class="flex items-center gap-3 border-b px-6 py-4">
                <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                    <x-icon name="box" class="size-4 text-primary" />
                </div>
                <h3 class="text-sm font-semibold">@lang('general.items')</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-muted/30">
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">#</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">@lang('general.product')</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">@lang('general.sku')</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.qty')</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.unit_price')</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.discount')</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.tax')</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.amount')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data->details as $i => $detail)
                            <tr class="border-b last:border-0">
                                <td class="px-4 py-3 text-muted-foreground">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-medium">{{ $detail->product_snapshot['name'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-muted-foreground">{{ $detail->product_snapshot['sku'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-muted-foreground">
                                    {{ $detail->discount_amount ? 'Rp ' . number_format($detail->discount_amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right text-muted-foreground">
                                    {{ $detail->tax_amount ? 'Rp ' . number_format($detail->tax_amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($detail->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="flex flex-col items-end gap-2 border-t px-6 py-4">
                <div class="flex items-center gap-8 text-sm">
                    <span class="text-muted-foreground">@lang('general.subtotal')</span>
                    <span class="font-medium w-40 text-right">Rp {{ number_format($data->subtotal_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-8 text-sm">
                    <span class="text-muted-foreground">@lang('general.discount')</span>
                    <span class="font-medium w-40 text-right">{{ $data->discount_amount ? 'Rp ' . number_format($data->discount_amount, 0, ',', '.') : '-' }}</span>
                </div>
                <div class="flex items-center gap-8 text-sm">
                    <span class="text-muted-foreground">@lang('general.tax')</span>
                    <span class="font-medium w-40 text-right">{{ $data->tax_amount ? 'Rp ' . number_format($data->tax_amount, 0, ',', '.') : '-' }}</span>
                </div>
                <div class="flex items-center gap-8 text-base font-semibold border-t pt-2 mt-1">
                    <span>@lang('general.total')</span>
                    <span class="w-40 text-right">Rp {{ number_format($data->amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Meta --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">
            <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.created_at')</span>
                    <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y | H:i') }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.created_by')</span>
                    <span class="text-sm font-medium">{{ $data->user_created_by->name ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
            <div class="flex gap-2 px-6 py-4">
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

    </div>
@endsection

@push('scripts')
<script>
function confirmCancel() {
    if (confirm('{{ __('general.confirm_cancel_invoice') }}')) {
        document.getElementById('cancel-form').submit();
    }
}
</script>
@endpush
