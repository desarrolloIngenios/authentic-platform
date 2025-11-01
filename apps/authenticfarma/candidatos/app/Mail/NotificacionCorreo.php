<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionCorreo extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('¡Bienvenido a AuthenticFarma!')
                    ->view('emails.notificacion');
    }
}
