<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Child;
use Carbon\Carbon;
use Mail;


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
                $birthday = Carbon::parse($child->date_of_birth);

                if ($today->isBirthday($birthday)) {
                    $child_name_for_subject = (substr($child->full_name, -1) != 's') ? $child->full_name . "'s" : $child->full_name . "'";

                    Mail::send('emails.birthday-notice', [
                        'child' => $child,
                        'child_name_for_subject' => $child_name_for_subject,
                        'expiration_date' => $today->addDays(7)
                    ], function($message) use($child_name_for_subject){
                        $message->to('joren.vh@hotmail.com', 'Scribblr')
                                ->subject('Celebrate ' . $child_name_for_subject . ' birthday with Scribblr!')
                                ->from("info@scribblr.be");
                        //FIXME email of user
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
