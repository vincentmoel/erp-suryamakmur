<div class="rounded-lg border bg-card text-card-foreground shadow-xs" data-section="company">
    <div class="flex items-center gap-3 border-b px-6 py-5">
        <x-icon name="building" class="size-5 text-primary" />
        <h3 class="text-sm font-semibold">@lang('general.company_information')</h3>
    </div>

    <div class="flex flex-col gap-6 p-6">

        {{-- Logo --}}
        @php
            $logoVariants = [
                ['key' => 'logo_full_dark',  'label' => __('general.logo_full_dark_label'),  'hint' => __('general.logo_full_hint'), 'bg' => 'bg-white',    'aspect' => 64/13, 'mini' => false, 'current' => \App\Models\Config::get('logo_full_dark')],
                ['key' => 'logo_full_light', 'label' => __('general.logo_full_light_label'), 'hint' => __('general.logo_full_hint'), 'bg' => 'bg-zinc-900', 'aspect' => 64/13, 'mini' => false, 'current' => \App\Models\Config::get('logo_full_light')],
                ['key' => 'logo_mini_dark',  'label' => __('general.logo_mini_dark_label'),  'hint' => __('general.logo_mini_hint'), 'bg' => 'bg-white',    'aspect' => 1,     'mini' => true,  'current' => \App\Models\Config::get('logo_mini_dark')],
                ['key' => 'logo_mini_light', 'label' => __('general.logo_mini_light_label'), 'hint' => __('general.logo_mini_hint'), 'bg' => 'bg-zinc-900', 'aspect' => 1,     'mini' => true,  'current' => \App\Models\Config::get('logo_mini_light')],
            ];
        @endphp
        <div>
            <label class="mb-2 block text-sm font-medium">@lang('general.company_logo')</label>
            <div class="grid grid-cols-2 gap-2">
                @foreach($logoVariants as $v)
                    @php $currentVal = $v['current']; @endphp
                    <div>
                        <div class="logo-upload-card flex items-center gap-3 rounded-lg border px-3 py-2.5"
                            data-key="{{ $v['key'] }}"
                            data-aspect="{{ $v['aspect'] }}">

                            {{-- Preview thumbnail --}}
                            <div class="flex shrink-0 items-center justify-center overflow-hidden rounded {{ $v['bg'] }} {{ $v['mini'] ? 'h-9 w-9' : 'h-9 w-24' }} border">
                                @if($currentVal)
                                    <img src="{{ asset('storage/' . $currentVal) }}"
                                        class="logo-preview-img h-full w-full object-contain"
                                        alt="{{ $v['label'] }}">
                                @else
                                    <img src="" class="logo-preview-img hidden h-full w-full object-contain" alt="{{ $v['label'] }}">
                                    <span class="logo-preview-empty text-[10px] {{ str_contains($v['bg'], '900') ? 'text-zinc-600' : 'text-zinc-400' }}">—</span>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium">{{ $v['label'] }}</p>
                                <p class="text-[11px] text-muted-foreground">{{ $v['hint'] }}</p>
                            </div>

                            {{-- Upload --}}
                            <label class="btn btn-outline btn-sm shrink-0 cursor-pointer text-xs">
                                <x-icon name="upload" class="size-3.5" />
                                @lang('general.company_logo_upload')
                                <input type="file" accept="image/*" class="hidden logo-file-trigger">
                            </label>
                            <input type="file" name="{{ $v['key'] }}" class="hidden logo-hidden-file">
                        </div>
                        <p class="logo-field-error mt-1 hidden text-xs text-destructive" data-for="{{ $v['key'] }}"></p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <x-form.field name="company_name" :label="__('general.company_name')" :required="true">
                <input type="text" name="company_name" class="input"
                    value="{{ $sections['company']['company_name']->value ?? '' }}" required>
            </x-form.field>

            <x-form.field name="company_phone" :label="__('general.phone')">
                <input type="text" name="company_phone" class="input"
                    value="{{ $sections['company']['company_phone']->value ?? '' }}">
            </x-form.field>

            <x-form.field name="company_email" :label="__('general.company_email')">
                <input type="email" name="company_email" class="input"
                    value="{{ $sections['company']['company_email']->value ?? '' }}">
            </x-form.field>

            <x-form.field name="company_website" :label="__('general.company_website')">
                <input type="text" name="company_website" class="input"
                    value="{{ $sections['company']['company_website']->value ?? '' }}"
                    placeholder="{{ __('general.company_website_placeholder') }}">
            </x-form.field>
        </div>

        <x-form.field name="company_address" :label="__('general.address')">
            <textarea name="company_address" class="input" rows="3"
                style="height:auto;padding-top:0.5rem;padding-bottom:0.5rem;">{{ $sections['company']['company_address']->value ?? '' }}</textarea>
        </x-form.field>

    </div>

    <div class="flex items-center justify-end gap-2 border-t px-6 py-4">
        <button type="button" class="btn btn-primary btn-save" data-section="company">
            <x-icon name="check" class="size-3.5" />
            @lang('general.save')
        </button>
    </div>
</div>
