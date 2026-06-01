<?php

namespace App\Http\Middleware;

use App\Enums\RoleId;
use App\Helpers\Response;
use App\Http\Controllers\AuthController;
use App\Models\User;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($userId = Auth::id()) {
            DB::table((new User())->getTable())->where('id', $userId)->update(['last_seen' => now()]);
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */

    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
