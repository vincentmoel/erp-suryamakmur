@extends('layouts.main', ['title' => __('general.units')])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.units')</h1>
            <p>@lang('general.units_desc')</p>
        </div>

        <x-datatable id="units-table" :search-placeholder="__('general.search')">

            <x-slot name="actions">
                <a href="{{ route('units.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> @lang('general.add_unit')
                </a>
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>@lang('general.name')</th>
                <th>@lang('general.abbreviation')</th>
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
            tableId: 'units-table',
            ajaxUrl: '{{ route('units.index') }}',
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'dt-cell-index dt-center'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'abbreviation',
                    name: 'abbreviation'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    className: 'dt-cell-muted'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
        });
    </script>
@endpush
