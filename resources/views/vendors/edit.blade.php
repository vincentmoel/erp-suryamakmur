@extends('layouts.main', ['title' => "Edit $title"])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Edit Vendor</h1>
            <p>Update vendor information.</p>
        </div>

        <form action="{{ route("$route.update", ['encryptedId' => $encryptedId]) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="flex flex-col gap-4">

                {{-- Identity --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <h3 class="text-sm font-semibold">Identity</h3>
                    </div>
                    <div class="flex flex-col gap-6 p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <x-form.field name="code" label="Code">
                                <input id="code"
                                       type="text"
                                       name="code"
                                       value="{{ old('code', $data->code) }}"
                                       placeholder="Vendor code"
                                       class="input {{ $errors->has('code') ? 'border-destructive' : '' }}">
                            </x-form.field>

                            <x-form.field name="type" label="Type" :required="true">
                                <x-form.single-select
                                    name="type"
                                    placeholder="Select type..."
                                    :selected="old('type', $data->type->value)"
                                    :options="collect($vendorTypes)->map(fn($t) => ['value' => $t->value, 'label' => $t->label()])->toArray()" />
                            </x-form.field>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <x-form.field name="name" label="Name" :required="true">
                                <input id="name"
                                       type="text"
                                       name="name"
                                       value="{{ old('name', $data->name) }}"
                                       placeholder="Vendor name"
                                       class="input {{ $errors->has('name') ? 'border-destructive' : '' }}">
                            </x-form.field>

                            <x-form.field name="tax_number" label="Tax Number">
                                <input id="tax_number"
                                       type="text"
                                       name="tax_number"
                                       value="{{ old('tax_number', $data->tax_number) }}"
                                       placeholder="e.g. 01.234.567.8-901.000"
                                       class="input {{ $errors->has('tax_number') ? 'border-destructive' : '' }}">
                            </x-form.field>
                        </div>
                    </div>
                </div>

                {{-- Contact --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <h3 class="text-sm font-semibold">Contact</h3>
                    </div>
                    <div class="flex flex-col gap-6 p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <x-form.field name="contact_person" label="Contact Person">
                                <input id="contact_person"
                                       type="text"
                                       name="contact_person"
                                       value="{{ old('contact_person', $data->contact_person) }}"
                                       placeholder="PIC name"
                                       class="input {{ $errors->has('contact_person') ? 'border-destructive' : '' }}">
                            </x-form.field>

                            <x-form.field name="phone" label="Phone">
                                <input id="phone"
                                       type="text"
                                       name="phone"
                                       value="{{ old('phone', $data->phone) }}"
                                       placeholder="Phone number"
                                       class="input {{ $errors->has('phone') ? 'border-destructive' : '' }}">
                            </x-form.field>
                        </div>

                        <x-form.field name="email" label="Email">
                            <input id="email"
                                   type="email"
                                   name="email"
                                   value="{{ old('email', $data->email) }}"
                                   placeholder="email@example.com"
                                   class="input {{ $errors->has('email') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>
                </div>

                {{-- Address --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <h3 class="text-sm font-semibold">Address</h3>
                    </div>
                    <div class="flex flex-col gap-6 p-6">
                        <x-form.field name="address" label="Street Address">
                            <textarea id="address"
                                      name="address"
                                      rows="3"
                                      placeholder="Full street address"
                                      style="height: auto; padding-top: 0.5rem; padding-bottom: 0.5rem;"
                                      class="input {{ $errors->has('address') ? 'border-destructive' : '' }}">{{ old('address', $data->address) }}</textarea>
                        </x-form.field>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                            <x-form.field name="city" label="City">
                                <input id="city"
                                       type="text"
                                       name="city"
                                       value="{{ old('city', $data->city) }}"
                                       placeholder="City"
                                       class="input {{ $errors->has('city') ? 'border-destructive' : '' }}">
                            </x-form.field>

                            <x-form.field name="province" label="Province">
                                <input id="province"
                                       type="text"
                                       name="province"
                                       value="{{ old('province', $data->province) }}"
                                       placeholder="Province"
                                       class="input {{ $errors->has('province') ? 'border-destructive' : '' }}">
                            </x-form.field>

                            <x-form.field name="postal_code" label="Postal Code">
                                <input id="postal_code"
                                       type="text"
                                       name="postal_code"
                                       value="{{ old('postal_code', $data->postal_code) }}"
                                       placeholder="12345"
                                       class="input {{ $errors->has('postal_code') ? 'border-destructive' : '' }}">
                            </x-form.field>
                        </div>
                    </div>
                </div>

                {{-- Bank --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <h3 class="text-sm font-semibold">Bank Information</h3>
                    </div>
                    <div class="flex flex-col gap-6 p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                            <x-form.field name="bank_name" label="Bank Name">
                                <input id="bank_name"
                                       type="text"
                                       name="bank_name"
                                       value="{{ old('bank_name', $data->bank_name) }}"
                                       placeholder="e.g. BCA, Mandiri"
                                       class="input {{ $errors->has('bank_name') ? 'border-destructive' : '' }}">
                            </x-form.field>

                            <x-form.field name="bank_account_number" label="Account Number">
                                <input id="bank_account_number"
                                       type="text"
                                       name="bank_account_number"
                                       value="{{ old('bank_account_number', $data->bank_account_number) }}"
                                       placeholder="Account number"
                                       class="input {{ $errors->has('bank_account_number') ? 'border-destructive' : '' }}">
                            </x-form.field>

                            <x-form.field name="bank_account_name" label="Account Holder Name">
                                <input id="bank_account_name"
                                       type="text"
                                       name="bank_account_name"
                                       value="{{ old('bank_account_name', $data->bank_account_name) }}"
                                       placeholder="Account holder name"
                                       class="input {{ $errors->has('bank_account_name') ? 'border-destructive' : '' }}">
                            </x-form.field>
                        </div>
                    </div>
                </div>

                {{-- Notes & Status --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex flex-col gap-6 p-6">
                        <x-form.field name="notes" label="Notes">
                            <textarea id="notes"
                                      name="notes"
                                      rows="4"
                                      placeholder="Additional notes"
                                      style="height: auto; padding-top: 0.5rem; padding-bottom: 0.5rem;"
                                      class="input {{ $errors->has('notes') ? 'border-destructive' : '' }}">{{ old('notes', $data->notes) }}</textarea>
                        </x-form.field>

                        <div class="flex items-center gap-3">
                            <input type="hidden" name="is_active" id="is_active_hidden" value="{{ old('is_active', $data->is_active ? '1' : '0') }}">
                            <button type="button" role="switch"
                                    data-slot="switch" data-size="default"
                                    data-toggle-input="is_active_hidden"
                                    class="group/switch inline-flex shrink-0 cursor-pointer items-center rounded-full border border-transparent shadow-xs transition-all outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 data-[size=default]:h-[1.15rem] data-[size=default]:w-8 data-[state=checked]:bg-primary data-[state=unchecked]:bg-input">
                                <span data-slot="switch-thumb"
                                      class="pointer-events-none block rounded-full bg-background ring-0 transition-transform group-data-[size=default]/switch:size-4 data-[state=checked]:translate-x-[calc(100%-2px)] data-[state=unchecked]:translate-x-0">
                                </span>
                            </button>
                            <span class="text-sm font-medium">Active</span>
                        </div>
                    </div>

                    @include('partials.form-actions-edit')
                </div>

            </div>
        </form>

    </div>
@endsection
