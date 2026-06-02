@extends('layouts.main', ['title' => $user->name])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>User Detail</h1>
            <p>View user account information.</p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Left: Profile Card --}}
            <div class="flex flex-col gap-4">

                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex flex-col items-center gap-4 p-6">

                        {{-- Avatar --}}
                        <div class="relative">
                            <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('src/img/default-profile.jpg') }}"
                                 alt="{{ $user->name }}"
                                 class="size-24 rounded-full object-cover ring-4 ring-border">

                            {{-- Online indicator --}}
                            @php
                                $isOnline = $user->last_seen && \Carbon\Carbon::parse($user->last_seen)->diffInMinutes() < 3;
                            @endphp
                            <span class="absolute bottom-1 right-1 size-3.5 rounded-full border-2 border-card {{ $isOnline ? 'bg-green-500' : 'bg-muted-foreground/40' }}"></span>
                        </div>

                        {{-- Name & Status --}}
                        <div class="flex flex-col items-center gap-1.5 text-center">
                            <h2 class="text-lg font-semibold leading-tight">{{ $user->name }}</h2>
                            <p class="text-sm text-muted-foreground">@{{ $user->username }}</p>

                            <span class="mt-1 inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium
                                {{ $isOnline ? 'bg-green-500/10 text-green-600' : 'bg-muted text-muted-foreground' }}">
                                <span class="size-1.5 rounded-full {{ $isOnline ? 'bg-green-500' : 'bg-muted-foreground/50' }}"></span>
                                {{ $isOnline ? 'Online' : 'Offline' }}
                            </span>
                        </div>

                        {{-- Roles --}}
                        @if($user->roles->isNotEmpty())
                            <div class="flex flex-wrap justify-center gap-1.5">
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary/10 text-primary">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        @endif

                    </div>

                    <div class="flex gap-2 border-t px-6 py-4">
                        <a href="{{ route('users.edit', ['encryptedId' => $encryptedId]) }}"
                           class="btn btn-outline btn-sm flex-1">
                            <i data-lucide="pencil" class="size-3.5"></i>
                            Edit
                        </a>
                        <a href="{{ route('users.index') }}"
                           class="btn btn-ghost btn-sm flex-1">
                            <i data-lucide="arrow-left" class="size-3.5"></i>
                            Back
                        </a>
                    </div>
                </div>

            </div>

            {{-- Right: Info Cards --}}
            <div class="flex flex-col gap-4 lg:col-span-2">

                {{-- Account Info --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                            <i data-lucide="user" class="size-4 text-primary"></i>
                        </div>
                        <h3 class="text-sm font-semibold">Account Information</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">

                        <div class="flex flex-col gap-1 px-6 py-4">
                            <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Full Name</span>
                            <span class="text-sm font-medium">{{ $user->name }}</span>
                        </div>

                        <div class="flex flex-col gap-1 px-6 py-4">
                            <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Username</span>
                            <span class="text-sm font-medium font-mono">{{ $user->username }}</span>
                        </div>

                    </div>
                </div>

                {{-- Roles --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                            <i data-lucide="shield-check" class="size-4 text-primary"></i>
                        </div>
                        <h3 class="text-sm font-semibold">Roles & Permissions</h3>
                    </div>

                    <div class="px-6 py-4">
                        @if($user->roles->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary/10 text-primary text-xs px-3 py-1">
                                        <i data-lucide="shield" class="size-3 mr-1"></i>
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-muted-foreground italic">No roles assigned.</p>
                        @endif
                    </div>
                </div>

                {{-- Activity --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                            <i data-lucide="activity" class="size-4 text-primary"></i>
                        </div>
                        <h3 class="text-sm font-semibold">Activity</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">

                        <div class="flex flex-col gap-1 px-6 py-4">
                            <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Last Seen</span>
                            <span class="text-sm font-medium">
                                @if($isOnline)
                                    <span class="text-green-600 font-medium">● Online now</span>
                                @elseif($user->last_seen)
                                    {{ \Carbon\Carbon::parse($user->last_seen)->diffForHumans() }}
                                @else
                                    <span class="text-muted-foreground italic">Never logged in</span>
                                @endif
                            </span>
                        </div>

                        <div class="flex flex-col gap-1 px-6 py-4">
                            <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Member Since</span>
                            <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d F Y') }}</span>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">

                        <div class="flex flex-col gap-1 px-6 py-4">
                            <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Created By</span>
                            <span class="text-sm font-medium">{{ $user->user_created_by->name ?? '-' }}</span>
                        </div>

                        <div class="flex flex-col gap-1 px-6 py-4">
                            <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Last Updated</span>
                            <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($user->updated_at)->translatedFormat('d F Y | H:i') }}</span>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
