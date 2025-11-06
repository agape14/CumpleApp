<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class FamiliarAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Permitir acceso sin autenticación si no hay ningún familiar registrado
        // Esto permite la configuración inicial del sistema
        $totalFamiliares = \App\Models\Familiar::count();
        
        if ($totalFamiliares === 0) {
            return $next($request);
        }

        // Verificar si hay un familiar autenticado en la sesión
        if (!Session::has('familiar_id')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder.');
        }

        // Verificar que el familiar todavía existe y tiene acceso
        $familiarId = Session::get('familiar_id');
        $familiar = \App\Models\Familiar::find($familiarId);

        if (!$familiar || !$familiar->puede_acceder) {
            Session::flush();
            return redirect()->route('login')->with('error', 'Tu acceso ha sido revocado. Contacta al administrador.');
        }

        return $next($request);
    }
}
