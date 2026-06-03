@extends('layouts.main', ['title' => $data->name])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Vendor Detail</h1>
            <p>View vendor information.</p>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-xs">

            <div class="flex items-center gap-3 border-b px-6 py-4">
                <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                    <x-icon name="building" class="size-4 text-primary" />
                </div>
                <h3 class="text-sm font-semibold">Vendor Information</h3>
            </div>

            {{-- Code & Type --}}
            <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Code</span>
                    <span class="text-sm font-medium">{{ $data->code }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Type</span>
                    <span class="text-sm font-medium">{{ $data->type->label() }}</span>
                </div>
            </div>

            {{-- Name & NPWP --}}
            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Name</span>
                    <span class="text-sm font-medium">{{ $data->name }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">NPWP</span>
                    <span class="text-sm font-medium">{{ $data->npwp ?? '-' }}</span>
                </div>
            </div>

            {{-- Phone & Email --}}
            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Phone</span>
                    <span class="text-sm font-medium">{{ $data->phone ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Email</span>
                    <span class="text-sm font-medium">{{ $data->email ?? '-' }}</span>
                </div>
            </div>

            {{-- Website & Contact Person --}}
            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Website</span>
                    <span class="text-sm font-medium">{{ $data->website ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Contact Person</span>
                    <span class="text-sm font-medium">{{ $data->contact_person ?? '-' }}</span>
                </div>
            </div>

            {{-- Address --}}
            <div class="border-t">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Address</span>
                    <span class="text-sm font-medium">{{ $data->address ?? '-' }}</span>
                </div>
            </div>

            {{-- City & Province --}}
            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">City</span>
                    <span class="text-sm font-medium">{{ $data->city ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Province</span>
                    <span class="text-sm font-medium">{{ $data->province ?? '-' }}</span>
                </div>
            </div>

            {{-- Postal Code --}}
            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Postal Code</span>
                    <span class="text-sm font-medium">{{ $data->postal_code ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4"></div>
            </div>

            {{-- Bank --}}
            <div class="flex items-center gap-3 border-t border-b px-6 py-4">
                <h3 class="text-sm font-semibold">Bank Information</h3>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Bank Name</span>
                    <span class="text-sm font-medium">{{ $data->bank_name ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Account Number</span>
                    <span class="text-sm font-medium">{{ $data->bank_account_number ?? '-' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Account Name</span>
                    <span class="text-sm font-medium">{{ $data->bank_account_name ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4"></div>
            </div>

            {{-- Notes --}}
            <div class="border-t">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Notes</span>
                    <span class="text-sm font-medium">{{ $data->notes ?? '-' }}</span>
                </div>
            </div>

            {{-- Meta --}}
            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Created At</span>
                    <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y | H:i') }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Last Updated</span>
                    <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($data->updated_at)->translatedFormat('d F Y | H:i') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Created By</span>
                    <span class="text-sm font-medium">{{ $data->user_created_by->name ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Updated By</span>
                    <span class="text-sm font-medium">{{ $data->user_updated_by->name ?? '-' }}</span>
                </div>
            </div>

            <div class="flex gap-2 border-t px-6 py-4">
                <a href="{{ route('vendors.edit', ['encryptedId' => $encryptedId]) }}"
                   class="btn btn-ghost btn-sm">
                    <x-icon name="edit" class="size-3.5" />
                    Edit
                </a>
            </div>

        </div>

    </div>
@endsection
