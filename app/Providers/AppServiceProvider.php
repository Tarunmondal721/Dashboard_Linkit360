<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;
use Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //

        if(config('app.env') === 'local') {
            // \URL::forceScheme('https');
        }else{
            \URL::forceScheme('https');
        }


        DB::listen(function($query) {


            $channel = Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/laravel-query'.date("Y-m-d").'.log'),
              ]);
               //channel('slack')
               //stack(['slack', $channel])

             /* Log::stack(['slack', $channel])->info(
                $query->sql,
                [
                    'bindings' => $query->bindings,
                    'time' => $query->time
                ]
            );*/
        });
    
    }
}
