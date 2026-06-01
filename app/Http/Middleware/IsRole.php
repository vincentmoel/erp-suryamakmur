<?php

namespace App\Http\Middleware;

use App\Helpers\Response;
use Closure;
use Illuminate\Http\Request;

class IsRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roleId)
    {
        $getAuthRoles = auth()->user()->roles->pluck('id')->toArray();
        if(in_array($roleId, $getAuthRoles)){
            return $next($request);
        }

        return Response::build(
            403,
            'This action is unauthorized.'
        );
    }
}