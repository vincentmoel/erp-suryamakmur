@props([
    'name',
    'options'     => [],   // [['value' => '', 'label' => ''], ...]
    'selected'    => null, // selected value (edit / repopulate)
    'placeholder' => 'Select option...',
    'searchable'  => true,
])

@php
    $uid       = 'ss-' . Str::random(8);
    $triggerId = $uid . '-trigger';
    $contentId = $uid . '-content';
    $searchId  = $uid . '-search';

    $oldValue = old($name, $selected);
    $oldValue = $oldValue !== null ? (string) $oldValue : null;

    $selectedLabel = collect($options)->firstWhere('value', $oldValue)['label'] ?? null;
@endphp

<div data-single-select="{{ $uid }}" class="relative">

    {{-- Trigger --}}
    <button type="button"
            id="{{ $triggerId }}"
            aria-expanded="false"
            aria-haspopup="listbox"
            class="select-trigger flex items-center justify-between w-full {{ $errors->has($name) ? 'border-destructive' : '' }}">
        <span data-ss-label class="text-sm {{ $selectedLabel ? '' : 'text-muted-foreground' }}">
            {{ $selectedLabel ?? $placeholder }}
        </span>
        <span class="flex items-center gap-1 shrink-0">
            {{-- Clear button --}}
            <span data-ss-clear
                  role="button"
                  tabindex="0"
                  class="{{ $selectedLabel ? '' : 'hidden' }} flex items-center justify-center rounded hover:text-destructive opacity-50 hover:opacity-100 transition-opacity"
                  title="Clear">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="size-3.5">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </span>
            {{-- Chevron --}}
            <svg data-ss-chevron xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 opacity-50 transition-transform duration-150">
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </span>
    </button>

    {{-- Dropdown --}}
    <div id="{{ $contentId }}"
         role="listbox"
         class="select-content hidden max-h-60 overflow-auto">

        @if($searchable)
            <div class="px-1 pb-2 sticky top-0 bg-popover">
                <input id="{{ $searchId }}"
                       type="text"
                       placeholder="Search..."
                       autocomplete="off"
                       class="input h-8 text-sm">
            </div>
        @endif

        <div data-ss-list>
            @foreach($options as $option)
                <div role="option"
                     data-ss-item
                     data-value="{{ $option['value'] }}"
                     data-label="{{ $option['label'] }}"
                     aria-selected="{{ (string) $option['value'] === $oldValue ? 'true' : 'false' }}"
                     class="select-item cursor-pointer">
                    {{ $option['label'] }}
                </div>
            @endforeach
        </div>

        <p data-ss-empty class="hidden px-2 py-4 text-center text-sm text-muted-foreground">No results found.</p>
    </div>

    {{-- Hidden input --}}
    <input type="hidden" name="{{ $name }}" data-ss-input value="{{ $oldValue ?? '' }}">
</div>

<script>
(function () {
    var uid         = {{ Js::from($uid) }};
    var placeholder = {{ Js::from($placeholder) }};

    var root        = document.querySelector('[data-single-select="' + uid + '"]');
    if (!root) return;

    var trigger     = document.getElementById({{ Js::from($triggerId) }});
    var content     = document.getElementById({{ Js::from($contentId) }});
    var searchInput = document.getElementById({{ Js::from($searchId) }});
    var labelEl     = root.querySelector('[data-ss-label]');
    var hiddenInput = root.querySelector('[data-ss-input]');
    var emptyEl     = root.querySelector('[data-ss-empty]');
    var clearBtn    = root.querySelector('[data-ss-clear]');
    var chevron     = root.querySelector('[data-ss-chevron]');

    // ── Chevron rotate ────────────────────────────────────────
    function setChevron(open) {
        chevron.style.transform = open ? 'rotate(180deg)' : '';
    }

    // ── Position & Open/Close ─────────────────────────────────
    function openDropdown() {
        var r = trigger.getBoundingClientRect();
        content.style.top   = (r.bottom + 4) + 'px';
        content.style.left  = r.left + 'px';
        content.style.width = r.width + 'px';
        content.classList.remove('hidden');
        trigger.setAttribute('aria-expanded', 'true');
        setChevron(true);
        if (searchInput) {
            searchInput.value = '';
            filterItems('');
            searchInput.focus();
        }
    }

    function closeDropdown() {
        content.classList.add('hidden');
        trigger.setAttribute('aria-expanded', 'false');
        setChevron(false);
    }

    trigger.addEventListener('click', function (e) {
        e.stopPropagation();
        content.classList.contains('hidden') ? openDropdown() : closeDropdown();
    });

    document.addEventListener('click', function (e) {
        if (!root.contains(e.target) && !content.contains(e.target)) closeDropdown();
    });

    window.addEventListener('scroll', function () { if (!content.classList.contains('hidden')) openDropdown(); }, true);
    window.addEventListener('resize', function () { if (!content.classList.contains('hidden')) closeDropdown(); });

    // ── Clear button ──────────────────────────────────────────
    if (clearBtn) {
        clearBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            clearSelection();
            hiddenInput.dispatchEvent(new Event('change'));
        });
        clearBtn.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); clearBtn.click(); }
        });
    }

    function clearSelection() {
        hiddenInput.value = '';
        labelEl.textContent = placeholder;
        labelEl.classList.add('text-muted-foreground');
        clearBtn && clearBtn.classList.add('hidden');
        root.querySelectorAll('[data-ss-item]').forEach(function (i) {
            i.setAttribute('aria-selected', 'false');
        });
        closeDropdown();
    }

    // ── Item select ───────────────────────────────────────────
    content.addEventListener('click', function (e) {
        var item = e.target.closest('[data-ss-item]');
        if (!item) return;
        e.stopPropagation();

        root.querySelectorAll('[data-ss-item]').forEach(function (i) {
            i.setAttribute('aria-selected', 'false');
        });

        var value = item.dataset.value;
        var label = item.dataset.label;

        item.setAttribute('aria-selected', 'true');
        hiddenInput.value = value;
        labelEl.textContent = label;
        labelEl.classList.remove('text-muted-foreground');
        clearBtn && clearBtn.classList.remove('hidden');

        hiddenInput.dispatchEvent(new Event('change'));
        closeDropdown();
    });

    // ── Search ────────────────────────────────────────────────
    function filterItems(query) {
        var q = query.toLowerCase();
        var visible = 0;
        root.querySelectorAll('[data-ss-item]').forEach(function (item) {
            var match = item.dataset.label.toLowerCase().includes(q);
            item.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        if (emptyEl) {
            visible === 0 ? emptyEl.classList.remove('hidden') : emptyEl.classList.add('hidden');
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () { filterItems(searchInput.value); });
        searchInput.addEventListener('click', function (e) { e.stopPropagation(); });
    }
})();
</script>
