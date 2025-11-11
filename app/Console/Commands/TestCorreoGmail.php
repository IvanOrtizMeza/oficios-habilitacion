<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
class TestCorreoGmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-correo-gmail {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar un correo de prueba para verificar la configuración Gmail SMTP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $destino = $this->argument('email') ?? env('MAIL_USERNAME');

        try {
            Mail::raw("Este es un correo de prueba enviado desde Laravel usando Gmail SMTP.", function ($message) use ($destino) {
                $message->to($destino)
                        ->subject("Prueba de Gmail SMTP");
            });

            $this->info("✅ Correo de prueba enviado correctamente a: {$destino}");
        } catch (\Exception $e) {
            $this->error("❌ Error al enviar el correo: " . $e->getMessage());
        }

        return 0;
    }
}
