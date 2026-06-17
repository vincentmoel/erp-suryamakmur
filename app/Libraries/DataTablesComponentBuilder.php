<?php

namespace App\Libraries;

use App\Services\PermissionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;

class DataTablesComponentBuilder
{
    private static array $cache = [];

    private static function icon(string $name, string $class = 'size-4'): string
    {
        return self::$cache["$name@$class"] ??= Blade::render('<x-icon name="' . $name . '" class="' . $class . '" />');
    }

    public static function userStatus($row): string
    {
        $dotStyle = 'width:0.55rem;height:0.55rem;flex-shrink:0;';

        $iconOnline  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="' . $dotStyle . 'fill:currentColor;stroke:none"><circle cx="12" cy="12" r="8"/></svg>';
        $iconOffline = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="' . $dotStyle . 'fill:none;stroke:currentColor;stroke-width:2.5"><circle cx="12" cy="12" r="8"/></svg>';

        if ($row->last_seen && Carbon::parse($row->last_seen)->diffInMinutes() < 3) {
            return '<span class="dt-status dt-status--online">' . $iconOnline . 'Online</span>';
        }

        $label = $row->last_seen
            ? 'Offline (' . Carbon::parse($row->last_seen)->diffForHumans() . ')'
            : 'Never';

        return '<span class="dt-status dt-status--offline">' . $iconOffline . $label . '</span>';
    }

    public static function userProfile($user, ?string $avatarUrl = null): string
    {
        $avatar = $avatarUrl ?? ($user->photo ? asset('storage/' . $user->photo) : asset('src/img/default-profile.jpg'));
        $name   = e($user->name);

        $roles = $user->roles->pluck('name')->map(fn($r) => e($r))->implode(' | ');

        $rolesHtml = $roles !== ''
            ? '<span class="dt-profile__meta" title="' . $roles . '">' . $roles . '</span>'
            : '';

        return '<div class="dt-profile">'
            . '<img src="' . $avatar . '" alt="' . $name . '" class="dt-profile__avatar">'
            . '<div class="dt-profile__info">'
            . '<span class="dt-profile__name">' . $name . '</span>'
            . $rolesHtml
            . '</div>'
            . '</div>';
    }

    public static function actionButton(array $route, $module, $customButtons = []): string
    {
        $perms = app(PermissionService::class);

        $renderCustom = function (string $position) use ($customButtons, $perms): string {
            $html = '';
            foreach ($customButtons as $btn) {
                $btnPosition = $btn['position'] ?? 'after';
                if ($btnPosition !== $position) continue;
                if ($perms->has($btn['module'], $btn['modulePermission']) && isset($btn['html'])) {
                    $html .= $btn['html'];
                }
            }
            return $html;
        };

        $html = '<div class="dt-action-cell">';

        $html .= $renderCustom('before');

        if ($perms->has($module, 'read') && isset($route['show'])) {
            $html .= '<a href="' . $route['show'] . '" class="dt-action-btn" title="View">' . self::icon('eye') . '</a>';
        }

        if ($perms->has($module, 'update') && isset($route['edit'])) {
            $html .= '<a href="' . $route['edit'] . '" class="dt-action-btn" title="Edit">' . self::icon('edit') . '</a>';
        }

        if ($perms->has($module, 'delete') && isset($route['delete'])) {
            $html .= '<button type="button" class="dt-action-btn dt-action-btn--destructive dt-delete-btn" data-url="' . $route['delete'] . '" title="Delete">' . self::icon('delete') . '</button>';
        }

        if ($perms->has($module, 'restore') && isset($route['restore'])) {
            $html .= '<form action="' . $route['restore'] . '" method="POST" class="dt-restore-form">'
                . '<input type="hidden" name="_method" value="PATCH">'
                . '<input type="hidden" name="_token" value="' . csrf_token() . '">'
                . '<button type="submit" class="dt-action-btn" title="Restore">' . self::icon('refresh') . '</button>'
                . '</form>';
        }

        $html .= $renderCustom('after');

        return $html . '</div>';
    }
}
