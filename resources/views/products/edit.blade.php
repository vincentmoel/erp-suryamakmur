@extends('layouts.main', ['title' => "Edit $title"])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Edit Product</h1>
            <p>Update product information.</p>
        </div>

        <form action="{{ route("$route.update", ['encryptedId' => $encryptedId]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="flex flex-col gap-4">

                {{-- Product Information --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <h3 class="text-sm font-semibold">Product Information</h3>
                    </div>
                    <div class="flex flex-col gap-6 p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <x-form.field name="name" label="Name" :required="true">
                                <input id="name"
                                       type="text"
                                       name="name"
                                       value="{{ old('name', $data->name) }}"
                                       placeholder="Product name"
                                       class="input {{ $errors->has('name') ? 'border-destructive' : '' }}">
                            </x-form.field>

                            <x-form.field name="sku" label="SKU">
                                <input id="sku"
                                       type="text"
                                       name="sku"
                                       value="{{ old('sku', $data->sku) }}"
                                       placeholder="Stock keeping unit (e.g. PRD-001)"
                                       class="input {{ $errors->has('sku') ? 'border-destructive' : '' }}">
                            </x-form.field>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <x-form.field name="category_id" label="Category">
                                <x-form.single-select
                                    name="category_id"
                                    placeholder="Select category..."
                                    :selected="old('category_id', $data->category_id)"
                                    :options="$categories->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()" />
                            </x-form.field>

                            <x-form.field name="unit_id" label="Unit" :required="true">
                                <x-form.single-select
                                    name="unit_id"
                                    placeholder="Select unit..."
                                    :selected="old('unit_id', $data->unit_id)"
                                    :options="$units->map(fn($u) => ['value' => $u->id, 'label' => $u->name])->toArray()" />
                            </x-form.field>
                        </div>
                    </div>
                </div>

                {{-- Pricing & Inventory --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <h3 class="text-sm font-semibold">Pricing & Inventory</h3>
                    </div>
                    <div class="flex flex-col gap-6 p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <x-form.field name="selling_price" label="Selling Price" :required="true">
                                <div data-slot="input-group" role="group"
                                     class="group/input-group relative flex w-full items-center rounded-md border border-input shadow-xs h-9 min-w-0 has-[[data-slot=input-group-control]:focus-visible]:border-ring has-[[data-slot=input-group-control]:focus-visible]:ring-[3px] has-[[data-slot=input-group-control]:focus-visible]:ring-ring/50 {{ $errors->has('selling_price') ? 'border-destructive' : '' }}">
                                    <div role="group" data-slot="input-group-addon" data-align="inline-start"
                                         class="order-first pl-3 flex h-auto cursor-text items-center justify-center gap-2 py-1.5 text-sm font-medium text-muted-foreground select-none">
                                        Rp
                                    </div>
                                    <input data-slot="input-group-control"
                                           data-money-display="selling_price"
                                           type="text"
                                           inputmode="numeric"
                                           placeholder="0"
                                           class="flex-1 rounded-none border-0 bg-transparent shadow-none focus-visible:ring-0 dark:bg-transparent h-full px-2 text-sm outline-none">
                                    <input type="hidden" name="selling_price" id="selling_price"
                                           value="{{ old('selling_price', $data->selling_price) }}">
                                </div>
                            </x-form.field>

                            <x-form.field name="stock_minimum" label="Stock Minimum" :required="true">
                                <input id="stock_minimum"
                                       type="number"
                                       name="stock_minimum"
                                       value="{{ old('stock_minimum', $data->stock_minimum) }}"
                                       min="0"
                                       placeholder="Minimum stock before reorder alert"
                                       class="input {{ $errors->has('stock_minimum') ? 'border-destructive' : '' }}">
                            </x-form.field>
                        </div>
                    </div>
                </div>

                {{-- Media & Notes --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <h3 class="text-sm font-semibold">Media & Notes</h3>
                    </div>
                    <div class="flex flex-col gap-6 p-6">
                        <x-form.field name="description" label="Description">
                            <textarea id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Product description, specifications, or additional details"
                                      style="height: auto; padding-top: 0.5rem; padding-bottom: 0.5rem;"
                                      class="input {{ $errors->has('description') ? 'border-destructive' : '' }}">{{ old('description', $data->description) }}</textarea>
                        </x-form.field>

                        <x-form.field name="image" label="Product Image">
                            <x-form.file-upload
                                name="image"
                                :max-size-mb="2"
                                :preview="$data->image ? asset('storage/' . $data->image) : null" />
                        </x-form.field>

                        {!! \App\Helpers\HtmlBuilder::toggle((bool) old('is_active', $data->is_active ? '1' : '0'), inputId: 'is_active_hidden') !!}
                    </div>
                </div>

                {{-- Add Stock --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center justify-between border-b px-6 py-4">
                        <div>
                            <h3 class="text-sm font-semibold">Add Stock</h3>
                            <p class="mt-0.5 text-xs text-muted-foreground">Optionally add new stock batches on save.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-muted-foreground">Enable</span>
                            <input type="hidden" name="initial_stock_enabled" id="initial_stock_enabled"
                                   value="{{ old('initial_stock_enabled', '0') }}">
                            <button type="button" role="switch" id="initial_stock_toggle_btn"
                                    data-slot="switch" data-size="default"
                                    data-toggle-input="initial_stock_enabled"
                                    class="group/switch inline-flex shrink-0 cursor-pointer items-center rounded-full border border-transparent shadow-xs transition-all outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 data-[size=default]:h-[1.15rem] data-[size=default]:w-8 data-[state=checked]:bg-primary data-[state=unchecked]:bg-input">
                                <span data-slot="switch-thumb"
                                      class="pointer-events-none block rounded-full bg-background ring-0 transition-transform group-data-[size=default]/switch:size-4 data-[state=checked]:translate-x-[calc(100%-2px)] data-[state=unchecked]:translate-x-0">
                                </span>
                            </button>
                        </div>
                    </div>

                    <div id="initial_stock_fields" class="hidden">
                        <div class="p-6">

                            {{-- Column headers --}}
                            <div class="mb-2 grid items-center gap-3" style="grid-template-columns: 1fr 1.4fr 0.8fr 2rem;">
                                <span class="text-xs font-medium text-muted-foreground">Received Date</span>
                                <span class="text-xs font-medium text-muted-foreground">Unit Cost (Modal)</span>
                                <span class="text-xs font-medium text-muted-foreground">Qty</span>
                                <span></span>
                            </div>

                            {{-- Dynamic rows --}}
                            <div id="stock_rows" class="flex flex-col gap-2">
                                @foreach(old('initial_stocks', [['received_at' => now()->toDateString(), 'unit_cost' => $inventory?->unit_cost ?? 0, 'quantity' => 1]]) as $i => $row)
                                <div class="grid items-center gap-3" style="grid-template-columns: 1fr 1.4fr 0.8fr 2rem;" data-stock-row>
                                    <input type="date"
                                           name="initial_stocks[{{ $i }}][received_at]"
                                           value="{{ $row['received_at'] ?? now()->toDateString() }}"
                                           class="input h-9 text-sm {{ $errors->has("initial_stocks.$i.received_at") ? 'border-destructive' : '' }}">

                                    <div data-slot="input-group" role="group"
                                         class="group/input-group relative flex w-full items-center rounded-md border border-input shadow-xs h-9 min-w-0 has-[[data-slot=input-group-control]:focus-visible]:border-ring has-[[data-slot=input-group-control]:focus-visible]:ring-[3px] has-[[data-slot=input-group-control]:focus-visible]:ring-ring/50 {{ $errors->has("initial_stocks.$i.unit_cost") ? 'border-destructive' : '' }}">
                                        <span data-slot="input-group-addon" data-align="inline-start"
                                              class="order-first pl-3 flex h-auto cursor-text items-center justify-center py-1.5 text-sm font-medium text-muted-foreground select-none">Rp</span>
                                        <input data-slot="input-group-control" type="text" inputmode="numeric"
                                               placeholder="0"
                                               class="flex-1 rounded-none border-0 bg-transparent shadow-none focus-visible:ring-0 dark:bg-transparent h-full px-2 text-sm outline-none">
                                        <input type="hidden" name="initial_stocks[{{ $i }}][unit_cost]"
                                               class="stock-unit-cost-hidden"
                                               value="{{ $row['unit_cost'] ?? 0 }}">
                                    </div>

                                    <input type="number"
                                           name="initial_stocks[{{ $i }}][quantity]"
                                           value="{{ $row['quantity'] ?? 1 }}"
                                           min="1"
                                           placeholder="1"
                                           class="input h-9 text-sm {{ $errors->has("initial_stocks.$i.quantity") ? 'border-destructive' : '' }}">

                                    <button type="button" data-remove-row
                                            class="btn-remove h-9 flex items-center justify-center text-destructive transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>

                            {{-- Add row button --}}
                            <button type="button" id="add_stock_row"
                                    class="mt-3 flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                                Add Batch
                            </button>
                        </div>
                    </div>

                    @include('partials.form-actions-edit')
                </div>

            </div>
        </form>

        {{-- Row template (not submitted) --}}
        <template id="stock_row_template">
            <div class="grid items-center gap-3" style="grid-template-columns: 1fr 1.4fr 0.8fr 2rem;" data-stock-row>
                <input type="date" class="input h-9 text-sm">
                <div data-slot="input-group" role="group"
                     class="group/input-group relative flex w-full items-center rounded-md border border-input shadow-xs h-9 min-w-0 has-[[data-slot=input-group-control]:focus-visible]:border-ring has-[[data-slot=input-group-control]:focus-visible]:ring-[3px] has-[[data-slot=input-group-control]:focus-visible]:ring-ring/50">
                    <span data-slot="input-group-addon" data-align="inline-start"
                          class="order-first pl-3 flex h-auto cursor-text items-center justify-center py-1.5 text-sm font-medium text-muted-foreground select-none">Rp</span>
                    <input data-slot="input-group-control" type="text" inputmode="numeric"
                           placeholder="0"
                           class="flex-1 rounded-none border-0 bg-transparent shadow-none focus-visible:ring-0 dark:bg-transparent h-full px-2 text-sm outline-none">
                    <input type="hidden" class="stock-unit-cost-hidden" value="0">
                </div>
                <input type="number" min="1" placeholder="1" value="1" class="input h-9 text-sm">
                <button type="button" data-remove-row
                        class="btn-remove h-9 flex items-center justify-center text-destructive transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </template>

    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var toggleBtn     = document.getElementById('initial_stock_toggle_btn');
    var hiddenInput   = document.getElementById('initial_stock_enabled');
    var fields        = document.getElementById('initial_stock_fields');
    var rowsContainer = document.getElementById('stock_rows');
    var addBtn        = document.getElementById('add_stock_row');
    var template      = document.getElementById('stock_row_template');

    function syncVisibility() {
        fields.classList.toggle('hidden', hiddenInput.value !== '1');
    }

    function bindRowMoneyInput(row) {
        var displayEl = row.querySelector('[data-slot="input-group-control"]');
        var hiddenEl  = row.querySelector('.stock-unit-cost-hidden');
        if (displayEl && hiddenEl) bindMoneyInput(displayEl, hiddenEl);
    }

    function reindexRows() {
        rowsContainer.querySelectorAll('[data-stock-row]').forEach(function (row, i) {
            var date = row.querySelector('input[type="date"]');
            var cost = row.querySelector('.stock-unit-cost-hidden');
            var qty  = row.querySelector('input[type="number"]');
            if (date) date.name = 'initial_stocks[' + i + '][received_at]';
            if (cost) cost.name = 'initial_stocks[' + i + '][unit_cost]';
            if (qty)  qty.name  = 'initial_stocks[' + i + '][quantity]';
        });
    }

    function updateRemoveButtons() {
        var rows = rowsContainer.querySelectorAll('[data-stock-row]');
        rows.forEach(function (row) {
            var btn = row.querySelector('.btn-remove');
            if (!btn) return;
            if (rows.length === 1) {
                btn.classList.add('opacity-30', 'cursor-not-allowed');
            } else {
                btn.classList.remove('opacity-30', 'cursor-not-allowed');
            }
        });
    }

    function addRow() {
        var clone = template.content.cloneNode(true).querySelector('[data-stock-row]');
        clone.querySelector('input[type="date"]').value = new Date().toISOString().split('T')[0];
        rowsContainer.appendChild(clone);
        bindRowMoneyInput(clone);
        reindexRows();
        updateRemoveButtons();
    }

    rowsContainer.querySelectorAll('[data-stock-row]').forEach(bindRowMoneyInput);

    rowsContainer.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-remove');
        if (!btn || btn.classList.contains('cursor-not-allowed')) return;
        btn.closest('[data-stock-row]').remove();
        reindexRows();
        updateRemoveButtons();
    });

    addBtn.addEventListener('click', addRow);

    toggleBtn.addEventListener('click', function () {
        requestAnimationFrame(syncVisibility);
    });

    updateRemoveButtons();
    syncVisibility();
});
</script>
@endpush
