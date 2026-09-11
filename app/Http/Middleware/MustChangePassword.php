<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustChangePassword
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->must_change_password) {
            // Allow the password change page and logout
            if ($request->is('settings/password') || $request->is('settings/password/*') || $request->route()?->named('password.*')) {
                return $next($request);
            }

            // Redirect to password change page
            return redirect()->route('password.edit');
        }

        return $next($request);
    }
}
