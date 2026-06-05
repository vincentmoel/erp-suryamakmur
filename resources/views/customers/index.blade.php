@extends('layouts.main', ['title' => 'Customers'])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Customers</h1>
            <p>Manage customer data.</p>
        </div>

        <x-datatable id="customers-table" search-placeholder="Search">

            <x-slot name="actions">
                <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> Add Customer
                </a>
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>Name</th>
                <th>Type</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Mobile</th>
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
            tableId: 'customers-table',
            ajaxUrl: '{{ route('customers.index') }}',
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'dt-cell-index dt-center'
                },
                { data: 'name', name: 'name' },
                { data: 'type', name: 'type' },
                { data: 'email', name: 'email', className: 'dt-cell-muted' },
                { data: 'phone', name: 'phone', className: 'dt-cell-muted' },
                { data: 'mobile', name: 'mobile', className: 'dt-cell-muted' },
                { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at', className: 'dt-cell-muted' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
    </script>
@endpush
