<?php
namespace App\Middlewares;

use App\Modules\Authentication\Services\AdminAuthService;
use App\Modules\Authentication\Services\UserAuthService;
use Closure;

class EnsureIsNotBlocked
{
    public function handle($request, Closure $next)
    {
        if($request->user() && $request->user()->isNotBlocked()){
            return $next($request);
        }
        (new UserAuthService)->logout();
        (new AdminAuthService)->logout();
        return redirect(route('index'))->with('error_status','You are blocked by admin.');
    }
}
