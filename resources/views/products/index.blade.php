@extends('layouts.main', ['title' => 'Products'])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Products</h1>
            <p>Manage product data.</p>
        </div>

        <x-datatable id="products-table" search-placeholder="Search">

            <x-slot name="actions">
                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> Add Product
                </a>
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Unit</th>
                <th>Price</th>
                <th>Status</th>
                <th>Created At</th>
                <th></th>
            </x-slot>

        </x-datatable>

    </div>
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
                    label.text(res.data.is_active ? 'Active' : 'Inactive');
                },
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
                { data: 'unit_id', name: 'unit_id', className: 'dt-cell-muted' },
                { data: 'selling_price', name: 'selling_price', className: 'dt-cell-muted' },
                { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at', className: 'dt-cell-muted' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
    </script>
@endpush
