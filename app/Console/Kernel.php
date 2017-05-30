<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Child;
use Carbon\Carbon;


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
        $schedule->call(function () {
            $today = Carbon::now();
            $children = Child::all();

            foreach ($children as $child) {
                // if ($child->date_of_birth ) {
                //     # code...
                // }
                $birthday = Carbon::parse($child->date_of_birth);

                $is_birthday_next_week = $today->isBirthday($birthday->subDays(7));

                if ($is_birthday_next_week) {
                    Mail::send('endOfPeriodMail', ['randomWinner' => $randomWinner, 'current_period_nr' => $current_period->period_nr, 'prize' => $prize], function($message){
                        $message->to(env('OWNER_EMAIL'), 'Spin2Win')->subject('Contest period has ended! Winner is inside!')->from("no-reply@spin2win.com");
                    });
                }
            }




        })

        // ->dailyAt('09:00')
        ->timezone('Europe/Brussels');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
