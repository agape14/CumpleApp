<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Comando para enviar notificaciones push de cumpleaños
 * 
 * @package App\Console\Commands
 */
class SendBirthdayNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthdays:send-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía notificaciones push para cumpleaños que son mañana';

    /**
     * Servicio de Firebase
     *
     * @var FirebaseNotificationService
     */
    protected $firebaseService;

    /**
     * Constructor del comando
     *
     * @param FirebaseNotificationService $firebaseService
     */
    public function __construct(FirebaseNotificationService $firebaseService)
    {
        parent::__construct();
        $this->firebaseService = $firebaseService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Buscando cumpleaños que son mañana...');

        try {
            $tomorrow = Carbon::tomorrow();
            $month = $tomorrow->month;
            $day = $tomorrow->day;

            // Buscar familiares que cumplen años mañana
            $birthdays = DB::table('familiares')
                ->whereRaw('MONTH(fecha_nacimiento) = ?', [$month])
                ->whereRaw('DAY(fecha_nacimiento) = ?', [$day])
                ->where('notificar', true)
                ->get();

            if ($birthdays->isEmpty()) {
                $this->info('ℹ️  No hay cumpleaños mañana.');
                return 0;
            }

            $this->info("✅ Encontrados {$birthdays->count()} cumpleaños para mañana.");

            $totalSuccess = 0;
            $totalFailed = 0;

            foreach ($birthdays as $birthday) {
                // Calcular edad
                $birthDate = Carbon::parse($birthday->fecha_nacimiento);
                $age = $tomorrow->year - $birthDate->year;
                
                // Ajustar si el cumpleaños aún no ha ocurrido este año
                if ($birthDate->copy()->setYear($tomorrow->year)->gt($tomorrow)) {
                    $age--;
                }

                $this->info("📤 Enviando notificación para: {$birthday->nombre} (cumple {$age} años)");

                // Enviar a todos los tokens registrados
                $result = $this->firebaseService->sendToAllTokens(
                    $birthday->nombre,
                    (string)$age
                );

                $totalSuccess += $result['success'];
                $totalFailed += $result['failed'];

                $this->line("   ✅ Enviadas: {$result['success']} | ❌ Fallidas: {$result['failed']}");
            }

            $this->newLine();
            $this->info("📊 Resumen:");
            $this->line("   Total enviadas: {$totalSuccess}");
            $this->line("   Total fallidas: {$totalFailed}");
            $this->info('✨ Proceso completado.');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error al enviar notificaciones: ' . $e->getMessage());
            return 1;
        }
    }
}
