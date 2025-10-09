<?php

namespace App\Http\Controllers;

use App\Models\Familiar;
use App\Models\Parentesco;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FamiliarController extends Controller
{
    /**
     * Muestra una lista de todos los familiares.
     */
    public function index(): View
    {
        $familiares = Familiar::with('parentesco')
            ->orderBy('nombre')
            ->get();

        return view('familiares.index', compact('familiares'));
    }

    /**
     * Muestra el formulario para crear un nuevo familiar.
     */
    public function create(): View
    {
        $parentescos = Parentesco::orderBy('nombre_parentesco')->get();

        return view('familiares.create', compact('parentescos'));
    }

    /**
     * Almacena un nuevo familiar en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'fecha_nacimiento' => 'required|date|before:today',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'notificar' => 'boolean',
            'notas' => 'nullable|string',
            'parentesco_id' => 'required|exists:parentescos,id',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 150 caracteres.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'email.email' => 'El email debe ser una dirección de correo válida.',
            'email.max' => 'El email no puede tener más de 100 caracteres.',
            'parentesco_id.required' => 'El parentesco es obligatorio.',
            'parentesco_id.exists' => 'El parentesco seleccionado no existe.',
        ]);

        $validated['notificar'] = $request->has('notificar');

        Familiar::create($validated);

        return redirect()
            ->route('familiares.index')
            ->with('success', '¡Familiar agregado exitosamente!');
    }

    /**
     * Muestra los detalles de un familiar específico.
     */
    public function show(Familiar $familiar): View
    {
        $familiar->load(['parentesco', 'ideasRegalos']);

        return view('familiares.show', compact('familiar'));
    }

    /**
     * Muestra el formulario para editar un familiar existente.
     */
    public function edit(Familiar $familiar): View
    {
        $parentescos = Parentesco::orderBy('nombre_parentesco')->get();

        return view('familiares.edit', compact('familiar', 'parentescos'));
    }

    /**
     * Actualiza un familiar en la base de datos.
     */
    public function update(Request $request, Familiar $familiar): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'fecha_nacimiento' => 'required|date|before:today',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'notificar' => 'boolean',
            'notas' => 'nullable|string',
            'parentesco_id' => 'required|exists:parentescos,id',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 150 caracteres.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'email.email' => 'El email debe ser una dirección de correo válida.',
            'email.max' => 'El email no puede tener más de 100 caracteres.',
            'parentesco_id.required' => 'El parentesco es obligatorio.',
            'parentesco_id.exists' => 'El parentesco seleccionado no existe.',
        ]);

        $validated['notificar'] = $request->has('notificar');

        $familiar->update($validated);

        return redirect()
            ->route('familiares.index')
            ->with('success', '¡Familiar actualizado exitosamente!');
    }

    /**
     * Elimina un familiar de la base de datos.
     */
    public function destroy(Familiar $familiar): RedirectResponse
    {
        $familiar->delete();

        return redirect()
            ->route('familiares.index')
            ->with('success', '¡Familiar eliminado exitosamente!');
    }
}

