@extends('layouts.main', ['title' => 'Edit User'])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Edit User</h1>
            <p>Update user account information.</p>
        </div>

        <form action="{{ route('users.update', ['encryptedId' => $encryptedId]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">

                <div class="flex flex-col gap-6 p-6">

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="name" label="Name" :required="true">
                            <input id="name"
                                   type="text"
                                   name="name"
                                   value="{{ old('name', $user->name) }}"
                                   placeholder="Full name"
                                   autocomplete="name"
                                   class="input {{ $errors->has('name') ? 'border-destructive' : '' }}">
                        </x-form.field>

                        <x-form.field name="username" label="Username" :required="true"
                                      hint="Lowercase letters, numbers, and underscores only.">
                            <input id="username"
                                   type="text"
                                   name="username"
                                   value="{{ old('username', $user->username) }}"
                                   placeholder="e.g. john_doe"
                                   autocomplete="username"
                                   class="input {{ $errors->has('username') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="password" label="Password"
                                      hint="Leave blank to keep current password.">
                            <input id="password"
                                   type="password"
                                   name="password"
                                   placeholder="Min. 8 characters"
                                   autocomplete="new-password"
                                   class="input {{ $errors->has('password') ? 'border-destructive' : '' }}">
                        </x-form.field>

                        <x-form.field name="password_confirmation" label="Confirm Password">
                            <input id="password_confirmation"
                                   type="password"
                                   name="password_confirmation"
                                   placeholder="Repeat password"
                                   autocomplete="new-password"
                                   class="input {{ $errors->has('password_confirmation') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    <x-form.field name="roles" label="Roles" :required="true">
                        <x-form.multi-select
                            name="roles"
                            placeholder="Select one or more roles..."
                            :options="$roles->map(fn($r) => ['value' => $r->id, 'label' => $r->name])->toArray()"
                            :selected="old('roles', $user->roles->pluck('id')->toArray())" />
                    </x-form.field>

                    <x-form.field name="photo" label="Photo">
                        <x-form.file-upload
                            name="photo"
                            :max-size-mb="2"
                            :preview="$user->photo ? asset('storage/' . $user->photo) : null" />
                    </x-form.field>

                </div>

                <div class="flex items-center justify-end gap-2 border-t px-6 py-4">
                    <button type="submit" name="_action" value="update" class="btn btn-primary">
                        <x-icon name="refresh-cw" class="size-3.5" />
                        Update
                    </button>
                </div>

            </div>
        </form>

    </div>
@endsection
