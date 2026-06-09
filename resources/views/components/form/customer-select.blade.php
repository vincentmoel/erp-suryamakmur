@props([
    'name'      => 'customer_id',
    'customers' => collect(),
    'selected'  => null,
    'required'  => false,
])

<x-form.field :name="$name" :label="__('general.customer')" :required="$required">
    <x-form.single-select
        :name="$name"
        :placeholder="__('general.select_customer_placeholder')"
        :options="$customers->map(fn($c) => [
            'value' => $c->id,
            'label' => $c->company_name ? $c->company_name . ' (' . $c->name . ')' : $c->name,
        ])->toArray()"
        :selected="$selected" />

    {{-- Customer info panel --}}
    <div id="customer-info-{{ $name }}"
         class="hidden mt-2 rounded-md border bg-muted/40 px-3 py-3 text-xs text-muted-foreground space-y-2">
        <div class="flex items-center gap-2 flex-wrap">
            <span id="ci-type-{{ $name }}" class="font-semibold text-foreground"></span>
            <span id="ci-company-{{ $name }}" class="hidden text-foreground/70"></span>
        </div>
        <div class="flex items-center gap-4 flex-wrap">
            <span id="ci-email-{{ $name }}"  class="hidden items-center gap-1.5"></span>
            <span id="ci-phone-{{ $name }}"  class="hidden items-center gap-1.5"></span>
            <span id="ci-mobile-{{ $name }}" class="hidden items-center gap-1.5"></span>
        </div>
        <div id="ci-tax-{{ $name }}" class="hidden items-center gap-1.5"></div>
        <div id="ci-notes-{{ $name }}" class="hidden italic border-t border-border/50 pt-2 mt-0.5"></div>
    </div>
</x-form.field>

@push('scripts')
<script>
(function () {
    var fieldName = {{ Js::from($name) }};
    var customerMap = {};
    @foreach($customers as $c)
    customerMap[{{ $c->id }}] = {
        type:         {{ Js::from($c->type->label()) }},
        company_name: {{ Js::from($c->company_name) }},
        tax_number:   {{ Js::from($c->tax_number) }},
        email:        {{ Js::from($c->email) }},
        phone:        {{ Js::from($c->phone) }},
        mobile:       {{ Js::from($c->mobile) }},
        notes:        {{ Js::from($c->notes) }},
    };
    @endforeach

    var iconMail   = {!! Js::from((string) str(view('components.icon', ['name' => 'mail',       'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};
    var iconPhone  = {!! Js::from((string) str(view('components.icon', ['name' => 'phone',      'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};
    var iconMobile = {!! Js::from((string) str(view('components.icon', ['name' => 'smartphone', 'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};
    var iconReceipt= {!! Js::from((string) str(view('components.icon', ['name' => 'receipt',    'class' => 'size-3.5 shrink-0 inline-block'])->render())) !!};

    var panel     = document.getElementById('customer-info-'  + fieldName);
    var ciType    = document.getElementById('ci-type-'    + fieldName);
    var ciCompany = document.getElementById('ci-company-' + fieldName);
    var ciEmail   = document.getElementById('ci-email-'   + fieldName);
    var ciPhone   = document.getElementById('ci-phone-'   + fieldName);
    var ciMobile  = document.getElementById('ci-mobile-'  + fieldName);
    var ciTax     = document.getElementById('ci-tax-'     + fieldName);
    var ciNotes   = document.getElementById('ci-notes-'   + fieldName);

    function setField(el, iconHtml, text) {
        if (text) {
            el.innerHTML = iconHtml + '<span>' + text + '</span>';
            el.classList.remove('hidden');
            el.classList.add('flex');
        } else {
            el.classList.add('hidden');
            el.classList.remove('flex');
        }
    }

    function showCustomer(id) {
        var data = customerMap[id];
        if (!id || !data) { panel.classList.add('hidden'); return; }
        ciType.textContent = data.type;
        setField(ciCompany, '', data.company_name);
        setField(ciEmail,   iconMail,    data.email);
        setField(ciPhone,   iconPhone,   data.phone);
        setField(ciMobile,  iconMobile,  data.mobile);
        if (data.tax_number) {
            ciTax.innerHTML = iconReceipt + '<span>NPWP: ' + data.tax_number + '</span>';
            ciTax.classList.remove('hidden');
            ciTax.classList.add('flex');
        } else {
            ciTax.classList.add('hidden');
            ciTax.classList.remove('flex');
        }
        if (data.notes) {
            ciNotes.textContent = data.notes;
            ciNotes.classList.remove('hidden');
        } else {
            ciNotes.classList.add('hidden');
        }
        panel.classList.remove('hidden');
    }

    var input = document.querySelector('[name="' + fieldName + '"]');
    if (input) {
        input.addEventListener('change', function () { showCustomer(this.value); });
        if (input.value) showCustomer(input.value);
    }
})();
</script>
@endpush
