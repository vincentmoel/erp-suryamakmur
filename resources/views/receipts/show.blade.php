@extends('layouts.main', ['title' => $data->code])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.receipt_detail')</h1>
            <p>{{ $data->code }}</p>
        </div>

        {{-- Receipt Header --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">
            <div class="flex items-center gap-3 border-b px-6 py-4">
                <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                    <x-icon name="money" class="size-4 text-primary" />
                </div>
                <h3 class="text-sm font-semibold">@lang('general.receipt_information')</h3>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.receipt_code')</span>
                    <span class="text-sm font-medium font-mono">{{ $data->code }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.customer')</span>
                    <span class="text-sm font-medium">{{ $data->customer->name ?? '-' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.receipt_date')</span>
                    <span class="text-sm font-medium">{{ $data->receipt_date->translatedFormat('d F Y') }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.payment_method')</span>
                    <span class="text-sm font-medium">{{ $data->payment_method->label() }}</span>
                </div>
            </div>

            @if($data->reference_number || $data->notes)
            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.reference_number')</span>
                    <span class="text-sm font-medium">{{ $data->reference_number ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.notes')</span>
                    <span class="text-sm">{{ $data->notes ?? '-' }}</span>
                </div>
            </div>
            @endif

            @if($data->image)
            <div class="border-t px-6 py-4">
                <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide block mb-2">@lang('general.image')</span>
                <img src="{{ asset('storage/' . $data->image) }}"
                     alt="Receipt image"
                     class="max-h-48 rounded-md border object-contain">
            </div>
            @endif
        </div>

        {{-- Allocations --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">
            <div class="flex items-center gap-3 border-b px-6 py-4">
                <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                    <x-icon name="invoice" class="size-4 text-primary" />
                </div>
                <h3 class="text-sm font-semibold">@lang('general.payment_allocations')</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-muted/30">
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">#</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">@lang('general.invoice_code')</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">@lang('general.invoice_date')</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.total')</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.allocation_amount')</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">@lang('general.status')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data->details as $i => $detail)
                            <tr class="border-b last:border-0">
                                <td class="px-4 py-3 text-muted-foreground">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-mono">
                                    @if($detail->invoice)
                                        <a href="{{ route('invoices.show', \App\Helpers\Encryption::encrypt($detail->invoice_id)) }}"
                                           class="text-primary hover:underline">
                                            {{ $detail->invoice->code }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ $detail->invoice?->invoice_date?->translatedFormat('d M Y') ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums">
                                    {{ $detail->invoice ? 'Rp ' . number_format($detail->invoice->amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums font-medium">
                                    Rp {{ number_format($detail->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($detail->invoice)
                                        <span class="badge {{ $detail->invoice->status->badgeClass() }}">
                                            {{ $detail->invoice->status->label() }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-end gap-8 border-t px-6 py-4 text-sm font-semibold">
                <span>@lang('general.total_allocated')</span>
                <span class="w-44 text-right tabular-nums">Rp {{ number_format($data->amount_total, 0, ',', '.') }}</span>
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
                <a href="{{ route('receipts.edit', ['encryptedId' => $encryptedId]) }}"
                   class="btn btn-ghost btn-sm">
                    <x-icon name="edit" class="size-3.5" />
                    @lang('general.edit')
                </a>
            </div>
        </div>

    </div>
@endsection
