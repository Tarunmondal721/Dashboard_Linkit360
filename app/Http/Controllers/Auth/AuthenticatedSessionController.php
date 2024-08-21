<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Utility;
use App\Models\UsersOperatorsServices;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use App\Models\role_operators;
use Jenssegers\Agent\Facades\Agent;
use Carbon\Carbon;
use App\Models\User;
use App\common\Utility as UserActivity;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function __construct()
    {
        if(!file_exists(storage_path() . "/installed"))
        {
            header('location:install');
            die;
        }
    }

    /*protected function authenticated(Request $request, $user)
    {
        if($user->delete_status == 1)
        {
            auth()->logout();
        }

        return redirect('/check');
    }*/

    public function store(LoginRequest $request)
    {
        $post = [];
        $request->authenticate();

        $post['ip'] = $request->ip();
        $post['device'] = Agent::device();
        $post['last_login'] = Carbon::now()->toDateTimeString();

        $request->session()->regenerate();

        $operatorsServices = UsersOperatorsServices::GetOperaterServiceByUserId(Auth::user()->id)->get();
        $operators = array_unique($operatorsServices->pluck('id_operator')->toArray());
        $services = array_unique($operatorsServices->pluck('id_service')->toArray());
        $userOperatorService = ['id_operators' => $operators,'id_services' => $services];
        
        if(!empty($operators) && !empty($services))
        {
            session(['userOperatorService' => $userOperatorService]);
        }

        $user = User::Uid(Auth::user()->id)->first();

        $user->update($post);

        UserActivity::user_activity('Login');

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function showLoginForm($lang = '')
    {
        if(empty($lang))
        {
            $lang = Utility::getValByName('default_language');
        }

        \App::setLocale($lang);
        return view('auth.login', compact('lang'));
    }

    public function showLinkRequestForm($lang = '')
    {
        if(empty($lang))
        {
            $lang = Utility::getValByName('default_language');
        }

        \App::setLocale($lang);

        return view('auth.forgot-password', compact('lang'));
    }

    public function destroy(Request $request)
    {
        UserActivity::user_activity('Logout');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
