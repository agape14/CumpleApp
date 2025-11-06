<?php

namespace App\Console\Commands;

use App\Models\Familiar;
use Illuminate\Console\Command;

class ActualizarDNI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'familiar:dni 
                            {id : ID del familiar}
                            {dni? : DNI a asignar}
                            {--acceso : Habilitar acceso al sistema}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el DNI de un familiar y opcionalmente habilita su acceso al sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $dni = $this->argument('dni');
        $habilitarAcceso = $this->option('acceso');

        // Buscar el familiar
        $familiar = Familiar::find($id);

        if (!$familiar) {
            $this->error("❌ No se encontró un familiar con ID: {$id}");
            $this->info("💡 Usa: php artisan familiar:buscar [nombre] para buscar familiares");
            return 1;
        }

        // Mostrar información actual
        $this->info("📋 Familiar Encontrado:");
        $this->line("   ID: {$familiar->id}");
        $this->line("   Nombre: {$familiar->nombre}");
        $this->line("   DNI Actual: " . ($familiar->dni ?? '(Sin DNI)'));
        $this->line("   Puede Acceder: " . ($familiar->puede_acceder ? '✅ Sí' : '❌ No'));
        $this->newLine();

        // Si no se proporciona DNI, pedirlo
        if (!$dni) {
            $dni = $this->ask('Ingresa el DNI a asignar');
        }

        // Validar que el DNI no esté en uso
        if ($dni) {
            $dniExistente = Familiar::where('dni', $dni)
                ->where('id', '!=', $id)
                ->first();

            if ($dniExistente) {
                $this->error("❌ El DNI '{$dni}' ya está asignado a: {$dniExistente->nombre}");
                return 1;
            }
        }

        // Confirmar la acción
        if (!$this->confirm("¿Deseas actualizar el DNI de '{$familiar->nombre}' a '{$dni}'?")) {
            $this->warn('⚠️  Operación cancelada');
            return 0;
        }

        // Actualizar DNI
        $familiar->dni = $dni;

        // Habilitar acceso si se especificó la opción
        if ($habilitarAcceso) {
            $familiar->puede_acceder = true;
        }

        $familiar->save();

        // Mostrar resultado
        $this->newLine();
        $this->info("✅ DNI actualizado exitosamente!");
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->line("👤 Familiar: {$familiar->nombre}");
        $this->line("🆔 DNI: {$familiar->dni}");
        $this->line("🔐 Puede Acceder: " . ($familiar->puede_acceder ? '✅ Sí' : '❌ No'));
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        if ($familiar->puede_acceder) {
            $this->newLine();
            $this->info("🎉 Credenciales de Acceso:");
            $this->line("   Usuario: {$familiar->dni}");
            $this->line("   Contraseña: {$familiar->dni} (por defecto)");
            $this->line("   URL: http://localhost:8000/login");
        } else {
            $this->newLine();
            $this->warn("⚠️  Para habilitar el acceso, ejecuta:");
            $this->line("   php artisan familiar:dni {$id} {$dni} --acceso");
        }

        return 0;
    }
}
