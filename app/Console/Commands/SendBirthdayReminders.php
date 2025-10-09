<?php

namespace App\Console\Commands;

use App\Models\Familiar;
use App\Mail\BirthdayReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendBirthdayReminders extends Command
{
    /**
     * El nombre y la firma del comando de consola.
     *
     * @var string
     */
    protected $signature = 'birthdays:send-reminders';

    /**
     * La descripción del comando de consola.
     *
     * @var string
     */
    protected $description = 'Envía recordatorios por correo de los cumpleaños del día';

    /**
     * Ejecuta el comando de consola.
     */
    public function handle(): int
    {
        $this->info('Buscando cumpleaños de hoy...');

        $today = Carbon::now();
        $familiares = Familiar::whereMonth('fecha_nacimiento', $today->month)
            ->whereDay('fecha_nacimiento', $today->day)
            ->where('notificar', true)
            ->get();

        if ($familiares->isEmpty()) {
            $this->info('No hay cumpleaños hoy.');
            return Command::SUCCESS;
        }

        $this->info("Se encontraron {$familiares->count()} cumpleaños hoy.");

        foreach ($familiares as $familiar) {
            try {
                // Verificar que el familiar tenga un email configurado
                if ($familiar->email) {
                    Mail::to($familiar->email)->send(new BirthdayReminderMail($familiar));
                    $this->info("✓ Recordatorio enviado a: {$familiar->nombre}");
                }

                // También puedes configurar para enviar al usuario de la aplicación
                // Por ejemplo, si tienes un email configurado en el .env
                $adminEmail = config('mail.from.address');
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new BirthdayReminderMail($familiar));
                    $this->info("✓ Recordatorio enviado al administrador para: {$familiar->nombre}");
                }
            } catch (\Exception $e) {
                $this->error("✗ Error al enviar recordatorio para {$familiar->nombre}: {$e->getMessage()}");
            }
        }

        $this->info('¡Proceso completado!');

        return Command::SUCCESS;
    }
}

