@extends('layouts.main', ['title' => $data->name])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.customer_detail')</h1>
            <p>@lang('general.customer_detail_desc')</p>
        </div>

        <div class="flex flex-col gap-4">

            {{-- Identity --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                <div class="flex items-center gap-3 border-b px-6 py-4">
                    <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                        <x-icon name="contact" class="size-4 text-primary" />
                    </div>
                    <h3 class="text-sm font-semibold">@lang('general.identity')</h3>
                </div>

                {{-- Type & Name --}}
                <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                    <div class="flex flex-col gap-1 px-6 py-4">
                        <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.type')</span>
                        <span class="text-sm font-medium">{{ $data->type->label() }}</span>
                    </div>
                    <div class="flex flex-col gap-1 px-6 py-4">
                        <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.name')</span>
                        <span class="text-sm font-medium">{{ $data->name }}</span>
                    </div>
                </div>

                {{-- Company Name & Tax Number --}}
                <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                    <div class="flex flex-col gap-1 px-6 py-4">
                        <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.company_name')</span>
                        <span class="text-sm font-medium">{{ $data->company_name ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col gap-1 px-6 py-4">
                        <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.tax_number')</span>
                        <span class="text-sm font-medium">{{ $data->tax_number ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Contact --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                <div class="flex items-center gap-3 border-b px-6 py-4">
                    <h3 class="text-sm font-semibold">@lang('general.contact')</h3>
                </div>

                {{-- Email & Phone --}}
                <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                    <div class="flex flex-col gap-1 px-6 py-4">
                        <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.email')</span>
                        <span class="text-sm font-medium">{{ $data->email ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col gap-1 px-6 py-4">
                        <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.phone')</span>
                        <span class="text-sm font-medium">{{ $data->phone ?? '-' }}</span>
                    </div>
                </div>

                {{-- Mobile & Status --}}
                <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                    <div class="flex flex-col gap-1 px-6 py-4">
                        <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.mobile')</span>
                        <span class="text-sm font-medium">{{ $data->mobile ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col gap-1 px-6 py-4">
                        <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.status')</span>
                        @if($data->is_active)
                            <span class="inline-flex w-fit items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">@lang('general.active')</span>
                        @else
                            <span class="inline-flex w-fit items-center rounded-full bg-muted px-2.5 py-0.5 text-xs font-medium text-muted-foreground">@lang('general.inactive')</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                <div class="border-b">
                    <div class="flex flex-col gap-1 px-6 py-4">
                        <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.notes')</span>
                        <span class="text-sm font-medium">{{ $data->notes ?? '-' }}</span>
                    </div>
                </div>

                {{-- Meta --}}
                <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                    <div class="flex flex-col gap-1 px-6 py-4">
                        <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.created_at')</span>
                        <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y | H:i') }}</span>
                    </div>
                    <div class="flex flex-col gap-1 px-6 py-4">
                        <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.last_updated')</span>
                        <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($data->updated_at)->translatedFormat('d F Y | H:i') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                    <div class="flex flex-col gap-1 px-6 py-4">
                        <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.created_by')</span>
                        <span class="text-sm font-medium">{{ $data->user_created_by->name ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col gap-1 px-6 py-4">
                        <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.updated_by')</span>
                        <span class="text-sm font-medium">{{ $data->user_updated_by->name ?? '-' }}</span>
                    </div>
                </div>

                <div class="flex gap-2 border-t px-6 py-4">
                    <a href="{{ route('customers.edit', ['encryptedId' => $encryptedId]) }}"
                       class="btn btn-ghost btn-sm">
                        <x-icon name="edit" class="size-3.5" />
                        @lang('general.edit')
                    </a>
                </div>
            </div>

        </div>

    </div>
@endsection
