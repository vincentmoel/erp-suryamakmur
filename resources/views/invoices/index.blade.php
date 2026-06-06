@extends('layouts.main', ['title' => __('general.invoices')])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.invoices')</h1>
            <p>@lang('general.invoices_desc')</p>
        </div>

        {{-- Filter Bar --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-xs mb-4">
            <div class="px-6 py-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                {{-- Customer --}}
                <div class="flex flex-col gap-3">
                    <label class="text-sm font-medium leading-none">@lang('general.customer')</label>
                    <x-form.single-select
                        name="filter_customer"
                        placeholder="All Customers"
                        :searchable="true"
                        :options="$customers->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()" />
                </div>

                {{-- Salesperson --}}
                <div class="flex flex-col gap-3">
                    <label class="text-sm font-medium leading-none">@lang('general.salesperson')</label>
                    <x-form.single-select
                        name="filter_salesperson"
                        placeholder="All Salespersons"
                        :searchable="true"
                        :options="$salespersons->map(fn($s) => ['value' => $s->id, 'label' => $s->name])->toArray()" />
                </div>

                {{-- Invoice Date Range --}}
                <div class="flex flex-col gap-3">
                    <label class="text-sm font-medium leading-none">@lang('general.invoice_date')</label>
                    <div class="relative">
                        <input id="filter-daterange"
                               type="text"
                               readonly
                               placeholder="All Dates"
                               class="input w-full cursor-pointer pr-8">
                        <span id="filter-daterange-clear"
                              class="hidden absolute right-2 top-1/2 -translate-y-1/2 flex items-center justify-center rounded text-muted-foreground hover:text-destructive opacity-60 hover:opacity-100 transition-opacity cursor-pointer"
                              title="Clear">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="size-3.5">
                                <path d="M18 6 6 18M6 6l12 12"/>
                            </svg>
                        </span>
                    </div>
                </div>

                {{-- Status --}}
                <div class="flex flex-col gap-3">
                    <label class="text-sm font-medium leading-none">@lang('general.status')</label>
                    <x-form.single-select
                        name="filter_status"
                        placeholder="All Statuses"
                        :searchable="false"
                        :options="collect($statuses)->map(fn($s) => ['value' => $s->value, 'label' => $s->label()])->toArray()" />
                </div>

            </div>
        </div>

        <x-datatable id="invoices-table" :search-placeholder="__('general.search')">

            <x-slot name="actions">
                <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> @lang('general.add_invoice')
                </a>
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>@lang('general.code')</th>
                <th>@lang('general.customer')</th>
                <th>@lang('general.salesperson')</th>
                <th>@lang('general.invoice_date')</th>
                <th>@lang('general.total')</th>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        var filterDateFrom = '';
        var filterDateTo   = '';

        const dt = initDataTable({
            tableId: 'invoices-table',
            ajaxUrl: '{{ route('invoices.index') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'dt-cell-index dt-center' },
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

        // Inject filter params into every DataTable AJAX call
        dt.settings()[0].ajax.data = function (d) {
            d.filter_customer    = document.querySelector('[name="filter_customer"]').value;
            d.filter_salesperson = document.querySelector('[name="filter_salesperson"]').value;
            d.filter_date_from   = filterDateFrom;
            d.filter_date_to     = filterDateTo;
            d.filter_status      = document.querySelector('[name="filter_status"]').value;
        };

        // Auto-reload on single-select change
        document.querySelectorAll('[name="filter_customer"], [name="filter_salesperson"], [name="filter_status"]')
            .forEach(function (input) {
                input.addEventListener('change', function () { dt.ajax.reload(); });
            });

        // Flatpickr date range
        const fpClear = document.getElementById('filter-daterange-clear');

        const fp = flatpickr('#filter-daterange', {
            mode: 'range',
            dateFormat: 'd M Y',
            locale: { rangeSeparator: ' – ' },
            onClose: function (selectedDates) {
                if (selectedDates.length === 2) {
                    filterDateFrom = selectedDates[0].toISOString().slice(0, 10);
                    filterDateTo   = selectedDates[1].toISOString().slice(0, 10);
                    fpClear.classList.remove('hidden');
                } else if (selectedDates.length === 0) {
                    filterDateFrom = '';
                    filterDateTo   = '';
                    fpClear.classList.add('hidden');
                }
                dt.ajax.reload();
            },
        });

        fpClear.addEventListener('click', function () {
            fp.clear();
            filterDateFrom = '';
            filterDateTo   = '';
            fpClear.classList.add('hidden');
            dt.ajax.reload();
        });
    </script>
@endpush
