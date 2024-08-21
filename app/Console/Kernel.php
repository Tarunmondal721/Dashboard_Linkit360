<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $todayObj =  Carbon::now();

        $today =  $todayObj->format('Y-m-d');

        $Cmonth = $todayObj->month;
        $Cyear = $todayObj->year;

        if ($Cmonth < 10) {
            $Cmonth = "0" . $Cmonth;
        }

        $todayfolder = Carbon::now()->format('Ymd');
        $previousdayObj = Carbon::now();
        $previousdaylastObj = Carbon::now();
        $previousday = $previousdayObj->subDays(1)->format('Y-m-d');

        $previousdaylast = $previousdaylastObj->subDays(2)->format('Y-m-d');

        $last7days = Carbon::now()->subDays(8)->format('Y-m-d');

        $startCurrentMonth = Carbon::now()->startOfMonth()->format('Y-m-d');

        $currentDate = Carbon::now()->format('Y-m-d');

        $Pmonth = $previousdayObj->month;

        if ($Pmonth < 10) {
            $Pmonth = "0" . $Pmonth;
        }

        $Pyear = $previousdayObj->year;

        $folder_path = "storage/logs/cron/" . $todayfolder;

        Storage::disk('local')->makeDirectory($folder_path);

        /* Emergency Cron Run  data */

        /*$schedule->command('CronPnlSummariseOperatorRangeSync --sdate=2024-03-01 --edate=2024-03-31')
            ->dailyAt('17:15')   //5:00
            ->runInBackground()
            ->appendOutputTo(public_path($folder_path . "/CronPnlSummariseOperatorRangeSync.log"))
            ->timezone('Asia/Jakarta')
            ->onSuccess(function () {

                $this->call('CronPnlMonthlyReportSummery', [
                    '--year' => '2024', '--month' => '06'
                ]);
            })
            ->onFailure(function () {
            });*/

        /*$schedule->command('CronDashboardSummarizeDay')
            ->dailyAt('13:35')
            ->appendOutputTo(public_path($folder_path."/CronDashboardSummarizeDay.log"))
            ->runInBackground()
            ->timezone('Asia/Jakarta')
            ->onSuccess(function () {})
            ->onFailure(function () {});*/

        /*$schedule->command('CornServiceDataDayRangeSync --sdate=2024-03-01  --edate=2024-03-31')
            ->dailyAt('13:00')   //5:00
            ->runInBackground()
            ->appendOutputTo(public_path($folder_path . "/CornServiceDataDayRangeSync.log"))
            ->timezone('Asia/Jakarta')
            ->onSuccess(function () {

                $this->call('CronServiceDataSummariseRangeSync', [
                    '--sdate' => '2024-03-01', '--edate' => '2024-03-31'
                ]);

                $this->call('CronUserServiceDataSummariseRangeSync', [
                    '--sdate' => '2024-03-01', '--edate' => '2024-03-31'
                ]);

                $this->call('CronMonthlyReportSummery', [
                    '--year' => '2024', '--month' => '06'
                ]);
            })
            ->onFailure(function () {
                // The task failed...
            });*/


        /* Range Data Run On Weekend */

        $schedule->command('CornServiceDataDayRangeSync --sdate=' . $last7days . ' --edate=' . $previousday)
            ->dailyAt('02:05')   //5:00
            ->runInBackground()
            ->appendOutputTo(public_path($folder_path . "/CornServiceDataDayRangeSync.log"))
            ->timezone('Asia/Jakarta')
            ->onSuccess(function () use ($previousday, $last7days) {

                $this->call('CronServiceDataSummariseRangeSync', [
                    '--sdate' => $last7days, '--edate' => $previousday
                ]);

                $this->call('CronUserServiceDataSummariseRangeSync', [
                    '--sdate' => $last7days, '--edate' => $previousday
                ]);
            })
            ->onFailure(function () {
                // The task failed...
            });

        $schedule->command('CronPnlSummariseOperatorRangeSync --sdate=' . $last7days . ' --edate=' . $previousday)
            ->dailyAt('07:25')   //5:00
            ->runInBackground()
            ->appendOutputTo(public_path($folder_path . "/CronPnlSummariseOperatorRangeSync.log"))
            ->timezone('Asia/Jakarta')
            ->onSuccess(function () {
                // The task succeeded...
            })
            ->onFailure(function () {
                // The task failed...
            });

        $schedule->command('CronPnlUserSummariseOperatorRangeSync --sdate=' . $last7days . ' --edate=' . $previousday)
            ->dailyAt('07:35')   //5:00
            ->runInBackground()
            ->appendOutputTo(public_path($folder_path . "/CronPnlUserSummariseOperatorRangeSync.log"))
            ->timezone('Asia/Jakarta')
            ->onSuccess(function () {
                // The task succeeded...
            })
            ->onFailure(function () {
                // The task failed...
            });

        /* Month Data Calculation for Sumemrise */

        $schedule->command('CronServiceSyncDayData --date=' . $previousday)
            ->dailyAt('5:10')   //5:00
            ->runInBackground()
            ->appendOutputTo(public_path($folder_path . "/CronServiceSyncDayData.log"))
            ->timezone('Asia/Jakarta')
            ->onSuccess(function () use ($previousday, $last7days) {
                // The task succeeded...
            })
            ->onFailure(function () {
                // The task failed...
            });

        $schedule->command('CronMonthlyReportSummery --year=' . $Cyear . '  --month=' . $Cmonth)
            //->dailyAt('14:09')   //5:00
            ->everyTwoHours()
            ->appendOutputTo(public_path($folder_path . "/CronMonthlyReportSummery.log"))
            ->runInBackground()
            ->timezone('Asia/Jakarta');

        $schedule->command('progressServiceTaskPastDueDate')
            ->daily()
            ->timezone('Asia/Jakarta')
            ->appendOutputTo(public_path($folder_path . "/CronServiceStatusPastDueDate.log"));

        $schedule->command('CronUserMonthlyReportSummery --year=' . $Cyear . '  --month=' . $Cmonth)
            //->dailyAt('6:40')   //5:00
            ->everyThreeHours()
            ->appendOutputTo(public_path($folder_path . "/CronUserMonthlyReportSummery.log"))
            ->runInBackground()
            ->timezone('Asia/Jakarta');

        $schedule->command('CronPnlSummariseOperator --date=' . $previousday)
            ->dailyAt('6:41')   //5:00
            ->runInBackground()
            ->appendOutputTo(public_path($folder_path . "/CronPnlSummariseOperator.log"))
            ->timezone('Asia/Jakarta');

        $schedule->command('CronPnlUserSummariseOperator --date=' . $previousday)
            ->dailyAt('6:50')   //5:00
            ->runInBackground()
            ->appendOutputTo(public_path($folder_path . "/CronPnlUserSummariseOperator.log"))
            ->timezone('Asia/Jakarta');

        $schedule->command('CronPnlMonthlyReportSummery --year=' . $Cyear . '  --month=' . $Cmonth)
            //->dailyAt('6:52')   //5:00
            ->everyTwoHours()
            ->appendOutputTo(public_path($folder_path . "/CronPnlMonthlyReportSummery.log"))
            ->runInBackground()
            ->timezone('Asia/Jakarta');

        /* END */

        /* Final Data retreive previous day */

        $schedule->command('CronServiceSyncDayData --date=' . $previousday)
            ->dailyAt('7:10')   //5:00
            ->runInBackground()
            ->appendOutputTo(public_path($folder_path . "/CronServiceSyncDayData.log"))
            ->timezone('Asia/Jakarta')
            ->onSuccess(function () {
                // The task succeeded...
            })
            ->onFailure(function () {
                // The task failed...
            });

        $schedule->command('CronMonthlyReportSummery --year=' . $Cyear . '  --month=' . $Cmonth)
            ->dailyAt('8:20')   //5:00
            // ->everyMinute()
            ->appendOutputTo(public_path($folder_path . "/CronMonthlyReportSummery.log"))
            ->runInBackground()
            ->timezone('Asia/Jakarta');

        $schedule->command('CronUserMonthlyReportSummery --year=' . $Cyear . '  --month=' . $Cmonth)
            ->dailyAt('8:40')   //5:00
            // ->everyMinute()
            ->appendOutputTo(public_path($folder_path . "/CronUserMonthlyReportSummery.log"))
            ->runInBackground()
            ->timezone('Asia/Jakarta');

        $schedule->command('CronPnlMonthlyReportSummery --year=' . $Cyear . '  --month=' . $Cmonth)
            ->dailyAt('9:10')   //5:00
            // ->everyMinute()
            ->appendOutputTo(public_path($folder_path . "/CronPnlMonthlyReportSummery.log"))
            ->runInBackground()
            ->timezone('Asia/Jakarta');

        /* End  */

        /* Daily Basis Cron */

        $schedule->command('CronOpertorSyn')
            ->dailyAt('00:30')
            ->appendOutputTo(public_path($folder_path . "/CronOpertorSyn.log"))
            ->runInBackground()
            ->timezone('Asia/Jakarta');

        $schedule->command('CronServiceSync')
            ->dailyAt('00:40')
            ->appendOutputTo(public_path($folder_path . "/CronServiceSync.log"))
            ->runInBackground()
            ->timezone('Asia/Jakarta');

        $schedule->command('CurrencyUpdate')
            ->dailyAt('3:40')
            ->appendOutputTo(public_path($folder_path . "/CurrencyUpdate.log"))
            ->runInBackground()
            ->timezone('Asia/Jakarta');

        // waki data sumarise

        $schedule->command('CronPnlSummariseOperator --date=' . $today)
            ->everyThreeHours()
            ->runInBackground()
            ->appendOutputTo(public_path($folder_path . "/CronPnlSummariseOperator.log"))
            ->timezone('Asia/Jakarta');

        $schedule->command('CronPnlUserSummariseOperator --date=' . $today)
            ->everyFourHours()
            ->runInBackground()
            ->appendOutputTo(public_path($folder_path . "/CronPnlUserSummariseOperator.log"))
            ->timezone('Asia/Jakarta');

        // Ferry Data Upto date

        $schedule->command('CronServiceSyncDayData --date=' . $today)
            ->everyThreeHours() // 9 ,19
            ->runInBackground()
            ->appendOutputTo(public_path($folder_path . "/CronServiceSyncDayData.log"))
            ->timezone('Asia/Jakarta')
            ->onSuccess(function () {
                // The task succeeded...
            })
            ->onFailure(function () {
                // The task failed...
            });

        /* Monthly Report Summery Data */

        $schedule->command('CronMonthlyReportSummery --year=' . $Cyear . '  --month=' . $Cmonth)
            ->everyTwoHours()
            ->appendOutputTo(public_path($folder_path . "/CronMonthlyReportSummery.log"))
            ->runInBackground()
            ->timezone('Asia/Jakarta');

        $schedule->command('CronUserMonthlyReportSummery --year=' . $Pyear . '  --month=' . $Pmonth)
            ->everyTwoHours()
            ->appendOutputTo(public_path($folder_path . "/CronUserMonthlyReportSummery.log"))
            ->runInBackground()
            ->timezone('Asia/Jakarta');

        /* END Monthly Report Summery Data */

        /* Monthly PNL Report Summery Data */

        $schedule->command('CronPnlMonthlyReportSummery --year=' . $Cyear . '  --month=' . $Cmonth)
            ->everyFourHours()
            ->appendOutputTo(public_path($folder_path . "/CronPnlMonthlyReportSummery.log"))
            ->runInBackground()
            ->timezone('Asia/Jakarta');

        $schedule->command('CronPnlMonthlyReportSummery --year=' . $Pyear . '  --month=' . $Pmonth)
            ->twiceDailyAt(2, 7, 40)
            ->appendOutputTo(public_path($folder_path . "/CronPnlMonthlyReportSummery.log"))
            ->runInBackground()
            ->timezone('Asia/Jakarta');

        /* END Monthly PNL Report Summery Data */

        // Previous day data update

        $schedule->command('CronServiceSyncDayData --date=' . $previousday)
            ->dailyAt('5:30')   //5:00
            ->runInBackground()
            ->appendOutputTo(public_path($folder_path . "/CronServiceSyncDayData.log"))
            ->timezone('Asia/Jakarta')
            ->onSuccess(function () {
                // The task succeeded...
            })
            ->onFailure(function () {
                // The task failed...
            });

        $schedule->command('CronDashboardSummarizeDay')
            ->everyFourHours()
            ->appendOutputTo(public_path($folder_path . "/CronDashboardSummarizeDay.log"))
            ->runInBackground()
            ->timezone('Asia/Jakarta');

        $schedule->command('CornServiceDataDayRangeSync --sdate=' . $startCurrentMonth . ' --edate=' . $currentDate)
            ->weeklyOn(6, '01:20')  //Saturday at 1:20
            ->runInBackground()
            ->appendOutputTo(public_path($folder_path . "/CornServiceDataDayRangeSync.log"))
            ->timezone('Asia/Jakarta')
            ->onSuccess(function () use ($currentDate, $startCurrentMonth) {

                $this->call('CronServiceDataSummariseRangeSync', [
                    '--sdate' => $startCurrentMonth, '--edate' => $currentDate
                ]);

                $this->call('CronUserServiceDataSummariseRangeSync', [
                    '--sdate' => $startCurrentMonth, '--edate' => $currentDate
                ]);
            })
            ->onFailure(function () {
                // The task failed...
            });

        $schedule->command('CronPnlSummariseOperatorRangeSync --sdate=' . $startCurrentMonth . ' --edate=' . $currentDate)
            ->weeklyOn(6, '05:40')   //Saturday at 5:40
            ->runInBackground()
            ->appendOutputTo(public_path($folder_path . "/CronPnlSummariseOperatorRangeSync.log"))
            ->timezone('Asia/Jakarta')
            ->onSuccess(function () {
                // The task succeeded...
            })
            ->onFailure(function () {
                // The task failed...
            });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
