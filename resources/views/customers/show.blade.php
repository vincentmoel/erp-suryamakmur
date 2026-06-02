@extends('layouts.main', ['title' => $data->name])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Customer Detail</h1>
            <p>View customer information.</p>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-xs">

            <div class="flex items-center gap-3 border-b px-6 py-4">
                <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                    <x-icon name="contact" class="size-4 text-primary" />
                </div>
                <h3 class="text-sm font-semibold">Customer Information</h3>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">

                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Type</span>
                    <span class="text-sm font-medium">{{ $data->type->label() }}</span>
                </div>

                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Name</span>
                    <span class="text-sm font-medium">{{ $data->name }}</span>
                </div>

            </div>

            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">

                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Company Name</span>
                    <span class="text-sm font-medium">{{ $data->company_name ?? '-' }}</span>
                </div>

                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Tax Number</span>
                    <span class="text-sm font-medium">{{ $data->tax_number ?? '-' }}</span>
                </div>

            </div>

            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">

                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Email</span>
                    <span class="text-sm font-medium">{{ $data->email ?? '-' }}</span>
                </div>

                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Phone</span>
                    <span class="text-sm font-medium">{{ $data->phone ?? '-' }}</span>
                </div>

            </div>

            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">

                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Mobile</span>
                    <span class="text-sm font-medium">{{ $data->mobile ?? '-' }}</span>
                </div>

                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Notes</span>
                    <span class="text-sm font-medium">{{ $data->notes ?? '-' }}</span>
                </div>

            </div>

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
                <a href="{{ route('customers.edit', ['encryptedId' => $encryptedId]) }}"
                   class="btn btn-ghost btn-sm">
                    <x-icon name="edit" class="size-3.5" />
                    Edit
                </a>
            </div>

        </div>

    </div>
@endsection
