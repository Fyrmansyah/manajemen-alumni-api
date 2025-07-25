<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirect based on guard
                if ($guard === 'admin') {
                    return redirect('/admin/dashboard');
                }
                
                if ($guard === 'alumni') {
                    return redirect('/alumni/dashboard');
                }
                
                if ($guard === 'company') {
                    return redirect('/company/dashboard');
                }
                
                // Default redirect
                return redirect('/home');
            }
        }

        return $next($request);
    }
} 