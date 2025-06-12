<?php
namespace App\Middlewares;

use App\Modules\Authentication\Services\AdminAuthService;
use App\Modules\Authentication\Services\UserAuthService;
use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureIsAdmin
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('admin')->check() && $request->user() && $request->user()->isAdmin() && $request->user()->isNotBlocked()) {
            return $next($request);
        }

        (new UserAuthService)->logout();
        (new AdminAuthService)->logout();
        abort(404);
    }
}
