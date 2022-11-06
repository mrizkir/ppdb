<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Storage;
use Exception;


class PendaftaranJob implements ShouldQueue
{
  use InteractsWithQueue, Queueable, SerializesModels;

  const LOG_CHANNEL = 'pendaftaran';

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct()
  {
    
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    \Log::channel(self::LOG_CHANNEL)->info("Jobs: Pendaftaran Ujian PMB");
  }
}