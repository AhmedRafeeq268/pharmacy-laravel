<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\ClosePosSessionJob;
use App\Models\PosSession;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // كل 5 دقائق، أغلق أي جلسة مفتوحة
        $schedule->call(function () {
            $openSessions = PosSession::where('status', 'open')->get();
            foreach ($openSessions as $session) {
                ClosePosSessionJob::dispatch($session->user_id);
            }
        })->everyFiveMinutes();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
