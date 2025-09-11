<?php

namespace App\Console\Commands;

use App\Jobs\AskForCommission;
use Illuminate\Console\Command;

class SendCommissionNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-commission-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending commission notification...');
        AskForCommission::dispatch();
        $this->info('Commission notification sent successfully');
    }
}
