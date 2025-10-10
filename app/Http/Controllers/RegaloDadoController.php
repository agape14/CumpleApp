<?php

namespace App\Http\Controllers;

use App\Models\Familiar;
use App\Models\RegaloDado;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RegaloDadoController extends Controller
{
    /**
     * Muestra el historial de regalos dados a un familiar.
     */
    public function index(Familiar $familiar): View
    {
        $regalos = $familiar->regalosDados()
            ->orderBy('fecha_entrega', 'desc')
            ->get();

        return view('regalos-dados.index', compact('familiar', 'regalos'));
    }

    /**
     * Almacena un nuevo regalo dado.
     */
    public function store(Request $request, Familiar $familiar): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_regalo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'precio' => 'nullable|numeric|min:0',
            'fecha_entrega' => 'required|date',
            'ocasion' => 'required|in:cumpleaños,navidad,aniversario,graduacion,otro',
            'lugar_compra' => 'nullable|string|max:200',
            'notas' => 'nullable|string',
            'foto' => 'nullable|image|max:2048', // Max 2MB
        ], [
            'nombre_regalo.required' => 'El nombre del regalo es obligatorio.',
            'nombre_regalo.max' => 'El nombre del regalo no puede tener más de 200 caracteres.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',
            'fecha_entrega.required' => 'La fecha de entrega es obligatoria.',
            'fecha_entrega.date' => 'La fecha de entrega debe ser una fecha válida.',
            'ocasion.required' => 'La ocasión es obligatoria.',
            'ocasion.in' => 'La ocasión seleccionada no es válida.',
            'lugar_compra.max' => 'El lugar de compra no puede tener más de 200 caracteres.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.max' => 'La imagen no puede superar los 2MB.',
        ]);

        $validated['familiar_id'] = $familiar->id;

        // Procesar la foto si existe
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('regalos', 'public');
            $validated['foto'] = $path;
        }

        RegaloDado::create($validated);

        return redirect()
            ->route('regalos-dados.index', $familiar)
            ->with('success', '¡Regalo registrado exitosamente en el historial!');
    }

    /**
     * Actualiza un regalo dado.
     */
    public function update(Request $request, RegaloDado $regalo): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_regalo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'precio' => 'nullable|numeric|min:0',
            'fecha_entrega' => 'required|date',
            'ocasion' => 'required|in:cumpleaños,navidad,aniversario,graduacion,otro',
            'lugar_compra' => 'nullable|string|max:200',
            'notas' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        // Procesar la foto si existe
        if ($request->hasFile('foto')) {
            // Eliminar la foto anterior si existe
            if ($regalo->foto && \Storage::disk('public')->exists($regalo->foto)) {
                \Storage::disk('public')->delete($regalo->foto);
            }
            
            $path = $request->file('foto')->store('regalos', 'public');
            $validated['foto'] = $path;
        }

        $regalo->update($validated);

        return redirect()
            ->route('regalos-dados.index', $regalo->familiar)
            ->with('success', '¡Regalo actualizado exitosamente!');
    }

    /**
     * Elimina un regalo dado.
     */
    public function destroy(RegaloDado $regalo): RedirectResponse
    {
        $familiar = $regalo->familiar;

        // Eliminar la foto si existe
        if ($regalo->foto && \Storage::disk('public')->exists($regalo->foto)) {
            \Storage::disk('public')->delete($regalo->foto);
        }

        $regalo->delete();

        return redirect()
            ->route('regalos-dados.index', $familiar)
            ->with('success', '¡Regalo eliminado del historial!');
    }

    /**
     * Obtiene estadísticas de regalos de un familiar.
     */
    public function estadisticas(Familiar $familiar): JsonResponse
    {
        $regalos = $familiar->regalosDados;

        $estadisticas = [
            'total_regalos' => $regalos->count(),
            'total_gastado' => $regalos->sum('precio'),
            'promedio_precio' => $regalos->avg('precio'),
            'por_ocasion' => $regalos->groupBy('ocasion')->map(function ($items) {
                return $items->count();
            }),
            'ultimo_regalo' => $regalos->sortByDesc('fecha_entrega')->first(),
        ];

        return response()->json([
            'success' => true,
            'estadisticas' => $estadisticas
        ]);
    }
}
