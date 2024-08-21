<?php

namespace App\Console\Commands;

use App\Mail\ServicePastDueDate;
use App\Models\ScServiceProgres;
use App\Models\ScServiceStatus;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mail;

class CronServiceStatusPastDueDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'progressServiceTaskPastDueDate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $timeToday = new DateTime('now');
        $data = DB::table('sc_services')
            ->select('sc_services.service_name', 'users.name', 'sc_service_progres.id as id_progres', 'sc_service_progres.status', 'sc_services.id as id_service', 'sc_service_statuses.name as task_status', 'sc_services.pmo', 'users.email')
            ->leftJoin('sc_service_progres', 'sc_service_progres.id_service', '=', 'sc_services.id')
            ->leftJoin('users', 'users.id', '=', 'sc_services.pmo')
            ->leftJoin('sc_service_statuses', 'sc_service_statuses.id', '=', 'sc_service_progres.id_service_status')
            ->whereNotNull('sc_service_progres.dute_date')
            ->whereNull('sc_service_progres.is_sent_email')
            ->where('sc_service_progres.status', '!=', 'complete')
            ->where('sc_service_progres.dute_date', '<=', $timeToday->format('Y-m-d'))
            ->get();
        if (count($data) == 0) {
            echo "Nothing Data" . PHP_EOL;
        }
        $mappingService = [];
        $idService = 0;
        $index = 0;
        $ids = [];
        foreach ($data as $item) {
            array_push($ids, $item->id_progres);
            if ($idService != $item->id_service) {
                $index++;

                $idService = $item->id_service;
                $details = [
                    'service_name' => $item->service_name,
                    'task_status' => $item->task_status,

                    'status' => $item->status
                ];
                $mappingService[] = [
                    'email' => $item->email,
                    'name' => $item->name,
                    'service_name' => $item->service_name,
                    'details' => [$details]
                ];
            } else {
                array_push($mappingService[$index - 1]['details'], [
                    'task_status' => $item->task_status,
                    'status' => $item->status
                ]);
            }

            echo $item->service_name . "|" . $item->task_status . PHP_EOL;
        }
        foreach ($mappingService as $item) {
            Mail::to($item['email'])->send(new ServicePastDueDate($item['details'], $item['name'], $item['service_name']));
        }

        ScServiceProgres::whereIn('id', $ids)->update([
            'is_sent_email' => 1
        ]);
        return 0;
    }
}
