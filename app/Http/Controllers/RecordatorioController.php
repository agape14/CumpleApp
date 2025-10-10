<?php

namespace App\Http\Controllers;

use App\Models\Familiar;
use App\Models\Recordatorio;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class RecordatorioController extends Controller
{
    /**
     * Almacena un nuevo recordatorio.
     */
    public function store(Request $request, Familiar $familiar): JsonResponse
    {
        $validated = $request->validate([
            'dias_antes' => 'required|integer|min:1|max:365',
            'enviar_email' => 'boolean',
            'enviar_whatsapp' => 'boolean',
            'activo' => 'boolean',
            'hora_envio' => 'required|date_format:H:i',
            'mensaje_personalizado' => 'nullable|string',
        ], [
            'dias_antes.required' => 'Los días de anticipación son obligatorios.',
            'dias_antes.integer' => 'Los días deben ser un número entero.',
            'dias_antes.min' => 'Debe ser al menos 1 día de anticipación.',
            'dias_antes.max' => 'No puede superar los 365 días de anticipación.',
            'hora_envio.required' => 'La hora de envío es obligatoria.',
            'hora_envio.date_format' => 'El formato de hora debe ser HH:MM.',
        ]);

        $validated['familiar_id'] = $familiar->id;
        $validated['enviar_email'] = $request->has('enviar_email');
        $validated['enviar_whatsapp'] = $request->has('enviar_whatsapp');
        $validated['activo'] = $request->has('activo');

        $recordatorio = Recordatorio::create($validated);

        return response()->json([
            'success' => true,
            'message' => '¡Recordatorio creado exitosamente!',
            'recordatorio' => $recordatorio
        ]);
    }

    /**
     * Actualiza un recordatorio.
     */
    public function update(Request $request, Recordatorio $recordatorio): JsonResponse
    {
        $validated = $request->validate([
            'dias_antes' => 'required|integer|min:1|max:365',
            'enviar_email' => 'boolean',
            'enviar_whatsapp' => 'boolean',
            'activo' => 'boolean',
            'hora_envio' => 'required|date_format:H:i',
            'mensaje_personalizado' => 'nullable|string',
        ]);

        $validated['enviar_email'] = $request->has('enviar_email');
        $validated['enviar_whatsapp'] = $request->has('enviar_whatsapp');
        $validated['activo'] = $request->has('activo');

        $recordatorio->update($validated);

        return response()->json([
            'success' => true,
            'message' => '¡Recordatorio actualizado exitosamente!',
            'recordatorio' => $recordatorio
        ]);
    }

    /**
     * Elimina un recordatorio.
     */
    public function destroy(Recordatorio $recordatorio): JsonResponse
    {
        $recordatorio->delete();

        return response()->json([
            'success' => true,
            'message' => '¡Recordatorio eliminado exitosamente!'
        ]);
    }

    /**
     * Activa o desactiva un recordatorio.
     */
    public function toggleActivo(Recordatorio $recordatorio): JsonResponse
    {
        $recordatorio->update(['activo' => !$recordatorio->activo]);

        return response()->json([
            'success' => true,
            'message' => $recordatorio->activo ? 'Recordatorio activado' : 'Recordatorio desactivado',
            'activo' => $recordatorio->activo
        ]);
    }

    /**
     * Obtiene todos los recordatorios de un familiar.
     */
    public function obtenerPorFamiliar(Familiar $familiar): JsonResponse
    {
        $recordatorios = $familiar->recordatorios()
            ->orderBy('dias_antes')
            ->get();

        return response()->json([
            'success' => true,
            'recordatorios' => $recordatorios
        ]);
    }
}
