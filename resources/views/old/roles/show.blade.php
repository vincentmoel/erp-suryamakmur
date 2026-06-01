@extends('layouts.main', [
    'title' => 'Role Details',
    'breadcrumbs' => [['title' => 'Roles', 'link' => route('roles.index')], ['title' => 'Role Details']],
])

@section('css')
    <link rel="stylesheet"
        href="{{ asset('src/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css?' . date('H:i:s')) }}">
@endsection


@section('content')
    <div class="card">
        <div class="card-body">
            <h6>Role Information</h6>

            <div class="line-separator"></div>

            <table class="table border customize-table">
                <tbody>
                    <tr>
                        <th>Name</th>
                        <td>{{ $role['name'] }}</td>
                    </tr>
                    <tr>
                        <th>Created By</th>
                        <td>{{ $role->user_created_by->name }}</td>
                    </tr>
                    <tr>
                        <th>Updated By</th>
                        <td>{{ $role->user_updated_by->name }}</td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ \Carbon\Carbon::parse($role['created_at'])->translatedFormat('d F Y (H:i:s)') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ \Carbon\Carbon::parse($role['updated_at'])->translatedFormat('d F Y (H:i:s)') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <section class="datatables datatable-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-4">
                            <h6>Users by Role</h6>
                            <div class="line-separator"></div>
                        </div>
                        <div class="table-responsive">
                            <table class="dataTable table border table-striped table-bordered text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Created By</th>
                                        <th>Updated By</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($role->users as $user)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->username }}</td>
                                            <td>{{ $user->user_created_by->name }}</td>
                                            <td>{{ $user->user_updated_by->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d F Y | H:i:s') }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($user->updated_at)->translatedFormat('d F Y | H:i:s') }}
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <a href="{{ route('users.show', ['encryptedId' => \App\Helpers\Encryption::encrypt($user->id)]) }}"
                                                        type="button"
                                                        class="btn btn-sm btn-light-primary text-primary">
                                                        <i class="ti ti-eye fs-4"></i>
                                                    </a>

                                                    <form action="{{ route('roles.delete-user-role', ['encryptedRoleId' => \App\Helpers\Encryption::encrypt($role->id), 'encryptedUserId' => \App\Helpers\Encryption::encrypt($user->id)]) }}"
                                                        method="POST"
                                                        class="btn btn-sm btn-light-danger text-danger delete-button">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <input type="hidden" name="_token"
                                                            value="MSiJ7wzDMNNH8u08kaos3ZlnZ6xtENopHcYE7yeZ">
                                                        <i class="ti ti-trash fs-4"></i>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <h6>Permissions</h6>

            <div class="line-separator"></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="text-center table table-bordered">
                            <thead>
                                <tr>
                                    <th class="col">Modules</th>
                                    <th colspan="6">Permission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($modules as $module)
                                    <tr>
                                        <td>
                                            {{ $module }}
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input" disabled
                                                name="permission[{{ $module }}][menu]"
                                                id="permission[{{ $module }}][menu]"
                                                {{ in_array(old('permission.' . $module . '.menu', $role['permissions'][$loop->index]['menu'] ?? 0), ['on', 1]) ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="permission[{{ $module }}][menu]">Menu</label>
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input" disabled
                                                name="permission[{{ $module }}][create]"
                                                id="permission[{{ $module }}][create]"
                                                {{ in_array(old('permission.' . $module . '.create', $role['permissions'][$loop->index]['create'] ?? 0), ['on', 1]) ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="permission[{{ $module }}][create]">Create</label>
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input" disabled
                                                name="permission[{{ $module }}][read]"
                                                id="permission[{{ $module }}][read]"
                                                {{ in_array(old('permission.' . $module . '.read', $role['permissions'][$loop->index]['read'] ?? 0), ['on', 1]) ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="permission[{{ $module }}][read]">Read</label>
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input" disabled
                                                name="permission[{{ $module }}][update]"
                                                id="permission[{{ $module }}][update]"
                                                {{ in_array(old('permission.' . $module . '.update', $role['permissions'][$loop->index]['update'] ?? 0), ['on', 1]) ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="permission[{{ $module }}][update]">Update</label>
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input" disabled
                                                name="permission[{{ $module }}][delete]"
                                                id="permission[{{ $module }}][delete]"
                                                {{ in_array(old('permission.' . $module . '.delete', $role['permissions'][$loop->index]['delete'] ?? 0), ['on', 1]) ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="permission[{{ $module }}][delete]">Delete</label>
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input" disabled
                                                name="permission[{{ $module }}][restore]"
                                                id="permission[{{ $module }}][restore]"
                                                {{ in_array(old('permission.' . $module . '.restore', $role['permissions'][$loop->index]['restore'] ?? 0), ['on', 1]) ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="permission[{{ $module }}][restore]">Restore</label>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('src/libs/datatables.net/js/jquery.dataTables.min.js?' . date('Y-m-d H:i:s')) }}"></script>
    <script src="{{ asset('src/js/custom-script.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.dataTable').DataTable({
                stripeClasses: false,
                processing: true,
                scrollX: true,
                responsive: true,
            });
        });
    </script>
@endsection
