<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\MonitorDebugRequests;

class MonitorRequestsCommand extends Command
{
    protected $signature = 'monitor:requests';
    protected $description = 'Monitor debugbar requests every 5 seconds';

    public function handle()
    {
        MonitorDebugRequests::dispatch();
        $this->info('Debug requests monitoring job dispatched');
    }
}