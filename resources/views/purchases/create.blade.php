@extends('layouts.main', ['title' => "Add $title"])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Add Purchase</h1>
            <p>Create a new purchase order from a vendor.</p>
        </div>

        <form action="{{ route('purchases.store') }}" method="POST" id="purchase-form">
            @csrf

            {{-- Header --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">
                <div class="flex items-center gap-3 border-b px-6 py-4">
                    <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                        <x-icon name="receipt" class="size-4 text-primary" />
                    </div>
                    <h3 class="text-sm font-semibold">Purchase Information</h3>
                </div>

                <div class="flex flex-col gap-6 p-6">

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="vendor_id" label="Vendor" :required="true">
                            <x-form.single-select
                                name="vendor_id"
                                placeholder="Select vendor..."
                                :options="$vendorOptions"
                                :selected="old('vendor_id')" />
                        </x-form.field>

                        <x-form.field name="invoice_number" label="Vendor Invoice No.">
                            <input type="text"
                                   name="invoice_number"
                                   value="{{ old('invoice_number') }}"
                                   placeholder="e.g. INV-12345"
                                   class="input {{ $errors->has('invoice_number') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="purchase_date" label="Purchase Date" :required="true">
                            <input type="date"
                                   name="purchase_date"
                                   value="{{ old('purchase_date', now()->format('Y-m-d')) }}"
                                   class="input {{ $errors->has('purchase_date') ? 'border-destructive' : '' }}">
                        </x-form.field>

                        <x-form.field name="notes" label="Notes">
                            <textarea name="notes"
                                      rows="1"
                                      placeholder="Optional notes..."
                                      style="height: auto; padding-top: 0.5rem; padding-bottom: 0.5rem;"
                                      class="input {{ $errors->has('notes') ? 'border-destructive' : '' }}">{{ old('notes') }}</textarea>
                        </x-form.field>
                    </div>

                </div>
            </div>

            {{-- Line Items --}}
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
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-40">Unit Price (Modal)</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-36">Discount</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground w-40">Subtotal</th>
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
                        <span class="text-muted-foreground w-28 text-right shrink-0">Subtotal</span>
                        <span class="font-medium w-44 text-right tabular-nums" id="summary-subtotal">Rp 0</span>
                    </div>

                    <div class="flex items-center gap-4 text-sm w-full justify-end mt-2">
                        <label for="pur-discount-display" class="text-muted-foreground w-28 text-right shrink-0 cursor-pointer">Discount</label>
                        <div class="w-44">
                            <input type="text"
                                   id="pur-discount-display"
                                   placeholder="0"
                                   autocomplete="off"
                                   class="input input-sm text-right w-full {{ $errors->has('discount_amount') ? 'border-destructive' : '' }}">
                            <input type="hidden" name="discount_amount" id="pur-discount" value="{{ old('discount_amount') }}">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-sm w-full justify-end">
                        <label for="pur-tax-display" class="text-muted-foreground w-28 text-right shrink-0 cursor-pointer">Tax (%)</label>
                        <div class="w-44">
                            <input type="number"
                                   id="pur-tax-display"
                                   name="tax_percent"
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   placeholder="0"
                                   value="{{ old('tax_percent') }}"
                                   autocomplete="off"
                                   class="input input-sm text-right w-full {{ $errors->has('tax_percent') ? 'border-destructive' : '' }}">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-sm w-full justify-end">
                        <span class="text-muted-foreground w-28 text-right shrink-0">Tax Amount</span>
                        <span class="font-medium w-44 text-right tabular-nums" id="summary-tax">Rp 0</span>
                    </div>

                    <div class="flex items-center gap-4 w-full justify-end border-t pt-3 mt-1">
                        <span class="font-semibold w-28 text-right shrink-0">Grand Total</span>
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
                    <button type="submit" name="status" value="ordered" class="btn btn-primary">
                        <x-icon name="check" class="size-3.5" />
                        Save as Ordered
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
<script>
(function () {
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
    var tbody      = document.getElementById('items-tbody');
    var addBtn     = document.getElementById('add-row');
    var purDisc    = document.getElementById('pur-discount');
    var taxPercent = document.getElementById('pur-tax-display');

    var productOptions = @json($productOptions);
    var productAjaxUrl = '{{ route('ajax.products.info', ['id' => '__ID__']) }}';
    var rowIndex = 0;

    function calcRow(row) {
        var qty      = parseInt(row.querySelector('.row-qty').value) || 0;
        var price    = parseInt(row.querySelector('.row-price').value) || 0;
        var discount = parseInt(row.querySelector('.row-discount').value) || 0;
        var subtotal = qty * price - discount;
        if (subtotal < 0) subtotal = 0;

        row.querySelector('.row-subtotal-hidden').value   = subtotal;
        row.querySelector('.row-subtotal-display').textContent = formatRupiah(subtotal);
    }

    function calcTotals() {
        var subtotal = 0;
        tbody.querySelectorAll('tr').forEach(function (row) {
            subtotal += parseInt(row.querySelector('.row-subtotal-hidden').value) || 0;
        });
        var discount   = parseInt(purDisc.value) || 0;
        var taxPct     = parseFloat(taxPercent.value) || 0;
        var taxAmount  = Math.round(subtotal * taxPct / 100);
        var grandTotal = subtotal - discount + taxAmount;

        document.getElementById('summary-subtotal').textContent = formatRupiah(subtotal);
        document.getElementById('summary-tax').textContent      = formatRupiah(taxAmount);
        document.getElementById('summary-total').textContent    = formatRupiah(grandTotal);
    }

    function addRow(data) {
        var i = rowIndex++;
        var d = data || {};

        var tr = document.createElement('tr');
        tr.className = 'border-b last:border-0';

        // Product
        var tdProduct = document.createElement('td');
        tdProduct.className = 'px-4 py-3';
        var combobox = buildCombobox('details[' + i + '][product_id]', productOptions, d.product_id || null, 'Select product...');
        var unitInfo = document.createElement('div');
        unitInfo.className = 'mt-1 text-xs text-muted-foreground';
        tdProduct.appendChild(combobox);
        tdProduct.appendChild(unitInfo);

        combobox.querySelector('[data-cb-input]').addEventListener('change', function () {
            var pid = this.value;
            if (!pid) { unitInfo.textContent = ''; return; }
            fetch(productAjaxUrl.replace('__ID__', pid))
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (data) {
                    if (!data) { unitInfo.textContent = ''; return; }
                    unitInfo.textContent = data.unit ? data.unit : '';
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

        // Subtotal
        var tdSub = document.createElement('td');
        tdSub.className = 'px-4 py-3 text-right';
        tdSub.innerHTML =
            '<input type="hidden" name="details[' + i + '][subtotal]" class="row-subtotal-hidden" value="' + (d.subtotal || 0) + '">' +
            '<div class="h-9 flex items-center justify-end">' +
                '<span class="row-subtotal-display font-medium tabular-nums">' + formatRupiah(d.subtotal || 0) + '</span>' +
            '</div>';

        // Remove
        var tdDel = document.createElement('td');
        tdDel.className = 'px-4 py-3 text-center';
        tdDel.innerHTML = '<div class="h-9 flex items-center justify-center"><button type="button" class="btn-remove text-destructive transition-colors"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></button></div>';

        [tdProduct, tdQty, tdPrice, tdDisc, tdSub, tdDel].forEach(function (td) { tr.appendChild(td); });
        tbody.appendChild(tr);

        bindRowMoney(tdPrice.querySelector('.row-price-display'), tr.querySelector('.row-price'), d.unit_price, tr);
        bindRowMoney(tdDisc.querySelector('.row-discount-display'), tr.querySelector('.row-discount'), d.discount_amount, tr);

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

    bindMoneyInput(document.getElementById('pur-discount-display'), purDisc, calcTotals);
    taxPercent.addEventListener('input', calcTotals);

    @if(old('details'))
        @foreach(old('details', []) as $detail)
            addRow({
                product_id:      {{ $detail['product_id'] ?? 'null' }},
                quantity:        {{ $detail['quantity'] ?? 1 }},
                unit_price:      {{ $detail['unit_price'] ?? 0 }},
                discount_amount: {{ $detail['discount_amount'] ?? 0 }},
                subtotal:        {{ $detail['subtotal'] ?? 0 }},
            });
        @endforeach
        calcTotals();
    @else
        addRow(); addRow(); addRow();
    @endif
})();
</script>
@endpush
