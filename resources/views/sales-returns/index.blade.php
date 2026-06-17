@extends('layouts.main', ['title' => __('general.sales_returns')])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.sales_returns')</h1>
            <p>@lang('general.sales_returns_desc')</p>
        </div>

        <x-datatable id="sales-returns-table" :search-placeholder="__('general.search')">

            <x-slot name="actions">
@if(app(\App\Services\PermissionService::class)->has('SalesReturn', 'create'))
                <a href="{{ route('sales-returns.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> @lang('general.add_sales_return')
                </a>
@endif
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>@lang('general.code')</th>
                <th>@lang('general.invoice')</th>
                <th>@lang('general.return_date')</th>
                <th>@lang('general.created_at')</th>
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
            tableId: 'sales-returns-table',
            ajaxUrl: '{{ route('sales-returns.index') }}',
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'dt-cell-index dt-center'
                },
                { data: 'code', name: 'code' },
                { data: 'invoice_id', name: 'invoice_id' },
                { data: 'return_date', name: 'return_date', className: 'dt-cell-muted' },
                { data: 'created_at', name: 'created_at', className: 'dt-cell-muted' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
    </script>
@endpush
