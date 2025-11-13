<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompanyRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Company $company) {}

    public function build()
    {
        return $this->subject('Pendaftaran Perusahaan Diterima')
            ->markdown('emails.company.registered', [
                'company' => $this->company,
            ]);
    }
}
