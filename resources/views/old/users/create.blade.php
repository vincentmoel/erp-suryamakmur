@extends('layouts.main', [
    "title" => "Create User",
    "breadcrumbs" => [
        ['title' => 'Users', 'link' => route('users.index')],
        ['title' => 'Create User'],
    ]
])

@section('content')
    <div class="card">
        <div class="card-body">
            <h6>User Information</h6>

            <div class="line-separator"></div>

            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="name" placeholder="Name" name="name" value="{{ old('name') }}">
                            <label for="name">Name</label>

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="username" placeholder="Username" name="username" value="{{ old('username') }}">
                            <label for="username">Username</label>

                            @error('username')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-floating">
                            <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                            <label for="password">Password</label>

                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="password" class="form-control" id="password_confirmation" placeholder="Password Confirmation" name="password_confirmation"/>
                            <label for="password_confirmation">Confirm Password</label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <select class="select2 form-select mr-sm-2" id="roles" name="roles[]" multiple>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" @selected(in_array($role->id, old('roles', [])))>{{ $role->name }}</option>
                                @endforeach
                            </select>

                            @error('roles')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
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
            </form>
        </div>
    </div>
@endsection

@section('script')

    <script>
        $(document).ready(function(){
            $(".select2").select2({
                placeholder: "Select Role",
            });
        });
    </script>
    
@endsection
