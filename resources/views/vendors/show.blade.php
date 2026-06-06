@extends('layouts.main', ['title' => $data->name])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.vendor_detail')</h1>
            <p>@lang('general.vendor_detail_desc')</p>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-xs">

            <div class="flex items-center gap-3 border-b px-6 py-4">
                <div class="flex size-8 items-center justify-center rounded-md bg-primary/10">
                    <x-icon name="building" class="size-4 text-primary" />
                </div>
                <h3 class="text-sm font-semibold">@lang('general.vendor_information')</h3>
            </div>

            {{-- Code & Type --}}
            <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.code')</span>
                    <span class="text-sm font-medium">{{ $data->code }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.type')</span>
                    <span class="text-sm font-medium">{{ $data->type->label() }}</span>
                </div>
            </div>

            {{-- Name & Tax Number --}}
            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.name')</span>
                    <span class="text-sm font-medium">{{ $data->name }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.tax_number')</span>
                    <span class="text-sm font-medium">{{ $data->tax_number ?? '-' }}</span>
                </div>
            </div>

            {{-- Phone & Email --}}
            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.phone')</span>
                    <span class="text-sm font-medium">{{ $data->phone ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.email')</span>
                    <span class="text-sm font-medium">{{ $data->email ?? '-' }}</span>
                </div>
            </div>

            {{-- Contact Person & Status --}}
            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.contact_person')</span>
                    <span class="text-sm font-medium">{{ $data->contact_person ?? '-' }}</span>
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

            {{-- Address --}}
            <div class="border-t">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.address')</span>
                    <span class="text-sm font-medium">{{ $data->address ?? '-' }}</span>
                </div>
            </div>

            {{-- City & Province --}}
            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.city')</span>
                    <span class="text-sm font-medium">{{ $data->city ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.province')</span>
                    <span class="text-sm font-medium">{{ $data->province ?? '-' }}</span>
                </div>
            </div>

            {{-- Postal Code --}}
            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.postal_code')</span>
                    <span class="text-sm font-medium">{{ $data->postal_code ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4"></div>
            </div>

            {{-- Bank --}}
            <div class="flex items-center gap-3 border-t border-b px-6 py-4">
                <h3 class="text-sm font-semibold">@lang('general.bank_information')</h3>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.bank_name')</span>
                    <span class="text-sm font-medium">{{ $data->bank_name ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.account_number')</span>
                    <span class="text-sm font-medium">{{ $data->bank_account_number ?? '-' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.account_name')</span>
                    <span class="text-sm font-medium">{{ $data->bank_account_name ?? '-' }}</span>
                </div>
                <div class="flex flex-col gap-1 px-6 py-4"></div>
            </div>

            {{-- Notes --}}
            <div class="border-t">
                <div class="flex flex-col gap-1 px-6 py-4">
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wide">@lang('general.notes')</span>
                    <span class="text-sm font-medium">{{ $data->notes ?? '-' }}</span>
                </div>
            </div>

            {{-- Meta --}}
            <div class="grid grid-cols-1 gap-0 divide-y border-t sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
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
                <a href="{{ route('vendors.edit', ['encryptedId' => $encryptedId]) }}"
                   class="btn btn-ghost btn-sm">
                    <x-icon name="edit" class="size-3.5" />
                    @lang('general.edit')
                </a>
            </div>

        </div>

    </div>
@endsection
