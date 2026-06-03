@extends('layouts.main', ['title' => 'Vendors'])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Vendors</h1>
            <p>Manage vendor data.</p>
        </div>

        <x-datatable id="vendors-table" search-placeholder="Search">

            <x-slot name="actions">
                <a href="{{ route('vendors.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> Add Vendor
                </a>
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Type</th>
                <th>Phone</th>
                <th>Email</th>
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
        initDataTable({
            tableId: 'vendors-table',
            ajaxUrl: '{{ route('vendors.index') }}',
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'dt-cell-index dt-center'
                },
                { data: 'code', name: 'code', className: 'dt-cell-muted' },
                { data: 'name', name: 'name' },
                { data: 'type', name: 'type' },
                { data: 'phone', name: 'phone', className: 'dt-cell-muted' },
                { data: 'email', name: 'email', className: 'dt-cell-muted' },
                { data: 'created_at', name: 'created_at', className: 'dt-cell-muted' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
    </script>
@endpush
