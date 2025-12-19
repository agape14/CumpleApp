<?php

/**
 * EJEMPLO DE CONTROLADOR API PARA CUMpleApp
 * 
 * Este es un ejemplo de cómo convertir tus controladores web en controladores API
 * que retornen JSON en lugar de vistas Blade.
 * 
 * Ubicación: app/Http/Controllers/Api/FamiliarApiController.php
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Familiar;
use App\Models\Parentesco;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FamiliarApiController extends Controller
{
    /**
     * Obtiene todos los familiares.
     * 
     * GET /api/v1/familiares
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Familiar::with('parentesco');
            
            // Filtros opcionales
            if ($request->has('search')) {
                $query->where('nombre', 'LIKE', '%' . $request->search . '%');
            }
            
            if ($request->has('parentesco_id')) {
                $query->where('parentesco_id', $request->parentesco_id);
            }
            
            $familiares = $query->orderBy('nombre')->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Familiares obtenidos exitosamente',
                'data' => $familiares,
                'count' => $familiares->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener familiares',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene un familiar específico.
     * 
     * GET /api/v1/familiares/{id}
     */
    public function show(int $id): JsonResponse
    {
        try {
            $familiar = Familiar::with([
                'parentesco',
                'ideasRegalos',
                'relaciones.familiarRelacionado',
                'regalosDados',
                'recordatorios',
                'cuotasMensuales'
            ])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Familiar obtenido exitosamente',
                'data' => $familiar
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Familiar no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Crea un nuevo familiar.
     * 
     * POST /api/v1/familiares
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:150',
                'fecha_nacimiento' => 'required|date|before:today',
                'telefono' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'dni' => 'nullable|string|max:20|unique:familiares,dni',
                'notificar' => 'boolean',
                'puede_acceder' => 'boolean',
                'notas' => 'nullable|string',
                'parentesco_id' => 'required|exists:parentescos,id',
            ], [
                'nombre.required' => 'El nombre es obligatorio.',
                'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
                'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
                'parentesco_id.required' => 'El parentesco es obligatorio.',
                'parentesco_id.exists' => 'El parentesco seleccionado no existe.',
                'dni.unique' => 'Este DNI ya está registrado.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            $validated['notificar'] = $request->has('notificar') ? true : false;
            $validated['puede_acceder'] = $request->has('puede_acceder') ? true : false;
            
            // Asignar usuario autenticado si existe
            if ($request->user()) {
                $validated['created_by'] = $request->user()->id;
                $validated['updated_by'] = $request->user()->id;
            }

            $familiar = Familiar::create($validated);
            $familiar->load('parentesco');

            return response()->json([
                'success' => true,
                'message' => 'Familiar creado exitosamente',
                'data' => $familiar
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear familiar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualiza un familiar existente.
     * 
     * PUT /api/v1/familiares/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $familiar = Familiar::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|required|string|max:150',
                'fecha_nacimiento' => 'sometimes|required|date|before:today',
                'telefono' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'dni' => 'nullable|string|max:20|unique:familiares,dni,' . $id,
                'notificar' => 'boolean',
                'puede_acceder' => 'boolean',
                'notas' => 'nullable|string',
                'parentesco_id' => 'sometimes|required|exists:parentescos,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            $validated['notificar'] = $request->has('notificar') ? true : false;
            $validated['puede_acceder'] = $request->has('puede_acceder') ? true : false;
            
            // Asignar usuario autenticado si existe
            if ($request->user()) {
                $validated['updated_by'] = $request->user()->id;
            }

            $familiar->update($validated);
            $familiar->load('parentesco');

            return response()->json([
                'success' => true,
                'message' => 'Familiar actualizado exitosamente',
                'data' => $familiar
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar familiar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un familiar.
     * 
     * DELETE /api/v1/familiares/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $familiar = Familiar::findOrFail($id);
            $familiar->delete();

            return response()->json([
                'success' => true,
                'message' => 'Familiar eliminado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar familiar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene los próximos cumpleaños.
     * 
     * GET /api/v1/familiares/proximos-cumpleanos
     */
    public function proximosCumpleanos(Request $request): JsonResponse
    {
        try {
            $dias = $request->get('dias', 30); // Por defecto 30 días
            
            $familiares = Familiar::whereNotNull('fecha_nacimiento')
                ->get()
                ->map(function ($familiar) {
                    return [
                        'id' => $familiar->id,
                        'nombre' => $familiar->nombre,
                        'fecha_nacimiento' => $familiar->fecha_nacimiento,
                        'edad' => $familiar->age,
                        'proximo_cumpleanos' => $familiar->next_birthday,
                        'dias_restantes' => $familiar->days_until_birthday,
                        'parentesco' => $familiar->parentesco->nombre_parentesco ?? null,
                    ];
                })
                ->filter(function ($familiar) use ($dias) {
                    return $familiar['dias_restantes'] <= $dias;
                })
                ->sortBy('dias_restantes')
                ->values();

            return response()->json([
                'success' => true,
                'message' => 'Próximos cumpleaños obtenidos exitosamente',
                'data' => $familiares,
                'count' => $familiares->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener próximos cumpleaños',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene estadísticas del dashboard.
     * 
     * GET /api/v1/familiares/dashboard
     */
    public function dashboard(): JsonResponse
    {
        try {
            $totalFamiliares = Familiar::count();
            $cumpleanosHoy = Familiar::whereNotNull('fecha_nacimiento')
                ->get()
                ->filter(function ($familiar) {
                    $hoy = now();
                    return $familiar->fecha_nacimiento->month == $hoy->month 
                        && $familiar->fecha_nacimiento->day == $hoy->day;
                })
                ->count();
            
            $proximoCumpleanos = Familiar::whereNotNull('fecha_nacimiento')
                ->get()
                ->sortBy('days_until_birthday')
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas obtenidas exitosamente',
                'data' => [
                    'total_familiares' => $totalFamiliares,
                    'cumpleanos_hoy' => $cumpleanosHoy,
                    'proximo_cumpleanos' => $proximoCumpleanos ? [
                        'id' => $proximoCumpleanos->id,
                        'nombre' => $proximoCumpleanos->nombre,
                        'dias_restantes' => $proximoCumpleanos->days_until_birthday,
                        'fecha' => $proximoCumpleanos->next_birthday,
                    ] : null,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

