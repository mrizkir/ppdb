<?php

namespace App\Mail;

use App\Models\System\ConfigurationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;

    public $resetUrl;

    /**
     * @param string $name
     * @param string $resetUrl
     */
    public function __construct($name, $resetUrl)
    {
        $this->name = $name;
        $this->resetUrl = $resetUrl;
    }

    /**
     * @return $this
     */
    public function build()
    {
        $config = ConfigurationModel::find(101);
        $sekolah = $config ? $config->config_value : env('APP_NAME', 'PPDB');

        return $this->subject('Reset Password')
            ->view('emails.ResetPassword')
            ->with([
                'NAMA_SEKOLAH' => $sekolah,
                'name' => $this->name,
                'resetUrl' => $this->resetUrl,
            ]);
    }
}
