<?php

namespace App\Http\Controllers;

use App\Models\CuotaMensual;
use App\Models\Familiar;
use App\Models\RelacionFamiliar;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class CuotaMensualController extends Controller
{
    /**
     * Muestra el panel de gestión de cuotas mensuales.
     */
    public function index(Request $request): View
    {
        $mesActual = $request->get('mes', now()->month);
        $anioActual = $request->get('anio', now()->year);

        // Obtener todos los hermanos (familiares con relación "hermano" o "hermana")
        $hermanos = $this->obtenerHermanos();

        // Obtener solo cuotas mensuales del período actual (no colectas especiales)
        $cuotas = CuotaMensual::with('hermano')
            ->mensuales()
            ->delPeriodo($mesActual, $anioActual)
            ->orderBy('estado')
            ->orderBy('hermano_id')
            ->get();

        // Estadísticas del mes actual
        $totalHermanos = $hermanos->count();
        $cuotasPagadas = $cuotas->where('estado', 'pagado')->count();
        $cuotasPendientes = $totalHermanos - $cuotasPagadas;
        $totalRecaudado = $cuotas->where('estado', 'pagado')->sum('monto');
        $montoCuota = $cuotas->first()->monto ?? 0;

        return view('cuotas-mensuales.index', compact(
            'hermanos',
            'cuotas',
            'mesActual',
            'anioActual',
            'totalHermanos',
            'cuotasPagadas',
            'cuotasPendientes',
            'totalRecaudado',
            'montoCuota'
        ));
    }

    /**
     * Muestra el historial de cuotas.
     */
    public function historial(Request $request): View
    {
        $anio = $request->get('anio', now()->year);
        
        // Obtener hermanos
        $hermanos = $this->obtenerHermanos();

        // Obtener solo cuotas mensuales del año agrupadas por mes (no colectas especiales)
        $cuotasPorMes = CuotaMensual::with('hermano')
            ->mensuales()
            ->delAnio($anio)
            ->orderBy('mes')
            ->orderBy('hermano_id')
            ->get()
            ->groupBy('mes');

        // Estadísticas anuales (solo cuotas mensuales)
        $totalRecaudadoAnual = CuotaMensual::mensuales()->delAnio($anio)->pagadas()->sum('monto');
        $cuotasPagadasAnual = CuotaMensual::mensuales()->delAnio($anio)->pagadas()->count();
        $cuotasPendientesAnual = CuotaMensual::mensuales()->delAnio($anio)->pendientes()->count();

        return view('cuotas-mensuales.historial', compact(
            'hermanos',
            'cuotasPorMes',
            'anio',
            'totalRecaudadoAnual',
            'cuotasPagadasAnual',
            'cuotasPendientesAnual'
        ));
    }

    /**
     * Registra una cuota mensual.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hermano_id' => 'required|exists:familiares,id',
            'anio' => 'required|integer|min:2020|max:2100',
            'mes' => 'required|integer|min:1|max:12',
            'monto' => 'required|numeric|min:0',
            'estado' => 'required|in:pendiente,pagado,parcial',
            'fecha_pago' => 'nullable|date',
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // Max 5MB
            'notas' => 'nullable|string',
        ], [
            'hermano_id.required' => 'Debe seleccionar un hermano.',
            'hermano_id.exists' => 'El hermano seleccionado no existe.',
            'anio.required' => 'El año es obligatorio.',
            'mes.required' => 'El mes es obligatorio.',
            'monto.required' => 'El monto es obligatorio.',
            'monto.numeric' => 'El monto debe ser un número.',
            'estado.required' => 'El estado es obligatorio.',
            'comprobante.file' => 'El comprobante debe ser un archivo.',
            'comprobante.mimes' => 'El comprobante debe ser JPG, PNG o PDF.',
            'comprobante.max' => 'El comprobante no puede superar los 5MB.',
        ]);

        // Asegurar que es tipo mensual
        $validated['tipo_cuota'] = 'mensual';

        // Verificar si ya existe una cuota para ese hermano en ese período
        $cuotaExistente = CuotaMensual::where('hermano_id', $validated['hermano_id'])
            ->where('tipo_cuota', 'mensual')
            ->where('anio', $validated['anio'])
            ->where('mes', $validated['mes'])
            ->first();

        if ($cuotaExistente) {
            return redirect()->back()->with('error', 'Ya existe una cuota registrada para este hermano en este período.');
        }

        // Procesar el comprobante si existe
        if ($request->hasFile('comprobante')) {
            $path = $request->file('comprobante')->store('cuotas', 'public');
            $validated['comprobante'] = $path;
        }

        CuotaMensual::create($validated);

        return redirect()->route('cuotas-mensuales.index')
            ->with('success', '¡Cuota registrada exitosamente!');
    }

    /**
     * Actualiza una cuota mensual.
     */
    public function update(Request $request, CuotaMensual $cuota): RedirectResponse
    {
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0',
            'estado' => 'required|in:pendiente,pagado,parcial',
            'fecha_pago' => 'nullable|date',
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notas' => 'nullable|string',
        ]);

        // Procesar el comprobante si existe
        if ($request->hasFile('comprobante')) {
            // Eliminar el comprobante anterior si existe
            if ($cuota->comprobante && \Storage::disk('public')->exists($cuota->comprobante)) {
                \Storage::disk('public')->delete($cuota->comprobante);
            }
            
            $path = $request->file('comprobante')->store('cuotas', 'public');
            $validated['comprobante'] = $path;
        }

        $cuota->update($validated);

        return redirect()->route('cuotas-mensuales.index')
            ->with('success', '¡Cuota actualizada exitosamente!');
    }

    /**
     * Marca una cuota como pagada.
     */
    public function marcarPagada(Request $request, CuotaMensual $cuota): JsonResponse
    {
        $validated = $request->validate([
            'fecha_pago' => 'nullable|date',
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $data = [
            'estado' => 'pagado',
            'fecha_pago' => $validated['fecha_pago'] ?? now()->toDateString(),
        ];

        // Procesar el comprobante si existe
        if ($request->hasFile('comprobante')) {
            $path = $request->file('comprobante')->store('cuotas', 'public');
            $data['comprobante'] = $path;
        }

        $cuota->update($data);

        return response()->json([
            'success' => true,
            'message' => '¡Cuota marcada como pagada!',
            'cuota' => $cuota->load('hermano')
        ]);
    }

    /**
     * Elimina una cuota.
     */
    public function destroy(CuotaMensual $cuota): RedirectResponse
    {
        // Eliminar el comprobante si existe
        if ($cuota->comprobante && \Storage::disk('public')->exists($cuota->comprobante)) {
            \Storage::disk('public')->delete($cuota->comprobante);
        }

        $cuota->delete();

        return redirect()->route('cuotas-mensuales.index')
            ->with('success', '¡Cuota eliminada exitosamente!');
    }

    /**
     * Genera las cuotas del mes para todos los hermanos.
     */
    public function generarCuotasMes(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mes' => 'required|integer|min:1|max:12',
            'anio' => 'required|integer|min:2020|max:2100',
            'monto' => 'required|numeric|min:0',
        ]);

        $hermanos = $this->obtenerHermanos();
        $cuotasCreadas = 0;
        $cuotasExistentes = 0;

        foreach ($hermanos as $hermano) {
            // Verificar si ya existe
            $existe = CuotaMensual::where('hermano_id', $hermano->id)
                ->where('tipo_cuota', 'mensual')
                ->where('anio', $validated['anio'])
                ->where('mes', $validated['mes'])
                ->exists();

            if (!$existe) {
                CuotaMensual::create([
                    'hermano_id' => $hermano->id,
                    'tipo_cuota' => 'mensual',
                    'anio' => $validated['anio'],
                    'mes' => $validated['mes'],
                    'monto' => $validated['monto'],
                    'estado' => 'pendiente',
                ]);
                $cuotasCreadas++;
            } else {
                $cuotasExistentes++;
            }
        }

        $mensaje = "Se crearon {$cuotasCreadas} cuotas.";
        if ($cuotasExistentes > 0) {
            $mensaje .= " {$cuotasExistentes} cuotas ya existían.";
        }

        return redirect()->route('cuotas-mensuales.index')
            ->with('success', $mensaje);
    }

    /**
     * Obtiene estadísticas de cuotas.
     */
    public function estadisticas(Request $request): JsonResponse
    {
        $anio = $request->get('anio', now()->year);

        $hermanos = $this->obtenerHermanos();
        $totalHermanos = $hermanos->count();

        $estadisticas = [];

        for ($mes = 1; $mes <= 12; $mes++) {
            $cuotasMes = CuotaMensual::delPeriodo($mes, $anio)->get();
            
            $estadisticas[] = [
                'mes' => $mes,
                'nombre_mes' => Carbon::create($anio, $mes, 1)->locale('es')->monthName,
                'total_hermanos' => $totalHermanos,
                'pagadas' => $cuotasMes->where('estado', 'pagado')->count(),
                'pendientes' => $totalHermanos - $cuotasMes->where('estado', 'pagado')->count(),
                'total_recaudado' => $cuotasMes->where('estado', 'pagado')->sum('monto'),
            ];
        }

        return response()->json([
            'success' => true,
            'estadisticas' => $estadisticas,
            'anio' => $anio
        ]);
    }

    /**
     * Muestra las colectas especiales.
     */
    public function colectasEspeciales(Request $request): View
    {
        $hermanos = $this->obtenerHermanos();
        
        // Obtener todas las colectas especiales agrupadas por concepto
        // Ordenar por fecha de creación más reciente primero
        $colectas = CuotaMensual::with('hermano')
            ->especiales()
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('concepto');

        return view('cuotas-mensuales.colectas-especiales', compact('hermanos', 'colectas'));
    }

    /**
     * Crea una nueva colecta especial.
     */
    public function crearColecta(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'concepto' => 'required|string|max:200',
            'meta_total' => 'required|numeric|min:0',
            'fecha_limite' => 'required|date',
            'monto_individual' => 'required|numeric|min:0',
        ], [
            'concepto.required' => 'El concepto es obligatorio.',
            'meta_total.required' => 'La meta total es obligatoria.',
            'fecha_limite.required' => 'La fecha límite es obligatoria.',
            'monto_individual.required' => 'El monto individual es obligatorio.',
        ]);

        $hermanos = $this->obtenerHermanos();
        $cuotasCreadas = 0;

        foreach ($hermanos as $hermano) {
            CuotaMensual::create([
                'hermano_id' => $hermano->id,
                'tipo_cuota' => 'especial',
                'concepto' => $validated['concepto'],
                'meta_total' => $validated['meta_total'],
                'fecha_limite' => $validated['fecha_limite'],
                'monto' => $validated['monto_individual'],
                'estado' => 'pendiente',
                'anio' => null,
                'mes' => null,
            ]);
            $cuotasCreadas++;
        }

        return redirect()->route('cuotas-mensuales.colectas-especiales')
            ->with('success', "¡Colecta '{$validated['concepto']}' creada para {$cuotasCreadas} hermanos!");
    }

    /**
     * Registra un aporte a una colecta especial.
     */
    public function registrarAporte(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hermano_id' => 'required|exists:familiares,id',
            'concepto' => 'required|string|max:200',
            'monto' => 'required|numeric|min:0.01',
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notas' => 'nullable|string',
        ]);

        // Buscar la cuota existente del hermano para este concepto
        $cuota = CuotaMensual::where('hermano_id', $validated['hermano_id'])
            ->where('tipo_cuota', 'especial')
            ->where('concepto', $validated['concepto'])
            ->first();

        if (!$cuota) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la cuota asignada para este hermano y concepto.'
            ], 404);
        }

        // Calcular el monto total ya pagado
        $montoPagadoActual = CuotaMensual::where('hermano_id', $validated['hermano_id'])
            ->where('tipo_cuota', 'especial')
            ->where('concepto', $validated['concepto'])
            ->where('estado', 'pagado')
            ->sum('monto');

        // Calcular pendiente
        $montoPendiente = $cuota->monto - $montoPagadoActual;

        // Validar que el aporte no exceda lo pendiente
        if ($validated['monto'] > $montoPendiente) {
            return response()->json([
                'success' => false,
                'message' => "El aporte de S/ {$validated['monto']} excede el monto pendiente de S/ " . number_format($montoPendiente, 2)
            ], 422);
        }

        // Crear el registro del aporte
        $datosAporte = [
            'hermano_id' => $validated['hermano_id'],
            'tipo_cuota' => 'especial',
            'concepto' => $validated['concepto'],
            'meta_total' => $cuota->meta_total,
            'fecha_limite' => $cuota->fecha_limite,
            'monto' => $validated['monto'],
            'estado' => 'pagado',
            'fecha_pago' => now()->toDateString(),
            'anio' => null,
            'mes' => null,
            'notas' => $validated['notas'] ?? null,
        ];

        // Procesar el comprobante si existe
        if ($request->hasFile('comprobante')) {
            $path = $request->file('comprobante')->store('cuotas', 'public');
            $datosAporte['comprobante'] = $path;
        }

        $nuevoAporte = CuotaMensual::create($datosAporte);

        // Calcular el total pagado después de este aporte
        $totalPagadoDespues = $montoPagadoActual + $validated['monto'];

        // Si completó el monto asignado, actualizar la cuota original a "pagado"
        if ($totalPagadoDespues >= $cuota->monto) {
            $cuota->update([
                'estado' => 'pagado',
                'fecha_pago' => now()->toDateString()
            ]);
            
            $mensaje = '¡Aporte registrado! Has completado tu cuota de S/ ' . number_format($cuota->monto, 2);
        } else {
            $pendienteRestante = $cuota->monto - $totalPagadoDespues;
            $mensaje = '¡Aporte registrado! Te faltan S/ ' . number_format($pendienteRestante, 2) . ' para completar tu cuota.';
        }

        return response()->json([
            'success' => true,
            'message' => $mensaje,
            'cuota' => $nuevoAporte->load('hermano'),
            'total_pagado' => $totalPagadoDespues,
            'monto_asignado' => $cuota->monto,
            'pendiente' => max(0, $cuota->monto - $totalPagadoDespues),
            'completado' => $totalPagadoDespues >= $cuota->monto
        ]);
    }

    /**
     * Obtiene todos los hermanos del sistema.
     */
    private function obtenerHermanos()
    {
        // Estrategia 1: Obtener familiares con relación "hermano" o "hermana" (bidireccional)
        $idsHermanos1 = RelacionFamiliar::whereIn('tipo_relacion', ['hermano', 'hermana'])
            ->pluck('familiar_id');

        $idsHermanos2 = RelacionFamiliar::whereIn('tipo_relacion', ['hermano', 'hermana'])
            ->pluck('familiar_relacionado_id');

        $idsHermanos = $idsHermanos1->merge($idsHermanos2)->unique();

        if ($idsHermanos->isNotEmpty()) {
            return Familiar::whereIn('id', $idsHermanos)
                ->orderBy('nombre')
                ->get();
        }

        // Estrategia 2: Si no hay relaciones, buscar por parentesco "Hermano/a"
        $parentescosHermanos = \App\Models\Parentesco::where(function($query) {
            $query->where('nombre_parentesco', 'LIKE', '%Hermano%')
                  ->orWhere('nombre_parentesco', 'LIKE', '%Hermana%');
        })->pluck('id');

        if ($parentescosHermanos->isNotEmpty()) {
            $hermanosPorParentesco = Familiar::whereIn('parentesco_id', $parentescosHermanos)
                ->orderBy('nombre')
                ->get();
                
            if ($hermanosPorParentesco->isNotEmpty()) {
                return $hermanosPorParentesco;
            }
        }

        // Estrategia 3: Si aún no hay, devolver todos los familiares
        // (permitir que el usuario seleccione cualquiera)
        return Familiar::orderBy('nombre')->get();
    }
}
