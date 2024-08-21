<?php

namespace App\Http\Middleware;

use App\Models\Utility;
use App\Models\LandingPageSections;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Session;

class XSS
{
    use \RachidLaasri\LaravelInstaller\Helpers\MigrationsHelper;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $currentURLname = Route::current()->getName();
        $allow_leauge =Session::get('userOperatorService');
        // dd(empty($allow_leauge));
        if(empty($allow_leauge)){
            $user = Auth::user();
            if($user != null){



            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);
            if(!$allowAllOperator){
                if($currentURLname=='report.summary' ||$currentURLname=='report.summary.daily.country' || $currentURLname=='report.user.filter.country' || $currentURLname=='report.user.filter.operator'){
                    // dd($currentURLname);
                    // return redirect()->route('error');
                    dd("Please Contact to admin , add Operator to your account");
                }
            }
        }
        }
        // dd($allow_leauge);
        if(Auth::check())
        {
            \App::setLocale(Auth::user()->lang);

            if(Auth::user()->type == 'Owner')
            {
                /*if(Schema::hasTable('ch_messages'))
                {
                    if(Schema::hasColumn('ch_messages', 'type') == false)
                    {
                        Schema::drop('ch_messages');
                        \DB::table('migrations')->where('migration', 'like', '%ch_messages%')->delete();
                    }
                }*/

                $migrations             = $this->getMigrations();
                $messengerMigration     = Utility::get_messenger_packages_migration();
                $dbMigrations           = $this->getExecutedMigrations();
                $numberOfUpdatesPending = (count($migrations) + $messengerMigration) - count($dbMigrations);



            }
        }

        if(\Request::route()->getName() == 'chatify')
        {
            if(!\Auth::check())
            {
                return redirect()->back();
            }

            if(empty(env('CHAT_MODULE')) || Auth::user()->type == 'Super Admin' || Auth::user()->type == 'Client')
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }

        $input = $request->all();
        array_walk_recursive(
            $input, function (&$input){
            $input = strip_tags($input);
        }
        );
        $request->merge($input);

        return $next($request);
    }
}
