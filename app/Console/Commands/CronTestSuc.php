<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CronTestSuc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CronTestSuc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CronTestSuc Command description';

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
        echo "start CronTestSuc ...";
        return 0;
    }
}
