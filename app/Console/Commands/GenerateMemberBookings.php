<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MemberSchedule;
use Carbon\Carbon;

class GenerateMemberBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'member:generate-bookings {--days=30 : Number of days to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate member bookings for the next specified days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $this->info("Generating member bookings (max 4 sessions per member per month)...");
        
        $activeSchedules = MemberSchedule::active()->get();
        $totalGenerated = 0;
        
        foreach ($activeSchedules as $schedule) {
            $remaining = $schedule->getRemainingQuota();
            
            if ($remaining <= 0) {
                $this->line("⚠️  {$schedule->member_name} ({$schedule->day_name}) - Kuota bulan ini habis (4/4)");
                continue;
            }
            
            $bookings = $schedule->generateBookingsFor30Days(Carbon::now());
            $count = count($bookings);
            $totalGenerated += $count;
            
            if ($count > 0) {
                $newRemaining = $remaining - $count;
                $this->line("✅ Generated {$count} bookings for {$schedule->member_name} ({$schedule->day_name}) - Sisa kuota: {$newRemaining}/4");
            } else {
                $this->line("ℹ️  No new bookings for {$schedule->member_name} ({$schedule->day_name}) - Sisa kuota: {$remaining}/4");
            }
        }
        
        $this->info("Total bookings generated: {$totalGenerated}");
        
        return Command::SUCCESS;
    }
}
