@extends('layouts.main', ['title' => 'Add User | ' . config('app.name')])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Add User</h1>
            <p>Create a new user account.</p>
        </div>

        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">

                <div class="flex flex-col gap-6 p-6">

                    {{-- Row 1: Name & Username --}}
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="name" label="Name" :required="true">
                            <input id="name"
                                   type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Full name"
                                   autocomplete="name"
                                   class="input {{ $errors->has('name') ? 'border-destructive' : '' }}">
                        </x-form.field>

                        <x-form.field name="username" label="Username" :required="true"
                                      hint="Lowercase letters, numbers, and underscores only.">
                            <input id="username"
                                   type="text"
                                   name="username"
                                   value="{{ old('username') }}"
                                   placeholder="e.g. john_doe"
                                   autocomplete="username"
                                   class="input {{ $errors->has('username') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    {{-- Row 2: Password & Confirm Password --}}
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="password" label="Password" :required="true">
                            <input id="password"
                                   type="password"
                                   name="password"
                                   placeholder="Min. 8 characters"
                                   autocomplete="new-password"
                                   class="input {{ $errors->has('password') ? 'border-destructive' : '' }}">
                        </x-form.field>

                        <x-form.field name="password_confirmation" label="Confirm Password" :required="true">
                            <input id="password_confirmation"
                                   type="password"
                                   name="password_confirmation"
                                   placeholder="Repeat password"
                                   autocomplete="new-password"
                                   class="input {{ $errors->has('password_confirmation') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    {{-- Row 3: Roles (full width) --}}
                    <x-form.field name="roles" label="Roles" :required="true">
                        <x-form.multi-select
                            name="roles"
                            placeholder="Select one or more roles..."
                            :options="$roles->map(fn($r) => ['value' => $r->id, 'label' => $r->name])->toArray()" />
                    </x-form.field>

                    {{-- Row 4: Photo --}}
                    <x-form.field name="photo" label="Photo">
                        <x-form.file-upload name="photo" :max-size-mb="2" />
                    </x-form.field>

                </div>

                <div class="flex items-center justify-end border-t px-6 py-4">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i data-lucide="save" class="size-3.5"></i>
                        Save
                    </button>
                </div>

            </div>
        </form>

    </div>
@endsection
