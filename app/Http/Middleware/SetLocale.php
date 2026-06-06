<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            app()->setLocale(auth()->user()->language ?? 'id');
        }

        return $next($request);
    }
}
