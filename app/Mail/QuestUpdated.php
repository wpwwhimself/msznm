<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuestUpdated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $quest;
    public $pl;
    public function __construct($quest)
    {
        $this->quest = $quest;
        $this->pl = client_polonize($quest->client->client_name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("[MSZNM] Zlecenie nr ".$this->quest->id." zostało zaktualizowane")
            ->view('emails.quest-updated');
    }
}
