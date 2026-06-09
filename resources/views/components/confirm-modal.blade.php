@props([
    'id'           => 'confirm-modal',
    'triggerId'    => null,
    'formId'       => null,
    'ajaxMethod'   => null,   {{-- e.g. 'DELETE' — if set, does AJAX instead of form.submit() --}}
    'title'        => '',
    'description'  => '',
    'keyword'      => __('general.confirm_keyword'),
    'confirmLabel' => __('general.update'),
])

<div id="{{ $id }}"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4"
     role="dialog" aria-modal="true">
    <div class="w-full max-w-md rounded-xl border bg-card shadow-xl p-6 space-y-5">

        {{-- Header --}}
        <div class="flex items-start gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-destructive/10">
                <x-icon name="warning" class="size-5 text-destructive" />
            </div>
            <div>
                <h3 class="text-sm font-semibold">{{ $title }}</h3>
                @if($description)
                    <p class="mt-1 text-sm text-muted-foreground">{{ $description }}</p>
                @endif
            </div>
        </div>

        {{-- Keyword input --}}
        <div class="space-y-2">
            <label class="text-sm font-medium leading-none" for="{{ $id }}-input">
                {!! __('general.confirm_type_to_proceed', ['keyword' => '<span class="font-mono font-bold text-destructive">' . e($keyword) . '</span>']) !!}
            </label>
            <input type="text"
                   id="{{ $id }}-input"
                   autocomplete="off"
                   spellcheck="false"
                   class="input mt-2"
                   placeholder="{{ $keyword }}">
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-2">
            <button type="button" class="btn btn-outline confirm-modal-cancel">
                @lang('general.cancel')
            </button>
            <button type="button" class="btn btn-destructive confirm-modal-submit" disabled>
                <x-icon name="check" class="size-3.5" />
                {{ $confirmLabel }}
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script>
(function () {
    var modalId    = {{ Js::from($id) }};
    var keyword    = {{ Js::from($keyword) }};
    var formId     = {{ Js::from($formId) }};
    var triggerId  = {{ Js::from($triggerId) }};
    var ajaxMethod = {{ Js::from($ajaxMethod) }};

    var modal     = document.getElementById(modalId);
    var input     = document.getElementById(modalId + '-input');
    var btnSubmit = modal.querySelector('.confirm-modal-submit');
    var btnCancel = modal.querySelector('.confirm-modal-cancel');
    var trigger   = triggerId ? document.getElementById(triggerId) : null;
    var form      = formId ? document.getElementById(formId) : null;

    var pendingUrl = null;

    function open(url) {
        pendingUrl = url || null;
        input.value = '';
        btnSubmit.disabled = true;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(function () { input.focus(); }, 50);
    }

    function close() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        input.value = '';
        btnSubmit.disabled = true;
        pendingUrl = null;
    }

    // Expose API so external JS can open this modal with a dynamic URL
    window['confirmModal_' + modalId] = { open: open, close: close };

    if (trigger) trigger.addEventListener('click', function () { open(); });
    btnCancel.addEventListener('click', close);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) close();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) close();
    });

    input.addEventListener('input', function () {
        btnSubmit.disabled = this.value !== keyword;
    });

    btnSubmit.addEventListener('click', function () {
        if (input.value !== keyword) return;
        setBtnLoading(btnSubmit);
        if (ajaxMethod && pendingUrl) {
            $.ajax({
                url: pendingUrl,
                type: ajaxMethod,
                data: { _token: '{{ csrf_token() }}' },
                success: function () {
                    close();
                    $(document).trigger('dt:refresh');
                },
                error: function (xhr) {
                    resetBtnLoading(btnSubmit);
                    close();
                    var msg = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'An error occurred.';
                    alert(msg);
                },
            });
        } else {
            if (form) form.submit();
        }
    });
})();
</script>
@endpush
