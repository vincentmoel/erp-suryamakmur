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

{{-- ── Cropper Modal ───────────────────────────────────────────── --}}
<div id="logo-crop-modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" id="crop-backdrop"></div>
    <div class="absolute inset-0 flex items-center justify-center p-8">
        {{-- overflow-hidden wajib agar Cropper.js tidak bleed ke luar card --}}
        <div class="relative z-10 flex w-full max-w-2xl flex-col overflow-hidden rounded-xl border bg-card shadow-2xl">
            {{-- Header --}}
            <div class="flex shrink-0 items-center justify-between border-b px-6 py-4">
                <h3 class="text-sm font-semibold">@lang('general.logo_crop_title')</h3>
                <button type="button" id="crop-close-btn" class="rounded p-1 text-muted-foreground hover:text-foreground">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            {{-- Crop area — overflow:hidden clips Cropper canvas, height drives the area size --}}
            <div id="crop-container" style="position:relative;overflow:hidden;background:#09090b;height:50vh;">
                <img id="crop-img" src="" alt="" style="display:block;max-width:100%;">
            </div>
            {{-- Footer --}}
            <div class="flex shrink-0 items-center justify-end gap-3 border-t px-6 py-4">
                <button type="button" id="crop-cancel-btn" class="btn btn-outline btn-sm">@lang('general.cancel')</button>
                <button type="button" id="crop-apply-btn" class="btn btn-primary btn-sm">
                    <x-icon name="check" class="size-3.5" />
                    @lang('general.logo_apply')
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
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

    // ── Cropper ───────────────────────────────────────────────────
    let cropperInstance = null;
    let activeCard      = null;

    document.querySelectorAll('.logo-file-trigger').forEach(trigger => {
        trigger.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            activeCard = this.closest('.logo-upload-card');
            const aspectRatio = parseFloat(activeCard.dataset.aspect);
            const reader = new FileReader();
            reader.onload = e => openCropModal(e.target.result, aspectRatio);
            reader.readAsDataURL(file);
            this.value = ''; // reset so same file can be re-selected
        });
    });

    function openCropModal(src, aspectRatio) {
        const modal = document.getElementById('logo-crop-modal');
        const img   = document.getElementById('crop-img');

        if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
        img.src = '';
        modal.classList.remove('hidden');

        // Wait two frames: first for modal to be in DOM, second for layout to settle
        requestAnimationFrame(() => requestAnimationFrame(() => {
            img.onload = () => {
                cropperInstance = new Cropper(img, {
                    aspectRatio:  aspectRatio,
                    viewMode:     1,
                    autoCropArea: 0.9,
                    movable:      true,
                    zoomable:     true,
                    rotatable:    false,
                    scalable:     false,
                    background:   false,
                });
            };
            img.src = src;
        }));
    }

    function closeCropModal() {
        document.getElementById('logo-crop-modal').classList.add('hidden');
        if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
        activeCard = null;
    }

    document.getElementById('crop-cancel-btn').addEventListener('click', closeCropModal);
    document.getElementById('crop-close-btn').addEventListener('click', closeCropModal);
    document.getElementById('crop-backdrop').addEventListener('click', closeCropModal);

    document.getElementById('crop-apply-btn').addEventListener('click', () => {
        if (!cropperInstance || !activeCard) return;
        const isMini  = parseFloat(activeCard.dataset.aspect) === 1;
        const maxSize = isMini ? 512 : 1920;
        const canvas  = cropperInstance.getCroppedCanvas({ maxWidth: maxSize, maxHeight: maxSize });

        // Compress: JPEG q=0.85 keeps file small while maintaining good quality
        canvas.toBlob(blob => {
            const key  = activeCard.dataset.key;
            const file = new File([blob], key + '.jpg', { type: 'image/jpeg' });

            const dt = new DataTransfer();
            dt.items.add(file);
            activeCard.querySelector('.logo-hidden-file').files = dt.files;

            const previewImg   = activeCard.querySelector('.logo-preview-img');
            const previewEmpty = activeCard.querySelector('.logo-preview-empty');
            if (previewEmpty) previewEmpty.classList.add('hidden');
            previewImg.src = canvas.toDataURL('image/jpeg', 0.85);
            previewImg.classList.remove('hidden');

            closeCropModal();
        }, 'image/jpeg', 0.85);
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
        card.querySelectorAll('.logo-field-error').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
    }

    function showErrors(card, errors) {
        Object.entries(errors).forEach(([field, messages]) => {
            // Logo fields: show error below the card row
            const logoError = card.querySelector(`.logo-field-error[data-for="${field}"]`);
            if (logoError) {
                logoError.textContent = messages[0];
                logoError.classList.remove('hidden');
                return;
            }
            // Regular fields
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
