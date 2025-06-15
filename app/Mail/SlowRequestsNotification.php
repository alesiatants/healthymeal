<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SlowRequestsNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The array of slow requests.
     *
     * @var array
     */
    public $slowRequests;

    /**
     * Create a new message instance.
     *
     * @param array $slowRequests
     * @return void
     */
    public function __construct(array $slowRequests)
    {
        $this->slowRequests = $slowRequests;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Обнаружены медленные запросы')
            ->markdown('emails.slow-requests');
    }
}