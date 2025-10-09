<?php

namespace App\Http\Controllers;

use App\Models\IdeaRegalo;
use App\Models\Familiar;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class IdeaRegaloController extends Controller
{
    /**
     * Almacena una nueva idea de regalo para un familiar.
     */
    public function store(Request $request, Familiar $familiar): RedirectResponse
    {
        $validated = $request->validate([
            'idea' => 'required|string|max:255',
            'precio_estimado' => 'nullable|numeric|min:0|max:999999.99',
            'link_compra' => 'nullable|url|max:255',
        ], [
            'idea.required' => 'La idea de regalo es obligatoria.',
            'idea.max' => 'La idea no puede tener más de 255 caracteres.',
            'precio_estimado.numeric' => 'El precio debe ser un número válido.',
            'precio_estimado.min' => 'El precio debe ser mayor o igual a 0.',
            'precio_estimado.max' => 'El precio no puede ser mayor a 999,999.99.',
            'link_compra.url' => 'El link de compra debe ser una URL válida.',
            'link_compra.max' => 'El link no puede tener más de 255 caracteres.',
        ]);

        $familiar->ideasRegalos()->create($validated);

        return redirect()
            ->route('familiares.show', $familiar)
            ->with('success', '¡Idea de regalo agregada exitosamente!');
    }

    /**
     * Actualiza una idea de regalo (principalmente para marcar como comprado).
     */
    public function update(Request $request, IdeaRegalo $idea): RedirectResponse
    {
        $validated = $request->validate([
            'comprado' => 'boolean',
        ]);

        $idea->update($validated);

        $message = $idea->comprado 
            ? '¡Regalo marcado como comprado!' 
            : '¡Regalo marcado como no comprado!';

        return redirect()
            ->route('familiares.show', $idea->familiar_id)
            ->with('success', $message);
    }

    /**
     * Elimina una idea de regalo.
     */
    public function destroy(IdeaRegalo $idea): RedirectResponse
    {
        $familiarId = $idea->familiar_id;
        $idea->delete();

        return redirect()
            ->route('familiares.show', $familiarId)
            ->with('success', '¡Idea de regalo eliminada exitosamente!');
    }
}

