<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Session;

class AllowServiceScope implements Scope
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


        if(!empty($allow_leauge))
        {

            $builder->whereIn('id_service', $allow_leauge['id_services']);


        }


        /// Contact to Admin for a List of Operator Permission .

            // dd();

    }
}
