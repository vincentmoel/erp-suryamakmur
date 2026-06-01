@extends('layouts.main', ['title' => 'Users | ' . config('app.name')])

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Users</h1>
            <p>Manage user accounts and permissions.</p>
        </div>

        <x-datatable id="users-table" search-placeholder="Search name or username...">

            <x-slot name="actions">
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                    <x-icon name="plus" /> Add User
                </a>
            </x-slot>

            <x-slot name="head">
                <th>#</th>
                <th>Name</th>
                <th>Username</th>
                <th>Last Seen</th>
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
            tableId: 'users-table',
            ajaxUrl: '{{ route('users.index') }}',
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    render: d => `<span class="dt-cell-index">${d}</span>`,
                },
                {
                    data: 'name',
                    name: 'name',
                    render: d => `<span>${d}</span>`,
                },
                {
                    data: 'username',
                    name: 'username',
                    render: d => `<span class="dt-cell-muted">${d}</span>`,
                },
                {
                    data: 'last_seen',
                    name: 'last_seen',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: d => `<span class="dt-cell-muted">${d}</span>`,
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                },
            ],
        });
    </script>
@endpush
