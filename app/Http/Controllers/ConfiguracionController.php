<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionUsuario;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ConfiguracionController extends Controller
{
    /**
     * Muestra la página de configuración.
     */
    public function index(): View
    {
        $configuraciones = ConfiguracionUsuario::all();
        
        return view('configuracion.index', compact('configuraciones'));
    }

    /**
     * Actualiza una configuración específica.
     */
    public function actualizar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'clave' => 'required|string|max:100',
            'valor' => 'nullable|string|max:255',
        ]);

        ConfiguracionUsuario::establecer(
            $validated['clave'],
            $validated['valor'] ?? ''
        );

        return response()->json([
            'success' => true,
            'message' => '¡Configuración actualizada exitosamente!'
        ]);
    }

    /**
     * Actualiza múltiples configuraciones.
     */
    public function actualizarMultiples(Request $request): RedirectResponse
    {
        $configuraciones = $request->input('configuraciones', []);

        foreach ($configuraciones as $clave => $valor) {
            ConfiguracionUsuario::establecer($clave, $valor);
        }

        return redirect()
            ->route('configuracion.index')
            ->with('success', '¡Configuraciones actualizadas exitosamente!');
    }

    /**
     * Actualiza el tema de la aplicación.
     */
    public function actualizarTema(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tema' => 'required|in:light,dark,blue,green,purple,pink',
            'color_primario' => 'nullable|string|max:7', // Formato HEX color
        ]);

        ConfiguracionUsuario::establecer('tema', $validated['tema']);
        
        if (isset($validated['color_primario'])) {
            ConfiguracionUsuario::establecer('color_primario', $validated['color_primario']);
        }

        return response()->json([
            'success' => true,
            'message' => '¡Tema actualizado exitosamente!',
            'tema' => $validated['tema'],
            'color_primario' => $validated['color_primario'] ?? null
        ]);
    }

    /**
     * Actualiza la configuración de Google Calendar.
     */
    public function actualizarGoogleCalendar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
        ]);

        ConfiguracionUsuario::establecer(
            'google_calendar_enabled',
            $validated['enabled'] ? 'true' : 'false'
        );

        return response()->json([
            'success' => true,
            'message' => $validated['enabled'] 
                ? '¡Integración con Google Calendar habilitada!' 
                : 'Integración con Google Calendar deshabilitada.'
        ]);
    }

    /**
     * Actualiza la configuración de WhatsApp/Twilio.
     */
    public function actualizarWhatsApp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'account_sid' => 'nullable|string',
            'auth_token' => 'nullable|string',
            'whatsapp_number' => 'nullable|string',
        ]);

        ConfiguracionUsuario::establecer(
            'whatsapp_enabled',
            $validated['enabled'] ? 'true' : 'false'
        );

        if (isset($validated['account_sid'])) {
            ConfiguracionUsuario::establecer('twilio_account_sid', $validated['account_sid']);
        }

        if (isset($validated['auth_token'])) {
            ConfiguracionUsuario::establecer('twilio_auth_token', $validated['auth_token']);
        }

        if (isset($validated['whatsapp_number'])) {
            ConfiguracionUsuario::establecer('twilio_whatsapp_number', $validated['whatsapp_number']);
        }

        return response()->json([
            'success' => true,
            'message' => '¡Configuración de WhatsApp actualizada exitosamente!'
        ]);
    }

    /**
     * Obtiene todas las configuraciones.
     */
    public function obtenerTodas(): JsonResponse
    {
        $configuraciones = ConfiguracionUsuario::obtenerTodas();

        return response()->json([
            'success' => true,
            'configuraciones' => $configuraciones
        ]);
    }

    /**
     * Obtiene una configuración específica.
     */
    public function obtener(string $clave): JsonResponse
    {
        $valor = ConfiguracionUsuario::obtener($clave);

        return response()->json([
            'success' => true,
            'clave' => $clave,
            'valor' => $valor
        ]);
    }

    /**
     * Restablece las configuraciones por defecto.
     */
    public function restablecerDefecto(): RedirectResponse
    {
        // Restablecer configuraciones de tema
        ConfiguracionUsuario::establecer('tema', 'light');
        ConfiguracionUsuario::establecer('color_primario', '#3B82F6');
        
        // Deshabilitar integraciones
        ConfiguracionUsuario::establecer('google_calendar_enabled', 'false');
        ConfiguracionUsuario::establecer('whatsapp_enabled', 'false');

        return redirect()
            ->route('configuracion.index')
            ->with('success', '¡Configuraciones restablecidas a valores por defecto!');
    }
}
