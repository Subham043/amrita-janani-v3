<?php
namespace App\Middlewares;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureIsAdmin
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('admin')->check() && $request->user() && $request->user()->isAdmin() && $request->user()->isNotBlocked()) {
            return $next($request);
        }

        abort(404);
    }
}
