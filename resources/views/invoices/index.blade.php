@extends('layouts.main', ['title' => 'Invoices'])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Invoices</h1>
            <p>Manage sales invoices.</p>
        </div>

        <x-datatable id="invoices-table" search-placeholder="Search invoice...">

            <x-slot name="actions">
                <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> Add Invoice
                </a>
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>Code</th>
                <th>Customer</th>
                <th>Salesperson</th>
                <th>Date</th>
                <th>Total</th>
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
            tableId: 'invoices-table',
            ajaxUrl: '{{ route('invoices.index') }}',
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'dt-cell-index dt-center'
                },
                { data: 'code', name: 'code' },
                { data: 'customer_id', name: 'customer_id' },
                { data: 'salesperson_id', name: 'salesperson_id', className: 'dt-cell-muted' },
                { data: 'invoice_date', name: 'invoice_date', className: 'dt-cell-muted' },
                { data: 'amount', name: 'amount' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at', className: 'dt-cell-muted' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
    </script>
@endpush
