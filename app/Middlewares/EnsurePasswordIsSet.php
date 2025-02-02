<?php

namespace App\Middlewares;

use Closure;

class EnsurePasswordIsSet
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|null
     */
    public function handle($request, Closure $next)
    {
        if($request->user() && !$request->user()->hasCompletedAccountSetup()){
            return $request->expectsJson()
                        ? abort(403, 'Please set your password first.')
                        : redirect()->route('profile.setup');
        }
        return $next($request);
    }
}
