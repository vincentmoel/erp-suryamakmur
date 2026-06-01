<?php

namespace App\Http\Middleware;

use App\Enums\Module;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        session([
            'permissions'   => $this->getUserPermissions(),
        ]);

        return $next($request);
    }

    private function getUserPermissions()
    {
        if(in_array(1, auth()->user()->roles->pluck('id')->toArray()))
        {
            $userPermission = [];
            foreach(Module::names() as $module)
            {
                $userPermission[$module] = [
                    'menu'      => 1,
                    'create'    => 1,
                    'read'      => 1,
                    'update'    => 1,
                    'delete'    => 1,
                    'restore'   => 1
                ];
            }

            return $userPermission;
        }

        $permissions = auth()->user()->roles->flatMap(function ($role) {
            return $role->permissions;
        });

        $permission = $permissions->map->only('module','menu' ,'create', 'read', 'update', 'delete', 'restore');

        $userPermission = [];

        foreach ($permission as $subArray) {

            $moduleId = $subArray['module'];
            unset($subArray['module']);
            if (!isset($userPermission[$moduleId])) {
                $userPermission[$moduleId] = $subArray;
            } else {
                foreach ($subArray as $key => $value) {
                    if ($value === 1) {
                        $userPermission[$moduleId][$key] = 1;
                    }
                }
            }
        }

        return $userPermission;
    }
}
