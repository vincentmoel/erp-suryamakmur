<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Session;

class DataTablesComponentBuilder
{
    // ── SVG helpers ────────────────────────────────────────────
    private static function svg(string $paths, string $extra = ''): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
            style="width:1rem;height:1rem;flex-shrink:0;' . $extra . '">' . $paths . '</svg>';
    }

    private static function iconEye(): string
    {
        return self::svg(
            '<path d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
            <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>'
        );
    }

    private static function iconEdit(): string
    {
        return self::svg(
            '<path d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>'
        );
    }

    private static function iconDelete(): string
    {
        return self::svg(
            '<path d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>',
            'color:var(--destructive);'
        );
    }

    private static function iconRestore(): string
    {
        return self::svg(
            '<path d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>'
        );
    }

    // ── Shared button style ────────────────────────────────────
    private static function iconBtnStyle(bool $destructive = false): string
    {
        $color = $destructive ? 'var(--destructive)' : 'var(--muted-foreground)';
        return "display:inline-flex;align-items:center;justify-content:center;width:1.75rem;height:1.75rem;" .
               "border-radius:calc(var(--radius) - 2px);color:{$color};background:none;border:none;" .
               "cursor:pointer;transition:background-color 150ms,color 150ms;text-decoration:none;flex-shrink:0;";
    }

    private static function hoverAttrs(bool $destructive = false): string
    {
        if ($destructive) {
            return 'onmouseover="this.style.backgroundColor=\'color-mix(in oklab,var(--destructive) 10%,transparent)\'"' .
                   ' onmouseout="this.style.backgroundColor=\'transparent\'"';
        }
        return 'onmouseover="this.style.backgroundColor=\'var(--accent)\';this.style.color=\'var(--foreground)\'"' .
               ' onmouseout="this.style.backgroundColor=\'transparent\';this.style.color=\'var(--muted-foreground)\'"';
    }

    // ── Public methods ─────────────────────────────────────────

    /**
     * Render a user avatar + name + roles cell for DataTables.
     * Expects $user->roles to already be eager-loaded (no extra query fired here).
     *
     * @param  \App\Models\User  $user
     * @param  string|null       $avatarUrl  Falls back to a default avatar when null.
     * @return string
     */
    public static function userProfile($user, ?string $avatarUrl = null): string
    {
        $avatar = $avatarUrl ?? asset('src/img/default-profile.jpg');
        $name   = e($user->name);

        $roles = $user->roles->pluck('name')->map(fn($r) => e($r))->implode(' | ');

        $rolesHtml = $roles !== ''
            ? '<span style="font-size:0.7rem;color:var(--muted-foreground);line-height:1.2;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:14rem;" title="' . $roles . '">' . $roles . '</span>'
            : '';

        return '<div style="display:flex;align-items:center;gap:0.5rem;">'
            . '<img src="' . $avatar . '" alt="' . $name . '" style="width:2rem;height:2rem;border-radius:9999px;object-fit:cover;flex-shrink:0;">'
            . '<div style="display:flex;flex-direction:column;line-height:1.3;min-width:0;">'
            . '<span style="font-size:0.875rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:14rem;">' . $name . '</span>'
            . $rolesHtml
            . '</div>'
            . '</div>';
    }

    public static function actionButton(array $route, $module, $customButtons = []): string
    {
        $container = '<div style="display:flex;gap:0.25rem;align-items:center;justify-content:center;">';

        foreach ($customButtons as $customButton) {
            if ((Session::get('permissions')[$customButton['module']][$customButton['modulePermission']] ?? false) && isset($customButton['html'])) {
                $container .= $customButton['html'];
            }
        }

        if ((Session::get('permissions')[$module]['read'] ?? false) && isset($route['show'])) {
            $container .= '<a href="' . $route['show'] . '" style="' . self::iconBtnStyle() . '" ' . self::hoverAttrs() . ' title="View">' . self::iconEye() . '</a>';
        }

        if ((Session::get('permissions')[$module]['update'] ?? false) && isset($route['edit'])) {
            $container .= '<a href="' . $route['edit'] . '" style="' . self::iconBtnStyle() . '" ' . self::hoverAttrs() . ' title="Edit">' . self::iconEdit() . '</a>';
        }

        if ((Session::get('permissions')[$module]['delete'] ?? false) && isset($route['delete'])) {
            $container .= '<button type="button" class="dt-delete-btn" data-url="' . $route['delete'] . '" style="' . self::iconBtnStyle(true) . '" ' . self::hoverAttrs(true) . ' title="Delete">' . self::iconDelete() . '</button>';
        }

        if ((Session::get('permissions')[$module]['restore'] ?? false) && isset($route['restore'])) {
            $container .= '<form action="' . $route['restore'] . '" method="POST" style="display:inline;" class="dt-restore-form">
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="_token" value="' . csrf_token() . '">
                <button type="submit" style="' . self::iconBtnStyle() . '" ' . self::hoverAttrs() . ' title="Restore">' . self::iconRestore() . '</button>
            </form>';
        }

        return $container . '</div>';
    }
}
