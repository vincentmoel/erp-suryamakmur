@php
    $sidebarConfig = config('sidebar');
    $menuGroups = [];

    foreach ($sidebarConfig as $section) {
        $groupLabel = isset($section['group']) ? __($section['group']) : null;
        $items = [];

        foreach ($section['children'] ?? [] as $item) {
            if (!isset($item['route'])) continue;
            try {
                $url = route($item['route']);
            } catch (\Exception $e) {
                continue;
            }
            $items[] = [
                'title' => __($item['title']),
                'icon'  => $item['icon'] ?? 'circle',
                'url'   => $url,
            ];
        }

        if (count($items)) {
            $menuGroups[] = ['label' => $groupLabel, 'items' => $items];
        }
    }
@endphp

<div data-command-palette class="fixed inset-0 z-50 hidden bg-black/40 p-4">
    <div class="mx-auto mt-20 max-w-xl overflow-hidden rounded-xl border bg-popover text-popover-foreground shadow-2xl" onclick="event.stopPropagation()">

            {{-- Search Input --}}
            <div class="flex items-center gap-3 border-b px-4 py-3">
                <x-icon name="search" class="size-4 shrink-0 text-muted-foreground" />
                <input
                    data-command-input
                    class="w-full bg-transparent text-sm outline-none placeholder:text-muted-foreground"
                    placeholder="{{ __('general.search_placeholder', [], null) ?? 'Search menu...' }}"
                    autocomplete="off"
                />
                <kbd class="shrink-0 rounded border px-1.5 py-0.5 text-[10px] text-muted-foreground">Esc</kbd>
            </div>

            {{-- Results --}}
            <div class="max-h-96 overflow-y-auto py-2" data-command-list>
                @foreach ($menuGroups as $group)
                    <div data-command-group>
                        @if ($group['label'])
                            <p class="px-3 pb-1 pt-3 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">
                                {{ $group['label'] }}
                            </p>
                        @endif

                        @foreach ($group['items'] as $item)
                            <a
                                data-command-item
                                href="{{ $item['url'] }}"
                                class="mx-1 flex items-center gap-3 rounded-lg px-3 py-2 text-sm hover:bg-accent"
                            >
                                <x-icon name="{{ $item['icon'] }}" class="size-4 shrink-0 text-muted-foreground" />
                                <span>{{ $item['title'] }}</span>
                            </a>
                        @endforeach
                    </div>
                @endforeach

                {{-- Empty state --}}
                <p data-command-empty class="hidden py-8 text-center text-sm text-muted-foreground">
                    {{ __('general.search_no_results', [], null) ?? 'No results found.' }}
                </p>
            </div>

            {{-- Footer hint --}}
            <div class="flex items-center gap-4 border-t px-4 py-2.5 text-[11px] text-muted-foreground">
                <span class="flex items-center gap-1"><kbd class="rounded border px-1 py-0.5">↑↓</kbd> navigate</span>
                <span class="flex items-center gap-1"><kbd class="rounded border px-1 py-0.5">↵</kbd> open</span>
                <span class="flex items-center gap-1"><kbd class="rounded border px-1 py-0.5">Esc</kbd> close</span>
            </div>
        </div>
</div>

@push('scripts')
<script>
// Auto-focus input & reset filter when palette opens
(function () {
    const palette = document.querySelector('[data-command-palette]');
    if (!palette) return;

    const input = palette.querySelector('[data-command-input]');
    const empty = palette.querySelector('[data-command-empty]');

    // Observe hidden class removal = palette opened
    new MutationObserver(function (mutations) {
        mutations.forEach(function (m) {
            if (m.attributeName === 'class') {
                const isOpen = !palette.classList.contains('hidden');
                if (isOpen) {
                    if (input) { input.value = ''; input.dispatchEvent(new Event('input')); input.focus(); }
                }
            }
        });
    }).observe(palette, { attributes: true });

    // Group label visibility during filter
    document.addEventListener('input', function (e) {
        if (!e.target.matches('[data-command-input]')) return;
        const q = e.target.value.trim().toLowerCase();
        let anyVisible = 0;
        palette.querySelectorAll('[data-command-group]').forEach(function (group) {
            let groupVisible = 0;
            group.querySelectorAll('[data-command-item]').forEach(function (item) {
                const match = !q || item.innerText.toLowerCase().includes(q);
                item.hidden = !match;
                if (match) groupVisible++;
            });
            const label = group.querySelector('p');
            if (label) label.hidden = (groupVisible === 0);
            anyVisible += groupVisible;
        });
        if (empty) empty.hidden = (anyVisible > 0);
    });
})();
</script>
@endpush

