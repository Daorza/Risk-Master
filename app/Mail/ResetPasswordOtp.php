<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordOtp extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode OTP Reset Password - RiskMaster',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '<div style="font-family:sans-serif;padding:20px;"><h2>Permintaan Reset Password</h2><p>Berikut adalah 6 digit kode OTP rahasia kamu:</p><h1 style="letter-spacing:5px;color:#1e40af;">' . $this->otp . '</h1><p>Masukkan kode ini ke dalam aplikasi RiskMaster untuk mengatur ulang passwordmu.</p><p><i>Abaikan email ini jika kamu tidak memintanya.</i></p></div>',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
