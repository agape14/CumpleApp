<?php

namespace App\Http\Controllers;

use App\Models\Familiar;
use App\Models\RelacionFamiliar;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RelacionFamiliarController extends Controller
{
    /**
     * Almacena una nueva relación familiar.
     */
    public function store(Request $request): JsonResponse
    {
        // Validaciones básicas
        $validated = $request->validate([
            'familiar_id' => 'required|exists:familiares,id',
            'tipo_relacion' => 'required|in:padre,madre,hijo,hija,esposo,esposa,pareja,hermano,hermana,abuelo,abuela,nieto,nieta,tio,tia,sobrino,sobrina,primo,prima,otro',
            'descripcion' => 'nullable|string|max:200',
            'crear_nuevo_familiar' => 'boolean',
            'nuevo_nombre' => 'required_if:crear_nuevo_familiar,true|string|max:255',
            'nuevo_fecha_nacimiento' => 'nullable|date',
            'nuevo_telefono' => 'nullable|string|max:20',
            'nuevo_email' => 'nullable|email|max:255',
            'familiar_relacionado_id' => 'required_if:crear_nuevo_familiar,false|exists:familiares,id|different:familiar_id',
        ], [
            'familiar_id.required' => 'El familiar es obligatorio.',
            'familiar_id.exists' => 'El familiar seleccionado no existe.',
            'familiar_relacionado_id.required_if' => 'El familiar relacionado es obligatorio cuando no se crea un nuevo familiar.',
            'familiar_relacionado_id.exists' => 'El familiar relacionado no existe.',
            'familiar_relacionado_id.different' => 'Un familiar no puede tener una relación consigo mismo.',
            'tipo_relacion.required' => 'El tipo de relación es obligatorio.',
            'tipo_relacion.in' => 'El tipo de relación no es válido.',
            'descripcion.max' => 'La descripción no puede tener más de 200 caracteres.',
            'nuevo_nombre.required_if' => 'El nombre del nuevo familiar es obligatorio.',
            'nuevo_nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'nuevo_email.email' => 'El email debe tener un formato válido.',
        ]);

        try {
            DB::beginTransaction();

            $familiarRelacionadoId = null;

            // Si se está creando un nuevo familiar
            if ($request->boolean('crear_nuevo_familiar')) {
                // Crear el nuevo familiar
                $nuevoFamiliar = Familiar::create([
                    'nombre' => $validated['nuevo_nombre'],
                    'fecha_nacimiento' => $validated['nuevo_fecha_nacimiento'] ?? null,
                    'telefono' => $validated['nuevo_telefono'] ?? null,
                    'email' => $validated['nuevo_email'] ?? null,
                    'parentesco_id' => 1, // Default parentesco
                    'notificar' => true,
                    'notas' => 'Familiar creado desde relación familiar'
                ]);
                
                $familiarRelacionadoId = $nuevoFamiliar->id;
            } else {
                $familiarRelacionadoId = $validated['familiar_relacionado_id'];
            }

            // Verificar si ya existe esta relación
            $relacionExistente = RelacionFamiliar::where('familiar_id', $validated['familiar_id'])
                ->where('familiar_relacionado_id', $familiarRelacionadoId)
                ->exists();

            if ($relacionExistente) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Esta relación familiar ya existe. No se puede duplicar.'
                ], 422);
            }

            // Crear la relación
            $relacion = RelacionFamiliar::create([
                'familiar_id' => $validated['familiar_id'],
                'familiar_relacionado_id' => $familiarRelacionadoId,
                'tipo_relacion' => $validated['tipo_relacion'],
                'descripcion' => $validated['descripcion']
            ]);
            
            // Crear automáticamente la relación inversa
            $this->crearRelacionInversa($relacion);

            DB::commit();

            $mensaje = $request->boolean('crear_nuevo_familiar') 
                ? '¡Familiar y relación creados exitosamente!'
                : '¡Relación familiar creada exitosamente!';

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'relacion' => $relacion->load(['familiar', 'familiarRelacionado'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la relación: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Elimina una relación familiar.
     */
    public function destroy(RelacionFamiliar $relacion): JsonResponse
    {
        try {
            // Eliminar también la relación inversa
            $this->eliminarRelacionInversa($relacion);
            
            $relacion->delete();

            return response()->json([
                'success' => true,
                'message' => '¡Relación familiar eliminada exitosamente!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la relación: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Obtiene todas las relaciones de un familiar.
     */
    public function obtenerRelaciones(Familiar $familiar): JsonResponse
    {
        $relaciones = $familiar->relaciones()
            ->with('familiarRelacionado')
            ->get()
            ->map(function ($relacion) {
                return [
                    'id' => $relacion->id,
                    'tipo' => $relacion->tipo_relacion,
                    'descripcion' => $relacion->descripcion,
                    'familiar' => [
                        'id' => $relacion->familiarRelacionado->id,
                        'nombre' => $relacion->familiarRelacionado->nombre,
                        'foto' => null, // Agregar foto si existe
                    ]
                ];
            });

        return response()->json([
            'success' => true,
            'relaciones' => $relaciones
        ]);
    }

    /**
     * Crea automáticamente la relación inversa.
     */
    private function crearRelacionInversa(RelacionFamiliar $relacion): void
    {
        $tiposInversos = [
            'padre' => 'hijo',
            'madre' => 'hija',
            'hijo' => 'padre',
            'hija' => 'madre',
            'esposo' => 'esposa',
            'esposa' => 'esposo',
            'pareja' => 'pareja',
            'hermano' => 'hermana',
            'hermana' => 'hermano',
            'abuelo' => 'nieto',
            'abuela' => 'nieta',
            'nieto' => 'abuelo',
            'nieta' => 'abuela',
            'tio' => 'sobrino',
            'tia' => 'sobrina',
            'sobrino' => 'tio',
            'sobrina' => 'tia',
            'primo' => 'prima',
            'prima' => 'primo',
            'otro' => 'otro',
        ];

        $tipoInverso = $tiposInversos[$relacion->tipo_relacion] ?? 'otro';

        // Verificar que no exista ya la relación inversa
        $existeInversa = RelacionFamiliar::where('familiar_id', $relacion->familiar_relacionado_id)
            ->where('familiar_relacionado_id', $relacion->familiar_id)
            ->exists();

        if (!$existeInversa) {
            RelacionFamiliar::create([
                'familiar_id' => $relacion->familiar_relacionado_id,
                'familiar_relacionado_id' => $relacion->familiar_id,
                'tipo_relacion' => $tipoInverso,
                'descripcion' => $relacion->descripcion,
            ]);
        }
    }

    /**
     * Elimina la relación inversa.
     */
    private function eliminarRelacionInversa(RelacionFamiliar $relacion): void
    {
        RelacionFamiliar::where('familiar_id', $relacion->familiar_relacionado_id)
            ->where('familiar_relacionado_id', $relacion->familiar_id)
            ->delete();
    }
}
