@extends('layouts.main', ['title' => $role->name])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>{{ $role->name }}</h1>
            <p>Role details and permissions.</p>
        </div>

        <div class="flex flex-col gap-6">

            {{-- Permission Matrix (read-only) --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                <div class="flex items-center justify-between gap-3 border-b px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex size-8 items-center justify-center">
                            <x-icon name="shield" class="size-4 text-primary" />
                        </div>
                        <h3 class="text-sm font-semibold">Permissions</h3>
                    </div>
                    <a href="{{ route('roles.edit', ['encryptedId' => $encryptedId]) }}" class="btn btn-ghost btn-sm">
                        <x-icon name="edit" class="size-3.5" /> Edit
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/30">
                                <th class="px-6 py-3 text-left font-medium text-muted-foreground w-48">Module</th>
                                @php $allActions = ['read','create','update','delete','restore','receive','cancel']; @endphp
                                @foreach ($allActions as $action)
                                    <th class="px-4 py-3 text-center font-medium text-muted-foreground capitalize">{{ \App\Enums\Module::actionLabel($action) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($modules as $module)
                                @php $available = $module->permissions(); @endphp
                                <tr class="hover:bg-muted/20 transition-colors">
                                    <td class="px-6 py-3 font-medium">{{ $module->label() }}</td>
                                    @foreach ($allActions as $action)
                                        <td class="px-4 py-3 text-center">
                                            @if (in_array($action, $available))
                                                @if (in_array($action, $granted[$module->value] ?? []))
                                                    <x-icon name="check" class="size-4 text-green-500 mx-auto" />
                                                @else
                                                    <x-icon name="x" class="size-4 text-muted-foreground/30 mx-auto" />
                                                @endif
                                            @else
                                                <span class="text-muted-foreground/20">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Users with this role --}}
            @if ($role->users->isNotEmpty())
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <div class="flex size-8 items-center justify-center">
                            <x-icon name="users" class="size-4 text-primary" />
                        </div>
                        <h3 class="text-sm font-semibold">Users with this Role</h3>
                    </div>
                    <div class="divide-y">
                        @foreach ($role->users as $user)
                            <div class="flex items-center justify-between px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('src/img/default-profile.jpg') }}"
                                         class="size-8 rounded-full object-cover" alt="{{ $user->name }}">
                                    <div>
                                        <p class="text-sm font-medium">{{ $user->name }}</p>
                                        <p class="text-xs text-muted-foreground">{{ $user->username }}</p>
                                    </div>
                                </div>
                                <form action="{{ route('roles.delete-user-role', ['encryptedRoleId' => $encryptedId, 'encryptedUserId' => \App\Helpers\Encryption::encrypt($user->id)]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm text-destructive hover:text-destructive">
                                        <x-icon name="trash" class="size-3.5" />
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>
@endsection
