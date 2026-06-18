@extends('layouts.main', ['title' => $data->code])

@section('content')
<div class="page-content">

    <div class="page-header">
        <h1>@lang('general.invoice_detail')</h1>
        <p>{{ $data->code }}</p>
    </div>

    <div class="flex gap-6 items-start">

        {{-- Kiri 70%: Invoice card --}}
        <div class="invoice-main rounded-lg border border-border bg-card shadow-xs overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <div>
                    <p class="text-xs text-muted-foreground uppercase tracking-wider">@lang('general.invoice')</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <h2 class="text-lg font-bold font-mono">{{ $data->code }}</h2>
                        <span class="badge {{ $data->status->badgeClass() }}">{{ $data->status->label() }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('invoices.pdf', ['encryptedId' => $encryptedId]) }}"
                       target="_blank"
                       class="btn btn-primary btn-sm">
                        <x-icon name="download" class="size-3.5" />
                        @lang('general.download_pdf')
                    </a>
                    <a href="{{ route('invoices.pdf', ['encryptedId' => $encryptedId]) }}"
                       target="_blank"
                       class="btn btn-secondary btn-sm">
                        <x-icon name="print" class="size-3.5" />
                        @lang('general.print')
                    </a>
                    <a href="mailto:{{ $data->customer_snapshot['email'] ?? '' }}?subject={{ urlencode(__('general.send_invoice_subject', ['code' => $data->code, 'company' => config('app.name')])) }}"
                       class="btn btn-secondary btn-sm">
                        <x-icon name="mail" class="size-3.5" />
                        @lang('general.send')
                    </a>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5" id="invoice-card">
                @include('invoices._preview', ['data' => $data])
            </div>

            {{-- Footer: edit & cancel --}}
            @if($data->status->canEdit() || $data->status->canCancel())
            <div class="flex items-center gap-2 px-6 py-4 border-t border-border">
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
            @endif

        </div>

        {{-- Kanan 30%: Payment history --}}
        <div class="invoice-side rounded-lg border border-border bg-card shadow-xs overflow-hidden">

            <div class="px-5 py-4 border-b border-border flex items-center gap-2">
                <x-icon name="receipt" class="size-4 text-muted-foreground" />
                <p class="text-sm font-semibold">@lang('general.payment_history')</p>
            </div>

            @php
                $receipts = $data->receiptDetails->filter(fn($d) => $d->receipt !== null)->sortByDesc(fn($d) => $d->receipt->receipt_date);
                $remaining = $data->amount - $data->paid_amount;
            @endphp

            <div class="px-5 py-4 space-y-4">

                {{-- Summary --}}
                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="flex-1 text-muted-foreground">@lang('general.total')</span>
                        <span class="font-medium">Rp {{ number_format($data->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="flex-1 text-muted-foreground">@lang('general.payment_received')</span>
                        <span class="font-medium">Rp {{ number_format($data->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    <hr class="border-border">
                    <div class="flex items-center gap-2 font-semibold">
                        <span class="flex-1">@lang('general.remaining_bill')</span>
                        @if($remaining > 0)
                            <span style="color: var(--destructive);">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                        @else
                            <span>Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div>

                {{-- List pembayaran --}}
                @if($receipts->isEmpty())
                    <div class="text-center py-6 text-xs text-muted-foreground">
                        @lang('general.no_payments_yet')
                    </div>
                @else
                    <div class="space-y-3 pt-1">
                        @foreach($receipts as $detail)
                            @php $receipt = $detail->receipt; @endphp
                            <div class="rounded-md border border-border p-4 text-sm space-y-2">
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('receipts.show', ['encryptedId' => \App\Helpers\Encryption::encrypt($receipt->id)]) }}"
                                       target="_blank"
                                       class="font-medium font-mono text-xs hover:underline">{{ $receipt->code }}</a>
                                    <span class="font-semibold">Rp {{ number_format($detail->amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs text-muted-foreground">
                                    <span>{{ $receipt->receipt_date->translatedFormat('d F Y') }}</span>
                                    <span>{{ $receipt->payment_method->label() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>

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
