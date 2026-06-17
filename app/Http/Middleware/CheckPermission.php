<?php

namespace App\Http\Middleware;

use App\Helpers\Response as HelpersResponse;
use App\Models\Permission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $module, $permissionType): Response
    {
        $roleIds = auth()->user()->roles->pluck('id');

        if ($roleIds->contains(1)) {
            return $next($request);
        }

        $hasPermission = Permission::whereIn('role_id', $roleIds)
            ->where('module', $module)
            ->where('action', $permissionType)
            ->exists();

        if ($hasPermission)
        {
            return $next($request);
        }
        else
        {
            if($request->expectsJson()){
                return HelpersResponse::build(
                    403,
                    "Unauthorized",
                );
            }

            return redirect()->back()->with("error", [
                'code'      => 403,
                'message'   => "This action is unauthorized."
            ]);
        }
    }
}
