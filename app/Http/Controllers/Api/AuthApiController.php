<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Familiar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    /**
     * Inicia sesión y retorna un token.
     * 
     * POST /api/v1/login
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'dni' => 'required|string',
                'password' => 'required|string',
            ], [
                'dni.required' => 'El DNI es obligatorio.',
                'password.required' => 'La contraseña es obligatoria.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Buscar familiar por DNI
            $familiar = Familiar::where('dni', $request->dni)->first();

            if (!$familiar) {
                return response()->json([
                    'success' => false,
                    'message' => 'DNI no encontrado en el sistema.'
                ], 404);
            }

            // Verificar si tiene acceso permitido
            if (!$familiar->puede_acceder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este familiar no tiene permisos para acceder al sistema.'
                ], 403);
            }

            // Verificar la contraseña (por defecto es el mismo DNI)
            if ($request->password !== $familiar->dni) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contraseña incorrecta.'
                ], 401);
            }

            // Crear token
            $token = $familiar->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login exitoso',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $familiar->id,
                        'nombre' => $familiar->nombre,
                        'dni' => $familiar->dni,
                        'email' => $familiar->email,
                        'telefono' => $familiar->telefono,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cierra sesión y revoca el token.
     * 
     * POST /api/v1/logout
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revocar el token actual
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene el usuario autenticado.
     * 
     * GET /api/v1/me
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $familiar = $request->user();
            $familiar->load('parentesco');

            return response()->json([
                'success' => true,
                'data' => $familiar
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

