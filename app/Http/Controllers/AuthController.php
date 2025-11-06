<?php

namespace App\Http\Controllers;

use App\Models\Familiar;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Procesa el login.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'dni' => 'required|string',
            'password' => 'required|string',
        ], [
            'dni.required' => 'El DNI es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // Buscar familiar por DNI
        $familiar = Familiar::where('dni', $credentials['dni'])->first();

        if (!$familiar) {
            return back()->withErrors([
                'dni' => 'DNI no encontrado en el sistema.',
            ])->withInput();
        }

        // Verificar si tiene acceso permitido
        if (!$familiar->puede_acceder) {
            return back()->withErrors([
                'dni' => 'Este familiar no tiene permisos para acceder al sistema.',
            ])->withInput();
        }

        // Verificar la contraseña (por defecto es el mismo DNI)
        if ($credentials['password'] !== $familiar->dni) {
            return back()->withErrors([
                'password' => 'Contraseña incorrecta.',
            ])->withInput();
        }

        // Guardar el familiar en la sesión
        Session::put('familiar_id', $familiar->id);
        Session::put('familiar_nombre', $familiar->nombre);
        Session::put('familiar_dni', $familiar->dni);

        return redirect()->route('dashboard')->with('success', "¡Bienvenido, {$familiar->nombre}!");
    }

    /**
     * Procesa el logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        $nombreFamiliar = Session::get('familiar_nombre', 'Usuario');
        
        Session::forget('familiar_id');
        Session::forget('familiar_nombre');
        Session::forget('familiar_dni');
        Session::flush();

        return redirect()->route('login')->with('success', "¡Hasta pronto, {$nombreFamiliar}!");
    }

    /**
     * Obtiene el familiar autenticado.
     */
    public static function getFamiliarAutenticado(): ?Familiar
    {
        $familiarId = Session::get('familiar_id');
        
        if (!$familiarId) {
            return null;
        }

        return Familiar::find($familiarId);
    }

    /**
     * Verifica si hay un familiar autenticado.
     */
    public static function isAuthenticated(): bool
    {
        return Session::has('familiar_id');
    }
}
