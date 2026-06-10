@extends('layouts.main', ['title' => __('general.edit_bill')])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.edit_bill')</h1>
            <p>{{ $data->code }}</p>
        </div>

        <form action="{{ route('bills.update', ['encryptedId' => $encryptedId]) }}" method="POST" id="bill-form">
            @csrf
            @method('PATCH')

            {{-- Header --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">
                <div class="flex items-center gap-3 border-b px-6 py-5">
                    <x-icon name="receipt" class="size-5 text-primary" />
                    <h3 class="text-sm font-semibold">@lang('general.bill_information')</h3>
                </div>

                <div class="flex flex-col gap-6 p-6">

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="vendor_id" :label="__('general.vendor')" :required="true">
                            <x-form.single-select
                                name="vendor_id"
                                :placeholder="__('general.select_vendor_placeholder')"
                                :options="$vendorOptions"
                                :selected="old('vendor_id', $data->vendor_id)" />

                            {{-- Vendor info panel --}}
                            <div id="vendor-info"
                                 class="hidden mt-2 rounded-md border bg-muted/40 px-3 py-3 text-xs text-muted-foreground space-y-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span id="vi-type" class="font-semibold text-foreground"></span>
                                </div>
                                <div class="flex items-center gap-4 flex-wrap">
                                    <span id="vi-contact" class="hidden items-center gap-1.5"></span>
                                    <span id="vi-email"   class="hidden items-center gap-1.5"></span>
                                    <span id="vi-phone"   class="hidden items-center gap-1.5"></span>
                                </div>
                                <div id="vi-tax-wrap" class="hidden items-center gap-1.5"></div>
                                <div id="vi-notes" class="hidden italic border-t border-border/50 pt-2 mt-0.5"></div>
                            </div>
                        </x-form.field>

                        <x-form.field name="invoice_number" :label="__('general.vendor_invoice_no')">
                            <input type="text"
                                   name="invoice_number"
                                   value="{{ old('invoice_number', $data->invoice_number) }}"
                                   placeholder="e.g. INV-12345"
                                   class="input {{ $errors->has('invoice_number') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="bill_date" :label="__('general.bill_date')" :required="true">
                            <input type="date"
                                   name="bill_date"
                                   value="{{ old('bill_date', $data->bill_date?->format('Y-m-d')) }}"
                                   class="input {{ $errors->has('bill_date') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    <x-form.field name="notes" :label="__('general.notes')">
                        <textarea name="notes"
                                  rows="3"
                                  class="input {{ $errors->has('notes') ? 'border-destructive' : '' }}"
                                  style="height: auto; padding-top: 0.5rem; padding-bottom: 0.5rem;"
                                  placeholder="{{ __('general.notes_placeholder') }}">{{ old('notes', $data->notes) }}</textarea>
                    </x-form.field>

                </div>
            </div>

            {{-- Line Items + Totals --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">

                <div class="flex items-center justify-between gap-3 border-b px-6 py-5">
                    <div class="flex items-center gap-3">
                        <x-icon name="box" class="size-5 text-primary" />
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
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-40">@lang('general.unit_price_modal')</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-36">@lang('general.discount')</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-40">@lang('general.subtotal')</th>
                                <th class="px-4 py-3 w-10"></th>
                            </tr>
                            <style>#items-tbody td { vertical-align: top; }</style>
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
                        <td class="px-4 py-3 text-right">
                            <input type="hidden" name="details[__INDEX__][subtotal]" class="row-subtotal-hidden" value="0">
                            <div class="h-9 flex items-center justify-end">
                                <span class="row-subtotal-display font-medium tabular-nums">Rp 0</span>
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
                        <label for="pur-discount-display" class="text-muted-foreground w-24 text-right shrink-0 cursor-pointer">@lang('general.discount')</label>
                        <div class="w-44 shrink-0">
                            <div data-slot="input-group" class="group/input-group relative flex items-center rounded-md border shadow-xs h-9 overflow-hidden has-[[data-slot=input-group-control]:focus-visible]:border-ring has-[[data-slot=input-group-control]:focus-visible]:ring-[3px] has-[[data-slot=input-group-control]:focus-visible]:ring-ring/50 {{ $errors->has('discount_amount') ? 'border-destructive' : 'border-input' }}">
                                <input data-slot="input-group-control" type="text"
                                       id="pur-discount-display"
                                       placeholder="0"
                                       autocomplete="off"
                                       class="flex-1 min-w-0 rounded-none border-0 bg-transparent shadow-none focus-visible:ring-0 h-full px-2 text-sm outline-none text-right">
                                <button type="button" id="pur-discount-mode-btn" data-mode="amount"
                                        class="shrink-0 w-10 h-full flex items-center justify-center text-xs font-bold bg-muted text-foreground hover:bg-muted/70 border-l border-input transition-colors select-none"
                                        tabindex="-1">Rp</button>
                            </div>
                            <input type="hidden" name="discount_percent" id="pur-discount-pct" value="{{ old('discount_percent', $data->discount_percent) }}">
                            <input type="hidden" name="discount_amount" id="pur-discount" value="{{ old('discount_amount', $data->discount_amount) }}">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-sm w-full justify-end">
                        <label for="pur-tax-display" class="text-muted-foreground w-24 text-right shrink-0 cursor-pointer">@lang('general.tax')</label>
                        <div class="w-44 shrink-0">
                            <div data-slot="input-group" class="group/input-group relative flex items-center rounded-md border shadow-xs h-9 overflow-hidden has-[[data-slot=input-group-control]:focus-visible]:border-ring has-[[data-slot=input-group-control]:focus-visible]:ring-[3px] has-[[data-slot=input-group-control]:focus-visible]:ring-ring/50 {{ $errors->has('tax_amount') ? 'border-destructive' : 'border-input' }}">
                                <input data-slot="input-group-control" type="text"
                                       id="pur-tax-display"
                                       placeholder="0"
                                       autocomplete="off"
                                       class="flex-1 min-w-0 rounded-none border-0 bg-transparent shadow-none focus-visible:ring-0 h-full px-2 text-sm outline-none text-right">
                                <button type="button" id="pur-tax-mode-btn" data-mode="amount"
                                        class="shrink-0 w-10 h-full flex items-center justify-center text-xs font-bold bg-muted text-foreground hover:bg-muted/70 border-l border-input transition-colors select-none"
                                        tabindex="-1">Rp</button>
                            </div>
                            <input type="hidden" name="tax_percent" id="pur-tax-pct" value="{{ old('tax_percent', $data->tax_percent > 0 ? $data->tax_percent : '') }}">
                            <input type="hidden" name="tax_amount" id="pur-tax" value="{{ old('tax_amount', $data->tax_amount) }}">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 w-full justify-end border-t pt-3 mt-1">
                        <span class="font-semibold w-24 text-right shrink-0">@lang('general.grand_total')</span>
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
                    <button type="submit" name="status" value="ordered" class="btn btn-primary">
                        <x-icon name="check" class="size-3.5" />
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
    // ── Vendor info panel ─────────────────────────────────────
    (function () {
        var vendorMap = {};
        @foreach($vendors as $v)
        vendorMap[{{ $v->id }}] = {
            type:           {{ Js::from($v->type->label()) }},
            tax_number:     {{ Js::from($v->tax_number) }},
            email:          {{ Js::from($v->email) }},
            phone:          {{ Js::from($v->phone) }},
            contact_person: {{ Js::from($v->contact_person) }},
            notes:          {{ Js::from($v->notes) }},
        };
        @endforeach

        var iconMail    = {!! Js::from((string) str(view('components.icon', ['name' => 'mail',    'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};
        var iconPhone   = {!! Js::from((string) str(view('components.icon', ['name' => 'phone',   'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};
        var iconContact = {!! Js::from((string) str(view('components.icon', ['name' => 'user',    'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};
        var iconReceipt = {!! Js::from((string) str(view('components.icon', ['name' => 'receipt', 'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};

        var panel     = document.getElementById('vendor-info');
        var viType    = document.getElementById('vi-type');
        var viContact = document.getElementById('vi-contact');
        var viEmail   = document.getElementById('vi-email');
        var viPhone   = document.getElementById('vi-phone');
        var viTaxWrap = document.getElementById('vi-tax-wrap');
        var viNotes   = document.getElementById('vi-notes');

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

        function showVendor(id) {
            var data = vendorMap[id];
            if (!id || !data) { panel.classList.add('hidden'); return; }
            viType.textContent = data.type;
            setField(viContact, iconContact, data.contact_person);
            setField(viEmail,   iconMail,    data.email);
            setField(viPhone,   iconPhone,   data.phone);
            if (data.tax_number) {
                viTaxWrap.innerHTML = iconReceipt + '<span>NPWP: ' + data.tax_number + '</span>';
                viTaxWrap.classList.remove('hidden');
                viTaxWrap.classList.add('flex');
            } else {
                viTaxWrap.classList.add('hidden');
                viTaxWrap.classList.remove('flex');
            }
            if (data.notes) {
                viNotes.textContent = data.notes;
                viNotes.classList.remove('hidden');
            } else {
                viNotes.classList.add('hidden');
            }
            panel.classList.remove('hidden');
        }

        var vendorInput = document.querySelector('[name="vendor_id"]');
        if (vendorInput) {
            vendorInput.addEventListener('change', function () { showVendor(this.value); });
            if (vendorInput.value) showVendor(vendorInput.value);
        }
    })();

    // ── Row calc ──────────────────────────────────────────────
    var tbody   = document.getElementById('items-tbody');
    var addBtn  = document.getElementById('add-row');
    var purDisc = document.getElementById('pur-discount');
    var purTax  = document.getElementById('pur-tax');

    var productOptions    = @json($productOptions);
    var productAjaxUrl    = '{{ route('ajax.products.info', ['id' => '__ID__']) }}';
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

        var net = subtotal - discAmount;
        if (net < 0) net = 0;

        row.querySelector('.row-subtotal-hidden').value        = net;
        row.querySelector('.row-subtotal-display').textContent = formatRupiah(net);
    }

    function calcTotals() {
        var subtotal = 0;
        tbody.querySelectorAll('tr').forEach(function (row) {
            subtotal += parseInt(row.querySelector('.row-subtotal-hidden').value) || 0;
        });

        var discModeBtn = document.getElementById('pur-discount-mode-btn');
        var discMode    = discModeBtn ? discModeBtn.dataset.mode : 'amount';
        var discAmount;
        if (discMode === 'percent') {
            var discPct = parseFloat(document.getElementById('pur-discount-display').value) || 0;
            discAmount  = Math.round(subtotal * discPct / 100);
            purDisc.value = discAmount;
            document.getElementById('pur-discount-pct').value = discPct || '';
        } else {
            discAmount = parseInt(purDisc.value) || 0;
            document.getElementById('pur-discount-pct').value = '';
        }

        var taxModeBtn = document.getElementById('pur-tax-mode-btn');
        var taxMode    = taxModeBtn ? taxModeBtn.dataset.mode : 'amount';
        var taxAmount;
        if (taxMode === 'percent') {
            var pct   = parseFloat(document.getElementById('pur-tax-display').value) || 0;
            taxAmount = Math.round((subtotal - discAmount) * pct / 100);
            purTax.value = taxAmount;
            document.getElementById('pur-tax-pct').value = pct || '';
        } else {
            taxAmount = parseInt(purTax.value) || 0;
            document.getElementById('pur-tax-pct').value = '';
        }

        var total = subtotal - discAmount + taxAmount;
        document.getElementById('summary-subtotal').textContent = formatRupiah(subtotal);
        document.getElementById('summary-total').textContent    = formatRupiah(total);
    }

    // ── Row builder ───────────────────────────────────────────
    function addRow(data) {
        var i = rowIndex++;
        var d = data || {};

        var tpl = document.getElementById('row-tpl');
        var tr  = tpl.content.cloneNode(true).querySelector('tr');

        tr.querySelectorAll('[name*="__INDEX__"]').forEach(function (el) {
            el.name = el.name.replace(/__INDEX__/g, i);
        });
        tr.querySelectorAll('script').forEach(function (s) { s.remove(); });

        var selectRoot = tr.querySelector('[data-single-select]');

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

        if (d.quantity) tr.querySelector('.row-qty').value = d.quantity;
        if (d.subtotal) {
            tr.querySelector('.row-subtotal-hidden').value        = d.subtotal;
            tr.querySelector('.row-subtotal-display').textContent = formatRupiah(d.subtotal);
        }

        tbody.appendChild(tr);

        initSingleSelect(selectRoot, productPlaceholder);

        var unitInfo = tr.querySelector('.row-unit-info');
        var ssInput  = tr.querySelector('[data-ss-input]');

        function fetchProductInfo(pid) {
            if (!pid) { unitInfo.textContent = ''; return; }
            fetch(productAjaxUrl.replace('__ID__', pid))
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (res) {
                    if (!res) { unitInfo.textContent = ''; return; }
                    unitInfo.textContent = res.stock != null ? '{{ __('general.stock') }}: ' + res.stock + (res.unit ? ' ' + res.unit : '') : (res.unit || '');
                });
        }

        ssInput.addEventListener('change', function () { fetchProductInfo(this.value); });

        if (d.product_id) fetchProductInfo(d.product_id);

        bindRowMoney(tr.querySelector('.row-price-display'), tr.querySelector('.row-price'), d.unit_price, tr);
        bindRowDiscount(tr.querySelector('.row-discount-display'), tr.querySelector('.row-discount-pct'), tr.querySelector('.row-discount'), tr.querySelector('.row-discount-mode-btn'), tr, d.discount_percent, d.discount_amount);

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

    function bindRowDiscount(displayEl, hiddenPct, hiddenAmt, modeBtn, row, initPct, initAmt) {
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

    document.getElementById('bill-form').addEventListener('submit', function () {
        tbody.querySelectorAll('tr').forEach(function (row) {
            var pid = row.querySelector('[data-ss-input]');
            if (pid && !pid.value) row.remove();
        });
    });

    // ── Summary discount Rp/% toggle ─────────────────────────
    (function () {
        var displayEl = document.getElementById('pur-discount-display');
        var hiddenPct = document.getElementById('pur-discount-pct');
        var modeBtn   = document.getElementById('pur-discount-mode-btn');
        var initPct   = hiddenPct.value;
        var initAmt   = purDisc.value;

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
            purDisc.value     = '';
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
                purDisc.value   = '';
            } else {
                var raw  = parseMoney(this.value);
                var cur  = this.selectionStart;
                var prev = this.value.length;
                this.value = raw ? raw.toLocaleString('id-ID') : '';
                var diff = this.value.length - prev;
                try { this.setSelectionRange(cur + diff, cur + diff); } catch(e) {}
                purDisc.value   = raw || '';
                hiddenPct.value = '';
            }
            calcTotals();
        });
    })();

    // ── Summary tax Rp/% toggle ───────────────────────────────
    (function () {
        var displayEl = document.getElementById('pur-tax-display');
        var hiddenPct = document.getElementById('pur-tax-pct');
        var modeBtn   = document.getElementById('pur-tax-mode-btn');
        var initPct   = hiddenPct.value;
        var initAmt   = purTax.value;

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
            purTax.value      = '';
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
                purTax.value    = '';
            } else {
                var raw  = parseMoney(this.value);
                var cur  = this.selectionStart;
                var prev = this.value.length;
                this.value = raw ? raw.toLocaleString('id-ID') : '';
                var diff = this.value.length - prev;
                try { this.setSelectionRange(cur + diff, cur + diff); } catch(e) {}
                purTax.value    = raw || '';
                hiddenPct.value = '';
            }
            calcTotals();
        });
    })();

    // ── Init rows ─────────────────────────────────────────────
    @if(old('details'))
        @foreach(old('details', []) as $detail)
            addRow({
                product_id:       {{ $detail['product_id'] ?? 'null' }},
                quantity:         {{ $detail['quantity'] ?? 1 }},
                unit_price:       {{ $detail['unit_price'] ?? 0 }},
                discount_percent: {{ isset($detail['discount_percent']) && $detail['discount_percent'] !== '' ? $detail['discount_percent'] : 'null' }},
                discount_amount:  {{ $detail['discount_amount'] ?? 0 }},
                subtotal:         {{ $detail['subtotal'] ?? 0 }},
            });
        @endforeach
    @else
        @foreach($data->details as $detail)
            addRow({
                product_id:       {{ $detail->product_id }},
                quantity:         {{ $detail->quantity }},
                unit_price:       {{ $detail->unit_price }},
                discount_percent: {{ $detail->discount_percent !== null ? $detail->discount_percent : 'null' }},
                discount_amount:  {{ $detail->discount_amount ?? 0 }},
                subtotal:         {{ $detail->subtotal }},
            });
        @endforeach
    @endif
    calcTotals();
})();
</script>
@endpush
