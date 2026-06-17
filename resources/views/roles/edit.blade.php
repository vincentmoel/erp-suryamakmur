@extends('layouts.main', ['title' => 'Edit Role'])

@push('styles')
@include('roles._permission-styles')
@endpush

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Edit Role</h1>
            <p>Update role name and permissions.</p>
        </div>

        <form action="{{ route('roles.update', ['encryptedId' => $encryptedId]) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="flex flex-col gap-6">

                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex flex-col gap-6 p-6">
                        <x-form.field name="name" label="Role Name" :required="true">
                            <input id="name"
                                   type="text"
                                   name="name"
                                   value="{{ old('name', $role->name) }}"
                                   placeholder="e.g. Kasir, Manager, Admin"
                                   class="input {{ $errors->has('name') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>
                </div>

                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <div class="flex size-8 items-center justify-center">
                            <x-icon name="shield" class="size-4 text-primary" />
                        </div>
                        <h3 class="text-sm font-semibold">Permissions</h3>
                    </div>

                    @include('roles._permission-table')
                </div>

                @include('partials.form-actions-edit')

            </div>
        </form>

    </div>
@endsection

@push('scripts')
@include('roles._permission-script')
@endpush
