<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetActiveCompanyTheme
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->active_company_id) {
            $company = $user->activeCompany;
            if ($company) {
                View::share('activeCompany', $company);
                View::share('primaryColor', $company->primary_color ?? '#6366f1');
                View::share('secondaryColor', $company->secondary_color ?? '#f59e0b');
            }
        }

        return $next($request);
    }
}
