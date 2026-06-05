<?php

namespace App\Helpers;

class HtmlBuilder
{
    /**
     * Render a shadcn-style toggle switch.
     *
     * Form mode  : pass $inputId — renders a hidden <input> bound via data-toggle-input.
     * AJAX mode  : pass $url    — renders a standalone button with data-toggle-url for DataTable use.
     */
    public static function toggle(
        bool $isActive,
        ?string $url = null,
        ?string $inputId = null,
        string $label = 'Active',
        string $name = 'is_active',
    ): string {
        $state   = $isActive ? 'checked' : 'unchecked';
        $dataAttr = $url
            ? "data-toggle-url=\"{$url}\""
            : "data-toggle-input=\"{$inputId}\"";

        $button = <<<HTML
            <button type="button" role="switch"
                    data-slot="switch" data-size="default" data-state="{$state}"
                    {$dataAttr}
                    class="group/switch inline-flex shrink-0 cursor-pointer items-center rounded-full border border-transparent shadow-xs transition-all outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 data-[size=default]:h-[1.15rem] data-[size=default]:w-8 data-[state=checked]:bg-primary data-[state=unchecked]:bg-input">
                <span data-slot="switch-thumb" data-state="{$state}"
                      class="pointer-events-none block rounded-full bg-background ring-0 transition-transform group-data-[size=default]/switch:size-4 data-[state=checked]:translate-x-[calc(100%-2px)] data-[state=unchecked]:translate-x-0">
                </span>
            </button>
        HTML;

        if ($inputId) {
            $value = $isActive ? '1' : '0';
            return <<<HTML
                <div class="flex items-center gap-3">
                    <input type="hidden" name="{$name}" id="{$inputId}" value="{$value}">
                    {$button}
                    <span class="text-sm font-medium">{$label}</span>
                </div>
            HTML;
        }

        return <<<HTML
            <div class="flex items-center gap-2">
                {$button}
                <span class="text-xs toggle-label">{$label}</span>
            </div>
        HTML;
    }
}
