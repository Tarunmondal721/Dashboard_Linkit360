<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Models\Utility;
use App\Models\Operator;
use Config;
use Session;

class AllowCountryScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $userAccessDetails =Session::get('userOperatorService');
        $Operators = isset($userAccessDetails['id_operators']) ? $userAccessDetails['id_operators'] : [];
        $county_ids = array();

        if(!empty($Operators))
        {
            $Operator = Operator::GetCountryIds($Operators)->get();
            $county_ids = $Operator->pluck('country_id')->toArray();
        }

        if(!empty($county_ids))
        {
            $builder->whereIn('id', $county_ids);
        }
    }
}
