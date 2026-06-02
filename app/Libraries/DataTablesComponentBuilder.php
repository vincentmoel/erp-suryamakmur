<?php

namespace App\Libraries;

use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Session;

class DataTablesComponentBuilder
{
    private static array $cache = [];

    private static function icon(string $name, string $class = 'size-4'): string
    {
        return self::$cache["$name@$class"] ??= Blade::render('<x-icon name="' . $name . '" class="' . $class . '" />');
    }

    public static function userStatus($row): string
    {
        if ($row->last_seen && Carbon::parse($row->last_seen)->diffInMinutes() < 3) {
            return '<span class="dt-status dt-status--online">' . self::icon('circle-filled', 'size-[0.6rem]') . 'Online</span>';
        }

        $label = $row->last_seen
            ? 'Offline (' . Carbon::parse($row->last_seen)->diffForHumans() . ')'
            : 'Never';

        return '<span class="dt-status dt-status--offline">' . self::icon('circle-outline', 'size-[0.6rem]') . $label . '</span>';
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
        $html = '<div class="dt-action-cell">';

        foreach ($customButtons as $btn) {
            if ((Session::get('permissions')[$btn['module']][$btn['modulePermission']] ?? false) && isset($btn['html'])) {
                $html .= $btn['html'];
            }
        }

        if ((Session::get('permissions')[$module]['read'] ?? false) && isset($route['show'])) {
            $html .= '<a href="' . $route['show'] . '" class="dt-action-btn" title="View">' . self::icon('eye') . '</a>';
        }

        if ((Session::get('permissions')[$module]['update'] ?? false) && isset($route['edit'])) {
            $html .= '<a href="' . $route['edit'] . '" class="dt-action-btn" title="Edit">' . self::icon('edit') . '</a>';
        }

        if ((Session::get('permissions')[$module]['delete'] ?? false) && isset($route['delete'])) {
            $html .= '<button type="button" class="dt-action-btn dt-action-btn--destructive dt-delete-btn" data-url="' . $route['delete'] . '" title="Delete">' . self::icon('delete') . '</button>';
        }

        if ((Session::get('permissions')[$module]['restore'] ?? false) && isset($route['restore'])) {
            $html .= '<form action="' . $route['restore'] . '" method="POST" class="dt-restore-form">'
                . '<input type="hidden" name="_method" value="PATCH">'
                . '<input type="hidden" name="_token" value="' . csrf_token() . '">'
                . '<button type="submit" class="dt-action-btn" title="Restore">' . self::icon('refresh') . '</button>'
                . '</form>';
        }

        return $html . '</div>';
    }
}
