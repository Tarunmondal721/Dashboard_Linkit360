<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Models\Utility;
use Config;
use Session;

class AllowOperaterScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {

        $allow_leauge =Session::get('userOperatorService');


        if(empty($allow_leauge))
        {
            $allow_leauge['id_operators'] = [];
        }
        $builder->whereIn('id_operator', $allow_leauge['id_operators']);


        /// Contact to Admin for a List of Operator Permission .

            // dd();
        
    }
}
