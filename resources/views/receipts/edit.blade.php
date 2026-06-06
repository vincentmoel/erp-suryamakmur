@extends('layouts.main', ['title' => __('general.edit_receipt')])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.edit_receipt')</h1>
            <p>{{ $data->code }}</p>
        </div>

        <form action="{{ route('receipts.update', ['encryptedId' => $encryptedId]) }}"
              method="POST"
              enctype="multipart/form-data"
              id="receipt-form">
            @csrf
            @method('PATCH')

            {{-- Header --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">
                <div class="flex items-center gap-3 border-b px-6 py-4">
                    <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                        <x-icon name="money" class="size-4 text-primary" />
                    </div>
                    <h3 class="text-sm font-semibold">@lang('general.receipt_information')</h3>
                </div>

                <div class="flex flex-col gap-6 p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="customer_id" :label="__('general.customer')" :required="true">
                            <x-form.single-select
                                name="customer_id"
                                :placeholder="__('general.select_customer_placeholder')"
                                :options="$customers->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()"
                                :selected="old('customer_id', $data->customer_id)" />
                        </x-form.field>

                        <x-form.field name="receipt_date" :label="__('general.receipt_date')" :required="true">
                            <input type="date"
                                   name="receipt_date"
                                   value="{{ old('receipt_date', $data->receipt_date->format('Y-m-d')) }}"
                                   class="input {{ $errors->has('receipt_date') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="payment_method" :label="__('general.payment_method')" :required="true">
                            <x-form.single-select
                                name="payment_method"
                                :placeholder="__('general.select_payment_method_placeholder')"
                                :options="collect($paymentMethods)->map(fn($m) => ['value' => $m->value, 'label' => $m->label()])->toArray()"
                                :selected="old('payment_method', $data->payment_method->value)" />
                        </x-form.field>

                        <x-form.field name="reference_number" :label="__('general.reference_number')">
                            <input type="text"
                                   name="reference_number"
                                   value="{{ old('reference_number', $data->reference_number) }}"
                                   placeholder="{{ __('general.reference_number_placeholder') }}"
                                   class="input {{ $errors->has('reference_number') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="notes" :label="__('general.notes')">
                            <textarea name="notes"
                                      rows="2"
                                      class="input {{ $errors->has('notes') ? 'border-destructive' : '' }}"
                                      style="height: auto; padding-top: 0.5rem; padding-bottom: 0.5rem;">{{ old('notes', $data->notes) }}</textarea>
                        </x-form.field>

                        <x-form.field name="image" :label="__('general.image')">
                            @if($data->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $data->image) }}"
                                         alt="Receipt image"
                                         class="max-h-24 rounded-md border object-contain">
                                </div>
                            @endif
                            <x-form.file-upload name="image" :max-size-mb="2" />
                        </x-form.field>
                    </div>
                </div>
            </div>

            {{-- Allocation Table --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">
                <div class="flex items-center gap-3 border-b px-6 py-4">
                    <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                        <x-icon name="invoice" class="size-4 text-primary" />
                    </div>
                    <h3 class="text-sm font-semibold">@lang('general.payment_allocations')</h3>
                </div>

                <div id="allocation-loading" class="hidden px-6 py-8 text-center text-sm text-muted-foreground">
                    <svg class="animate-spin inline size-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Loading invoices...
                </div>

                <div id="allocation-empty" class="hidden px-6 py-8 text-center text-sm text-muted-foreground">
                    @lang('general.no_open_invoices')
                </div>

                <div id="allocation-table-wrap">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-muted/30">
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">@lang('general.code')</th>
                                    <th class="px-4 py-3 text-left font-medium text-muted-foreground">@lang('general.invoice_date')</th>
                                    <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.total')</th>
                                    <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.paid_amount')</th>
                                    <th class="px-4 py-3 text-right font-medium text-muted-foreground">@lang('general.invoice_remaining')</th>
                                    <th class="px-4 py-3 text-right font-medium text-muted-foreground w-44">@lang('general.allocation_amount')</th>
                                </tr>
                            </thead>
                            <tbody id="allocation-tbody"></tbody>
                        </table>
                    </div>

                    @error('allocations')
                        <p class="px-6 py-2 text-sm text-destructive">{{ $message }}</p>
                    @enderror

                    <div class="flex items-center justify-end gap-8 border-t px-6 py-4 text-sm font-semibold">
                        <span>@lang('general.total_allocated')</span>
                        <span id="summary-total" class="w-44 text-right tabular-nums">Rp 0</span>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                <div class="flex items-center justify-end gap-2 px-6 py-4">
                    <a href="{{ route('receipts.show', ['encryptedId' => $encryptedId]) }}" class="btn btn-outline">
                        @lang('general.cancel')
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <x-icon name="save" class="size-3.5" />
                        @lang('general.update')
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    var receiptId       = {{ $data->id }};
    var ajaxUrl         = '{{ route('ajax.customers.invoices', ['id' => '__CID__']) }}';
    var tbody           = document.getElementById('allocation-tbody');
    var summaryTotal    = document.getElementById('summary-total');
    var loading         = document.getElementById('allocation-loading');
    var emptyMsg        = document.getElementById('allocation-empty');
    var tableWrap       = document.getElementById('allocation-table-wrap');

    // Pre-existing allocations indexed by invoice_id
    var existingAllocations = @json($data->details->keyBy('invoice_id')->map(fn($d) => $d->amount));

    function showState(state) {
        loading.classList.add('hidden');
        emptyMsg.classList.add('hidden');
        tableWrap.classList.add('hidden');
        if (state === 'loading') loading.classList.remove('hidden');
        if (state === 'empty')   emptyMsg.classList.remove('hidden');
        if (state === 'table')   tableWrap.classList.remove('hidden');
    }

    function calcTotal() {
        var total = 0;
        tbody.querySelectorAll('.alloc-input').forEach(function (inp) {
            total += parseInt(inp.value) || 0;
        });
        summaryTotal.textContent = formatRupiah(total);
    }

    function buildRow(inv) {
        var tr = document.createElement('tr');
        tr.className = 'border-b last:border-0';
        tr.dataset.remaining = inv.remaining_amount;
        var prefilledAmount = existingAllocations[inv.id] || 0;
        tr.innerHTML =
            '<td class="px-4 py-3 font-mono text-sm">' + inv.code + '</td>' +
            '<td class="px-4 py-3 text-muted-foreground">' + inv.invoice_date + '</td>' +
            '<td class="px-4 py-3 text-right tabular-nums">Rp ' + formatNum(inv.grand_total) + '</td>' +
            '<td class="px-4 py-3 text-right tabular-nums text-muted-foreground">Rp ' + formatNum(inv.paid_amount) + '</td>' +
            '<td class="px-4 py-3 text-right tabular-nums font-medium">Rp ' + formatNum(inv.remaining_amount) + '</td>' +
            '<td class="px-4 py-3">' +
                '<input type="hidden" name="allocations[' + inv.id + '][invoice_id]" value="' + inv.id + '">' +
                '<div class="flex items-center gap-1">' +
                    '<input type="text" class="input input-sm text-right alloc-display" placeholder="0" autocomplete="off"' +
                        (prefilledAmount ? ' value="' + prefilledAmount.toLocaleString("id-ID") + '"' : '') + '>' +
                    '<input type="hidden" name="allocations[' + inv.id + '][amount]" class="alloc-input" value="' + (prefilledAmount || '') + '">' +
                '</div>' +
            '</td>';

        var display = tr.querySelector('.alloc-display');
        var hidden  = tr.querySelector('.alloc-input');

        display.addEventListener('input', function () {
            var raw = parseMoney(this.value);
            var max = parseInt(tr.dataset.remaining);
            if (raw > max) raw = max;
            var cur  = this.selectionStart;
            var prev = this.value.length;
            this.value = raw ? raw.toLocaleString('id-ID') : '';
            var diff = this.value.length - prev;
            try { this.setSelectionRange(cur + diff, cur + diff); } catch(e) {}
            hidden.value = raw || '';
            calcTotal();
        });

        return tr;
    }

    function loadInvoices(customerId) {
        if (!customerId) { showState('empty'); return; }
        showState('loading');
        var url = ajaxUrl.replace('__CID__', customerId) + '?receipt_id=' + receiptId;
        fetch(url)
            .then(function (r) { return r.ok ? r.json() : []; })
            .then(function (invoices) {
                tbody.innerHTML = '';
                if (!invoices.length) { showState('empty'); return; }
                invoices.forEach(function (inv) { tbody.appendChild(buildRow(inv)); });
                calcTotal();
                showState('table');
            })
            .catch(function () { showState('empty'); });
    }

    var customerInput = document.querySelector('[name="customer_id"]');
    if (customerInput) {
        customerInput.addEventListener('change', function () {
            existingAllocations = {};  // clear prefilled on customer change
            loadInvoices(this.value);
        });
        // Load initial invoices for the pre-selected customer
        if (customerInput.value) loadInvoices(customerInput.value);
    }

    function formatNum(n) { return parseInt(n).toLocaleString('id-ID'); }
})();
</script>
@endpush
