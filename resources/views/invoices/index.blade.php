@extends('layouts.main', ['title' => __('general.invoices')])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.invoices')</h1>
            <p>@lang('general.invoices_desc')</p>
        </div>

        <x-datatable id="invoices-table" :search-placeholder="__('general.search')">

            <x-slot name="filters">
                <x-form.single-select
                    name="filter_customer"
                    placeholder="All Customers"
                    :searchable="true"
                    :options="$customers->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()" />
                <x-form.single-select
                    name="filter_salesperson"
                    placeholder="All Salespersons"
                    :searchable="true"
                    :options="$salespersons->map(fn($s) => ['value' => $s->id, 'label' => $s->name])->toArray()" />
                <x-form.daterange
                    name-from="filter_date_from"
                    name-to="filter_date_to"
                    placeholder="All Dates" />
                <x-form.single-select
                    name="filter_status"
                    placeholder="All Statuses"
                    :searchable="false"
                    :options="collect($statuses)->map(fn($s) => ['value' => $s->value, 'label' => $s->label()])->toArray()" />
            </x-slot>

            <x-slot name="actions">
                @if(app(\App\Services\PermissionService::class)->has('Invoice', 'create'))
                <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> @lang('general.add_invoice')
                </a>
                @endif
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>@lang('general.code')</th>
                <th>@lang('general.customer')</th>
                <th>@lang('general.salesperson')</th>
                <th>@lang('general.invoice_date')</th>
                <th>@lang('general.total')</th>
                <th>@lang('general.paid_amount')</th>
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
        const dt = initDataTable({
            tableId: 'invoices-table',
            ajaxUrl: '{{ route('invoices.index') }}',
            order: [{ name: 'created_at', dir: 'desc' }],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'dt-cell-index dt-center' },
                { data: 'code', name: 'code' },
                { data: 'customer_id', name: 'customer_id', orderable: false },
                { data: 'salesperson_id', name: 'salesperson_id', orderable: false, className: 'dt-cell-muted' },
                { data: 'invoice_date', name: 'invoice_date', className: 'dt-cell-muted' },
                { data: 'amount', name: 'amount' },
                { data: 'paid_amount', name: 'paid_amount', orderable: false, searchable: false },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at', className: 'dt-cell-muted' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

        dt.settings()[0].ajax.data = function (d) {
            d.filter_customer    = document.querySelector('[name="filter_customer"]').value;
            d.filter_salesperson = document.querySelector('[name="filter_salesperson"]').value;
            d.filter_date_from   = document.querySelector('[name="filter_date_from"]').value;
            d.filter_date_to     = document.querySelector('[name="filter_date_to"]').value;
            d.filter_status      = document.querySelector('[name="filter_status"]').value;
        };

        document.querySelectorAll('[name="filter_customer"], [name="filter_salesperson"], [name="filter_status"], [name="filter_date_from"]')
            .forEach(function (el) { el.addEventListener('change', function () { dt.ajax.reload(); }); });
    </script>
@endpush
