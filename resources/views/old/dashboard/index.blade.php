@php
    use App\Helpers\Encryption;
@endphp

@extends('layouts.main', [
    'title' => 'Dashboard',
    'useBreadcrumb' => false,
])

@section('css')
@endsection


@section('content')
@endsection

@section('script')
    <script src="{{ asset('src/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset('src/js/custom-script.js') }}"></script>
@endsection
