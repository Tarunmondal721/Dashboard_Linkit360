<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Models\Operator;
use App\Models\UserPivot;
use App\Http\Requests\PivotRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PivotFilterController extends Controller
{
    public function index(){
        if(\Auth::user()->can('Pivot Management'))
        {
        Session::forget('error');
        $user_id=Auth::user()->id;
        $pivotUser = UserPivot::GetByUserId($user_id)->first();
        return view('service.pivotFilter',compact('pivotUser'));
    }
    else
    {
        return redirect()->back()->with('error', __('Permission Denied.'));
    }
    }

    public function update(PivotRequest $request){
        // return view('service.pivotFilter');

        // return($type);
        $user = \Auth::user();

        $pivotdata[] =
        ['user_id'=>$user->id,
        'report_type'=> json_encode($request->type),
        'report_column'=> json_encode($request->data),
        'description'=> json_encode($request->date)
        ];


        UserPivot::upsert($pivotdata,['user_id'],['report_type','report_column','description']);
        return redirect()->back()->with(
            'success', __('User Pivot successfully create!')
        );


    }


}
