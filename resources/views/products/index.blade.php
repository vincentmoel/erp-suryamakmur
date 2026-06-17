@extends('layouts.main', ['title' => __('general.products')])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.products')</h1>
            <p>@lang('general.products_desc')</p>
        </div>

        <x-datatable id="products-table" :search-placeholder="__('general.search')">

            <x-slot name="filters">
                <x-form.single-select
                    name="filter_category"
                    placeholder="All Categories"
                    :searchable="true"
                    :options="$categories->map(fn($c) => ['value' => $c->id, 'label' => $c->name, 'badge' => $c->trashed() ? 'Deleted' : null])->toArray()" />
                <x-form.single-select
                    name="filter_unit"
                    placeholder="All Units"
                    :searchable="true"
                    :options="$units->map(fn($u) => ['value' => $u->id, 'label' => $u->name, 'badge' => $u->trashed() ? 'Deleted' : null])->toArray()" />
                <x-form.single-select
                    name="filter_is_active"
                    placeholder="All Statuses"
                    :searchable="false"
                    :options="[['value' => '1', 'label' => __('general.active')], ['value' => '0', 'label' => __('general.inactive')]]" />
            </x-slot>

            <x-slot name="actions">
@if(app(\App\Services\PermissionService::class)->has('Product', 'create'))
                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> @lang('general.add_product')
                </a>
@endif
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>@lang('general.name')</th>
                <th>@lang('general.sku')</th>
                <th>@lang('general.category')</th>
                <th>@lang('general.selling_price')</th>
                <th>@lang('general.stock')</th>
                <th>@lang('general.status')</th>
                <th>@lang('general.created_at')</th>
                <th></th>
            </x-slot>

        </x-datatable>

    </div>

    {{-- Stock Detail Modal --}}
    <dialog id="stock-modal" class="app-modal">
        <div class="modal-header">
            <h2 style="font-weight:600;font-size:0.9375rem;" id="stock-modal-title">Stock Detail</h2>
            <button type="button" id="stock-modal-close" style="color:var(--muted-foreground);line-height:1;background:none;border:none;cursor:pointer;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.25rem;height:1.25rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="stock-modal-body" class="modal-body">
            <p style="font-size:0.875rem;color:var(--muted-foreground);">Loading...</p>
        </div>
    </dialog>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="{{ asset('src/js/datatable.js') }}"></script>
    <script>
        $(document).on('click', '[data-slot="switch"][data-toggle-url]', function () {
            const btn   = $(this);
            const thumb = btn.find('[data-slot="switch-thumb"]');
            const label = btn.closest('.flex').find('.toggle-label');

            $.ajax({
                url: btn.data('toggle-url'),
                type: 'PATCH',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    const state = res.data.is_active ? 'checked' : 'unchecked';
                    btn.attr('data-state', state);
                    thumb.attr('data-state', state);
                    label.text(res.data.is_active ? '{{ __('general.active') }}' : '{{ __('general.inactive') }}');
                },
            });
        });

        const lang = {
            unitCost: '{{ __('general.unit_cost') }}',
            stock:    '{{ __('general.stock') }}',
            noStock:  '{{ __('general.no_stock_available') }}',
        };

        const stockModal      = document.getElementById('stock-modal');
        const stockModalTitle = document.getElementById('stock-modal-title');
        const stockModalBody  = document.getElementById('stock-modal-body');

        document.getElementById('stock-modal-close').addEventListener('click', () => stockModal.close());
        stockModal.addEventListener('click', e => { if (e.target === stockModal) stockModal.close(); });

        $(document).on('click', '.btn-stock-detail', function () {
            const productId = $(this).data('product-id');
            stockModalTitle.innerHTML = '<span class="skeleton" style="width:140px;height:1rem;"></span>';
            stockModalBody.innerHTML =
                `<table>
                    <thead>
                        <tr>
                            <th style="text-align:left;">${lang.unitCost}</th>
                            <th style="text-align:right;">${lang.stock}</th>
                        </tr>
                    </thead>
                    <tbody>${skeletonRows([80, 50], 3)}</tbody>
                </table>`;
            stockModal.showModal();

            $.get('{{ url('ajax/products') }}/' + productId + '/stock', function (res) {
                setTimeout(function () {
                stockModalTitle.textContent = res.data.product_name;

                if (!res.data.batches.length) {
                    stockModalBody.innerHTML = `<p style="font-size:0.875rem;color:var(--muted-foreground);">${lang.noStock}</p>`;
                    return;
                }

                let rows = res.data.batches.map(b =>
                    `<tr>
                        <td>Rp ${Number(b.unit_cost).toLocaleString('id-ID')}</td>
                        <td style="text-align:right;font-weight:500;">${b.total_quantity} ${res.data.unit}</td>
                    </tr>`
                ).join('');

                stockModalBody.innerHTML =
                    `<table>
                        <thead>
                            <tr>
                                <th style="text-align:left;">${lang.unitCost}</th>
                                <th style="text-align:right;">${lang.stock}</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>`;
                }, 300);
            });
        });

        const dt = initDataTable({
            tableId: 'products-table',
            ajaxUrl: '{{ route('products.index') }}',
            order: [{ name: 'created_at', dir: 'desc' }],
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'dt-cell-index dt-center'
                },
                { data: 'name', name: 'name' },
                { data: 'sku', name: 'sku', className: 'dt-cell-muted' },
                { data: 'category_id', name: 'category_id', className: 'dt-cell-muted' },
                { data: 'selling_price', name: 'selling_price', className: 'dt-cell-muted' },
                { data: 'stock', name: 'stock', orderable: false, searchable: false, className: 'dt-cell-muted' },
                { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at', className: 'dt-cell-muted' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

        dt.settings()[0].ajax.data = function (d) {
            d.filter_category  = document.querySelector('[name="filter_category"]').value;
            d.filter_unit      = document.querySelector('[name="filter_unit"]').value;
            d.filter_is_active = document.querySelector('[name="filter_is_active"]').value;
        };

        document.querySelectorAll('[name="filter_category"], [name="filter_unit"], [name="filter_is_active"]')
            .forEach(function (el) { el.addEventListener('change', function () { dt.ajax.reload(); }); });
    </script>
@endpush
