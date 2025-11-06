<?php

namespace App\Console\Commands;

use App\Models\Familiar;
use Illuminate\Console\Command;

class ListarFamiliares extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'familiar:listar 
                            {--solo-hermanos : Mostrar solo hermanos}
                            {--con-acceso : Mostrar solo los que tienen acceso}
                            {--sin-dni : Mostrar solo los que NO tienen DNI}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lista todos los familiares con sus datos principales';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Familiar::with('parentesco');

        // Aplicar filtros según las opciones
        if ($this->option('solo-hermanos')) {
            $parentescosHermanos = \App\Models\Parentesco::where(function($q) {
                $q->where('nombre_parentesco', 'LIKE', '%Hermano%')
                  ->orWhere('nombre_parentesco', 'LIKE', '%Hermana%');
            })->pluck('id');
            
            $query->whereIn('parentesco_id', $parentescosHermanos);
        }

        if ($this->option('con-acceso')) {
            $query->where('puede_acceder', true);
        }

        if ($this->option('sin-dni')) {
            $query->whereNull('dni');
        }

        $familiares = $query->orderBy('nombre')->get();

        if ($familiares->isEmpty()) {
            $this->warn('⚠️  No se encontraron familiares con los filtros especificados');
            return 0;
        }

        $this->info("📋 Total de familiares: {$familiares->count()}\n");

        // Crear tabla
        $headers = ['ID', 'Nombre', 'DNI', 'Parentesco', 'Acceso', 'Teléfono'];
        $rows = [];

        foreach ($familiares as $familiar) {
            $rows[] = [
                $familiar->id,
                $familiar->nombre,
                $familiar->dni ?? '❌ Sin DNI',
                $familiar->parentesco->nombre_parentesco ?? '-',
                $familiar->puede_acceder ? '✅ Sí' : '❌ No',
                $familiar->telefono ?? '-',
            ];
        }

        $this->table($headers, $rows);

        // Estadísticas
        $this->newLine();
        $this->info("📊 Estadísticas:");
        $this->line("   Con DNI: " . $familiares->whereNotNull('dni')->count());
        $this->line("   Sin DNI: " . $familiares->whereNull('dni')->count());
        $this->line("   Con Acceso: " . $familiares->where('puede_acceder', true)->count());

        // Comandos útiles
        $this->newLine();
        $this->info("💡 Comandos útiles:");
        $this->line("   php artisan familiar:buscar [nombre]     - Buscar por nombre");
        $this->line("   php artisan familiar:dni {id} {dni}      - Asignar DNI");
        $this->line("   php artisan familiar:dni {id} {dni} --acceso - Asignar DNI y dar acceso");

        return 0;
    }
}
