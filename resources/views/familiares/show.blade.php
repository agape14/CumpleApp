@extends('layouts.app')

@section('title', $familiar->nombre)

@section('content')
<div class="row">
    <!-- Botón de regresar -->
    <div class="col-12 mb-3">
        <a href="{{ route('familiares.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver a la lista
        </a>
    </div>

    <!-- Información del familiar -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-circle"></i> Información Personal
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <i class="bi bi-person-fill" style="font-size: 4rem; color: white;"></i>
                    </div>
                    <h3 class="mt-3 mb-0">{{ $familiar->nombre }}</h3>
                    @if($familiar->parentesco)
                    <span class="badge bg-info mt-2">
                        {{ $familiar->parentesco->nombre_parentesco }}
                    </span>
                    @endif
                    @if($familiar->zodiac_sign)
                    <br>
                    <span class="zodiac-sign mt-2">
                        <i class="bi bi-star-fill"></i> {{ $familiar->zodiac_sign }}
                    </span>
                    @endif
                </div>

                <table class="table table-borderless">
                    @if($familiar->fecha_nacimiento)
                    <tr>
                        <td class="text-muted"><i class="bi bi-cake2-fill text-primary"></i> Fecha de Nacimiento:</td>
                        <td><strong>{{ $familiar->fecha_nacimiento->format('d/m/Y') }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted"><i class="bi bi-calendar-fill text-success"></i> Edad:</td>
                        <td><strong>{{ $familiar->age }} años</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted"><i class="bi bi-clock-fill text-warning"></i> Próximo Cumpleaños:</td>
                        <td>
                            @if($familiar->days_until_birthday == 0)
                                <span class="badge bg-danger">¡Hoy!</span>
                            @else
                                En {{ $familiar->days_until_birthday }} días 
                                ({{ $familiar->next_birthday->format('d/m/Y') }})
                            @endif
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="2" class="text-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            Fecha de nacimiento no registrada
                        </td>
                    </tr>
                    @endif
                    @if($familiar->telefono)
                    <tr>
                        <td class="text-muted"><i class="bi bi-telephone-fill text-info"></i> Teléfono:</td>
                        <td>{{ $familiar->telefono }}</td>
                    </tr>
                    @endif
                    @if($familiar->email)
                    <tr>
                        <td class="text-muted"><i class="bi bi-envelope-fill text-danger"></i> Email:</td>
                        <td>{{ $familiar->email }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted"><i class="bi bi-bell-fill"></i> Notificaciones:</td>
                        <td>
                            @if($familiar->notificar)
                                <span class="badge bg-success">Activadas</span>
                            @else
                                <span class="badge bg-secondary">Desactivadas</span>
                            @endif
                        </td>
                    </tr>
                </table>

                @if($familiar->notas)
                <div class="alert alert-info mt-3">
                    <h6><i class="bi bi-journal-text"></i> Notas:</h6>
                    <p class="mb-0">{{ $familiar->notas }}</p>
                </div>
                @endif

                <hr>

                <div class="d-flex gap-2 flex-wrap">
                    @if($familiar->telefono)
                    <a href="tel:{{ $familiar->telefono }}" class="btn btn-success">
                        <i class="bi bi-telephone-fill"></i> Llamar
                    </a>
                    @endif
                    @if($familiar->email)
                    <a href="mailto:{{ $familiar->email }}" class="btn btn-primary">
                        <i class="bi bi-envelope-fill"></i> Enviar Email
                    </a>
                    @endif
                    <a href="{{ route('familiares.edit', $familiar) }}" class="btn btn-warning">
                        <i class="bi bi-pencil-fill"></i> Editar
                    </a>
                    <form action="{{ route('familiares.destroy', $familiar) }}" 
                          method="POST" 
                          class="d-inline"
                          onsubmit="return confirm('¿Estás seguro de eliminar a {{ $familiar->nombre }}? Esta acción también eliminará todas sus ideas de regalos.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash-fill"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Ideas de regalos -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-gift-fill"></i> Ideas de Regalos</span>
                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addGiftModal">
                    <i class="bi bi-plus-circle-fill"></i> Agregar
                </button>
            </div>
            <div class="card-body">
                @if($familiar->ideasRegalos->count() > 0)
                <div class="list-group">
                    @foreach($familiar->ideasRegalos as $idea)
                    <div class="list-group-item {{ $idea->comprado ? 'bg-light' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 {{ $idea->comprado ? 'text-decoration-line-through text-muted' : '' }}">
                                    {{ $idea->idea }}
                                </h6>
                                @if($idea->precio_estimado)
                                <p class="mb-1">
                                    <small class="text-muted">
                                        <i class="bi bi-currency-dollar"></i> 
                                        Precio estimado: ${{ number_format($idea->precio_estimado, 2) }}
                                    </small>
                                </p>
                                @endif
                                @if($idea->link_compra)
                                <p class="mb-1">
                                    <a href="{{ $idea->link_compra }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-cart-fill"></i> Ver producto
                                    </a>
                                </p>
                                @endif
                                @if($idea->comprado)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle-fill"></i> Comprado
                                </span>
                                @endif
                            </div>
                            <div class="btn-group-vertical ms-2">
                                <form action="{{ route('ideas.update', $idea) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="comprado" value="{{ $idea->comprado ? '0' : '1' }}">
                                    <button type="submit" 
                                            class="btn btn-sm {{ $idea->comprado ? 'btn-warning' : 'btn-success' }}"
                                            title="{{ $idea->comprado ? 'Marcar como no comprado' : 'Marcar como comprado' }}">
                                        <i class="bi bi-{{ $idea->comprado ? 'x-circle' : 'check-circle' }}-fill"></i>
                                    </button>
                                </form>
                                <form action="{{ route('ideas.destroy', $idea) }}" 
                                      method="POST"
                                      onsubmit="return confirm('¿Eliminar esta idea de regalo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger"
                                            title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    @php
                        $totalIdeas = $familiar->ideasRegalos->count();
                        $compradas = $familiar->ideasRegalos->where('comprado', true)->count();
                        $precioTotal = $familiar->ideasRegalos->sum('precio_estimado');
                    @endphp
                    <div class="row text-center">
                        <div class="col-4">
                            <h5 class="text-primary">{{ $totalIdeas }}</h5>
                            <small class="text-muted">Total Ideas</small>
                        </div>
                        <div class="col-4">
                            <h5 class="text-success">{{ $compradas }}</h5>
                            <small class="text-muted">Compradas</small>
                        </div>
                        <div class="col-4">
                            <h5 class="text-warning">${{ number_format($precioTotal, 2) }}</h5>
                            <small class="text-muted">Precio Total</small>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-gift" style="font-size: 3rem; color: #cbd5e1;"></i>
                    <p class="text-muted mt-2 mb-0">No hay ideas de regalos aún</p>
                    <small class="text-muted">Agrega algunas ideas para no olvidar qué regalar</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar idea de regalo -->
<div class="modal fade" id="addGiftModal" tabindex="-1" aria-labelledby="addGiftModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title" id="addGiftModalLabel">
                    <i class="bi bi-gift-fill"></i> Agregar Idea de Regalo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('familiares.ideas.store', $familiar) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="idea" class="form-label">
                            <i class="bi bi-lightbulb"></i> Idea de Regalo *
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="idea" 
                               name="idea" 
                               required
                               placeholder="Ej: Libro de cocina, Reloj, etc.">
                    </div>

                    <div class="mb-3">
                        <label for="precio_estimado" class="form-label">
                            <i class="bi bi-currency-dollar"></i> Precio Estimado
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="precio_estimado" 
                               name="precio_estimado" 
                               step="0.01"
                               min="0"
                               placeholder="0.00">
                    </div>

                    <div class="mb-3">
                        <label for="link_compra" class="form-label">
                            <i class="bi bi-link-45deg"></i> Link de Compra
                        </label>
                        <input type="url" 
                               class="form-control" 
                               id="link_compra" 
                               name="link_compra"
                               placeholder="https://ejemplo.com/producto">
                        <small class="text-muted">URL completa del producto (opcional)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save-fill"></i> Guardar Idea
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

