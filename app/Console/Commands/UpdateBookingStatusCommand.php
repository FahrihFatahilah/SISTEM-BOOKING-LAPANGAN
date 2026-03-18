<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateBookingStatus;

class UpdateBookingStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update booking status based on current time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating booking status...');
        
        UpdateBookingStatus::dispatch();
        
        $this->info('Booking status update job dispatched successfully!');
        
        return 0;
    }
}