<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define la programación de comandos de la aplicación.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Enviar recordatorios de cumpleaños todos los días a las 8:00 AM
        $schedule->command('birthdays:send-reminders')
            ->dailyAt('08:00')
            ->timezone('America/Mexico_City'); // Ajusta según tu zona horaria
    }

    /**
     * Registra los comandos de la aplicación.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

