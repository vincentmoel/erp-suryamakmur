<div class="rounded-lg border bg-card text-card-foreground shadow-xs" data-section="bank">
    <div class="flex items-center gap-3 border-b px-6 py-5">
        <x-icon name="money" class="size-5 text-primary" />
        <h3 class="text-sm font-semibold">@lang('general.bank_section')</h3>
    </div>

    <div class="flex flex-col gap-6 p-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <x-form.field name="bank_name" :label="__('general.bank_name')">
                <input type="text" name="bank_name" class="input"
                    value="{{ $sections['bank']['bank_name']->value ?? '' }}">
            </x-form.field>

            <x-form.field name="bank_account_number" :label="__('general.account_number')">
                <input type="text" name="bank_account_number" class="input"
                    value="{{ $sections['bank']['bank_account_number']->value ?? '' }}">
            </x-form.field>
        </div>

        <x-form.field name="bank_account_holder" :label="__('general.bank_account_holder')">
            <input type="text" name="bank_account_holder" class="input"
                value="{{ $sections['bank']['bank_account_holder']->value ?? '' }}">
        </x-form.field>
    </div>

    <div class="flex items-center justify-end gap-2 border-t px-6 py-4">
        <button type="button" class="btn btn-primary btn-save" data-section="bank">
            <x-icon name="check" class="size-3.5" />
            @lang('general.save')
        </button>
    </div>
</div>
