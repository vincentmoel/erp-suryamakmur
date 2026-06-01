@props([
    'name',
    'options'     => [],   // [['value' => '', 'label' => ''], ...]
    'selected'    => [],   // selected values (edit / repopulate)
    'placeholder' => 'Select options...',
    'searchable'  => true,
])

@php
    $uid       = 'ms-' . Str::random(8);
    $triggerId = $uid . '-trigger';
    $contentId = $uid . '-content';
    $searchId  = $uid . '-search';

    $oldValues = old($name, $selected);
    $oldValues = is_array($oldValues) ? array_map('strval', $oldValues) : [];
@endphp

<div data-multi-select="{{ $uid }}" class="relative">

    {{-- Trigger --}}
    <button type="button"
            id="{{ $triggerId }}"
            aria-expanded="false"
            aria-haspopup="listbox"
            class="select-trigger min-h-9 h-auto flex-wrap gap-1 py-1.5 {{ $errors->has($name) ? 'border-destructive' : '' }}">
        <span data-ms-placeholder class="text-muted-foreground text-sm {{ count($oldValues) ? 'hidden' : '' }}">{{ $placeholder }}</span>
        <span data-ms-badges class="flex flex-wrap gap-1 {{ count($oldValues) ? '' : 'hidden' }}"></span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0 ms-auto opacity-50">
            <path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/>
        </svg>
    </button>

    {{-- Dropdown (position: fixed, positioned by JS) --}}
    <div id="{{ $contentId }}"
         role="listbox"
         aria-multiselectable="true"
         class="select-content hidden max-h-60 overflow-auto">

        @if($searchable)
            <div class="px-1 pb-1 sticky top-0 bg-popover">
                <input id="{{ $searchId }}"
                       type="text"
                       placeholder="Search..."
                       autocomplete="off"
                       class="input h-8 text-sm">
            </div>
        @endif

        <div data-ms-list>
            @forelse($options as $option)
                <div role="option"
                     data-ms-item
                     data-value="{{ $option['value'] }}"
                     data-label="{{ $option['label'] }}"
                     aria-selected="{{ in_array((string) $option['value'], $oldValues) ? 'true' : 'false' }}"
                     class="select-item cursor-pointer">
                    {{ $option['label'] }}
                </div>
            @empty
                <p class="px-2 py-4 text-center text-sm text-muted-foreground">No options available.</p>
            @endforelse
        </div>

        <p data-ms-empty class="hidden px-2 py-4 text-center text-sm text-muted-foreground">No results found.</p>
    </div>

    {{-- Hidden inputs submitted as name[] --}}
    <div data-ms-hidden-inputs>
        @foreach($oldValues as $val)
            <input type="hidden" name="{{ $name }}[]" value="{{ $val }}">
        @endforeach
    </div>
</div>

<script>
(function () {
    var uid         = {{ Js::from($uid) }};
    var inputName   = {{ Js::from($name) }};
    var placeholder = {{ Js::from($placeholder) }};

    var root        = document.querySelector('[data-multi-select="' + uid + '"]');
    if (!root) return;

    var trigger     = document.getElementById({{ Js::from($triggerId) }});
    var content     = document.getElementById({{ Js::from($contentId) }});
    var searchInput = document.getElementById({{ Js::from($searchId) }});
    var badgesEl    = root.querySelector('[data-ms-badges]');
    var placeholderEl = root.querySelector('[data-ms-placeholder]');
    var hiddenWrap  = root.querySelector('[data-ms-hidden-inputs]');
    var emptyEl     = root.querySelector('[data-ms-empty]');

    // ── Position & Open/Close ─────────────────────────────────
    function openDropdown() {
        var r = trigger.getBoundingClientRect();
        content.style.top   = (r.bottom + 4) + 'px';
        content.style.left  = r.left + 'px';
        content.style.width = r.width + 'px';
        content.classList.remove('hidden');
        trigger.setAttribute('aria-expanded', 'true');
        if (searchInput) {
            searchInput.value = '';
            filterItems('');
            searchInput.focus();
        }
    }

    function closeDropdown() {
        content.classList.add('hidden');
        trigger.setAttribute('aria-expanded', 'false');
    }

    trigger.addEventListener('click', function (e) {
        e.stopPropagation();
        content.classList.contains('hidden') ? openDropdown() : closeDropdown();
    });

    document.addEventListener('click', function (e) {
        if (!root.contains(e.target) && !content.contains(e.target)) closeDropdown();
    });

    // Reposition on scroll/resize
    window.addEventListener('scroll', function () { if (!content.classList.contains('hidden')) openDropdown(); }, true);
    window.addEventListener('resize', function () { if (!content.classList.contains('hidden')) closeDropdown(); });

    // ── Render ────────────────────────────────────────────────
    function getSelected() {
        return Array.from(root.querySelectorAll('[data-ms-item][aria-selected="true"]'));
    }

    function renderBadges() {
        var selected = getSelected();

        // Hidden inputs
        hiddenWrap.innerHTML = selected.map(function (item) {
            return '<input type="hidden" name="' + inputName + '[]" value="' + item.dataset.value + '">';
        }).join('');

        if (selected.length === 0) {
            badgesEl.classList.add('hidden');
            placeholderEl.classList.remove('hidden');
            badgesEl.innerHTML = '';
        } else {
            placeholderEl.classList.add('hidden');
            badgesEl.classList.remove('hidden');
            badgesEl.innerHTML = selected.map(function (item) {
                return '<span class="badge bg-primary/10 text-primary gap-1 pe-1">'
                    + item.dataset.label
                    + '<button type="button" data-remove="' + item.dataset.value + '" '
                    + 'class="hover:text-destructive transition-colors" tabindex="-1">'
                    + '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" '
                    + 'fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">'
                    + '<path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>'
                    + '</button></span>';
            }).join('');
        }
    }

    // ── Item toggle ───────────────────────────────────────────
    content.addEventListener('click', function (e) {
        var item = e.target.closest('[data-ms-item]');
        if (!item) return;
        e.stopPropagation();
        var isSelected = item.getAttribute('aria-selected') === 'true';
        item.setAttribute('aria-selected', isSelected ? 'false' : 'true');
        renderBadges();
    });

    // Remove badge
    badgesEl.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-remove]');
        if (!btn) return;
        e.stopPropagation();
        var item = root.querySelector('[data-ms-item][data-value="' + btn.dataset.remove + '"]');
        if (item) { item.setAttribute('aria-selected', 'false'); renderBadges(); }
    });

    // ── Search ────────────────────────────────────────────────
    function filterItems(query) {
        var q = query.toLowerCase();
        var visible = 0;
        root.querySelectorAll('[data-ms-item]').forEach(function (item) {
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

    // ── Init ──────────────────────────────────────────────────
    renderBadges();
})();
</script>
