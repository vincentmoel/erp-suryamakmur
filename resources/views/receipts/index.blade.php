@extends('layouts.main', ['title' => __('general.receipts')])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.receipts')</h1>
            <p>@lang('general.receipts_desc')</p>
        </div>

        <x-datatable id="receipts-table" :search-placeholder="__('general.search')">

            <x-slot name="filters">
                <x-form.single-select
                    name="filter_customer"
                    placeholder="All Customers"
                    :searchable="true"
                    :options="$customers->map(fn($c) => ['value' => $c->id, 'label' => $c->name, 'badge' => $c->trashed() ? 'Deleted' : null])->toArray()" />
                <x-form.single-select
                    name="filter_payment_method"
                    placeholder="All Payment Methods"
                    :searchable="false"
                    :options="collect($paymentMethods)->map(fn($m) => ['value' => $m->value, 'label' => $m->label()])->toArray()" />
                <x-form.daterange
                    name-from="filter_date_from"
                    name-to="filter_date_to"
                    placeholder="All Receipt Dates" />
            </x-slot>

            <x-slot name="actions">
@if(app(\App\Services\PermissionService::class)->has('Receipt', 'create'))
                <a href="{{ route('receipts.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> @lang('general.add_receipt')
                </a>
@endif
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>@lang('general.code')</th>
                <th>@lang('general.customer')</th>
                <th>@lang('general.receipt_date')</th>
                <th>@lang('general.payment_method')</th>
                <th>@lang('general.total')</th>
                <th>@lang('general.invoices')</th>
                <th>@lang('general.created_at')</th>
                <th></th>
            </x-slot>

        </x-datatable>

    </div>

    <x-confirm-modal
        id="receiptDeleteModal"
        ajax-method="DELETE"
        :title="__('general.confirm_delete_receipt_title')"
        :description="__('general.confirm_delete_receipt')"
        :confirm-label="__('general.delete')" />
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="{{ asset('src/js/datatable.js') }}"></script>
    <script>
        const dt = initDataTable({
            tableId: 'receipts-table',
            ajaxUrl: '{{ route('receipts.index') }}',
            order: [{ name: 'created_at', dir: 'desc' }],
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'dt-cell-index dt-center'
                },
                { data: 'code',           name: 'code' },
                { data: 'customer_id',    name: 'customer_id' },
                { data: 'receipt_date',   name: 'receipt_date', className: 'dt-cell-muted' },
                { data: 'payment_method', name: 'payment_method', className: 'dt-cell-muted' },
                { data: 'amount_total',      name: 'amount_total', orderable: false, searchable: false },
                { data: 'allocation_summary', name: 'allocation_summary', orderable: false, searchable: false, className: 'dt-cell-muted' },
                { data: 'created_at',     name: 'created_at', className: 'dt-cell-muted' },
                { data: 'action',         name: 'action', orderable: false, searchable: false },
            ],
        });

        dt.settings()[0].ajax.data = function (d) {
            d.filter_customer       = document.querySelector('[name="filter_customer"]').value;
            d.filter_payment_method = document.querySelector('[name="filter_payment_method"]').value;
            d.filter_date_from      = document.querySelector('[name="filter_date_from"]').value;
            d.filter_date_to        = document.querySelector('[name="filter_date_to"]').value;
        };

        document.querySelectorAll('[name="filter_customer"], [name="filter_payment_method"], [name="filter_date_from"]')
            .forEach(function (el) { el.addEventListener('change', function () { dt.ajax.reload(); }); });
    </script>
    <script>
    $(document).on('click', '.receipt-delete-btn', function () {
        window.confirmModal_receiptDeleteModal.open($(this).data('url'));
    });
    </script>
@endpush
