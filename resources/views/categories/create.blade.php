@extends('layouts.main', ['title' => "Add $title"])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Add Category</h1>
            <p>Create a new item category.</p>
        </div>

        <form action="{{ route("$route.store") }}" method="POST">
            @csrf

            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">

                <div class="flex flex-col gap-6 p-6">

                    <x-form.field name="name" label="Name" :required="true">
                        <input id="name"
                               type="text"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="Category name"
                               class="input {{ $errors->has('name') ? 'border-destructive' : '' }}">
                    </x-form.field>

                </div>

                @include('partials.form-actions-create')

            </div>
        </form>

    </div>
@endsection
