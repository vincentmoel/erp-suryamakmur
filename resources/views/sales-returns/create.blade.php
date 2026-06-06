@extends('layouts.main', ['title' => __('general.add_sales_return')])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.add_sales_return')</h1>
            <p>@lang('general.add_sales_return_desc')</p>
        </div>

        <form action="{{ route('sales-returns.store') }}" method="POST" id="return-form">
            @csrf

            {{-- Header --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">
                <div class="flex items-center gap-3 border-b px-6 py-4">
                    <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                        <x-icon name="return" class="size-4 text-primary" />
                    </div>
                    <h3 class="text-sm font-semibold">@lang('general.return_information')</h3>
                </div>

                <div class="flex flex-col gap-6 p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="invoice_id" :label="__('general.invoice')" :required="true">
                            <x-form.single-select
                                name="invoice_id"
                                :placeholder="__('general.select_invoice_placeholder')"
                                :options="$invoiceOptions"
                                :selected="old('invoice_id')" />
                        </x-form.field>

                        <x-form.field name="return_date" :label="__('general.return_date')" :required="true">
                            <input type="date"
                                   name="return_date"
                                   value="{{ old('return_date', now()->format('Y-m-d')) }}"
                                   class="input {{ $errors->has('return_date') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    <x-form.field name="notes" :label="__('general.notes')">
                        <textarea name="notes"
                                  rows="1"
                                  placeholder="{{ __('general.optional_notes_placeholder') }}"
                                  style="height: auto; padding-top: 0.5rem; padding-bottom: 0.5rem;"
                                  class="input {{ $errors->has('notes') ? 'border-destructive' : '' }}">{{ old('notes') }}</textarea>
                    </x-form.field>
                </div>
            </div>

            {{-- Returnable Batches --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4" id="batches-card" style="display:none;">
                <div class="flex items-center gap-3 border-b px-6 py-4">
                    <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                        <x-icon name="box" class="size-4 text-primary" />
                    </div>
                    <h3 class="text-sm font-semibold">@lang('general.returnable_batches')</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/30">
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">@lang('general.product')</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.unit_cost')</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.original_qty')</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.already_returned')</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.returnable_qty')</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.return_qty')</th>
                            </tr>
                        </thead>
                        <tbody id="batches-body">
                        </tbody>
                    </table>
                </div>

                <div id="batches-empty" class="px-6 py-8 text-center text-sm text-muted-foreground" style="display:none;">
                    @lang('general.no_returnable_batches')
                </div>
            </div>

            @if ($errors->has('details'))
                <p class="text-sm text-destructive mb-4">{{ $errors->first('details') }}</p>
            @endif

            @include('partials.form-actions-create')
        </form>

    </div>
@endsection

@push('scripts')
<script>
(function () {
    const invoiceSelect = document.querySelector('[name="invoice_id"]');
    const batchesCard   = document.getElementById('batches-card');
    const batchesBody   = document.getElementById('batches-body');
    const batchesEmpty  = document.getElementById('batches-empty');

    const encryptedIdMap = @json(collect($invoiceOptions)->pluck('encrypted_id', 'value'));

    function loadBatches(invoiceId) {
        const encryptedId = encryptedIdMap[invoiceId];
        if (!encryptedId) return;

        fetch(`/ajax/invoices/${encryptedId}/returnable-batches`)
            .then(res => res.json())
            .then(batches => {
                batchesBody.innerHTML = '';
                batchesCard.style.display = '';

                if (!batches.length) {
                    batchesEmpty.style.display = '';
                    return;
                }

                batchesEmpty.style.display = 'none';

                batches.forEach((batch, idx) => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-b last:border-0';
                    tr.innerHTML = `
                        <input type="hidden" name="details[${idx}][invoice_detail_batch_id]" value="${batch.invoice_detail_batch_id}">
                        <td class="px-4 py-3 font-medium">${batch.product_name}</td>
                        <td class="px-4 py-3 text-right text-muted-foreground">${batch.unit_cost_formatted}</td>
                        <td class="px-4 py-3 text-right">${batch.original_qty}</td>
                        <td class="px-4 py-3 text-right text-muted-foreground">${batch.already_returned}</td>
                        <td class="px-4 py-3 text-right font-medium">${batch.returnable_qty}</td>
                        <td class="px-4 py-3 text-right">
                            <input type="number"
                                   name="details[${idx}][quantity]"
                                   min="0"
                                   max="${batch.returnable_qty}"
                                   value="0"
                                   class="input text-right w-24">
                        </td>
                    `;
                    batchesBody.appendChild(tr);
                });
            });
    }

    if (invoiceSelect) {
        invoiceSelect.addEventListener('change', function () {
            loadBatches(this.value);
        });

        const oldVal = invoiceSelect.value;
        if (oldVal) loadBatches(oldVal);
    }

    // Strip rows with quantity=0 before submit to avoid validation noise
    document.getElementById('return-form').addEventListener('submit', function () {
        document.querySelectorAll('#batches-body tr').forEach((tr, idx) => {
            const qtyInput = tr.querySelector('input[type="number"]');
            if (qtyInput && parseInt(qtyInput.value, 10) === 0) {
                tr.querySelectorAll('input').forEach(inp => inp.disabled = true);
            }
        });
    });
}());
</script>
@endpush
