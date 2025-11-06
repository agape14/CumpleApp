@extends('layouts.app')

@section('title', 'Familiares')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="text-white">
                <i class="bi bi-people-fill"></i> Mis Familiares
            </h2>
            <a href="{{ route('familiares.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill"></i> Agregar Familiar
            </a>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-ul"></i> Lista de Familiares ({{ $familiares->count() }})
            </div>
            <div class="card-body">
                @if($familiares->count() > 0)
                    @php
                        // Agrupar familiares por tipo de parentesco
                        $hermanos = $familiares->filter(function($f) {
                            $parentesco = strtolower($f->parentesco->nombre_parentesco ?? '');
                            return str_contains($parentesco, 'herman');
                        });
                        
                        $padres = $familiares->filter(function($f) {
                            $parentesco = strtolower($f->parentesco->nombre_parentesco ?? '');
                            return str_contains($parentesco, 'padre') || str_contains($parentesco, 'madre') || 
                                   str_contains($parentesco, 'papá') || str_contains($parentesco, 'mamá');
                        });
                        
                        $otros = $familiares->filter(function($f) use ($hermanos, $padres) {
                            return !$hermanos->contains($f) && !$padres->contains($f);
                        });
                    @endphp
                    
                    <!-- Tabs de Bootstrap 5 -->
                    <ul class="nav nav-pills mb-4" id="familiaresTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="hermanos-tab" data-bs-toggle="pill" 
                                    data-bs-target="#hermanos" type="button" role="tab">
                                <i class="bi bi-people-fill"></i> Hermanos 
                                <span class="badge bg-primary">{{ $hermanos->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="padres-tab" data-bs-toggle="pill" 
                                    data-bs-target="#padres" type="button" role="tab">
                                <i class="bi bi-heart-fill"></i> Padres 
                                <span class="badge bg-danger">{{ $padres->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="otros-tab" data-bs-toggle="pill" 
                                    data-bs-target="#otros" type="button" role="tab">
                                <i class="bi bi-person-fill"></i> Otros 
                                <span class="badge bg-info">{{ $otros->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="todos-tab" data-bs-toggle="pill" 
                                    data-bs-target="#todos" type="button" role="tab">
                                <i class="bi bi-list-ul"></i> Todos 
                                <span class="badge bg-secondary">{{ $familiares->count() }}</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="familiaresTabContent">
                        <!-- Tab Hermanos -->
                        <div class="tab-pane fade show active" id="hermanos" role="tabpanel">
                            @if($hermanos->count() > 0)
                                @include('familiares.partials.tabla-familiares', ['familiares_filtrados' => $hermanos])
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> No hay hermanos registrados.
                                </div>
                            @endif
                        </div>

                        <!-- Tab Padres -->
                        <div class="tab-pane fade" id="padres" role="tabpanel">
                            @if($padres->count() > 0)
                                @include('familiares.partials.tabla-familiares', ['familiares_filtrados' => $padres])
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> No hay padres registrados.
                                </div>
                            @endif
                        </div>

                        <!-- Tab Otros -->
                        <div class="tab-pane fade" id="otros" role="tabpanel">
                            @if($otros->count() > 0)
                                @include('familiares.partials.tabla-familiares', ['familiares_filtrados' => $otros])
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> No hay otros familiares registrados.
                                </div>
                            @endif
                        </div>

                        <!-- Tab Todos -->
                        <div class="tab-pane fade" id="todos" role="tabpanel">
                            @include('familiares.partials.tabla-familiares', ['familiares_filtrados' => $familiares])
                        </div>
                    </div>

                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        No hay familiares registrados aún.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar relación familiar -->
<div class="modal fade" id="agregarRelacionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-diagram-3"></i> Agregar Relación Familiar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAgregarRelacion" onsubmit="guardarRelacion(event)">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Familiar Principal:</strong> <span id="familiarPrincipalNombre"></span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_relacion" class="form-label">
                                    <i class="bi bi-arrow-right"></i> Tipo de Relación
                                </label>
                                <select class="form-select" id="tipo_relacion" name="tipo_relacion" required onchange="actualizarOpcionesRelacion()">
                                    <option value="">Seleccionar...</option>
                                    <optgroup label="Relaciones Directas">
                                        <option value="padre">Padre</option>
                                        <option value="madre">Madre</option>
                                        <option value="hijo">Hijo</option>
                                        <option value="hija">Hija</option>
                                    </optgroup>
                                    <optgroup label="Pareja">
                                        <option value="esposo">Esposo</option>
                                        <option value="esposa">Esposa</option>
                                        <option value="pareja">Pareja</option>
                                    </optgroup>
                                    <optgroup label="Hermanos">
                                        <option value="hermano">Hermano</option>
                                        <option value="hermana">Hermana</option>
                                    </optgroup>
                                    <optgroup label="Abuelos">
                                        <option value="abuelo">Abuelo</option>
                                        <option value="abuela">Abuela</option>
                                    </optgroup>
                                    <optgroup label="Nietos">
                                        <option value="nieto">Nieto</option>
                                        <option value="nieta">Nieta</option>
                                    </optgroup>
                                    <optgroup label="Tíos">
                                        <option value="tio">Tío</option>
                                        <option value="tia">Tía</option>
                                    </optgroup>
                                    <optgroup label="Sobrinos">
                                        <option value="sobrino">Sobrino</option>
                                        <option value="sobrina">Sobrina</option>
                                    </optgroup>
                                    <optgroup label="Primos">
                                        <option value="primo">Primo</option>
                                        <option value="prima">Prima</option>
                                    </optgroup>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">
                                    <i class="bi bi-chat-text"></i> Descripción (opcional)
                                </label>
                                <input type="text" class="form-control" id="descripcion" name="descripcion" 
                                       placeholder="Ej: Medio hermano, Adoptivo, etc." maxlength="200">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="crearNuevoFamiliar" name="crear_nuevo" onchange="toggleOpcionesFamiliar()">
                            <label class="form-check-label" for="crearNuevoFamiliar">
                                <strong>Crear nuevo familiar</strong>
                            </label>
                        </div>
                        <small class="text-muted">Si no está marcado, selecciona un familiar existente</small>
                    </div>

                    <!-- Opción 1: Familiar existente -->
                    <div id="opcionFamiliarExistente">
                        <div class="mb-3">
                            <label for="familiar_relacionado_id" class="form-label">
                                <i class="bi bi-person-check"></i> Familiar Relacionado
                            </label>
                            <select class="form-select" id="familiar_relacionado_id" name="familiar_relacionado_id">
                                <option value="">Seleccionar familiar existente...</option>
                            </select>
                        </div>
                    </div>

                    <!-- Opción 2: Nuevo familiar -->
                    <div id="opcionNuevoFamiliar" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nuevo_nombre" class="form-label">
                                        <i class="bi bi-person"></i> Nombre <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="nuevo_nombre" name="nuevo_nombre" 
                                           placeholder="Nombre completo" maxlength="255">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nuevo_fecha_nacimiento" class="form-label">
                                        <i class="bi bi-calendar"></i> Fecha de Nacimiento (opcional)
                                    </label>
                                    <input type="date" class="form-control" id="nuevo_fecha_nacimiento" name="nuevo_fecha_nacimiento">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nuevo_telefono" class="form-label">
                                        <i class="bi bi-telephone"></i> Teléfono (opcional)
                                    </label>
                                    <input type="text" class="form-control" id="nuevo_telefono" name="nuevo_telefono" 
                                           placeholder="Número de teléfono" maxlength="20">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nuevo_email" class="form-label">
                                        <i class="bi bi-envelope"></i> Email (opcional)
                                    </label>
                                    <input type="email" class="form-control" id="nuevo_email" name="nuevo_email" 
                                           placeholder="correo@ejemplo.com" maxlength="255">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Guardar Relación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let familiarActualId = null;

// Abrir modal para agregar relación
function abrirModalRelacion(familiarId, familiarNombre) {
    familiarActualId = familiarId;
    document.getElementById('familiarPrincipalNombre').textContent = familiarNombre;
    
    // Resetear formulario
    document.getElementById('formAgregarRelacion').reset();
    document.getElementById('crearNuevoFamiliar').checked = false;
    toggleOpcionesFamiliar();
    
    // Cargar familiares disponibles
    cargarFamiliaresDisponibles();
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('agregarRelacionModal'));
    modal.show();
}

// Toggle entre familiar existente y nuevo
function toggleOpcionesFamiliar() {
    const crearNuevo = document.getElementById('crearNuevoFamiliar').checked;
    const opcionExistente = document.getElementById('opcionFamiliarExistente');
    const opcionNuevo = document.getElementById('opcionNuevoFamiliar');
    
    if (crearNuevo) {
        opcionExistente.style.display = 'none';
        opcionNuevo.style.display = 'block';
        document.getElementById('familiar_relacionado_id').required = false;
        document.getElementById('nuevo_nombre').required = true;
    } else {
        opcionExistente.style.display = 'block';
        opcionNuevo.style.display = 'none';
        document.getElementById('familiar_relacionado_id').required = true;
        document.getElementById('nuevo_nombre').required = false;
    }
}

// Cargar familiares disponibles
function cargarFamiliaresDisponibles() {
    fetch('/api/familiares')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('familiar_relacionado_id');
                select.innerHTML = '<option value="">Seleccionar familiar existente...</option>';
                
                data.familiares.forEach(familiar => {
                    // No mostrar el familiar actual en la lista
                    if (familiar.id != familiarActualId) {
                        const option = document.createElement('option');
                        option.value = familiar.id;
                        option.textContent = familiar.nombre;
                        select.appendChild(option);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error al cargar familiares:', error);
        });
}

// Actualizar opciones según el tipo de relación
function actualizarOpcionesRelacion() {
    const tipoRelacion = document.getElementById('tipo_relacion').value;
    const descripcion = document.getElementById('descripcion');
    
    // Sugerir descripción según el tipo de relación
    const sugerencias = {
        'padre': 'Padre biológico',
        'madre': 'Madre biológica',
        'hijo': 'Hijo biológico',
        'hija': 'Hija biológica',
        'esposo': 'Esposo',
        'esposa': 'Esposa',
        'pareja': 'Pareja sentimental',
        'hermano': 'Hermano',
        'hermana': 'Hermana',
        'abuelo': 'Abuelo paterno/materno',
        'abuela': 'Abuela paterna/materna',
        'nieto': 'Nieto',
        'nieta': 'Nieta',
        'tio': 'Tío paterno/materno',
        'tia': 'Tía paterna/materna',
        'sobrino': 'Sobrino',
        'sobrina': 'Sobrina',
        'primo': 'Primo',
        'prima': 'Prima',
        'otro': 'Especificar relación'
    };
    
    if (sugerencias[tipoRelacion]) {
        descripcion.placeholder = sugerencias[tipoRelacion];
    }
}

// Guardar relación
function guardarRelacion(event) {
    event.preventDefault();
    
    const formData = new FormData(document.getElementById('formAgregarRelacion'));
    const crearNuevo = document.getElementById('crearNuevoFamiliar').checked;
    
    const data = {
        familiar_id: familiarActualId,
        tipo_relacion: formData.get('tipo_relacion'),
        descripcion: formData.get('descripcion')
    };
    
    if (crearNuevo) {
        // Crear nuevo familiar y relación
        data.crear_nuevo_familiar = true;
        data.nuevo_nombre = formData.get('nuevo_nombre');
        data.nuevo_fecha_nacimiento = formData.get('nuevo_fecha_nacimiento');
        data.nuevo_telefono = formData.get('nuevo_telefono');
        data.nuevo_email = formData.get('nuevo_email');
    } else {
        // Usar familiar existente
        data.familiar_relacionado_id = formData.get('familiar_relacionado_id');
    }
    
    fetch('/relaciones-familiares', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessAlert('¡Éxito!', data.message);
            bootstrap.Modal.getInstance(document.getElementById('agregarRelacionModal')).hide();
            // Recargar la página para mostrar los cambios
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showErrorAlert('Error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorAlert('Error', 'Error al guardar la relación');
    });
}

</script>
@endsection

