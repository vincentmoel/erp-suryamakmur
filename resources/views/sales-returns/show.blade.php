@extends('layouts.main', ['title' => $data->code])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.sales_return_detail')</h1>
            <p>{{ $data->code }}</p>
        </div>

        {{-- Header --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">

            <div class="flex items-center gap-3 border-b px-6 py-4">
                <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                    <x-icon name="return" class="size-4 text-primary" />
                </div>
                <h3 class="text-sm font-semibold">@lang('general.return_information')</h3>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.code')</span>
                    <span class="text-sm font-medium font-mono">{{ $data->code }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.invoice')</span>
                    <span class="text-sm font-medium">{{ $data->invoice->code ?? '-' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.return_date')</span>
                    <span class="text-sm font-medium">{{ $data->return_date->translatedFormat('d F Y') }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.notes')</span>
                    <span class="text-sm font-medium">{{ $data->notes ?? '-' }}</span>
                </div>
            </div>

        </div>

        {{-- Return Details --}}
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
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.unit_cost')</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.qty')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data->details as $i => $detail)
                            <tr class="border-b last:border-0">
                                <td class="px-4 py-3 text-muted-foreground">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-medium">
                                    {{ $detail->invoiceDetailBatch->invoiceDetail->product_snapshot['name'] ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right text-muted-foreground">
                                    Rp {{ number_format($detail->invoiceDetailBatch->unit_cost, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-medium">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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

    </div>
@endsection
