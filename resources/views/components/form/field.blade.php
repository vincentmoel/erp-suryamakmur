@props([
    'label',
    'name',
    'required' => false,
    'hint'     => null,
])

<div class="flex flex-col gap-3">
    <label for="{{ $name }}" class="text-sm font-medium leading-none">
        {{ $label }}
        @if($required)
            <span class="text-destructive ms-0.5">*</span>
        @endif
    </label>

    <div class="flex flex-col gap-1.5">
        {{ $slot }}

        @if($hint && !$errors->has($name))
            <p class="text-xs text-muted-foreground">{{ $hint }}</p>
        @endif

        @error($name)
            <p class="text-xs text-destructive">{{ $message }}</p>
        @enderror
    </div>
</div>
