@extends('layouts.app')

@section('title', 'Editar Familiar')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-fill"></i> Editar Familiar: {{ $familiar->nombre }}
            </div>
            <div class="card-body">
                <form action="{{ route('familiares.update', $familiar) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">
                                <i class="bi bi-person"></i> Nombre Completo *
                            </label>
                            <input type="text" 
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="{{ old('nombre', $familiar->nombre) }}" 
                                   required
                                   placeholder="Ej: Juan Pérez">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="dni" class="form-label">
                                <i class="bi bi-person-vcard"></i> DNI
                            </label>
                            <input type="text" 
                                   class="form-control @error('dni') is-invalid @enderror" 
                                   id="dni" 
                                   name="dni" 
                                   value="{{ old('dni', $familiar->dni) }}"
                                   placeholder="12345678">
                            @error('dni')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Para acceso al sistema</small>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="parentesco_id" class="form-label">
                                <i class="bi bi-heart"></i> Parentesco *
                            </label>
                            <select class="form-select @error('parentesco_id') is-invalid @enderror" 
                                    id="parentesco_id" 
                                    name="parentesco_id" 
                                    required>
                                <option value="">Seleccionar...</option>
                                @foreach($parentescos as $parentesco)
                                    <option value="{{ $parentesco->id }}" 
                                            {{ old('parentesco_id', $familiar->parentesco_id) == $parentesco->id ? 'selected' : '' }}>
                                        {{ $parentesco->nombre_parentesco }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parentesco_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="fecha_nacimiento" class="form-label">
                                <i class="bi bi-cake2"></i> Fecha de Nacimiento *
                            </label>
                            <input type="date" 
                                   class="form-control @error('fecha_nacimiento') is-invalid @enderror" 
                                   id="fecha_nacimiento" 
                                   name="fecha_nacimiento" 
                                   value="{{ old('fecha_nacimiento', $familiar->fecha_nacimiento->format('Y-m-d')) }}" 
                                   required>
                            @error('fecha_nacimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="telefono" class="form-label">
                                <i class="bi bi-telephone"></i> Teléfono
                            </label>
                            <input type="tel" 
                                   class="form-control @error('telefono') is-invalid @enderror" 
                                   id="telefono" 
                                   name="telefono" 
                                   value="{{ old('telefono', $familiar->telefono) }}"
                                   placeholder="Ej: +52 123 456 7890">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope"></i> Email
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $familiar->email) }}"
                                   placeholder="ejemplo@correo.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notas" class="form-label">
                            <i class="bi bi-journal-text"></i> Notas
                        </label>
                        <textarea class="form-control @error('notas') is-invalid @enderror" 
                                  id="notas" 
                                  name="notas" 
                                  rows="3"
                                  placeholder="Información adicional, gustos, preferencias, etc.">{{ old('notas', $familiar->notas) }}</textarea>
                        @error('notas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="notificar" 
                                       name="notificar" 
                                       value="1"
                                       {{ old('notificar', $familiar->notificar) ? 'checked' : '' }}>
                                <label class="form-check-label" for="notificar">
                                    <i class="bi bi-bell"></i> Recibir notificaciones de cumpleaños
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="puede_acceder" 
                                       name="puede_acceder" 
                                       value="1"
                                       {{ old('puede_acceder', $familiar->puede_acceder) ? 'checked' : '' }}>
                                <label class="form-check-label" for="puede_acceder">
                                    <i class="bi bi-shield-check"></i> Puede acceder al sistema
                                </label>
                                <br><small class="text-muted">Requiere DNI configurado</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('familiares.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save-fill"></i> Actualizar Familiar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-3">
            <small class="text-white">
                <i class="bi bi-info-circle-fill"></i> 
                Los campos marcados con * son obligatorios
            </small>
        </div>
    </div>
</div>
@endsection

