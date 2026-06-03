@extends('layouts.main', ['title' => "Edit $title"])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Edit Invoice</h1>
            <p>Update invoice {{ $data->code }}.</p>
        </div>

        <form action="{{ route('invoices.update', ['encryptedId' => $encryptedId]) }}" method="POST" id="invoice-form">
            @csrf
            @method('PATCH')

            {{-- Header --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">
                <div class="flex items-center gap-3 border-b px-6 py-4">
                    <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                        <x-icon name="invoice" class="size-4 text-primary" />
                    </div>
                    <h3 class="text-sm font-semibold">Invoice Information</h3>
                </div>

                <div class="flex flex-col gap-6 p-6">

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="customer_id" label="Customer" :required="true">
                            <x-form.single-select
                                name="customer_id"
                                placeholder="Select customer..."
                                :options="$customers->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()"
                                :selected="old('customer_id', $data->customer_id)" />

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

                        <x-form.field name="salesperson_id" label="Salesperson" :required="true">
                            <x-form.single-select
                                name="salesperson_id"
                                placeholder="Select salesperson..."
                                :options="$salespersons->map(fn($u) => ['value' => $u->id, 'label' => $u->name])->toArray()"
                                :selected="old('salesperson_id', $data->salesperson_id)" />
                        </x-form.field>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="invoice_date" label="Invoice Date" :required="true">
                            <input type="date"
                                   name="invoice_date"
                                   value="{{ old('invoice_date', $data->invoice_date?->format('Y-m-d')) }}"
                                   class="input {{ $errors->has('invoice_date') ? 'border-destructive' : '' }}">
                        </x-form.field>

                        <x-form.field name="due_date" label="Due Date">
                            <input type="date"
                                   name="due_date"
                                   value="{{ old('due_date', $data->due_date?->format('Y-m-d')) }}"
                                   class="input {{ $errors->has('due_date') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                </div>
            </div>

            {{-- Line Items + Totals --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">

                <div class="flex items-center justify-between gap-3 border-b px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                            <x-icon name="box" class="size-4 text-primary" />
                        </div>
                        <h3 class="text-sm font-semibold">Items</h3>
                    </div>
                    <button type="button" id="add-row" class="btn btn-outline btn-sm">
                        <x-icon name="plus" class="size-3.5" />
                        Add Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/30">
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground min-w-52">Product</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-24">Qty</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-40">Unit Price</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-36">Discount</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-36">Tax</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-40">Amount</th>
                                <th class="px-4 py-3 w-10"></th>
                            </tr>
                            <style>#items-tbody td { vertical-align: top; }</style>
                        </thead>
                        <tbody id="items-tbody"></tbody>
                    </table>
                </div>

                @error('details')
                    <p class="px-6 py-2 text-sm text-destructive">{{ $message }}</p>
                @enderror

                {{-- Totals --}}
                <div class="flex flex-col items-end gap-2 border-t px-6 py-4">

                    <div class="flex items-center gap-4 text-sm w-full justify-end">
                        <span class="text-muted-foreground w-24 text-right shrink-0">Subtotal</span>
                        <span class="font-medium w-44 text-right tabular-nums" id="summary-subtotal">Rp 0</span>
                    </div>

                    <div class="flex items-center gap-4 text-sm w-full justify-end mt-2">
                        <label for="inv-discount-display" class="text-muted-foreground w-24 text-right shrink-0 cursor-pointer">Discount</label>
                        <div class="w-44">
                            <input type="text"
                                   id="inv-discount-display"
                                   placeholder="0"
                                   autocomplete="off"
                                   class="input input-sm text-right w-full {{ $errors->has('discount_amount') ? 'border-destructive' : '' }}">
                            <input type="hidden" name="discount_amount" id="inv-discount" value="{{ old('discount_amount', $data->discount_amount) }}">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-sm w-full justify-end">
                        <label for="inv-tax-display" class="text-muted-foreground w-24 text-right shrink-0 cursor-pointer">Tax</label>
                        <div class="w-44">
                            <input type="text"
                                   id="inv-tax-display"
                                   placeholder="0"
                                   autocomplete="off"
                                   class="input input-sm text-right w-full {{ $errors->has('tax_amount') ? 'border-destructive' : '' }}">
                            <input type="hidden" name="tax_amount" id="inv-tax" value="{{ old('tax_amount', $data->tax_amount) }}">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 w-full justify-end border-t pt-3 mt-1">
                        <span class="font-semibold w-24 text-right shrink-0">Total</span>
                        <span class="font-semibold text-base w-44 text-right tabular-nums" id="summary-total">Rp 0</span>
                    </div>

                </div>

            </div>

            {{-- Form Actions --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                <div class="flex items-center justify-end gap-2 px-6 py-4">
                    <button type="submit" name="status" value="draft" class="btn btn-outline">
                        <x-icon name="save" class="size-3.5" />
                        Save as Draft
                    </button>
                    <button type="submit" name="status" value="waiting_for_payment" class="btn btn-primary">
                        <x-icon name="check" class="size-3.5" />
                        Update
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

        var iconMail    = {!! Js::from((string) str(view('components.icon', ['name' => 'mail',       'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};
        var iconPhone   = {!! Js::from((string) str(view('components.icon', ['name' => 'phone',      'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};
        var iconMobile  = {!! Js::from((string) str(view('components.icon', ['name' => 'smartphone', 'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};
        var iconReceipt = {!! Js::from((string) str(view('components.icon', ['name' => 'receipt',    'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};

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

    // ── Helpers ───────────────────────────────────────────────
    function fmt(n) {
        return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    }

    function parseMoney(str) {
        return parseInt((str || '').replace(/\D/g, ''), 10) || 0;
    }

    function bindMoneyInput(displayEl, hiddenEl) {
        var init = parseInt(hiddenEl.value, 10);
        if (init) displayEl.value = init.toLocaleString('id-ID');

        displayEl.addEventListener('input', function () {
            var raw  = parseMoney(this.value);
            var cur  = this.selectionStart;
            var prev = this.value.length;
            this.value = raw ? raw.toLocaleString('id-ID') : '';
            var diff = this.value.length - prev;
            this.setSelectionRange(cur + diff, cur + diff);
            hiddenEl.value = raw || '';
            calcTotals();
        });
    }

    // ── Combobox builder ──────────────────────────────────────
    var CARET_SVG = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0 opacity-50"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>';

    function buildCombobox(name, options, selectedValue, placeholder) {
        var wrap = document.createElement('div');
        wrap.className = 'relative';

        var selectedLabel = '';
        if (selectedValue) {
            var found = options.find(function(o) { return String(o.value) === String(selectedValue); });
            if (found) selectedLabel = found.label;
        }

        var itemsHtml = options.map(function (o) {
            var sel = selectedValue && String(o.value) === String(selectedValue);
            return '<div role="option" data-cb-item data-value="' + o.value + '" data-label="' + escAttr(o.label) + '"' +
                   (sel ? ' aria-selected="true"' : '') +
                   ' class="select-item cursor-pointer">' + escHtml(o.label) + '</div>';
        }).join('');

        wrap.innerHTML =
            '<button type="button" class="select-trigger flex items-center justify-between w-full" aria-haspopup="listbox" aria-expanded="false">' +
                '<span data-cb-label class="text-sm ' + (selectedLabel ? '' : 'text-muted-foreground') + '">' +
                    escHtml(selectedLabel || placeholder) +
                '</span>' +
                CARET_SVG +
            '</button>' +
            '<div data-cb-content role="listbox" class="select-content hidden max-h-60 overflow-auto">' +
                '<div class="px-1 pb-1 sticky top-0 bg-popover">' +
                    '<input type="text" data-cb-search placeholder="Search..." autocomplete="off" class="input h-8 text-sm">' +
                '</div>' +
                '<div data-cb-list>' +
                    '<div role="option" data-cb-item data-value="" data-label="' + escAttr(placeholder) + '" class="select-item cursor-pointer text-muted-foreground">' + escHtml(placeholder) + '</div>' +
                    itemsHtml +
                '</div>' +
                '<p data-cb-empty class="hidden px-2 py-4 text-center text-sm text-muted-foreground">No results found.</p>' +
            '</div>' +
            '<input type="hidden" name="' + name + '" data-cb-input value="' + escAttr(selectedValue || '') + '">';

        initCombobox(wrap, placeholder);
        return wrap;
    }

    function initCombobox(root, placeholder) {
        var trigger  = root.querySelector('button[aria-haspopup]');
        var content  = root.querySelector('[data-cb-content]');
        var search   = root.querySelector('[data-cb-search]');
        var labelEl  = root.querySelector('[data-cb-label]');
        var hiddenIn = root.querySelector('[data-cb-input]');
        var emptyEl  = root.querySelector('[data-cb-empty]');

        function open() {
            var r = trigger.getBoundingClientRect();
            content.style.top   = (r.bottom + 4) + 'px';
            content.style.left  = r.left + 'px';
            content.style.width = r.width + 'px';
            content.classList.remove('hidden');
            trigger.setAttribute('aria-expanded', 'true');
            if (search) { search.value = ''; filterItems(''); search.focus(); }
        }
        function close() {
            content.classList.add('hidden');
            trigger.setAttribute('aria-expanded', 'false');
        }

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            content.classList.contains('hidden') ? open() : close();
        });
        document.addEventListener('click', function (e) {
            if (!root.contains(e.target) && !content.contains(e.target)) close();
        });
        window.addEventListener('scroll', function () { if (!content.classList.contains('hidden')) open(); }, true);
        window.addEventListener('resize', function () { if (!content.classList.contains('hidden')) close(); });

        content.addEventListener('click', function (e) {
            var item = e.target.closest('[data-cb-item]');
            if (!item) return;
            e.stopPropagation();
            root.querySelectorAll('[data-cb-item]').forEach(function (i) { i.setAttribute('aria-selected', 'false'); });
            var val = item.dataset.value;
            var lbl = item.dataset.label;
            if (val === '') {
                hiddenIn.value = '';
                labelEl.textContent = placeholder;
                labelEl.classList.add('text-muted-foreground');
            } else {
                item.setAttribute('aria-selected', 'true');
                hiddenIn.value = val;
                labelEl.textContent = lbl;
                labelEl.classList.remove('text-muted-foreground');
            }
            hiddenIn.dispatchEvent(new Event('change'));
            close();
        });

        function filterItems(q) {
            q = q.toLowerCase();
            var visible = 0;
            root.querySelectorAll('[data-cb-item]').forEach(function (item) {
                if (item.dataset.value === '') return;
                var match = item.dataset.label.toLowerCase().includes(q);
                item.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            if (emptyEl) visible === 0 ? emptyEl.classList.remove('hidden') : emptyEl.classList.add('hidden');
        }

        if (search) {
            search.addEventListener('input', function () { filterItems(search.value); });
            search.addEventListener('click', function (e) { e.stopPropagation(); });
        }
    }

    function escAttr(s) { return String(s || '').replace(/"/g, '&quot;'); }
    function escHtml(s) { return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    // ── Row calc ──────────────────────────────────────────────
    var tbody   = document.getElementById('items-tbody');
    var addBtn  = document.getElementById('add-row');
    var invDisc = document.getElementById('inv-discount');
    var invTax  = document.getElementById('inv-tax');

    var productOptions = @json($productOptions);
    var productAjaxUrl = '{{ route('ajax.products.info', ['id' => '__ID__']) }}';
    var rowIndex = 0;

    function calcRow(row) {
        var qty      = parseInt(row.querySelector('.row-qty').value) || 0;
        var price    = parseInt(row.querySelector('.row-price').value) || 0;
        var discount = parseInt(row.querySelector('.row-discount').value) || 0;
        var tax      = parseInt(row.querySelector('.row-tax').value) || 0;
        var subtotal = qty * price;
        var amount   = subtotal - discount + tax;

        row.querySelector('.row-subtotal-hidden').value = subtotal;
        row.querySelector('.row-amount-hidden').value   = amount;
        row.querySelector('.row-amount-display').textContent = fmt(amount);
    }

    function calcTotals() {
        var subtotal = 0;
        tbody.querySelectorAll('tr').forEach(function (row) {
            subtotal += parseInt(row.querySelector('.row-amount-hidden').value) || 0;
        });
        var discount = parseInt(invDisc.value) || 0;
        var tax      = parseInt(invTax.value) || 0;
        var total    = subtotal - discount + tax;

        document.getElementById('summary-subtotal').textContent = fmt(subtotal);
        document.getElementById('summary-total').textContent    = fmt(total);
    }

    // ── Row builder ───────────────────────────────────────────
    function addRow(data) {
        var i = rowIndex++;
        var d = data || {};

        var tr = document.createElement('tr');
        tr.className = 'border-b last:border-0';

        // Product combobox cell
        var tdProduct = document.createElement('td');
        tdProduct.className = 'px-4 py-3';
        var combobox = buildCombobox(
            'details[' + i + '][product_id]',
            productOptions,
            d.product_id || null,
            'Select product...'
        );
        var stockInfo = document.createElement('div');
        stockInfo.className = 'mt-1 text-xs text-muted-foreground';
        stockInfo.textContent = 'Stock: 0';
        tdProduct.appendChild(combobox);
        tdProduct.appendChild(stockInfo);

        // Wire product change → AJAX
        combobox.querySelector('[data-cb-input]').addEventListener('change', function () {
            var pid = this.value;
            if (!pid) { stockInfo.textContent = 'Stock: 0'; return; }
            fetch(productAjaxUrl.replace('__ID__', pid))
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (data) {
                    if (!data) { stockInfo.textContent = 'Stock: 0'; return; }
                    stockInfo.textContent = 'Stock: ' + data.stock_available + (data.unit ? ' ' + data.unit : '');
                });
        });

        // Qty
        var tdQty = document.createElement('td');
        tdQty.className = 'px-4 py-3';
        tdQty.innerHTML = '<input type="number" name="details[' + i + '][quantity]" class="input row-qty text-right" min="1" value="' + (d.quantity || 1) + '" required>';

        // Unit price
        var tdPrice = document.createElement('td');
        tdPrice.className = 'px-4 py-3';
        tdPrice.innerHTML =
            '<input type="text" class="input row-price-display text-right" placeholder="0" autocomplete="off">' +
            '<input type="hidden" name="details[' + i + '][unit_price]" class="row-price" value="' + (d.unit_price || '') + '">';

        // Discount
        var tdDisc = document.createElement('td');
        tdDisc.className = 'px-4 py-3';
        tdDisc.innerHTML =
            '<input type="text" class="input row-discount-display text-right" placeholder="0" autocomplete="off">' +
            '<input type="hidden" name="details[' + i + '][discount_amount]" class="row-discount" value="' + (d.discount_amount || '') + '">';

        // Tax
        var tdTax = document.createElement('td');
        tdTax.className = 'px-4 py-3';
        tdTax.innerHTML =
            '<input type="text" class="input row-tax-display text-right" placeholder="0" autocomplete="off">' +
            '<input type="hidden" name="details[' + i + '][tax_amount]" class="row-tax" value="' + (d.tax_amount || '') + '">';

        // Amount
        var tdAmt = document.createElement('td');
        tdAmt.className = 'px-4 py-3 text-right';
        tdAmt.innerHTML =
            '<input type="hidden" name="details[' + i + '][subtotal_amount]" class="row-subtotal-hidden" value="' + (d.subtotal_amount || 0) + '">' +
            '<input type="hidden" name="details[' + i + '][amount]" class="row-amount-hidden" value="' + (d.amount || 0) + '">' +
            '<div class="h-9 flex items-center justify-end">' +
                '<span class="row-amount-display font-medium tabular-nums">' + fmt(d.amount || 0) + '</span>' +
            '</div>';

        // Remove
        var tdDel = document.createElement('td');
        tdDel.className = 'px-4 py-3 text-center';
        tdDel.innerHTML = '<div class="h-9 flex items-center justify-center"><button type="button" class="btn-remove text-destructive transition-colors"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></button></div>';

        [tdProduct, tdQty, tdPrice, tdDisc, tdTax, tdAmt, tdDel].forEach(function (td) { tr.appendChild(td); });
        tbody.appendChild(tr);

        bindRowMoney(tdPrice.querySelector('.row-price-display'), tr.querySelector('.row-price'), d.unit_price, tr);
        bindRowMoney(tdDisc.querySelector('.row-discount-display'), tr.querySelector('.row-discount'), d.discount_amount, tr);
        bindRowMoney(tdTax.querySelector('.row-tax-display'), tr.querySelector('.row-tax'), d.tax_amount, tr);

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

    function bindRowMoney(displayEl, hiddenEl, initVal, row) {
        if (initVal) {
            var n = parseInt(initVal, 10);
            if (n) displayEl.value = n.toLocaleString('id-ID');
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

    bindMoneyInput(document.getElementById('inv-discount-display'), invDisc);
    bindMoneyInput(document.getElementById('inv-tax-display'), invTax);

    // Load existing details
    @if(old('details'))
        @foreach(old('details', []) as $detail)
            addRow({
                product_id:      {{ $detail['product_id'] ?? 'null' }},
                quantity:        {{ $detail['quantity'] ?? 1 }},
                unit_price:      {{ $detail['unit_price'] ?? 0 }},
                discount_amount: {{ $detail['discount_amount'] ?? 0 }},
                tax_amount:      {{ $detail['tax_amount'] ?? 0 }},
                subtotal_amount: {{ $detail['subtotal_amount'] ?? 0 }},
                amount:          {{ $detail['amount'] ?? 0 }},
            });
        @endforeach
    @else
        @foreach($data->details as $detail)
            addRow({
                product_id:      {{ $detail->product_id }},
                quantity:        {{ $detail->quantity }},
                unit_price:      {{ $detail->unit_price }},
                discount_amount: {{ $detail->discount_amount ?? 0 }},
                tax_amount:      {{ $detail->tax_amount ?? 0 }},
                subtotal_amount: {{ $detail->subtotal_amount }},
                amount:          {{ $detail->amount }},
            });
        @endforeach
    @endif
    calcTotals();
})();
</script>
@endpush
