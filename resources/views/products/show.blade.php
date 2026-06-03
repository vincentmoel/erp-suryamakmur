@extends('layouts.main', ['title' => $data->name])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Product Detail</h1>
            <p>View product information.</p>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-xs">

            <div class="flex items-center gap-3 border-b px-6 py-4">
                <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                    <x-icon name="box" class="size-4 text-primary" />
                </div>
                <h3 class="text-sm font-semibold">Product Information</h3>
            </div>

            @if($data->image)
                <div class="border-b px-6 py-4">
                    <img src="{{ asset('storage/' . $data->image) }}" alt="{{ $data->name }}" class="h-40 rounded-md object-cover">
                </div>
            @endif

            <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Name</span>
                    <span class="text-sm font-medium">{{ $data->name }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">SKU</span>
                    <span class="text-sm font-medium">{{ $data->sku ?? '-' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Category</span>
                    <span class="text-sm font-medium">{{ $data->category?->name ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Unit</span>
                    <span class="text-sm font-medium">{{ $data->unit?->name ?? '-' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-3 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Stock Available</span>
                    <span class="text-sm font-medium">{{ $data->stock_available }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Stock Reserved</span>
                    <span class="text-sm font-medium">{{ $data->stock_reserved }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Stock Minimum</span>
                    <span class="text-sm font-medium">{{ $data->stock_minimum }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-0 border-t">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Description</span>
                    <span class="text-sm font-medium">{{ $data->description ?? '-' }}</span>
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
                <a href="{{ route('products.edit', ['encryptedId' => $encryptedId]) }}"
                   class="btn btn-ghost btn-sm">
                    <x-icon name="edit" class="size-3.5" />
                    Edit
                </a>
            </div>

        </div>

    </div>
@endsection
