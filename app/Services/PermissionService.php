<?php

namespace App\Services;

use App\Enums\Module;
use App\Models\Permission;

class PermissionService
{
    private ?array $permissions = null;
    private ?bool  $superAdmin  = null;

    public function isSuperAdmin(): bool
    {
        if ($this->superAdmin === null) {
            $this->superAdmin = auth()->user()?->roles->contains('id', 1) ?? false;
        }
        return $this->superAdmin;
    }

    /** Semua permission user saat ini: ['Module' => ['read' => true, ...]] */
    public function all(): array
    {
        if ($this->permissions !== null) {
            return $this->permissions;
        }

        if ($this->isSuperAdmin()) {
            $this->permissions = [];
            foreach (Module::cases() as $mod) {
                foreach ($mod->permissions() as $action) {
                    $this->permissions[$mod->value][$action] = true;
                }
            }
            return $this->permissions;
        }

        $roleIds = auth()->user()->roles->pluck('id');

        $this->permissions = [];
        Permission::whereIn('role_id', $roleIds)
            ->get(['module', 'action'])
            ->each(function ($perm) {
                $this->permissions[$perm->module][$perm->action] = true;
            });

        return $this->permissions;
    }

    public function has(string $module, string $action): bool
    {
        return $this->all()[$module][$action] ?? false;
    }

    /** Daftar module yang punya action tertentu (misal 'read') */
    public function modulesWithAction(string $action): array
    {
        $result = [];
        foreach ($this->all() as $module => $actions) {
            if (!empty($actions[$action])) {
                $result[$module] = true;
            }
        }
        return $result;
    }
}
