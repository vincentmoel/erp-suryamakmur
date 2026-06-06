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

            <x-slot name="actions">
                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> @lang('general.add_product')
                </a>
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
    <style>
        #stock-modal {
            position: fixed;
            inset: 0;
            margin: auto;
            height: fit-content;
            background-color: var(--card);
            color: var(--foreground);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.15);
            padding: 0;
            width: 100%;
            max-width: 28rem;
        }
        #stock-modal::backdrop {
            background: rgba(0,0,0,.45);
        }
        #stock-modal .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        #stock-modal .modal-body {
            padding: 1.25rem 1.5rem;
        }
        #stock-modal table { width: 100%; border-collapse: collapse; }
        #stock-modal th {
            padding-bottom: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--muted-foreground);
            border-bottom: 1px solid var(--border);
        }
        #stock-modal td {
            padding: 0.5rem 0;
            font-size: 0.875rem;
            border-bottom: 1px solid color-mix(in oklab, var(--border) 50%, transparent);
        }
        #stock-modal tr:last-child td { border-bottom: none; }
    </style>
    <dialog id="stock-modal">
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

        const stockModal      = document.getElementById('stock-modal');
        const stockModalTitle = document.getElementById('stock-modal-title');
        const stockModalBody  = document.getElementById('stock-modal-body');

        document.getElementById('stock-modal-close').addEventListener('click', () => stockModal.close());
        stockModal.addEventListener('click', e => { if (e.target === stockModal) stockModal.close(); });

        $(document).on('click', '.btn-stock-detail', function () {
            const productId = $(this).data('product-id');
            stockModalTitle.textContent = 'Stock Detail';
            stockModalBody.innerHTML = '<p class="text-sm text-muted-foreground">Loading...</p>';
            stockModal.showModal();

            $.get('{{ url('ajax/products') }}/' + productId + '/stock', function (res) {
                stockModalTitle.textContent = res.data.product_name;

                if (!res.data.batches.length) {
                    stockModalBody.innerHTML = '<p class="text-sm text-muted-foreground">No stock available.</p>';
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
                                <th style="text-align:left;">Unit Cost</th>
                                <th style="text-align:right;">Qty</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>`;
            });
        });

        initDataTable({
            tableId: 'products-table',
            ajaxUrl: '{{ route('products.index') }}',
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
    </script>
@endpush
