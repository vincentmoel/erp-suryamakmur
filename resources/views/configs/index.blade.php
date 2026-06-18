@extends('layouts.main', ['title' => __('general.settings')])

@section('content')
<div class="page-content">

    <div class="page-header">
        <h1>@lang('general.settings')</h1>
        <p>@lang('general.settings_desc')</p>
    </div>

    <div class="flex flex-col gap-6">

        @include('configs._company_section', ['sections' => $sections])

        @include('configs._bank_section', ['sections' => $sections])

        @include('configs._numbering_section', [
            'sectionKey' => 'invoice_numbering',
            'doc'        => 'invoice',
            'titleKey'   => 'general.invoice_numbering',
            'icon'       => 'invoice',
            'data'       => $sections['invoice_numbering'],
        ])

        @include('configs._numbering_section', [
            'sectionKey' => 'bill_numbering',
            'doc'        => 'bill',
            'titleKey'   => 'general.bill_numbering',
            'icon'       => 'receipt',
            'data'       => $sections['bill_numbering'],
        ])

    </div>
</div>

@include('configs._cropper_modal')

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
            const logoError = card.querySelector(`.logo-field-error[data-for="${field}"]`);
            if (logoError) {
                logoError.textContent = messages[0];
                logoError.classList.remove('hidden');
                return;
            }
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

        formatEl.addEventListener('change', update);
        paddingEl?.addEventListener('input', update);
    }

    initNumberingPreview('invoice');
    initNumberingPreview('bill');
})();
</script>
@endpush
@endsection
