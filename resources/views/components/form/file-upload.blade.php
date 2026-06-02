@props([
    'name'        => 'photo',
    'accept'      => 'image/*',
    'maxSizeMb'   => 2,
    'preview'     => null,   // existing image URL (for edit page)
])

@php
    $uid      = 'fu-' . $name . '-' . uniqid();
    $inputId  = $uid . '-input';
    $previewId= $uid . '-preview';
    $labelId  = $uid . '-label';
@endphp

<div data-file-upload="{{ $uid }}" class="{{ $errors->has($name) ? 'ring-1 ring-destructive rounded-[calc(var(--radius)-2px)]' : '' }}">

    <input type="file"
           id="{{ $inputId }}"
           name="{{ $name }}"
           accept="{{ $accept }}"
           class="sr-only">

    {{-- Drop zone / click area --}}
    <label id="{{ $labelId }}"
           for="{{ $inputId }}"
           class="flex flex-col items-center justify-center gap-3 rounded-xl border border-dashed border-input bg-background/50 px-4 py-8 cursor-pointer transition-colors hover:bg-accent/50 has-[img]:py-4">

        {{-- Preview --}}
        @if($preview)
            <img id="{{ $previewId }}"
                 src="{{ $preview }}"
                 alt="Preview"
                 class="size-24 rounded-full object-cover ring-2 ring-border">
        @else
            <img id="{{ $previewId }}"
                 src=""
                 alt="Preview"
                 class="size-24 rounded-full object-cover ring-2 ring-border hidden">
        @endif

        {{-- Placeholder icon + text --}}
        <div id="{{ $uid }}-placeholder" class="flex flex-col items-center gap-1 text-center {{ $preview ? 'hidden' : '' }}">
            <div class="flex size-10 items-center justify-center rounded-full bg-muted">
                <x-icon name="image-up" class="size-5 text-muted-foreground" />
            </div>
            <p class="text-sm font-medium">Click to upload photo</p>
            <p class="text-xs text-muted-foreground">PNG, JPG, WEBP &mdash; max {{ $maxSizeMb }}MB</p>
        </div>

        {{-- Filename shown after pick --}}
        <p id="{{ $uid }}-filename" class="text-xs text-muted-foreground hidden"></p>
    </label>
</div>

@error($name)
    <p class="mt-1.5 text-xs text-destructive">{{ $message }}</p>
@enderror

<script>
(function () {
    const uid         = @json($uid);
    const maxBytes    = @json($maxSizeMb) * 1024 * 1024;
    const input       = document.getElementById(@json($inputId));
    const preview     = document.getElementById(@json($previewId));
    const placeholder = document.getElementById(`${uid}-placeholder`);
    const filename    = document.getElementById(`${uid}-filename`);

    if (!input) return;

    input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) return;

        if (file.size > maxBytes) {
            input.value = '';
            showToast('File too large', 'Maximum allowed size is {{ $maxSizeMb }}MB.', 'warning');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
            filename.textContent = file.name;
            filename.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    });

    // Drag & drop
    const label = document.getElementById(@json($labelId));
    ['dragover', 'dragenter'].forEach(ev => {
        label.addEventListener(ev, (e) => { e.preventDefault(); label.classList.add('bg-accent/50'); });
    });
    ['dragleave', 'drop'].forEach(ev => {
        label.addEventListener(ev, () => label.classList.remove('bg-accent/50'));
    });
    label.addEventListener('drop', (e) => {
        e.preventDefault();
        const file = e.dataTransfer?.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            input.dispatchEvent(new Event('change'));
        }
    });
})();
</script>
