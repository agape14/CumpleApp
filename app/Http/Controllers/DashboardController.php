<?php

namespace App\Http\Controllers;

use App\Models\Familiar;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard con estadísticas de cumpleaños.
     */
    public function index(): View
    {
        // Obtener todos los familiares
        $familiares = Familiar::with('parentesco')->get();

        // Calcular el próximo cumpleaños
        $proximoCumpleanos = $familiares->sortBy('next_birthday')->first();

        // Obtener cumpleaños del mes actual
        $mesActual = Carbon::now()->month;
        $cumpleanosMesActual = $familiares->filter(function ($familiar) use ($mesActual) {
            return $familiar->fecha_nacimiento && $familiar->fecha_nacimiento->month == $mesActual;
        })->sortBy(function ($familiar) {
            return $familiar->fecha_nacimiento->day;
        });

        // Calcular cumpleaños por mes (para el gráfico)
        $cumpleanosPorMes = [];
        for ($i = 1; $i <= 12; $i++) {
            $cumpleanosPorMes[$i] = $familiares->filter(function ($familiar) use ($i) {
                return $familiar->fecha_nacimiento && $familiar->fecha_nacimiento->month == $i;
            })->count();
        }

        // Estadísticas adicionales
        $totalFamiliares = $familiares->count();
        $cumpleanosHoy = $familiares->filter(function ($familiar) {
            return $familiar->fecha_nacimiento 
                && $familiar->fecha_nacimiento->month == Carbon::now()->month
                && $familiar->fecha_nacimiento->day == Carbon::now()->day;
        });

        // Próximos 5 cumpleaños
        $proximosCumpleanos = $familiares->sortBy('next_birthday')->take(5);

        return view('dashboard', compact(
            'proximoCumpleanos',
            'cumpleanosMesActual',
            'cumpleanosPorMes',
            'totalFamiliares',
            'cumpleanosHoy',
            'proximosCumpleanos'
        ));
    }
}

