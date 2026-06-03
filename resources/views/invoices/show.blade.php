@extends('layouts.main', ['title' => $data->code])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Invoice Detail</h1>
            <p>{{ $data->code }}</p>
        </div>

        {{-- Invoice Header --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">

            <div class="flex items-center justify-between gap-3 border-b px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                        <x-icon name="invoice" class="size-4 text-primary" />
                    </div>
                    <h3 class="text-sm font-semibold">Invoice Information</h3>
                </div>
                <span class="badge {{ $data->status->badgeClass() }}">{{ $data->status->label() }}</span>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Invoice Code</span>
                    <span class="text-sm font-medium font-mono">{{ $data->code }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Customer</span>
                    <span class="text-sm font-medium">{{ $data->customer->name ?? '-' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Salesperson</span>
                    <span class="text-sm font-medium">{{ $data->salesperson->name ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Invoice Date</span>
                    <span class="text-sm font-medium">{{ $data->invoice_date->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Due Date</span>
                    <span class="text-sm font-medium">{{ $data->due_date ? $data->due_date->translatedFormat('d F Y') : '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Paid Amount</span>
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
                <h3 class="text-sm font-semibold">Items</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-muted/30">
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">#</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Product</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">SKU</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Qty</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Unit Price</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Discount</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Tax</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Amount</th>
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
                    <span class="text-muted-foreground">Subtotal</span>
                    <span class="font-medium w-40 text-right">Rp {{ number_format($data->subtotal_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-8 text-sm">
                    <span class="text-muted-foreground">Discount</span>
                    <span class="font-medium w-40 text-right">{{ $data->discount_amount ? 'Rp ' . number_format($data->discount_amount, 0, ',', '.') : '-' }}</span>
                </div>
                <div class="flex items-center gap-8 text-sm">
                    <span class="text-muted-foreground">Tax</span>
                    <span class="font-medium w-40 text-right">{{ $data->tax_amount ? 'Rp ' . number_format($data->tax_amount, 0, ',', '.') : '-' }}</span>
                </div>
                <div class="flex items-center gap-8 text-base font-semibold border-t pt-2 mt-1">
                    <span>Total</span>
                    <span class="w-40 text-right">Rp {{ number_format($data->amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Meta --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">
            <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Created At</span>
                    <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y | H:i') }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Created By</span>
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
                        Edit
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
                            Cancel Invoice
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
    if (confirm('Are you sure you want to cancel this invoice? This action cannot be undone.')) {
        document.getElementById('cancel-form').submit();
    }
}
</script>
@endpush
