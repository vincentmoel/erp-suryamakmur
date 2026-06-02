@extends('layouts.main', ['title' => "Add $title"])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Add Customer</h1>
            <p>Create a new customer.</p>
        </div>

        <form action="{{ route("$route.store") }}" method="POST">
            @csrf

            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">

                <div class="flex flex-col gap-6 p-6">

                    {{-- Type & Name --}}
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="type" label="Type" :required="true">
                            <x-form.single-select
                                name="type"
                                placeholder="Select customer type..."
                                :options="collect($customerTypes)->map(fn($t) => ['value' => $t->value, 'label' => $t->label()])->toArray()" />
                        </x-form.field>

                        <x-form.field name="name" label="Name" :required="true">
                            <input id="name"
                                   type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Customer name"
                                   class="input {{ $errors->has('name') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    {{-- Company Name (visible only when type = COMPANY) --}}
                    <div id="field-company-name" class="{{ old('type') === 'COMPANY' ? '' : 'hidden' }}">
                        <x-form.field name="company_name" label="Company Name">
                            <input id="company_name"
                                   type="text"
                                   name="company_name"
                                   value="{{ old('company_name') }}"
                                   placeholder="Company name"
                                   class="input {{ $errors->has('company_name') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    {{-- Tax Number & Email --}}
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="tax_number" label="Tax Number">
                            <input id="tax_number"
                                   type="text"
                                   name="tax_number"
                                   value="{{ old('tax_number') }}"
                                   placeholder="Tax number"
                                   class="input {{ $errors->has('tax_number') ? 'border-destructive' : '' }}">
                        </x-form.field>

                        <x-form.field name="email" label="Email">
                            <input id="email"
                                   type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="email@example.com"
                                   class="input {{ $errors->has('email') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    {{-- Phone & Mobile --}}
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="phone" label="Phone">
                            <input id="phone"
                                   type="text"
                                   name="phone"
                                   value="{{ old('phone') }}"
                                   placeholder="Phone number"
                                   class="input {{ $errors->has('phone') ? 'border-destructive' : '' }}">
                        </x-form.field>

                        <x-form.field name="mobile" label="Mobile">
                            <input id="mobile"
                                   type="text"
                                   name="mobile"
                                   value="{{ old('mobile') }}"
                                   placeholder="Mobile number"
                                   class="input {{ $errors->has('mobile') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    {{-- Notes --}}
                    <x-form.field name="notes" label="Notes">
                        <textarea id="notes"
                                  name="notes"
                                  rows="6"
                                  placeholder="Additional notes"
                                  style="height: auto; padding-top: 0.5rem; padding-bottom: 0.5rem;"
                                  class="input {{ $errors->has('notes') ? 'border-destructive' : '' }}">{{ old('notes') }}</textarea>
                    </x-form.field>

                </div>

                @include('partials.form-actions-create')

            </div>
        </form>

    </div>
@endsection

@push('scripts')
<script>
(function () {
    var typeInput   = document.querySelector('[data-single-select] [data-ss-input]');
    var companyWrap = document.getElementById('field-company-name');

    function toggleCompany() {
        if (!typeInput || !companyWrap) return;
        if (typeInput.value === 'COMPANY') {
            companyWrap.classList.remove('hidden');
        } else {
            companyWrap.classList.add('hidden');
        }
    }

    if (typeInput) {
        typeInput.addEventListener('change', toggleCompany);
    }
})();
</script>
@endpush
