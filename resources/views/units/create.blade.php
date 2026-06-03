@extends('layouts.main', ['title' => "Add $title"])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Add Unit</h1>
            <p>Create a new unit of measurement.</p>
        </div>

        <form action="{{ route("$route.store") }}" method="POST">
            @csrf

            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">

                <div class="flex flex-col gap-6 p-6">

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                        <x-form.field name="name" label="Name" :required="true">
                            <input id="name"
                                   type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="e.g. Kilogram"
                                   class="input {{ $errors->has('name') ? 'border-destructive' : '' }}">
                        </x-form.field>

                        <x-form.field name="abbreviation" label="Abbreviation" :required="true">
                            <input id="abbreviation"
                                   type="text"
                                   name="abbreviation"
                                   value="{{ old('abbreviation') }}"
                                   placeholder="e.g. Kg"
                                   class="input {{ $errors->has('abbreviation') ? 'border-destructive' : '' }}">
                        </x-form.field>

                    </div>

                </div>

                @include('partials.form-actions-create')

            </div>
        </form>

    </div>
@endsection
