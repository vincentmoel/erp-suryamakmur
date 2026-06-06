@extends('layouts.main', ['title' => __('general.add_invoice')])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.add_invoice')</h1>
            <p>@lang('general.add_invoice_desc')</p>
        </div>
        
        <form action="{{ route('invoices.store') }}" method="POST" id="invoice-form">
            @csrf

            {{-- Header --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">
                <div class="flex items-center gap-3 border-b px-6 py-4">
                    <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                        <x-icon name="invoice" class="size-4 text-primary" />
                    </div>
                    <h3 class="text-sm font-semibold">@lang('general.invoice_information')</h3>
                </div>

                <div class="flex flex-col gap-6 p-6">

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="customer_id" :label="__('general.customer')" :required="true">
                            <x-form.single-select
                                name="customer_id"
                                :placeholder="__('general.select_customer_placeholder')"
                                :options="$customers->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()"
                                :selected="old('customer_id')" />

                            {{-- Customer info panel --}}
                            <div id="customer-info"
                                 class="hidden mt-2 rounded-md border bg-muted/40 px-3 py-3 text-xs text-muted-foreground space-y-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span id="ci-type" class="font-semibold text-foreground"></span>
                                    <span id="ci-company" class="hidden text-foreground/70"></span>
                                </div>
                                <div class="flex items-center gap-4 flex-wrap">
                                    <span id="ci-email"  class="hidden items-center gap-1.5"></span>
                                    <span id="ci-phone"  class="hidden items-center gap-1.5"></span>
                                    <span id="ci-mobile" class="hidden items-center gap-1.5"></span>
                                </div>
                                <div id="ci-tax-wrap" class="hidden items-center gap-1.5"></div>
                                <div id="ci-notes" class="hidden italic border-t border-border/50 pt-2 mt-0.5"></div>
                            </div>
                        </x-form.field>

                        <x-form.field name="salesperson_id" :label="__('general.salesperson')" :required="true">
                            <x-form.single-select
                                name="salesperson_id"
                                :placeholder="__('general.select_salesperson_placeholder')"
                                :options="$salespersons->map(fn($u) => ['value' => $u->id, 'label' => $u->name])->toArray()"
                                :selected="old('salesperson_id', auth()->id())" />
                        </x-form.field>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="invoice_date" :label="__('general.invoice_date')" :required="true">
                            <input type="date"
                                   name="invoice_date"
                                   value="{{ old('invoice_date', now()->format('Y-m-d')) }}"
                                   class="input {{ $errors->has('invoice_date') ? 'border-destructive' : '' }}">
                        </x-form.field>

                        <x-form.field name="due_date" :label="__('general.due_date')">
                            <input type="date"
                                   name="due_date"
                                   value="{{ old('due_date') }}"
                                   class="input {{ $errors->has('due_date') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    <x-form.field name="notes" :label="__('general.notes')">
                        <textarea name="notes"
                                  rows="3"
                                  class="input {{ $errors->has('notes') ? 'border-destructive' : '' }}"
                                  style="height: auto; padding-top: 0.5rem; padding-bottom: 0.5rem;"
                                  placeholder="{{ __('general.notes_placeholder') }}">{{ old('notes') }}</textarea>
                    </x-form.field>

                </div>
            </div>

            {{-- Line Items + Totals --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">

                <div class="flex items-center justify-between gap-3 border-b px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                            <x-icon name="box" class="size-4 text-primary" />
                        </div>
                        <h3 class="text-sm font-semibold">@lang('general.items')</h3>
                    </div>
                    <button type="button" id="add-row" class="btn btn-outline btn-sm">
                        <x-icon name="plus" class="size-3.5" />
                        @lang('general.add_item')
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/30">
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground min-w-52">@lang('general.product')</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-24">@lang('general.qty')</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-40">@lang('general.unit_price')</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-36">@lang('general.discount')</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-36">@lang('general.tax')</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-40">@lang('general.amount')</th>
                                <th class="px-4 py-3 w-10"></th>
                            </tr>
                            <style>
                                #items-tbody td { vertical-align: top; }
                            </style>
                        </thead>
                        <tbody id="items-tbody"></tbody>
                    </table>
                </div>

                {{-- Row template (cloned by JS, never displayed) --}}
                <template id="row-tpl">
                    <tr class="border-b last:border-0">
                        <td class="px-4 py-3">
                            <x-form.single-select
                                name="details[__INDEX__][product_id]"
                                :options="$productOptions"
                                :placeholder="__('general.select_product_placeholder')" />
                            <div class="mt-1 text-xs text-muted-foreground row-unit-info"></div>
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="details[__INDEX__][quantity]" class="input row-qty text-right" min="1" value="1" required>
                        </td>
                        <td class="px-4 py-3">
                            <div data-slot="input-group" class="group/input-group relative flex w-full items-center rounded-md border border-input shadow-xs h-9 min-w-0 has-[[data-slot=input-group-control]:focus-visible]:border-ring has-[[data-slot=input-group-control]:focus-visible]:ring-[3px] has-[[data-slot=input-group-control]:focus-visible]:ring-ring/50">
                                <span data-slot="input-group-addon" class="order-first pl-3 flex h-auto items-center text-sm font-medium text-muted-foreground select-none">Rp</span>
                                <input data-slot="input-group-control" type="text" class="row-price-display flex-1 rounded-none border-0 bg-transparent shadow-none focus-visible:ring-0 h-full px-2 text-sm outline-none text-right" placeholder="0" autocomplete="off">
                            </div>
                            <input type="hidden" name="details[__INDEX__][unit_price]" class="row-price" value="">
                        </td>
                        <td class="px-4 py-3">
                            <div data-slot="input-group" class="group/input-group relative flex w-full items-center rounded-md border border-input shadow-xs h-9 min-w-0 has-[[data-slot=input-group-control]:focus-visible]:border-ring has-[[data-slot=input-group-control]:focus-visible]:ring-[3px] has-[[data-slot=input-group-control]:focus-visible]:ring-ring/50 overflow-hidden">
                                <input data-slot="input-group-control" type="text" class="row-discount-display flex-1 rounded-none border-0 bg-transparent shadow-none focus-visible:ring-0 h-full px-2 text-sm outline-none text-right" placeholder="0" autocomplete="off">
                                <button type="button" data-mode="amount" class="row-discount-mode-btn shrink-0 w-10 h-full flex items-center justify-center text-xs font-bold bg-muted text-foreground hover:bg-muted/70 border-l border-input transition-colors select-none" tabindex="-1">Rp</button>
                            </div>
                            <input type="hidden" name="details[__INDEX__][discount_percent]" class="row-discount-pct" value="">
                            <input type="hidden" name="details[__INDEX__][discount_amount]" class="row-discount" value="">
                        </td>
                        <td class="px-4 py-3">
                            <div data-slot="input-group" class="group/input-group relative flex w-full items-center rounded-md border border-input shadow-xs h-9 min-w-0 has-[[data-slot=input-group-control]:focus-visible]:border-ring has-[[data-slot=input-group-control]:focus-visible]:ring-[3px] has-[[data-slot=input-group-control]:focus-visible]:ring-ring/50 overflow-hidden">
                                <input data-slot="input-group-control" type="text" class="row-tax-display flex-1 rounded-none border-0 bg-transparent shadow-none focus-visible:ring-0 h-full px-2 text-sm outline-none text-right" placeholder="0" autocomplete="off">
                                <button type="button" data-mode="amount" class="row-tax-mode-btn shrink-0 w-10 h-full flex items-center justify-center text-xs font-bold bg-muted text-foreground hover:bg-muted/70 border-l border-input transition-colors select-none" tabindex="-1">Rp</button>
                            </div>
                            <input type="hidden" name="details[__INDEX__][tax_percent]" class="row-tax-pct" value="">
                            <input type="hidden" name="details[__INDEX__][tax_amount]" class="row-tax" value="">
                        </td>
                        <td class="px-4 py-3 text-right">
                            <input type="hidden" name="details[__INDEX__][subtotal_amount]" class="row-subtotal-hidden" value="0">
                            <input type="hidden" name="details[__INDEX__][amount]" class="row-amount-hidden" value="0">
                            <div class="h-9 flex items-center justify-end">
                                <span class="row-amount-display font-medium tabular-nums">Rp 0</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="h-9 flex items-center justify-center">
                                <button type="button" class="btn-remove text-destructive transition-colors">
                                    <x-icon name="close" class="size-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>

                @error('details')
                    <p class="px-6 py-2 text-sm text-destructive">{{ $message }}</p>
                @enderror

                {{-- Totals --}}
                <div class="flex flex-col items-end gap-2 border-t px-6 py-4">

                    <div class="flex items-center gap-4 text-sm w-full justify-end">
                        <span class="text-muted-foreground w-24 text-right shrink-0">@lang('general.subtotal')</span>
                        <span class="font-medium w-44 text-right tabular-nums" id="summary-subtotal">Rp 0</span>
                    </div>

                    <div class="flex items-center gap-4 text-sm w-full justify-end mt-2">
                        <label for="inv-discount-display" class="text-muted-foreground w-24 text-right shrink-0 cursor-pointer">@lang('general.discount')</label>
                        <div class="w-44 shrink-0">
                            <div data-slot="input-group" class="group/input-group relative flex items-center rounded-md border shadow-xs h-9 overflow-hidden has-[[data-slot=input-group-control]:focus-visible]:border-ring has-[[data-slot=input-group-control]:focus-visible]:ring-[3px] has-[[data-slot=input-group-control]:focus-visible]:ring-ring/50 {{ $errors->has('discount_amount') ? 'border-destructive' : 'border-input' }}">
                                <input data-slot="input-group-control" type="text"
                                       id="inv-discount-display"
                                       placeholder="0"
                                       autocomplete="off"
                                       class="flex-1 min-w-0 rounded-none border-0 bg-transparent shadow-none focus-visible:ring-0 h-full px-2 text-sm outline-none text-right">
                                <button type="button" id="inv-discount-mode-btn" data-mode="amount"
                                        class="shrink-0 w-10 h-full flex items-center justify-center text-xs font-bold bg-muted text-foreground hover:bg-muted/70 border-l border-input transition-colors select-none"
                                        tabindex="-1">Rp</button>
                            </div>
                            <input type="hidden" name="discount_percent" id="inv-discount-pct" value="{{ old('discount_percent') }}">
                            <input type="hidden" name="discount_amount" id="inv-discount" value="{{ old('discount_amount') }}">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-sm w-full justify-end">
                        <label for="inv-tax-display" class="text-muted-foreground w-24 text-right shrink-0 cursor-pointer">@lang('general.tax')</label>
                        <div class="w-44 shrink-0">
                            <div data-slot="input-group" class="group/input-group relative flex items-center rounded-md border shadow-xs h-9 overflow-hidden has-[[data-slot=input-group-control]:focus-visible]:border-ring has-[[data-slot=input-group-control]:focus-visible]:ring-[3px] has-[[data-slot=input-group-control]:focus-visible]:ring-ring/50 {{ $errors->has('tax_amount') ? 'border-destructive' : 'border-input' }}">
                                <input data-slot="input-group-control" type="text"
                                       id="inv-tax-display"
                                       placeholder="0"
                                       autocomplete="off"
                                       class="flex-1 min-w-0 rounded-none border-0 bg-transparent shadow-none focus-visible:ring-0 h-full px-2 text-sm outline-none text-right">
                                <button type="button" id="inv-tax-mode-btn" data-mode="amount"
                                        class="shrink-0 w-10 h-full flex items-center justify-center text-xs font-bold bg-muted text-foreground hover:bg-muted/70 border-l border-input transition-colors select-none"
                                        tabindex="-1">Rp</button>
                            </div>
                            <input type="hidden" name="tax_percent" id="inv-tax-pct" value="{{ old('tax_percent') }}">
                            <input type="hidden" name="tax_amount" id="inv-tax" value="{{ old('tax_amount') }}">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 w-full justify-end border-t pt-3 mt-1">
                        <span class="font-semibold w-24 text-right shrink-0">@lang('general.total')</span>
                        <span class="font-semibold text-base w-44 text-right tabular-nums" id="summary-total">Rp 0</span>
                    </div>

                </div>

            </div>

            {{-- Form Actions --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                <div class="flex items-center justify-end gap-2 px-6 py-4">
                    <button type="submit" name="status" value="draft" class="btn btn-outline">
                        <x-icon name="save" class="size-3.5" />
                        @lang('general.save_as_draft')
                    </button>
                    <button type="submit" name="status" value="waiting_for_payment" class="btn btn-primary">
                        <x-icon name="check" class="size-3.5" />
                        @lang('general.save')
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    // ── Customer info panel ───────────────────────────────────
    (function () {
        var customerMap = {};
        @foreach($customers as $c)
        customerMap[{{ $c->id }}] = {
            type:         {{ Js::from($c->type->label()) }},
            company_name: {{ Js::from($c->company_name) }},
            tax_number:   {{ Js::from($c->tax_number) }},
            email:        {{ Js::from($c->email) }},
            phone:        {{ Js::from($c->phone) }},
            mobile:       {{ Js::from($c->mobile) }},
            notes:        {{ Js::from($c->notes) }},
        };
        @endforeach

        var iconMail     = {!! Js::from((string) str(view('components.icon', ['name' => 'mail',       'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};
        var iconPhone    = {!! Js::from((string) str(view('components.icon', ['name' => 'phone',      'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};
        var iconMobile   = {!! Js::from((string) str(view('components.icon', ['name' => 'smartphone', 'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};
        var iconReceipt  = {!! Js::from((string) str(view('components.icon', ['name' => 'receipt',    'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};

        var panel     = document.getElementById('customer-info');
        var ciType    = document.getElementById('ci-type');
        var ciCompany = document.getElementById('ci-company');
        var ciEmail   = document.getElementById('ci-email');
        var ciPhone   = document.getElementById('ci-phone');
        var ciMobile  = document.getElementById('ci-mobile');
        var ciTaxWrap = document.getElementById('ci-tax-wrap');
        var ciNotes   = document.getElementById('ci-notes');

        function setField(el, iconHtml, text) {
            if (text) {
                el.innerHTML = iconHtml + '<span>' + text + '</span>';
                el.classList.remove('hidden');
                el.classList.add('flex');
            } else {
                el.classList.add('hidden');
                el.classList.remove('flex');
            }
        }

        function showCustomer(id) {
            var data = customerMap[id];
            if (!id || !data) { panel.classList.add('hidden'); return; }
            ciType.textContent = data.type;
            setField(ciCompany, '',           data.company_name);
            setField(ciEmail,   iconMail,     data.email);
            setField(ciPhone,   iconPhone,    data.phone);
            setField(ciMobile,  iconMobile,   data.mobile);
            if (data.tax_number) {
                ciTaxWrap.innerHTML = iconReceipt + '<span>NPWP: ' + data.tax_number + '</span>';
                ciTaxWrap.classList.remove('hidden');
                ciTaxWrap.classList.add('flex');
            } else {
                ciTaxWrap.classList.add('hidden');
                ciTaxWrap.classList.remove('flex');
            }
            if (data.notes) {
                ciNotes.textContent = data.notes;
                ciNotes.classList.remove('hidden');
            } else {
                ciNotes.classList.add('hidden');
            }
            panel.classList.remove('hidden');
        }

        var customerInput = document.querySelector('[name="customer_id"]');
        if (customerInput) {
            customerInput.addEventListener('change', function () { showCustomer(this.value); });
            if (customerInput.value) showCustomer(customerInput.value);
        }
    })();

    // ── Row calc ──────────────────────────────────────────────
    var tbody   = document.getElementById('items-tbody');
    var addBtn  = document.getElementById('add-row');
    var invDisc = document.getElementById('inv-discount');
    var invTax  = document.getElementById('inv-tax');

    var productOptions   = @json($productOptions);
    var productAjaxUrl   = '{{ route('ajax.products.info', ['id' => '__ID__']) }}';
    var productPlaceholder = '{{ __('general.select_product_placeholder') }}';
    var rowIndex = 0;

    function calcRow(row) {
        var qty      = parseInt(row.querySelector('.row-qty').value) || 0;
        var price    = parseInt(row.querySelector('.row-price').value) || 0;
        var subtotal = qty * price;

        var discModeBtn = row.querySelector('.row-discount-mode-btn');
        var discMode    = discModeBtn ? discModeBtn.dataset.mode : 'amount';
        var discAmount;
        if (discMode === 'percent') {
            var discPct = parseFloat(row.querySelector('.row-discount-display').value) || 0;
            discAmount  = Math.round(subtotal * discPct / 100);
            row.querySelector('.row-discount').value     = discAmount;
            row.querySelector('.row-discount-pct').value = discPct || '';
        } else {
            discAmount = parseInt(row.querySelector('.row-discount').value) || 0;
            row.querySelector('.row-discount-pct').value = '';
        }

        var taxModeBtn = row.querySelector('.row-tax-mode-btn');
        var taxMode    = taxModeBtn ? taxModeBtn.dataset.mode : 'amount';
        var taxAmount;
        if (taxMode === 'percent') {
            var pct   = parseFloat(row.querySelector('.row-tax-display').value) || 0;
            taxAmount = Math.round((subtotal - discAmount) * pct / 100);
            row.querySelector('.row-tax').value     = taxAmount;
            row.querySelector('.row-tax-pct').value = pct || '';
        } else {
            taxAmount = parseInt(row.querySelector('.row-tax').value) || 0;
            row.querySelector('.row-tax-pct').value = '';
        }

        var amount = subtotal - discAmount + taxAmount;
        row.querySelector('.row-subtotal-hidden').value = subtotal;
        row.querySelector('.row-amount-hidden').value   = amount;
        row.querySelector('.row-amount-display').textContent = formatRupiah(amount);
    }

    function calcTotals() {
        var subtotal = 0;
        tbody.querySelectorAll('tr').forEach(function (row) {
            subtotal += parseInt(row.querySelector('.row-amount-hidden').value) || 0;
        });

        var discModeBtn = document.getElementById('inv-discount-mode-btn');
        var discMode    = discModeBtn ? discModeBtn.dataset.mode : 'amount';
        var discAmount;
        if (discMode === 'percent') {
            var discPct = parseFloat(document.getElementById('inv-discount-display').value) || 0;
            discAmount  = Math.round(subtotal * discPct / 100);
            invDisc.value = discAmount;
            document.getElementById('inv-discount-pct').value = discPct || '';
        } else {
            discAmount = parseInt(invDisc.value) || 0;
            document.getElementById('inv-discount-pct').value = '';
        }

        var invTaxModeBtn = document.getElementById('inv-tax-mode-btn');
        var taxMode = invTaxModeBtn ? invTaxModeBtn.dataset.mode : 'amount';
        var taxAmount;
        if (taxMode === 'percent') {
            var pct   = parseFloat(document.getElementById('inv-tax-display').value) || 0;
            taxAmount = Math.round((subtotal - discAmount) * pct / 100);
            invTax.value = taxAmount;
            document.getElementById('inv-tax-pct').value = pct || '';
        } else {
            taxAmount = parseInt(invTax.value) || 0;
            document.getElementById('inv-tax-pct').value = '';
        }

        var total = subtotal - discAmount + taxAmount;
        document.getElementById('summary-subtotal').textContent = formatRupiah(subtotal);
        document.getElementById('summary-total').textContent    = formatRupiah(total);
    }

    // ── Row builder ───────────────────────────────────────────
    function addRow(data) {
        var i = rowIndex++;
        var d = data || {};

        // Clone the Blade-rendered template row
        var tpl = document.getElementById('row-tpl');
        var tr  = tpl.content.cloneNode(true).querySelector('tr');

        // Replace __INDEX__ in all name attributes
        tr.querySelectorAll('[name*="__INDEX__"]').forEach(function (el) {
            el.name = el.name.replace(/__INDEX__/g, i);
        });

        // Remove scripts cloned from the template — initSingleSelect is called manually below
        tr.querySelectorAll('script').forEach(function (s) { s.remove(); });

        var selectRoot = tr.querySelector('[data-single-select]');

        // Pre-select product if provided (before append, DOM manipulation only)
        if (d.product_id) {
            var hiddenInput = selectRoot.querySelector('[data-ss-input]');
            var labelEl     = selectRoot.querySelector('[data-ss-label]');
            var clearBtn    = selectRoot.querySelector('[data-ss-clear]');
            var found       = productOptions.find(function (o) { return String(o.value) === String(d.product_id); });

            hiddenInput.value = d.product_id;
            if (found) {
                labelEl.textContent = found.label;
                labelEl.classList.remove('text-muted-foreground');
                if (clearBtn) clearBtn.classList.remove('hidden');
            }
            selectRoot.querySelectorAll('[data-ss-item]').forEach(function (item) {
                item.setAttribute('aria-selected', String(item.dataset.value) === String(d.product_id) ? 'true' : 'false');
            });
        }

        // Set initial field values
        if (d.quantity) tr.querySelector('.row-qty').value = d.quantity;
        if (d.subtotal_amount) tr.querySelector('.row-subtotal-hidden').value = d.subtotal_amount;
        if (d.amount) {
            tr.querySelector('.row-amount-hidden').value = d.amount;
            tr.querySelector('.row-amount-display').textContent = formatRupiah(d.amount);
        }

        tbody.appendChild(tr);

        // Init single-select after element is in DOM
        initSingleSelect(selectRoot, productPlaceholder);

        // Wire product change → AJAX (unit info)
        var unitInfo = tr.querySelector('.row-unit-info');
        var ssInput  = tr.querySelector('[data-ss-input]');

        function fetchProductInfo(pid, skipPrice) {
            if (!pid) { unitInfo.textContent = ''; return; }
            fetch(productAjaxUrl.replace('__ID__', pid))
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (res) {
                    if (!res) { unitInfo.textContent = ''; return; }
                    unitInfo.textContent = res.stock != null ? '{{ __('general.stock') }}: ' + res.stock + (res.unit ? ' ' + res.unit : '') : '';
                    if (!skipPrice) {
                        var price = parseInt(res.selling_price) || 0;
                        var priceHidden  = tr.querySelector('.row-price');
                        var priceDisplay = tr.querySelector('.row-price-display');
                        priceHidden.value  = price;
                        priceDisplay.value = price ? price.toLocaleString('id-ID') : '';
                        calcRow(tr); calcTotals();
                    }
                });
        }

        ssInput.addEventListener('change', function () { fetchProductInfo(this.value, false); });

        // Re-fetch unit info when restoring from old() — price already restored, only need unit label
        if (d.product_id) fetchProductInfo(d.product_id, true);

        bindRowMoney(tr.querySelector('.row-price-display'), tr.querySelector('.row-price'), d.unit_price, tr);
        bindRowTax(tr.querySelector('.row-discount-display'), tr.querySelector('.row-discount-pct'), tr.querySelector('.row-discount'), tr.querySelector('.row-discount-mode-btn'), tr, d.discount_percent, d.discount_amount);
        bindRowTax(tr.querySelector('.row-tax-display'), tr.querySelector('.row-tax-pct'), tr.querySelector('.row-tax'), tr.querySelector('.row-tax-mode-btn'), tr, d.tax_percent, d.tax_amount);

        tr.querySelector('.row-qty').addEventListener('input', function () { calcRow(tr); calcTotals(); });
        tr.querySelector('.btn-remove').addEventListener('click', function () { tr.remove(); updateRemoveButtons(); calcTotals(); });

        if (d.quantity && d.unit_price) calcRow(tr);
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        var rows = tbody.querySelectorAll('tr');
        rows.forEach(function (row) {
            var btn = row.querySelector('.btn-remove');
            if (rows.length <= 1) {
                btn.disabled = true;
                btn.classList.add('opacity-30', 'cursor-not-allowed');
            } else {
                btn.disabled = false;
                btn.classList.remove('opacity-30', 'cursor-not-allowed');
            }
        });
    }

    function bindRowTax(displayEl, hiddenPct, hiddenAmt, modeBtn, row, initPct, initAmt) {
        if (initPct) {
            modeBtn.dataset.mode = 'percent';
            modeBtn.textContent  = '%';
            displayEl.value      = initPct;
            hiddenPct.value      = initPct;
        } else if (initAmt) {
            var n = parseInt(initAmt, 10);
            if (n) displayEl.value = n.toLocaleString('id-ID');
            hiddenAmt.value = initAmt;
        }

        modeBtn.addEventListener('click', function () {
            var newMode = this.dataset.mode === 'amount' ? 'percent' : 'amount';
            this.dataset.mode = newMode;
            this.textContent  = newMode === 'percent' ? '%' : 'Rp';
            displayEl.value   = '';
            hiddenAmt.value   = '';
            hiddenPct.value   = '';
            displayEl.placeholder = '0';
            calcRow(row);
            calcTotals();
        });

        displayEl.addEventListener('input', function () {
            var mode = modeBtn.dataset.mode;
            if (mode === 'percent') {
                var val = this.value.replace(/[^0-9.]/g, '');
                var dots = val.match(/\./g);
                if (dots && dots.length > 1) val = val.substring(0, val.lastIndexOf('.'));
                if (parseFloat(val) > 100) val = '100';
                this.value      = val;
                hiddenPct.value = val;
                hiddenAmt.value = '';
            } else {
                var raw  = parseMoney(this.value);
                var cur  = this.selectionStart;
                var prev = this.value.length;
                this.value = raw ? raw.toLocaleString('id-ID') : '';
                var diff = this.value.length - prev;
                try { this.setSelectionRange(cur + diff, cur + diff); } catch(e) {}
                hiddenAmt.value = raw || '';
                hiddenPct.value = '';
            }
            calcRow(row);
            calcTotals();
        });
    }

    function bindRowMoney(displayEl, hiddenEl, initVal, row) {
        if (initVal) {
            var n = parseInt(initVal, 10);
            if (n) { displayEl.value = n.toLocaleString('id-ID'); hiddenEl.value = n; }
        }
        displayEl.addEventListener('input', function () {
            var raw  = parseMoney(this.value);
            var cur  = this.selectionStart;
            var prev = this.value.length;
            this.value = raw ? raw.toLocaleString('id-ID') : '';
            var diff = this.value.length - prev;
            try { this.setSelectionRange(cur + diff, cur + diff); } catch(e) {}
            hiddenEl.value = raw || '';
            calcRow(row);
            calcTotals();
        });
    }

    addBtn.addEventListener('click', function () { addRow(); });

    document.getElementById('invoice-form').addEventListener('submit', function () {
        tbody.querySelectorAll('tr').forEach(function (row) {
            var pid = row.querySelector('[data-ss-input]');
            if (pid && !pid.value) row.remove();
        });
    });

    // Bind invoice-level discount with % / Rp toggle
    (function () {
        var displayEl = document.getElementById('inv-discount-display');
        var hiddenPct = document.getElementById('inv-discount-pct');
        var modeBtn   = document.getElementById('inv-discount-mode-btn');
        var initPct   = hiddenPct.value;
        var initAmt   = invDisc.value;

        if (initPct) {
            modeBtn.dataset.mode = 'percent';
            modeBtn.textContent  = '%';
            displayEl.value      = initPct;
        } else if (initAmt) {
            var n = parseInt(initAmt, 10);
            if (n) displayEl.value = n.toLocaleString('id-ID');
        }

        modeBtn.addEventListener('click', function () {
            var newMode = this.dataset.mode === 'amount' ? 'percent' : 'amount';
            this.dataset.mode = newMode;
            this.textContent  = newMode === 'percent' ? '%' : 'Rp';
            displayEl.value   = '';
            invDisc.value     = '';
            hiddenPct.value   = '';
            calcTotals();
        });

        displayEl.addEventListener('input', function () {
            var mode = modeBtn.dataset.mode;
            if (mode === 'percent') {
                var val = this.value.replace(/[^0-9.]/g, '');
                var dots = val.match(/\./g);
                if (dots && dots.length > 1) val = val.substring(0, val.lastIndexOf('.'));
                if (parseFloat(val) > 100) val = '100';
                this.value      = val;
                hiddenPct.value = val;
                invDisc.value   = '';
            } else {
                var raw  = parseMoney(this.value);
                var cur  = this.selectionStart;
                var prev = this.value.length;
                this.value = raw ? raw.toLocaleString('id-ID') : '';
                var diff = this.value.length - prev;
                try { this.setSelectionRange(cur + diff, cur + diff); } catch(e) {}
                invDisc.value   = raw || '';
                hiddenPct.value = '';
            }
            calcTotals();
        });
    })();

    // Bind invoice-level tax with % / Rp toggle
    (function () {
        var displayEl = document.getElementById('inv-tax-display');
        var hiddenPct = document.getElementById('inv-tax-pct');
        var modeBtn   = document.getElementById('inv-tax-mode-btn');
        var initPct   = hiddenPct.value;
        var initAmt   = invTax.value;

        if (initPct) {
            modeBtn.dataset.mode = 'percent';
            modeBtn.textContent  = '%';
            displayEl.value      = initPct;
        } else if (initAmt) {
            var n = parseInt(initAmt, 10);
            if (n) displayEl.value = n.toLocaleString('id-ID');
        }

        modeBtn.addEventListener('click', function () {
            var newMode = this.dataset.mode === 'amount' ? 'percent' : 'amount';
            this.dataset.mode = newMode;
            this.textContent  = newMode === 'percent' ? '%' : 'Rp';
            displayEl.value   = '';
            invTax.value      = '';
            hiddenPct.value   = '';
            calcTotals();
        });

        displayEl.addEventListener('input', function () {
            var mode = modeBtn.dataset.mode;
            if (mode === 'percent') {
                var val = this.value.replace(/[^0-9.]/g, '');
                var dots = val.match(/\./g);
                if (dots && dots.length > 1) val = val.substring(0, val.lastIndexOf('.'));
                if (parseFloat(val) > 100) val = '100';
                this.value      = val;
                hiddenPct.value = val;
                invTax.value    = '';
            } else {
                var raw  = parseMoney(this.value);
                var cur  = this.selectionStart;
                var prev = this.value.length;
                this.value = raw ? raw.toLocaleString('id-ID') : '';
                var diff = this.value.length - prev;
                try { this.setSelectionRange(cur + diff, cur + diff); } catch(e) {}
                invTax.value    = raw || '';
                hiddenPct.value = '';
            }
            calcTotals();
        });
    })();

    // Init rows
    @if(old('details'))
        @foreach(old('details', []) as $detail)
            addRow({
                product_id:       {{ $detail['product_id'] ?? 'null' }},
                quantity:         {{ $detail['quantity'] ?? 1 }},
                unit_price:       {{ $detail['unit_price'] ?? 0 }},
                discount_percent: {{ isset($detail['discount_percent']) && $detail['discount_percent'] !== '' ? $detail['discount_percent'] : 'null' }},
                discount_amount:  {{ $detail['discount_amount'] ?? 0 }},
                tax_percent:      {{ isset($detail['tax_percent']) && $detail['tax_percent'] !== '' ? $detail['tax_percent'] : 'null' }},
                tax_amount:       {{ $detail['tax_amount'] ?? 0 }},
                subtotal_amount:  {{ $detail['subtotal_amount'] ?? 0 }},
                amount:           {{ $detail['amount'] ?? 0 }},
            });
        @endforeach
        calcTotals();
    @else
        addRow(); addRow(); addRow();
    @endif
})();
</script>
@endpush
