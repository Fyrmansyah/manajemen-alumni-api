<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('company');

        if ($user && !$user->is_verified) {
            // Allow access to the pending page and logout/update profile routes minimally
            if ($request->routeIs('company.pending') ||
                $request->routeIs('company.profile') ||
                $request->routeIs('company.profile.update') ||
                $request->routeIs('logout')
            ) {
                return $next($request);
            }
            return redirect()->route('company.pending');
        }

        return $next($request);
    }
}
