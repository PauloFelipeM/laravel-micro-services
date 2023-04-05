<?php

namespace App\Observers;

use Illuminate\Support\Str;
use App\Models\Company;

class CompanyObserver
{
    /**
     * Handle the Company "created" event.
     *
     * @param Company $company
     * @return void
     */
    public function creating(Company $company): void
    {
        $company->url = Str::slug($company->name);
        $company->uuid = Str::uuid();
    }

    /**
     * Handle the Company "updated" event.
     *
     * @param Company $company
     * @return void
     */
    public function updating(Company $company): void
    {
        $company->url = Str::slug($company->name);
    }

}
