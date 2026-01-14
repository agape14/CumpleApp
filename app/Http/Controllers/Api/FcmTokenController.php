<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para gestionar tokens FCM (Firebase Cloud Messaging)
 * 
 * @package App\Http\Controllers\Api
 */
class FcmTokenController extends Controller
{
    /**
     * Guardar o actualizar FCM token
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'device_type' => 'nullable|string|in:android,ios',
            'familiar_id' => 'nullable|exists:familiares,id',
        ], [
            'token.required' => 'El token es obligatorio',
            'token.string' => 'El token debe ser una cadena de texto',
            'device_type.in' => 'El tipo de dispositivo debe ser android o ios',
            'familiar_id.exists' => 'El familiar especificado no existe',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $familiarId = $request->input('familiar_id');
        $token = $request->input('token');
        $deviceType = $request->input('device_type', 'android');

        try {
            // Buscar si el token ya existe
            $existingToken = DB::table('fcm_tokens')
                ->where('token', $token)
                ->first();

            if ($existingToken) {
                // Actualizar token existente
                DB::table('fcm_tokens')
                    ->where('id', $existingToken->id)
                    ->update([
                        'familiar_id' => $familiarId,
                        'device_type' => $deviceType,
                        'last_used_at' => now(),
                        'updated_at' => now(),
                    ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Token actualizado correctamente'
                ]);
            } else {
                // Crear nuevo token
                DB::table('fcm_tokens')->insert([
                    'familiar_id' => $familiarId,
                    'token' => $token,
                    'device_type' => $deviceType,
                    'last_used_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Token guardado correctamente'
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un token FCM
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ], [
            'token.required' => 'El token es obligatorio',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $deleted = DB::table('fcm_tokens')
                ->where('token', $request->input('token'))
                ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Token eliminado correctamente'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Token no encontrado'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
