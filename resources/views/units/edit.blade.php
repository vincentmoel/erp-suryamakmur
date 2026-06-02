@extends('layouts.main', ['title' => "Edit $title"])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Edit Unit</h1>
            <p>Update unit information.</p>
        </div>

        <form action="{{ route("$route.update", ['encryptedId' => $encryptedId]) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">

                <div class="flex flex-col gap-6 p-6">

                    <x-form.field name="name" label="Name" :required="true">
                        <input id="name"
                               type="text"
                               name="name"
                               value="{{ old('name', $data->name) }}"
                               placeholder="Unit name (e.g. kg, pcs, liter)"
                               class="input {{ $errors->has('name') ? 'border-destructive' : '' }}">
                    </x-form.field>

                </div>

                @include('partials.form-actions-edit')

            </div>
        </form>

    </div>
@endsection
