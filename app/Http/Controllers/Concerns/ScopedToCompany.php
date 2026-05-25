<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Company;

trait ScopedToCompany
{
    protected function company(): Company
    {
        return auth()->user()->activeCompany;
    }

    protected function companyId(): int
    {
        return (int) auth()->user()->active_company_id;
    }
}
