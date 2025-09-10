<?php

namespace App\Console\Commands;

use App\Jobs\DeleteExpiredBanners;
use Illuminate\Console\Command;

class DeleteBanners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-banners';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired banners';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DeleteExpiredBanners::dispatch();
    }
}
