@extends('layouts.main', ['title' => 'Roles'])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Roles</h1>
            <p>Manage roles and their permissions.</p>
        </div>

        <x-datatable id="roles-table" search-placeholder="Search">

            <x-slot name="actions">
                @if(app(\App\Services\PermissionService::class)->has('Role', 'create'))
                <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> Add Role
                </a>
                @endif
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>Name</th>
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
            tableId: 'roles-table',
            ajaxUrl: '{{ route('roles.index') }}',
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
