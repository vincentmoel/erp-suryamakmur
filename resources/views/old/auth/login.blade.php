@extends('layouts.blank')

@section('content')
    <div
        class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100">
                <div class="col-md-8 col-lg-6 col-xxl-3">
                    <div class="card mb-0">
                        <div class="card-body">
                            <span class="text-nowrap logo-img text-center d-block mb-4 w-100">
                                <img src="{{ asset('src/images/logos/logo.webp') }}" width="200">
                            </span>

                            <form method="POST" action="{{ route('authenticate') }}">
                                @csrf
                                <div class="mb-3">

                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" id="username"
                                        aria-describedby="emailHelp">

                                    @error('username')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>
                                <div class="mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" id="password">

                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <button class="btn btn-dark w-100 py-8 rounded-2" type="submit">Sign In</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
