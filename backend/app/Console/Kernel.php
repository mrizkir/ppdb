<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

//job feeder
use App\Jobs\PendaftaranJob;

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
    //job feeder
    $schedule->job(new PendaftaranJob, 'pendaftaran')
      ->everyMinute()
      // ->hourly()
      ->sendOutputTo(storage_path() . '/logs/laravel.log')      
      ->withoutOverlapping();
  }
  /**
   * Get the timezone that should be used by default for scheduled events.
   *
   * @return \DateTimeZone|string|null
   */
  protected function scheduleTimezone()
  {
    return 'Asia/Jakarta';
  }
}
