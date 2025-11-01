<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionCorreo; // Asegúrate de tener esta clase creada
use App\Models\User;

class EnviarCorreo extends Command
{
    protected $signature = 'correo:enviar {email}';
    protected $description = 'Envía un correo de prueba al email indicado';

    public function handle()
    {
        $email = $this->argument('email');

        Mail::to($email)->send(new NotificacionCorreo());

        $this->info("Correo enviado a $email");
    }
}
