<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StoreEventMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private string $mailSubject,
        private string $viewName,
        private array $mailData = []
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject($this->mailSubject)
            ->view($this->viewName, $this->mailData);
    }
}
