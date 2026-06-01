@extends('layouts.blank')

@section('content')
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-sidebartype="full" data-sidebar-position="fixed"
        data-header-position="fixed">
        <div class="position-relative overflow-hidden min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-lg-4">
                        <div class="text-center">
                            <img src="{{ asset('src/images/backgrounds/maintenance.svg') }}" alt="" class="img-fluid"
                                width="500">
                            <h1 class="fw-semibold my-7 fs-9">Maintenance Mode!!!</h1>
                            <h4 class="fw-semibold mb-7">Website is Under Construction. Check back later!</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
