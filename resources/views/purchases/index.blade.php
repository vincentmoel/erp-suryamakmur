@extends('layouts.main', ['title' => 'Purchases'])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Purchases</h1>
            <p>Manage purchase orders from vendors.</p>
        </div>

        <x-datatable id="purchases-table" search-placeholder="Search purchase...">

            <x-slot name="actions">
                <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> Add Purchase
                </a>
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>Code</th>
                <th>Vendor</th>
                <th>Date</th>
                <th>Grand Total</th>
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
        initDataTable({
            tableId: 'purchases-table',
            ajaxUrl: '{{ route('purchases.index') }}',
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'dt-cell-index dt-center'
                },
                { data: 'code', name: 'code' },
                { data: 'vendor_id', name: 'vendor_id' },
                { data: 'purchase_date', name: 'purchase_date', className: 'dt-cell-muted' },
                { data: 'grand_total', name: 'grand_total' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at', className: 'dt-cell-muted' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
    </script>
@endpush
