<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\common\Utility;

class CronTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        Utility::CronLog("Start Cron");

        $userId = $this->option('date');

        Utility::CronLog("End Cron");

      //  $this->call('CronOpertorSyn');

        return 0;
    }
}
