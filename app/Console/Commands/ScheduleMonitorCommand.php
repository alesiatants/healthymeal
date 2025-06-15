<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScheduleMonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:monitor';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor debugbar requests';

    /**
     * 
     * @var \Illuminate\Console\Scheduling\Schedule
     */
    public function schedule(Schedule $schedule)
    {
        $schedule->job(new MonitorDebugRequests) // Schedule the job to run every two seconds
                 ->everyTwoSeconds() 
                 ->name('monitor_debug_requests') // Name the job for easier identification
                 ->withoutOverlapping() // Prevent overlapping executions
                 ->onOneServer(); // Ensure the job runs on one server
    }

    public function handle()
    {
        $this->info('Scheduler is running. Use Ctrl+C to stop.');
        
        while (true) {
            $this->call('schedule:run'); // Call the schedule:run command to execute scheduled tasks
            $this->info('Scheduler executed at ' . now());
            sleep(2);
        }
    }
}
