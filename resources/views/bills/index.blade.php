@extends('layouts.main', ['title' => __('general.bills')])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.bills')</h1>
            <p>@lang('general.bills_desc')</p>
        </div>

        <x-datatable id="bills-table" :search-placeholder="__('general.search')">

            <x-slot name="actions">
@if(app(\App\Services\PermissionService::class)->has('Bill', 'create'))
                <a href="{{ route('bills.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> @lang('general.add_bill')
                </a>
@endif
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>@lang('general.code')</th>
                <th>@lang('general.vendor')</th>
                <th>@lang('general.bill_date')</th>
                <th>@lang('general.grand_total')</th>
                <th>@lang('general.status')</th>
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
            tableId: 'bills-table',
            ajaxUrl: '{{ route('bills.index') }}',
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
                { data: 'bill_date', name: 'bill_date', className: 'dt-cell-muted' },
                { data: 'grand_total', name: 'grand_total' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at', className: 'dt-cell-muted' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
    </script>
@endpush
