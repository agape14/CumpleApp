<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Servicio para enviar notificaciones push mediante Firebase Cloud Messaging
 * 
 * @package App\Services
 */
class FirebaseNotificationService
{
    protected $messaging;

    /**
     * Constructor del servicio
     * Inicializa la conexión con Firebase
     */
    public function __construct()
    {
        try {
            $credentialsPath = storage_path('app/firebase/firebase-credentials.json');
            
            if (!file_exists($credentialsPath)) {
                Log::warning("Archivo de credenciales de Firebase no encontrado en: {$credentialsPath}");
                // No lanzar excepción durante construcción para permitir migraciones
                $this->messaging = null;
                return;
            }
            
            $factory = (new Factory)->withServiceAccount($credentialsPath);
            $this->messaging = $factory->createMessaging();
            
            Log::info("Servicio de Firebase inicializado correctamente");
        } catch (\Exception $e) {
            Log::error("Error al inicializar Firebase: " . $e->getMessage());
            $this->messaging = null;
        }
    }

    /**
     * Enviar notificación a un token específico
     * 
     * @param string $token Token FCM del dispositivo
     * @param string $userName Nombre del familiar que cumple años
     * @param string $years Edad que cumple
     * @return bool
     */
    public function sendToToken(string $token, string $userName, string $years): bool
    {
        if ($this->messaging === null) {
            Log::error("Servicio de Firebase no inicializado. Verifica las credenciales.");
            return false;
        }

        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create(
                    '🎉 Recordatorio de Cumpleaños',
                    '¡Mañana es el cumpleaños de ' . $userName . '! Cumple ' . $years . ' años'
                ))
                ->withData([
                    'userName' => $userName,
                    'years' => $years,
                    'type' => 'birthday_reminder',
                ]);

            $this->messaging->send($message);
            
            Log::info("✅ Notificación enviada a token: {$token} para {$userName}");
            
            // Actualizar last_used_at del token
            DB::table('fcm_tokens')
                ->where('token', $token)
                ->update(['last_used_at' => now()]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("❌ Error enviando notificación a token {$token}: " . $e->getMessage());
            
            // Si el token es inválido, eliminarlo de la base de datos
            if (str_contains($e->getMessage(), 'not-found') || 
                str_contains($e->getMessage(), 'invalid-registration-token')) {
                DB::table('fcm_tokens')->where('token', $token)->delete();
                Log::info("Token inválido eliminado: {$token}");
            }
            
            return false;
        }
    }

    /**
     * Enviar notificación a múltiples tokens
     * 
     * @param array $tokens Array de tokens FCM
     * @param string $userName Nombre del familiar
     * @param string $years Edad que cumple
     * @return array Estadísticas del envío
     */
    public function sendToMultipleTokens(array $tokens, string $userName, string $years): array
    {
        $successCount = 0;
        $failCount = 0;

        foreach ($tokens as $token) {
            if ($this->sendToToken($token, $userName, $years)) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        return [
            'success' => $successCount,
            'failed' => $failCount,
            'total' => count($tokens)
        ];
    }

    /**
     * Enviar notificación a todos los tokens registrados
     * 
     * @param string $userName Nombre del familiar
     * @param string $years Edad que cumple
     * @return array Estadísticas del envío
     */
    public function sendToAllTokens(string $userName, string $years): array
    {
        $tokens = DB::table('fcm_tokens')
            ->whereNotNull('token')
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            Log::warning("⚠️ No hay tokens FCM registrados");
            return ['success' => 0, 'failed' => 0, 'total' => 0];
        }

        Log::info("📤 Enviando notificaciones a " . count($tokens) . " dispositivos");
        
        return $this->sendToMultipleTokens($tokens, $userName, $years);
    }

    /**
     * Enviar notificación a tokens de un familiar específico
     * 
     * @param int $familiarId ID del familiar
     * @param string $userName Nombre del familiar
     * @param string $years Edad que cumple
     * @return array Estadísticas del envío
     */
    public function sendToFamiliar(int $familiarId, string $userName, string $years): array
    {
        $tokens = DB::table('fcm_tokens')
            ->where('familiar_id', $familiarId)
            ->whereNotNull('token')
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            Log::warning("⚠️ No hay tokens FCM para el familiar ID: {$familiarId}");
            return ['success' => 0, 'failed' => 0, 'total' => 0];
        }

        return $this->sendToMultipleTokens($tokens, $userName, $years);
    }

    /**
     * Enviar notificación personalizada
     * 
     * @param string $token Token FCM
     * @param string $title Título de la notificación
     * @param string $body Cuerpo de la notificación
     * @param array $data Datos adicionales
     * @return bool
     */
    public function sendCustomNotification(string $token, string $title, string $body, array $data = []): bool
    {
        if ($this->messaging === null) {
            Log::error("Servicio de Firebase no inicializado. Verifica las credenciales.");
            return false;
        }

        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body))
                ->withData($data);

            $this->messaging->send($message);
            
            Log::info("✅ Notificación personalizada enviada: {$title}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("❌ Error enviando notificación personalizada: " . $e->getMessage());
            return false;
        }
    }
}

