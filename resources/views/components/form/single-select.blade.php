@props([
    'name',
    'options'     => [],   // [['value' => '', 'label' => ''], ...]
    'selected'    => null, // selected value (edit / repopulate)
    'placeholder' => 'Select option...',
    'searchable'  => true,
])

@php
    $uid = 'ss-' . Str::random(8);

    $oldValue = old($name, $selected);
    $oldValue = $oldValue !== null ? (string) $oldValue : null;

    $selectedLabel = collect($options)->firstWhere('value', $oldValue)['label'] ?? null;
@endphp

<div data-single-select="{{ $uid }}" class="relative">

    {{-- Trigger --}}
    <button type="button"
            aria-expanded="false"
            aria-haspopup="listbox"
            class="select-trigger flex items-center justify-between w-full {{ $errors->has($name) ? 'border-destructive' : '' }}">
        <span data-ss-label class="text-sm truncate {{ $selectedLabel ? '' : 'text-muted-foreground' }}">
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
    <div role="listbox" class="select-content hidden max-h-60 overflow-auto">

        @if($searchable)
            <div class="px-1 pb-2 sticky top-0 bg-popover">
                <input data-ss-search
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
    var root = document.querySelector('[data-single-select="{{ $uid }}"]');
    if (root) initSingleSelect(root, {{ Js::from($placeholder) }});
})();
</script>
