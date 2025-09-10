<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompanyVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Company $company) {}

    public function build()
    {
        return $this->subject('Akun Perusahaan Anda Telah Diverifikasi')
            ->markdown('emails.company.verified', [
                'company' => $this->company,
            ]);
    }
}
