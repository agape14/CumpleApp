<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Familiar;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    /**
     * Obtiene las estadísticas del dashboard.
     * 
     * GET /api/v1/dashboard
     */
    public function index(): JsonResponse
    {
        try {
            $familiares = Familiar::with('parentesco')->get();

            // Calcular el próximo cumpleaños
            $proximoCumpleanos = $familiares->sortBy('next_birthday')->first();

            // Obtener cumpleaños del mes actual
            $mesActual = Carbon::now()->month;
            $cumpleanosMesActual = $familiares->filter(function ($familiar) use ($mesActual) {
                return $familiar->fecha_nacimiento && $familiar->fecha_nacimiento->month == $mesActual;
            })->sortBy(function ($familiar) {
                return $familiar->fecha_nacimiento->day;
            })->values();

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
            })->values();

            // Próximos 5 cumpleaños
            $proximosCumpleanos = $familiares->sortBy('next_birthday')
                ->take(5)
                ->map(function ($familiar) {
                    return [
                        'id' => $familiar->id,
                        'nombre' => $familiar->nombre,
                        'fecha_nacimiento' => $familiar->fecha_nacimiento?->format('Y-m-d'),
                        'edad' => $familiar->age,
                        'proximo_cumpleanos' => $familiar->next_birthday?->format('Y-m-d'),
                        'dias_restantes' => $familiar->days_until_birthday,
                        'parentesco' => $familiar->parentesco->nombre_parentesco ?? null,
                        'signo_zodiacal' => $familiar->zodiac_sign,
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard obtenido exitosamente',
                'data' => [
                    'total_familiares' => $totalFamiliares,
                    'cumpleanos_hoy' => $cumpleanosHoy->map(function ($familiar) {
                        return [
                            'id' => $familiar->id,
                            'nombre' => $familiar->nombre,
                            'edad' => $familiar->age,
                            'parentesco' => $familiar->parentesco->nombre_parentesco ?? null,
                        ];
                    }),
                    'proximo_cumpleanos' => $proximoCumpleanos ? [
                        'id' => $proximoCumpleanos->id,
                        'nombre' => $proximoCumpleanos->nombre,
                        'dias_restantes' => $proximoCumpleanos->days_until_birthday,
                        'fecha' => $proximoCumpleanos->next_birthday?->format('Y-m-d'),
                        'edad' => $proximoCumpleanos->age,
                    ] : null,
                    'proximos_5_cumpleanos' => $proximosCumpleanos,
                    'cumpleanos_mes_actual' => $cumpleanosMesActual->map(function ($familiar) {
                        return [
                            'id' => $familiar->id,
                            'nombre' => $familiar->nombre,
                            'dia' => $familiar->fecha_nacimiento->day,
                            'edad' => $familiar->age,
                            'parentesco' => $familiar->parentesco->nombre_parentesco ?? null,
                        ];
                    }),
                    'cumpleanos_por_mes' => $cumpleanosPorMes,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene el dashboard público (sin autenticación).
     * 
     * GET /api/v1/dashboard/public
     */
    public function publicDashboard(): JsonResponse
    {
        try {
            // Obtener todos los familiares con fecha de nacimiento
            $familiares = Familiar::whereNotNull('fecha_nacimiento')
                ->with('parentesco')
                ->get();

            // Calcular el próximo cumpleaños
            $proximoCumpleanos = $familiares->sortBy('next_birthday')->first();

            // Obtener cumpleaños del mes actual
            $mesActual = Carbon::now()->month;
            $cumpleanosMesActual = $familiares->filter(function ($familiar) use ($mesActual) {
                return $familiar->fecha_nacimiento && $familiar->fecha_nacimiento->month == $mesActual;
            })->sortBy(function ($familiar) {
                return $familiar->fecha_nacimiento->day;
            })->values();

            // Estadísticas adicionales
            $totalFamiliares = $familiares->count();
            
            // Cumpleaños de hoy
            $cumpleanosHoy = $familiares->filter(function ($familiar) {
                return $familiar->fecha_nacimiento 
                    && $familiar->fecha_nacimiento->month == Carbon::now()->month
                    && $familiar->fecha_nacimiento->day == Carbon::now()->day;
            })->map(function ($familiar) {
                return [
                    'id' => $familiar->id,
                    'nombre' => $familiar->nombre,
                    'edad' => $familiar->age,
                    'parentesco' => $familiar->parentesco->nombre_parentesco ?? 'Sin parentesco',
                    'fecha_nacimiento' => $familiar->fecha_nacimiento->format('Y-m-d'),
                    'proximo_cumpleanos' => $familiar->next_birthday?->format('Y-m-d'),
                    'dias_restantes' => $familiar->days_until_birthday ?? 0,
                ];
            })->values();

            // Próximo cumpleaños (el primero que no sea hoy)
            $proximoCumpleanosData = null;
            if ($proximoCumpleanos && $proximoCumpleanos->days_until_birthday > 0) {
                $proximoCumpleanosData = [
                    'id' => $proximoCumpleanos->id,
                    'nombre' => $proximoCumpleanos->nombre,
                    'dias_restantes' => $proximoCumpleanos->days_until_birthday,
                    'fecha' => $proximoCumpleanos->next_birthday?->format('Y-m-d'),
                    'proximo_cumpleanos' => $proximoCumpleanos->next_birthday?->format('Y-m-d'),
                    'edad' => $proximoCumpleanos->age,
                    'parentesco' => $proximoCumpleanos->parentesco->nombre_parentesco ?? 'Sin parentesco',
                ];
            } elseif ($proximoCumpleanos) {
                // Si el próximo es hoy, buscar el siguiente
                $siguienteCumpleanos = $familiares->sortBy('next_birthday')
                    ->filter(function ($familiar) {
                        return $familiar->days_until_birthday > 0;
                    })
                    ->first();
                
                if ($siguienteCumpleanos) {
                    $proximoCumpleanosData = [
                        'id' => $siguienteCumpleanos->id,
                        'nombre' => $siguienteCumpleanos->nombre,
                        'dias_restantes' => $siguienteCumpleanos->days_until_birthday,
                        'fecha' => $siguienteCumpleanos->next_birthday?->format('Y-m-d'),
                        'proximo_cumpleanos' => $siguienteCumpleanos->next_birthday?->format('Y-m-d'),
                        'edad' => $siguienteCumpleanos->age,
                        'parentesco' => $siguienteCumpleanos->parentesco->nombre_parentesco ?? 'Sin parentesco',
                    ];
                }
            }

            // Próximos 5 cumpleaños
            $proximos5Cumpleanos = $familiares->sortBy('next_birthday')
                ->take(5)
                ->map(function ($familiar) {
                    return [
                        'id' => $familiar->id,
                        'nombre' => $familiar->nombre,
                        'edad' => $familiar->age,
                        'parentesco' => $familiar->parentesco->nombre_parentesco ?? 'Sin parentesco',
                        'fecha_nacimiento' => $familiar->fecha_nacimiento->format('Y-m-d'),
                        'proximo_cumpleanos' => $familiar->next_birthday?->format('Y-m-d'),
                        'dias_restantes' => $familiar->days_until_birthday ?? 0,
                    ];
                })
                ->values();

            // Cumpleaños del mes actual
            $cumpleanosMesActualData = $cumpleanosMesActual->map(function ($familiar) {
                return [
                    'id' => $familiar->id,
                    'nombre' => $familiar->nombre,
                    'dia' => $familiar->fecha_nacimiento->day,
                    'edad' => $familiar->age,
                    'parentesco' => $familiar->parentesco->nombre_parentesco ?? 'Sin parentesco',
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard público cargado',
                'data' => [
                    'total_familiares' => $totalFamiliares,
                    'cumpleanos_hoy' => $cumpleanosHoy,
                    'proximo_cumpleanos' => $proximoCumpleanosData,
                    'proximos_5_cumpleanos' => $proximos5Cumpleanos,
                    'cumpleanos_mes_actual' => $cumpleanosMesActualData,
                    'cumpleanos_por_mes' => [],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener dashboard público: ' . $e->getMessage(),
                'data' => [
                    'total_familiares' => 0,
                    'cumpleanos_hoy' => [],
                    'proximo_cumpleanos' => null,
                    'proximos_5_cumpleanos' => [],
                    'cumpleanos_mes_actual' => [],
                    'cumpleanos_por_mes' => [],
                ],
            ], 500);
        }
    }
}

