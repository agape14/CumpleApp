<?php

namespace App\Http\Controllers;

use App\Models\Familiar;
use App\Models\ConfiguracionUsuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class GoogleCalendarController extends Controller
{
    /**
     * Exporta un cumpleaños a Google Calendar.
     */
    public function exportar(Familiar $familiar): JsonResponse
    {
        try {
            // Verificar si Google Calendar está habilitado
            $enabled = ConfiguracionUsuario::obtener('google_calendar_enabled', 'false');
            
            if ($enabled !== 'true') {
                return response()->json([
                    'success' => false,
                    'message' => 'La integración con Google Calendar no está habilitada. Por favor, configúrela primero.'
                ], 422);
            }

            // Generar el enlace de Google Calendar
            $enlace = $this->generarEnlaceGoogleCalendar($familiar);

            return response()->json([
                'success' => true,
                'message' => '¡Enlace generado exitosamente!',
                'enlace' => $enlace
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el enlace: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exporta todos los cumpleaños a Google Calendar.
     */
    public function exportarTodos(): JsonResponse
    {
        try {
            $familiares = Familiar::where('notificar', true)->get();
            $enlaces = [];

            foreach ($familiares as $familiar) {
                $enlaces[] = [
                    'familiar_id' => $familiar->id,
                    'nombre' => $familiar->nombre,
                    'enlace' => $this->generarEnlaceGoogleCalendar($familiar)
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Enlaces generados exitosamente',
                'total' => count($enlaces),
                'enlaces' => $enlaces
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar los enlaces: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Genera un archivo ICS para importar en Google Calendar.
     */
    public function generarICS(Request $request)
    {
        $familiarIds = $request->input('familiar_ids', []);
        
        if (empty($familiarIds)) {
            $familiares = Familiar::where('notificar', true)->get();
        } else {
            $familiares = Familiar::whereIn('id', $familiarIds)->get();
        }

        $icsContent = $this->generarContenidoICS($familiares);

        return response($icsContent)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="cumpleanos.ics"');
    }

    /**
     * Genera el enlace de Google Calendar para un cumpleaños.
     */
    private function generarEnlaceGoogleCalendar(Familiar $familiar): string
    {
        $titulo = "🎂 Cumpleaños de {$familiar->nombre}";
        $descripcion = "Hoy cumple años {$familiar->nombre}! 🎉\\n\\n";
        $descripcion .= "Edad: {$familiar->age} años\\n";
        
        if ($familiar->telefono) {
            $descripcion .= "Teléfono: {$familiar->telefono}\\n";
        }
        
        if ($familiar->email) {
            $descripcion .= "Email: {$familiar->email}\\n";
        }

        // Obtener el próximo cumpleaños
        $proximoCumple = $familiar->next_birthday;
        $fechaInicio = $proximoCumple->format('Ymd');
        $fechaFin = $proximoCumple->format('Ymd');

        // Construir la URL de Google Calendar
        $params = [
            'action' => 'TEMPLATE',
            'text' => $titulo,
            'details' => $descripcion,
            'dates' => "{$fechaInicio}/{$fechaFin}",
            'recur' => 'RRULE:FREQ=YEARLY', // Evento anual
            'ctz' => 'America/Lima', // Zona horaria
        ];

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params);
    }

    /**
     * Genera el contenido del archivo ICS.
     */
    private function generarContenidoICS($familiares): string
    {
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//CumpleApp//ES\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:PUBLISH\r\n";
        $ics .= "X-WR-CALNAME:Cumpleaños - CumpleApp\r\n";
        $ics .= "X-WR-TIMEZONE:America/Lima\r\n";

        foreach ($familiares as $familiar) {
            $ics .= $this->generarEventoICS($familiar);
        }

        $ics .= "END:VCALENDAR\r\n";

        return $ics;
    }

    /**
     * Genera un evento ICS para un familiar.
     */
    private function generarEventoICS(Familiar $familiar): string
    {
        $uid = uniqid() . '@cumpleapp.local';
        $dtstamp = Carbon::now()->format('Ymd\THis\Z');
        $dtstart = $familiar->fecha_nacimiento->format('md');
        $summary = "🎂 Cumpleaños de {$familiar->nombre}";
        $description = "Hoy cumple años {$familiar->nombre}!";

        $evento = "BEGIN:VEVENT\r\n";
        $evento .= "UID:{$uid}\r\n";
        $evento .= "DTSTAMP:{$dtstamp}\r\n";
        $evento .= "DTSTART;VALUE=DATE:{$dtstart}\r\n";
        $evento .= "SUMMARY:{$summary}\r\n";
        $evento .= "DESCRIPTION:{$description}\r\n";
        $evento .= "RRULE:FREQ=YEARLY\r\n";
        $evento .= "TRANSP:TRANSPARENT\r\n";
        $evento .= "END:VEVENT\r\n";

        return $evento;
    }
}
