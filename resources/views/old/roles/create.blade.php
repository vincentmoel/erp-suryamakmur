@extends('layouts.main', [
    'title' => 'Create Role',
    'breadcrumbs' => [['title' => 'Roles', 'link' => route('roles.index')], ['title' => 'Create Role']],
])

@section('content')
    <div class="card">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf

            <div class="card-body">
                <h6>Role Information</h6>

                <div class="line-separator"></div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="name" placeholder="Name" name="name"
                                value="{{ old('name') }}">
                            <label for="name">Name *</label>

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

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
                                        <th>Select All</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($modules as $moduleKey => $moduleValue)
                                        <tr>
                                            <td>
                                                {{ $moduleValue }}
                                            </td>
                                            <td>
                                                <input type="checkbox" class="form-check-input"
                                                    name="permission[{{ $moduleKey }}][menu]"
                                                    id="permission[{{ $moduleKey }}][menu]"
                                                    {{ old("permission.$moduleKey.menu") == 'on' ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="permission[{{ $moduleKey }}][menu]">Menu</label>
                                            </td>
                                            <td>
                                                <input type="checkbox" class="form-check-input"
                                                    name="permission[{{ $moduleKey }}][create]"
                                                    id="permission[{{ $moduleKey }}][create]"
                                                    {{ old("permission.$moduleKey.create") == 'on' ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="permission[{{ $moduleKey }}][create]">Create</label>
                                            </td>
                                            <td>
                                                <input type="checkbox" class="form-check-input"
                                                    name="permission[{{ $moduleKey }}][read]"
                                                    id="permission[{{ $moduleKey }}][read]"
                                                    {{ old("permission.$moduleKey.read") == 'on' ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="permission[{{ $moduleKey }}][read]">Read</label>
                                            </td>
                                            <td>
                                                <input type="checkbox" class="form-check-input"
                                                    name="permission[{{ $moduleKey }}][update]"
                                                    id="permission[{{ $moduleKey }}][update]"
                                                    {{ old("permission.$moduleKey.update") == 'on' ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="permission[{{ $moduleKey }}][update]">Update</label>
                                            </td>
                                            <td>
                                                <input type="checkbox" class="form-check-input"
                                                    name="permission[{{ $moduleKey }}][delete]"
                                                    id="permission[{{ $moduleKey }}][delete]"
                                                    {{ old("permission.$moduleKey.delete") == 'on' ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="permission[{{ $moduleKey }}][delete]">Delete</label>
                                            </td>
                                            <td>
                                                <input type="checkbox" class="form-check-input"
                                                    name="permission[{{ $moduleKey }}][restore]"
                                                    id="permission[{{ $moduleKey }}][restore]"
                                                    {{ old("permission.$moduleKey.restore") == 'on' ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="permission[{{ $moduleKey }}][restore]">Restore</label>
                                            </td>
                                            <td>
                                                <button type="button" class="action-btn btn btn-outline-dark mb-2"
                                                    style="width: 95px"></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-md-flex align-items-center mt-4">
                    <div class="mt-3 mt-md-0">
                        <button type="submit" class="btn btn-info font-medium rounded-pill px-4">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.action-btn').each(function() {
                updateButton($(this));
            });

            $('.action-btn').click(function() {
                var row = $(this).closest('tr');
                var checkboxes = row.find('input[type="checkbox"]');
                var allChecked = checkboxes.length === checkboxes.filter(':checked').length;

                checkboxes.prop('checked', !allChecked);
                updateButton($(this));
            });

            $('input[type="checkbox"]').change(function() {
                updateButton($(this).closest('tr').find('.action-btn'));
            });
        });

        function updateButton(button) {
            var row = button.closest('tr');
            var checkboxes = row.find('input[type="checkbox"]');
            var allChecked = checkboxes.length === checkboxes.filter(':checked').length;

            button.text(allChecked ? 'Disable' : 'Enable');
            button.addClass(allChecked ? 'btn-outline-danger' : 'btn-outline-dark');
            button.removeClass(allChecked ? 'btn-outline-dark' : 'btn-outline-danger');
        }
    </script>
@endsection
