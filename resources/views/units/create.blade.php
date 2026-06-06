@extends('layouts.main', ['title' => __('general.add_unit')])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.add_unit')</h1>
            <p>@lang('general.add_unit_desc')</p>
        </div>

        <form action="{{ route("$route.store") }}" method="POST">
            @csrf

            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">

                <div class="flex flex-col gap-6 p-6">

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                        <x-form.field name="name" :label="__('general.name')" :required="true">
                            <input id="name"
                                   type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="{{ __('general.unit_name_placeholder') }}"
                                   class="input {{ $errors->has('name') ? 'border-destructive' : '' }}">
                        </x-form.field>

                        <x-form.field name="abbreviation" :label="__('general.abbreviation')" :required="true">
                            <input id="abbreviation"
                                   type="text"
                                   name="abbreviation"
                                   value="{{ old('abbreviation') }}"
                                   placeholder="{{ __('general.unit_abbreviation_placeholder') }}"
                                   class="input {{ $errors->has('abbreviation') ? 'border-destructive' : '' }}">
                        </x-form.field>

                    </div>

                </div>

                @include('partials.form-actions-create')

            </div>
        </form>

    </div>
@endsection
