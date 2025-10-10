<?php

namespace App\Http\Controllers;

use App\Models\Familiar;
use App\Models\ConfiguracionUsuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WhatsAppController extends Controller
{
    /**
     * Envía una notificación de cumpleaños por WhatsApp.
     */
    public function enviarNotificacion(Request $request, Familiar $familiar): JsonResponse
    {
        try {
            // Verificar si WhatsApp está habilitado
            $enabled = ConfiguracionUsuario::obtener('whatsapp_enabled', 'false');
            
            if ($enabled !== 'true') {
                return response()->json([
                    'success' => false,
                    'message' => 'Las notificaciones por WhatsApp no están habilitadas. Por favor, configúrelas primero.'
                ], 422);
            }

            // Verificar que el familiar tenga teléfono
            if (!$familiar->telefono) {
                return response()->json([
                    'success' => false,
                    'message' => 'El familiar no tiene un número de teléfono registrado.'
                ], 422);
            }

            // Obtener credenciales de Twilio
            $accountSid = ConfiguracionUsuario::obtener('twilio_account_sid');
            $authToken = ConfiguracionUsuario::obtener('twilio_auth_token');
            $whatsappNumber = ConfiguracionUsuario::obtener('twilio_whatsapp_number');

            if (!$accountSid || !$authToken || !$whatsappNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las credenciales de Twilio no están configuradas correctamente.'
                ], 422);
            }

            // Preparar el mensaje
            $mensaje = $request->input('mensaje', $this->generarMensajeDefault($familiar));

            // Enviar el mensaje usando Twilio
            $resultado = $this->enviarMensajeTwilio(
                $accountSid,
                $authToken,
                $whatsappNumber,
                $familiar->telefono,
                $mensaje
            );

            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Mensaje de WhatsApp enviado exitosamente!',
                    'sid' => $resultado['sid']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al enviar el mensaje: ' . $resultado['error']
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el mensaje: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envía recordatorios por WhatsApp a todos los próximos cumpleaños.
     */
    public function enviarRecordatorios(Request $request): JsonResponse
    {
        try {
            $diasAnticipacion = $request->input('dias_anticipacion', 7);
            $familiares = Familiar::where('notificar', true)
                ->whereNotNull('telefono')
                ->get();

            $enviados = [];
            $errores = [];

            foreach ($familiares as $familiar) {
                if ($familiar->days_until_birthday <= $diasAnticipacion) {
                    $resultado = $this->enviarNotificacion(
                        new Request(['mensaje' => $this->generarMensajeRecordatorio($familiar)]),
                        $familiar
                    );

                    if ($resultado->getData()->success) {
                        $enviados[] = $familiar->nombre;
                    } else {
                        $errores[] = $familiar->nombre;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Proceso completado',
                'enviados' => count($enviados),
                'errores' => count($errores),
                'detalles' => [
                    'enviados' => $enviados,
                    'errores' => $errores
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar los recordatorios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prueba la configuración de WhatsApp.
     */
    public function probarConfiguracion(Request $request): JsonResponse
    {
        try {
            $telefono = $request->input('telefono');
            
            if (!$telefono) {
                return response()->json([
                    'success' => false,
                    'message' => 'Por favor proporciona un número de teléfono para la prueba.'
                ], 422);
            }

            $accountSid = ConfiguracionUsuario::obtener('twilio_account_sid');
            $authToken = ConfiguracionUsuario::obtener('twilio_auth_token');
            $whatsappNumber = ConfiguracionUsuario::obtener('twilio_whatsapp_number');

            if (!$accountSid || !$authToken || !$whatsappNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las credenciales de Twilio no están configuradas.'
                ], 422);
            }

            $mensaje = "🎉 Este es un mensaje de prueba de CumpleApp. ¡La configuración de WhatsApp funciona correctamente!";

            $resultado = $this->enviarMensajeTwilio(
                $accountSid,
                $authToken,
                $whatsappNumber,
                $telefono,
                $mensaje
            );

            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Mensaje de prueba enviado exitosamente!',
                    'sid' => $resultado['sid']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al enviar el mensaje de prueba: ' . $resultado['error']
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Genera el mensaje por defecto para un cumpleaños.
     */
    private function generarMensajeDefault(Familiar $familiar): string
    {
        return "🎉🎂 ¡Feliz cumpleaños {$familiar->nombre}! 🎂🎉\n\n"
             . "Te deseamos un día maravilloso lleno de alegría y amor. "
             . "¡Que cumplas muchos años más! 🎈✨";
    }

    /**
     * Genera el mensaje de recordatorio.
     */
    private function generarMensajeRecordatorio(Familiar $familiar): string
    {
        $dias = $familiar->days_until_birthday;
        
        if ($dias == 0) {
            return "🎂 ¡Hoy es el cumpleaños de {$familiar->nombre}! No olvides felicitarlo/a. 🎉";
        } elseif ($dias == 1) {
            return "📅 Recordatorio: Mañana es el cumpleaños de {$familiar->nombre}. 🎂";
        } else {
            return "📅 Recordatorio: Faltan {$dias} días para el cumpleaños de {$familiar->nombre}. 🎂";
        }
    }

    /**
     * Envía un mensaje usando la API de Twilio.
     */
    private function enviarMensajeTwilio(
        string $accountSid,
        string $authToken,
        string $from,
        string $to,
        string $mensaje
    ): array {
        try {
            // Formatear números de teléfono (WhatsApp requiere formato E.164)
            $from = 'whatsapp:' . preg_replace('/[^0-9+]/', '', $from);
            $to = 'whatsapp:' . preg_replace('/[^0-9+]/', '', $to);

            // Asegurar que el número tenga el formato correcto
            if (!str_starts_with($to, 'whatsapp:+')) {
                $to = 'whatsapp:+' . ltrim($to, 'whatsapp:');
            }

            // Preparar la petición a la API de Twilio
            $url = "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json";
            
            $data = [
                'From' => $from,
                'To' => $to,
                'Body' => $mensaje
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, "{$accountSid}:{$authToken}");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded'
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300) {
                return [
                    'success' => true,
                    'sid' => $result['sid'] ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $result['message'] ?? 'Error desconocido'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
