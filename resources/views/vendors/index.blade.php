@extends('layouts.main', ['title' => __('general.vendors')])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.vendors')</h1>
            <p>@lang('general.vendors_desc')</p>
        </div>

        <x-datatable id="vendors-table" :search-placeholder="__('general.search')">

            <x-slot name="actions">
@if(app(\App\Services\PermissionService::class)->has('Vendor', 'create'))
                <a href="{{ route('vendors.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> @lang('general.add_vendor')
                </a>
@endif
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>@lang('general.code')</th>
                <th>@lang('general.name')</th>
                <th>@lang('general.type')</th>
                <th>@lang('general.phone')</th>
                <th>@lang('general.email')</th>
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
                    label.text(res.data.is_active ? '{{ __('general.active') }}' : '{{ __('general.inactive') }}');
                },
            });
        });

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
                { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at', className: 'dt-cell-muted' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
    </script>
@endpush
