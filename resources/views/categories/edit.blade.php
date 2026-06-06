@extends('layouts.main', ['title' => __('general.edit_category')])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.edit_category')</h1>
            <p>@lang('general.edit_category_desc')</p>
        </div>

        <form action="{{ route("$route.update", ['encryptedId' => $encryptedId]) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">

                <div class="flex flex-col gap-6 p-6">

                    <x-form.field name="name" :label="__('general.name')" :required="true">
                        <input id="name"
                               type="text"
                               name="name"
                               value="{{ old('name', $data->name) }}"
                               placeholder="{{ __('general.category_name_placeholder') }}"
                               class="input {{ $errors->has('name') ? 'border-destructive' : '' }}">
                    </x-form.field>

                </div>

                @include('partials.form-actions-edit')

            </div>
        </form>

    </div>
@endsection
