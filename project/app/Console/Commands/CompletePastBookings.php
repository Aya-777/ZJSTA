<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CompletePastBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:complete-past-bookings';

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
      $affectedRows = Booking::where('status', 'confirmed')
          ->where('end_date', '<', now())
          ->update(['status' => 'completed']);

      $this->info("Successfully completed {$affectedRows} bookings.");
    }
}
