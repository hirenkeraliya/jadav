<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanySelected
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->active_company_id) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No company selected.'], 403);
            }
            return redirect()->route('company.select');
        }

        return $next($request);
    }
}
