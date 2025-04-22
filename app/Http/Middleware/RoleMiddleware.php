<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user() || ! $request->user()->hasAnyRole($roles)) {
            if ($request->header('X-Inertia')) {
                return Inertia::render('Errors/403', [
                    'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini',
                ])->toResponse($request)->setStatusCode(403);
            }
            return Inertia::render('Errors/403', [
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini',
            ])->toResponse($request)->setStatusCode(403);

            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini');
        }
        return $next($request);
    }
}
