<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();

        if (! $user || (! $user->is_admin && ! $request->session()->has('impersonator_id'))) {
            abort(Response::HTTP_FORBIDDEN, 'This area is restricted to administrators.');
        }

        return $next($request);
    }
}
