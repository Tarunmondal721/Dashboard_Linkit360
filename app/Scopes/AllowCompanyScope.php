<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Models\Utility;
use App\Models\CompanyOperators;
use App\Models\Company;
use Config;
use Session;

class AllowCompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $userAccessDetails =Session::get('userOperatorService');
        
        $Operators = isset($userAccessDetails['id_operators']) ? $userAccessDetails['id_operators'] : [];
        $company_ids = array();

        if(!empty($Operators))
        {
            $CompanyOperators = CompanyOperators::GetCompanyIds($Operators)->get();
            $company_ids = $CompanyOperators->pluck('company_id')->toArray(); 
        }

        if(!empty($company_ids))
        {
            $builder->whereIn('id', $company_ids);
        }
    }
}
