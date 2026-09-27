<?php

namespace App\Jobs;

use App\Mail\VerifyEmailAddress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Lumen\Bus\PendingDispatch;

class SendVerifyEmailJob implements ShouldQueue
{
  use InteractsWithQueue, Queueable, SerializesModels;

  const LOG_CHANNEL = 'feeder';

  public $tries = 3;

  public $retryAfter = 30;

  private $email;

  private $code;

  public function __construct($email, $code)
  {
    $this->email = $email;
    $this->code = $code;
  }

  /**
   * Lumen 7 tidak memuat trait Dispatchable. PendingDispatch mengantre
   * job ini saat objeknya dihancurkan di akhir pernyataan.
   */
  public static function dispatch($email, $code)
  {
    return new PendingDispatch(new static($email, $code));
  }

  public function handle()
  {
    app()->mailer->to($this->email)->send(new VerifyEmailAddress($this->code));
    \Log::channel(self::LOG_CHANNEL)->info("Job SendVerifyEmailJob: email verifikasi terkirim ke {$this->email}");
  }

  public function failed(\Throwable $exception)
  {
    \Log::channel(self::LOG_CHANNEL)->error("Job SendVerifyEmailJob gagal untuk {$this->email}: ".$exception->getMessage());
  }
}
