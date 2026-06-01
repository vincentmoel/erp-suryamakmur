@php
    $user = auth()->user();
    $roles = $user->roles->pluck('name')->toArray();
    $rolesText = implode(' | ', $roles);
    $needsMarquee = count($roles) > 1;
@endphp

<div class="sidebar-profile-meta grid flex-1 text-left text-sm leading-tight overflow-hidden">
    <span class="truncate font-semibold">{{ $user->name }}</span>
    @if (!empty($roles))
        @if ($needsMarquee)
            <div class="sidebar-roles-wrapper" aria-label="{{ $rolesText }}">
                {{--
                    Duplicate the text so the marquee loops seamlessly.
                    The animation moves -50% so the second copy fills the gap.
                --}}
                <span class="sidebar-roles-marquee text-muted-foreground text-xs">{{ $rolesText }} | {{ $rolesText }} | </span>
            </div>
        @else
            <span class="text-muted-foreground truncate text-xs" title="{{ $rolesText }}">{{ $rolesText }}</span>
        @endif
    @endif
</div>
