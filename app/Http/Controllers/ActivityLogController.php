<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\UserActivity;

class ActivityLogController extends Controller
{
    public function user()
    {
        if(\Auth::user()->can('User Activity'))
        {
            $user  = Auth::user();
            $users = User::where('created_by', '=', $user->ownerId())->where('type', '!=', 'Client')->get();

            foreach ($users as $key => $value) {
                $user_activity = UserActivity::where('user_id', '=', $value['id'])->latest()->first();

                $users[$key]['action'] = isset($user_activity) ? $user_activity['action'] : 'N/A';
                $users[$key]['action_time'] = isset($user_activity) ? $user_activity['created_at'] : 'N/A';
            }

            return view('activity.users', compact('users'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function system()
    {
        if(isset($_GET['date'])){
            echo $_GET['date'];
        }
        return view('report.index');
    }
}
