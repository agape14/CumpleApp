<?php

namespace App\Console\Commands;

use App\Models\Familiar;
use Illuminate\Console\Command;

class BuscarFamiliar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'familiar:buscar {nombre? : Texto a buscar en el nombre del familiar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Busca familiares por nombre y muestra su ID y DNI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nombre = $this->argument('nombre');

        // Si no se proporciona nombre, pedirlo
        if (!$nombre) {
            $nombre = $this->ask('Ingresa el texto a buscar en el nombre');
        }

        // Buscar familiares
        $familiares = Familiar::where('nombre', 'LIKE', "%{$nombre}%")
            ->orderBy('nombre')
            ->get();

        if ($familiares->isEmpty()) {
            $this->error("❌ No se encontraron familiares con el nombre '{$nombre}'");
            return 1;
        }

        $this->info("✅ Se encontraron {$familiares->count()} familiar(es):\n");

        // Crear tabla con los resultados
        $headers = ['ID', 'Nombre', 'DNI', 'Parentesco', 'Puede Acceder'];
        $rows = [];

        foreach ($familiares as $familiar) {
            $rows[] = [
                $familiar->id,
                $familiar->nombre,
                $familiar->dni ?? '(Sin DNI)',
                $familiar->parentesco->nombre_parentesco ?? '-',
                $familiar->puede_acceder ? '✅ Sí' : '❌ No',
            ];
        }

        $this->table($headers, $rows);

        // Mostrar información adicional
        $this->newLine();
        $this->info("💡 Tips:");
        $this->line("• Para actualizar DNI: php artisan familiar:dni {id} {dni}");
        $this->line("• Para dar acceso: Editar en la aplicación web");

        return 0;
    }
}
