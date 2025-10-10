<?php

namespace App\Http\Controllers;

use App\Models\Familiar;
use App\Models\RelacionFamiliar;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ArbolGenealogicoController extends Controller
{
    /**
     * Muestra la vista del árbol genealógico.
     */
    public function index(): View
    {
        $familiares = Familiar::with(['relaciones', 'parentesco'])->get();
        
        return view('arbol-genealogico.index', compact('familiares'));
    }

    /**
     * Genera los datos del árbol genealógico para un familiar específico.
     */
    public function generarArbol(Familiar $familiar = null): JsonResponse
    {
        if (!$familiar) {
            // Si no se especifica un familiar, buscar el más antiguo (raíz del árbol)
            $familiar = $this->encontrarRaiz();
        }

        if (!$familiar) {
            return response()->json([
                'success' => true,
                'arbol' => null,
                'message' => 'No hay familiares registrados'
            ]);
        }

        $arbol = $this->construirArbol($familiar);

        return response()->json([
            'success' => true,
            'arbol' => $arbol
        ]);
    }

    /**
     * Genera los datos del árbol genealógico completo.
     */
    public function generarArbolCompleto(): JsonResponse
    {
        $familiares = Familiar::with(['relaciones.familiarRelacionado', 'parentesco'])->get();
        
        $nodos = [];
        $enlaces = [];

        foreach ($familiares as $familiar) {
            $nodos[] = [
                'id' => $familiar->id,
                'nombre' => $familiar->nombre,
                'fecha_nacimiento' => $familiar->fecha_nacimiento?->format('d/m/Y'),
                'edad' => $familiar->age,
                'parentesco' => $familiar->parentesco?->nombre_parentesco,
            ];

            foreach ($familiar->relaciones as $relacion) {
                $enlaces[] = [
                    'source' => $familiar->id,
                    'target' => $relacion->familiar_relacionado_id,
                    'tipo' => $relacion->tipo_relacion,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'nodos' => $nodos,
            'enlaces' => $enlaces
        ]);
    }

    /**
     * Encuentra el familiar raíz (el más antiguo) del árbol.
     */
    private function encontrarRaiz(): ?Familiar
    {
        // Buscar el familiar más antiguo que no tenga padres
        $familiar = Familiar::whereDoesntHave('relacionesInversas', function ($query) {
            $query->whereIn('tipo_relacion', ['hijo', 'hija']);
        })
        ->orderBy('fecha_nacimiento')
        ->first();

        // Si no encuentra ninguno, devuelve el familiar más antiguo
        if (!$familiar) {
            $familiar = Familiar::orderBy('fecha_nacimiento')->first();
        }

        return $familiar;
    }

    /**
     * Construye el árbol genealógico de forma recursiva con estructura jerárquica.
     */
    private function construirArbol(Familiar $familiar, array &$procesados = []): array
    {
        // Evitar ciclos infinitos
        if (in_array($familiar->id, $procesados)) {
            return [
                'id' => $familiar->id,
                'nombre' => $familiar->nombre,
                'procesado' => true
            ];
        }

        $procesados[] = $familiar->id;

        // Cargar las relaciones con eager loading
        $familiar->load(['relaciones.familiarRelacionado', 'parentesco']);

        $nodo = [
            'id' => $familiar->id,
            'nombre' => $familiar->nombre,
            'fecha_nacimiento' => $familiar->fecha_nacimiento?->format('d/m/Y'),
            'edad' => $familiar->age,
            'parentesco' => $familiar->parentesco?->nombre_parentesco,
            'proximo_cumpleanos' => $familiar->days_until_birthday,
            'pareja' => null,
            'hijos' => [],
            'padres' => [],
            'hermanos' => [],
        ];

        // Obtener pareja (esposo, esposa, pareja)
        $parejaRelacion = $familiar->relaciones()
            ->whereIn('tipo_relacion', ['esposo', 'esposa', 'pareja'])
            ->with('familiarRelacionado')
            ->first();
            
        if ($parejaRelacion && $parejaRelacion->familiarRelacionado) {
            $nodo['pareja'] = [
                'id' => $parejaRelacion->familiarRelacionado->id,
                'nombre' => $parejaRelacion->familiarRelacionado->nombre,
                'fecha_nacimiento' => $parejaRelacion->familiarRelacionado->fecha_nacimiento?->format('d/m/Y'),
                'edad' => $parejaRelacion->familiarRelacionado->age,
                'tipo_relacion' => $parejaRelacion->tipo_relacion,
            ];
        }

        // Obtener hijos (recursivo para mostrar nietos)
        $hijosRelaciones = $familiar->relaciones()
            ->whereIn('tipo_relacion', ['hijo', 'hija'])
            ->with('familiarRelacionado')
            ->get();
            
        foreach ($hijosRelaciones as $relacion) {
            if ($relacion->familiarRelacionado && !in_array($relacion->familiar_relacionado_id, $procesados)) {
                $hijo = $this->construirArbol($relacion->familiarRelacionado, $procesados);
                $hijo['tipo_relacion'] = $relacion->tipo_relacion;
                $nodo['hijos'][] = $hijo;
            }
        }

        // Obtener padres (solo información, no recursivo para evitar ciclos)
        $padresRelaciones = $familiar->relaciones()
            ->whereIn('tipo_relacion', ['padre', 'madre'])
            ->with('familiarRelacionado')
            ->get();
            
        foreach ($padresRelaciones as $relacion) {
            if ($relacion->familiarRelacionado) {
                $nodo['padres'][] = [
                    'id' => $relacion->familiarRelacionado->id,
                    'nombre' => $relacion->familiarRelacionado->nombre,
                    'fecha_nacimiento' => $relacion->familiarRelacionado->fecha_nacimiento?->format('d/m/Y'),
                    'edad' => $relacion->familiarRelacionado->age,
                    'tipo_relacion' => $relacion->tipo_relacion,
                ];
            }
        }

        // Obtener hermanos (solo información, no recursivo)
        $hermanosRelaciones = $familiar->relaciones()
            ->whereIn('tipo_relacion', ['hermano', 'hermana'])
            ->with('familiarRelacionado')
            ->get();
            
        foreach ($hermanosRelaciones as $relacion) {
            if ($relacion->familiarRelacionado) {
                $nodo['hermanos'][] = [
                    'id' => $relacion->familiarRelacionado->id,
                    'nombre' => $relacion->familiarRelacionado->nombre,
                    'fecha_nacimiento' => $relacion->familiarRelacionado->fecha_nacimiento?->format('d/m/Y'),
                    'edad' => $relacion->familiarRelacionado->age,
                    'tipo_relacion' => $relacion->tipo_relacion,
                ];
            }
        }

        return $nodo;
    }

    /**
     * Obtiene los descendientes de un familiar.
     */
    public function obtenerDescendientes(Familiar $familiar): JsonResponse
    {
        $descendientes = $this->obtenerDescendientesRecursivo($familiar);

        return response()->json([
            'success' => true,
            'descendientes' => $descendientes
        ]);
    }

    /**
     * Obtiene los descendientes de forma recursiva.
     */
    private function obtenerDescendientesRecursivo(Familiar $familiar, int $nivel = 0, array &$procesados = []): array
    {
        if (in_array($familiar->id, $procesados)) {
            return [];
        }

        $procesados[] = $familiar->id;
        $descendientes = [];

        $hijos = $familiar->hijos()->get();
        foreach ($hijos as $relacion) {
            if ($relacion->familiarRelacionado) {
                $hijo = $relacion->familiarRelacionado;
                $descendientes[] = [
                    'id' => $hijo->id,
                    'nombre' => $hijo->nombre,
                    'nivel' => $nivel + 1,
                    'tipo_relacion' => $relacion->tipo_relacion,
                ];

                // Agregar descendientes del hijo
                $subDescendientes = $this->obtenerDescendientesRecursivo($hijo, $nivel + 1, $procesados);
                $descendientes = array_merge($descendientes, $subDescendientes);
            }
        }

        return $descendientes;
    }
}
