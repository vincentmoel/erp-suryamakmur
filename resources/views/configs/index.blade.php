@extends('layouts.main', ['title' => __('general.settings')])

@section('content')
<div class="page-content">

    <div class="page-header">
        <h1>@lang('general.settings')</h1>
        <p>@lang('general.settings_desc')</p>
    </div>

    <div class="flex flex-col gap-6">

        {{-- ── PERUSAHAAN ────────────────────────────────────────── --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-xs" data-section="company">
            <div class="flex items-center gap-3 border-b px-6 py-5">
                <x-icon name="building" class="size-5 text-primary" />
                <h3 class="text-sm font-semibold">@lang('general.company_information')</h3>
            </div>

            <div class="flex flex-col gap-6 p-6">

                {{-- Logo --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium">@lang('general.company_logo')</label>
                    <div class="flex items-center gap-4">
                        @php $logo = $sections['company']['company_logo']->value ?? ''; @endphp
                        <div id="logo-preview-wrap">
                            @if($logo)
                                <img src="{{ asset('storage/' . $logo) }}" id="logo-preview-img" alt="Logo" class="h-14 w-auto rounded-md border object-contain p-1">
                            @else
                                <div id="logo-preview-placeholder" class="flex h-14 w-28 items-center justify-center rounded-md border bg-muted text-xs text-muted-foreground">@lang('general.company_logo_empty')</div>
                            @endif
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="btn btn-outline cursor-pointer text-sm">
                                <x-icon name="upload" class="size-4" />
                                <span>{{ $logo ? __('general.company_logo_change') : __('general.company_logo_upload') }}</span>
                                <input type="file" name="company_logo" accept="image/*" class="hidden" id="logo-input">
                            </label>
                            <span class="text-xs text-muted-foreground">@lang('general.company_logo_hint')</span>
                        </div>
                        <span id="logo-filename" class="text-sm text-muted-foreground"></span>
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

        {{-- ── BANK ──────────────────────────────────────────────── --}}
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

        {{-- ── PENOMORAN INVOICE ─────────────────────────────────── --}}
        @include('configs._numbering_section', [
            'sectionKey' => 'invoice_numbering',
            'doc'        => 'invoice',
            'titleKey'   => 'general.invoice_numbering',
            'icon'       => 'invoice',
            'data'       => $sections['invoice_numbering'],
        ])

        {{-- ── PENOMORAN BILL ────────────────────────────────────── --}}
        @include('configs._numbering_section', [
            'sectionKey' => 'bill_numbering',
            'doc'        => 'bill',
            'titleKey'   => 'general.bill_numbering',
            'icon'       => 'receipt',
            'data'       => $sections['bill_numbering'],
        ])

    </div>
</div>

@push('scripts')
<script>
(function () {
    const SAVE_URL    = '{{ route('ajax.configs.save') }}';
    const PREVIEW_URL = '{{ route('ajax.configs.preview-code') }}';
    const CSRF        = document.querySelector('meta[name="csrf-token"]').content;

    const LANG = {
        saved:       @json(__('general.settings_saved_title')),
        savedMsg:    @json(__('general.settings_saved')),
        failed:      @json(__('general.settings_save_failed')),
        serverError: @json(__('general.settings_save_error')),
        saving:      @json(__('general.save')) + '...',
    };

    // ── Logo preview ─────────────────────────────────────────────
    document.getElementById('logo-input').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        document.getElementById('logo-filename').textContent = file.name;
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.getElementById('logo-preview-wrap');
            let img = document.getElementById('logo-preview-img');
            const placeholder = document.getElementById('logo-preview-placeholder');
            if (placeholder) placeholder.remove();
            if (!img) {
                img = document.createElement('img');
                img.id = 'logo-preview-img';
                img.alt = 'Logo';
                img.className = 'h-14 w-auto rounded-md border object-contain p-1';
                wrap.appendChild(img);
            }
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    // ── Save handler ─────────────────────────────────────────────
    document.querySelectorAll('.btn-save').forEach(btn => {
        btn.addEventListener('click', function () {
            const section = this.dataset.section;
            const card    = document.querySelector(`[data-section="${section}"]`);
            const inputs  = card.querySelectorAll('input, textarea, select');

            const formData = new FormData();
            formData.append('section', section);

            inputs.forEach(el => {
                if (el.type === 'file') {
                    if (el.files[0]) formData.append(el.name, el.files[0]);
                } else if (el.name) {
                    formData.append(el.name, el.value);
                }
            });

            const originalHtml = this.innerHTML;
            setLoading(this, true);
            clearErrors(card);

            fetch(SAVE_URL, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body:    formData,
            })
            .then(r => r.json())
            .then(data => {
                if (data.code === 200) {
                    showToast(LANG.saved, LANG.savedMsg, 'success');
                } else if (data.code === 422 && data.errors) {
                    showErrors(card, data.errors);
                    showToast(LANG.saved, LANG.failed, 'error');
                } else {
                    showToast(LANG.saved, data.message ?? LANG.serverError, 'error');
                }
            })
            .catch(() => showToast(LANG.saved, LANG.serverError, 'error'))
            .finally(() => setLoading(this, false, originalHtml));
        });
    });

    function setLoading(btn, loading, originalHtml = '') {
        btn.disabled = loading;
        if (loading) {
            btn.innerHTML = '<svg class="size-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> ' + LANG.saving;
        } else {
            btn.innerHTML = originalHtml;
        }
    }

    function clearErrors(card) {
        card.querySelectorAll('.field-error').forEach(el => el.remove());
        card.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
    }

    function showErrors(card, errors) {
        Object.entries(errors).forEach(([field, messages]) => {
            const input = card.querySelector(`[name="${field}"]`);
            if (!input) return;
            input.classList.add('input-error');
            const p = document.createElement('p');
            p.className = 'field-error mt-1 text-xs text-destructive';
            p.textContent = messages[0];
            input.parentNode.appendChild(p);
        });
    }

    // ── Numbering preview ─────────────────────────────────────────
    function initNumberingPreview(doc) {
        const previewEl = document.getElementById(doc + '_preview');
        const formatEl  = document.getElementById(doc + '_format');
        const paddingEl = document.getElementById(doc + '_padding');
        if (!previewEl || !formatEl) return;

        function update() {
            const params = new URLSearchParams({
                format:  formatEl.value,
                padding: paddingEl?.value ?? 4,
            });
            fetch(PREVIEW_URL + '?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            })
            .then(r => r.json())
            .then(d => { previewEl.textContent = d.preview; })
            .catch(() => {});
        }

        // hidden input di-update oleh composer JS di partial via dispatchEvent('change')
        formatEl.addEventListener('change', update);
        paddingEl?.addEventListener('input', update);
    }

    initNumberingPreview('invoice');
    initNumberingPreview('bill');
})();
</script>
@endpush
@endsection
