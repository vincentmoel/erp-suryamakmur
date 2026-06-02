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

                                @php
                                $isOnline = $user->last_seen && \Carbon\Carbon::parse($user->last_seen)->diffInMinutes() < 3;
                            @endphp
                            <span class="absolute bottom-1 right-1 size-3.5 rounded-full border-2 border-card {{ $isOnline ? 'bg-green-500' : 'bg-muted-foreground/40' }}"></span>
                        </div>

                        {{-- Name & Status --}}
                        <div class="flex flex-col items-center gap-1.5 text-center">
                            <h2 class="text-lg font-semibold leading-tight">{{ $user->name }}</h2>
                            <p class="text-sm text-muted-foreground">{{ $user->username }}</p>
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
                           class="btn btn-ghost btn-sm flex-1">
                            <x-icon name="edit" class="size-3.5" />
                            Edit
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
                            <x-icon name="user" class="size-4 text-primary" />
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

                {{-- Activity --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                            <x-icon name="calendar-clock" class="size-4 text-primary" />
                        </div>
                        <h3 class="text-sm font-semibold">Activity</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">

                        <div class="flex flex-col gap-1 px-6 py-4">
                            <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Last Seen</span>
                            <span class="text-sm font-medium">
                                @if($isOnline)
                                    <span class="dt-status dt-status--online">
                                        <x-icon name="circle-filled" style="width:0.55rem;height:0.55rem;flex-shrink:0;" />
                                        Online
                                    </span>
                                @elseif($user->last_seen)
                                    <span class="dt-status dt-status--offline">
                                        <x-icon name="circle-outline" style="width:0.55rem;height:0.55rem;flex-shrink:0;" />
                                        Offline ({{ \Carbon\Carbon::parse($user->last_seen)->diffForHumans() }})
                                    </span>
                                @else
                                    Never
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
