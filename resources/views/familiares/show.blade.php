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
                    <a href="tel:{{ $familiar->telefono }}" class="btn btn-success btn-sm">
                        <i class="bi bi-telephone-fill"></i> Llamar
                    </a>
                    <button class="btn btn-success btn-sm" onclick="enviarWhatsApp({{ $familiar->id }})">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                    </button>
                    @endif
                    @if($familiar->email)
                    <a href="mailto:{{ $familiar->email }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-envelope-fill"></i> Email
                    </a>
                    @endif
                    <button class="btn btn-info btn-sm" onclick="exportarGoogleCalendar({{ $familiar->id }})">
                        <i class="bi bi-calendar-plus"></i> Exportar Calendar
                    </button>
                    <a href="{{ route('regalos-dados.index', $familiar) }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-gift"></i> Historial Regalos
                    </a>
                    <a href="{{ route('familiares.edit', $familiar) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil-fill"></i> Editar
                    </a>
                    <form action="{{ route('familiares.destroy', $familiar) }}" 
                          method="POST" 
                          class="d-inline"
                          id="deleteForm{{ $familiar->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm"
                                onclick="confirmDeleteWithElement(this, '¿Estás seguro de eliminar a {{ $familiar->nombre }}?', 'Esta acción también eliminará todas sus ideas de regalos.')">
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
                                        Precio estimado: S/ {{ number_format($idea->precio_estimado, 2) }}
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
                                      id="deleteIdeaForm{{ $idea->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            class="btn btn-sm btn-danger"
                                            title="Eliminar"
                                            onclick="confirmDeleteWithElement(this, '¿Eliminar esta idea de regalo?', 'Esta acción no se puede deshacer')">
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
                            <h5 class="text-warning">S/ {{ number_format($precioTotal, 2) }}</h5>
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

    <!-- Relaciones Familiares -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-diagram-3"></i> Relaciones Familiares</span>
                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addRelacionModal">
                    <i class="bi bi-plus-circle-fill"></i> Agregar
                </button>
            </div>
            <div class="card-body">
                <div id="relacionesContainer">
                    <div class="text-center py-4">
                        <i class="bi bi-diagram-3" style="font-size: 3rem; color: #cbd5e1;"></i>
                        <p class="text-muted mt-2 mb-0">Cargando relaciones...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recordatorios -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-alarm"></i> Recordatorios Personalizados</span>
                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addRecordatorioModal">
                    <i class="bi bi-plus-circle-fill"></i> Agregar
                </button>
            </div>
            <div class="card-body">
                <div id="recordatoriosContainer">
                    <div class="text-center py-4">
                        <i class="bi bi-alarm" style="font-size: 3rem; color: #cbd5e1;"></i>
                        <p class="text-muted mt-2 mb-0">Cargando recordatorios...</p>
                    </div>
                </div>
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

<!-- Modal para agregar relación familiar -->
<div class="modal fade" id="addRelacionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title">
                    <i class="bi bi-diagram-3"></i> Agregar Relación Familiar
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAgregarRelacion">
                    <div class="mb-3">
                        <label for="familiar_relacionado_id" class="form-label">Familiar:</label>
                        <select class="form-select" id="familiar_relacionado_id" name="familiar_relacionado_id" required>
                            <option value="">Seleccionar...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_relacion" class="form-label">Tipo de Relación:</label>
                        <select class="form-select" id="tipo_relacion" name="tipo_relacion" required>
                            <option value="">Seleccionar...</option>
                            <option value="hijo">👦 Hijo</option>
                            <option value="hija">👧 Hija</option>
                            <option value="esposo">👨 Esposo</option>
                            <option value="esposa">👩 Esposa</option>
                            <option value="pareja">💑 Pareja</option>
                            <option value="padre">👴 Padre</option>
                            <option value="madre">👵 Madre</option>
                            <option value="hermano">👦 Hermano</option>
                            <option value="hermana">👧 Hermana</option>
                            <option value="otro">➕ Otro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion_relacion" class="form-label">Descripción (opcional):</label>
                        <input type="text" class="form-control" id="descripcion_relacion" name="descripcion">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarRelacion()">
                    <i class="bi bi-save-fill"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar recordatorio -->
<div class="modal fade" id="addRecordatorioModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title">
                    <i class="bi bi-alarm"></i> Agregar Recordatorio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAgregarRecordatorio">
                    <div class="mb-3">
                        <label for="dias_antes" class="form-label">Días de Anticipación:</label>
                        <input type="number" class="form-control" id="dias_antes" name="dias_antes" value="7" min="1" max="365" required>
                        <small class="text-muted">Cuántos días antes del cumpleaños deseas recibir el recordatorio</small>
                    </div>
                    <div class="mb-3">
                        <label for="hora_envio" class="form-label">Hora de Envío:</label>
                        <input type="time" class="form-control" id="hora_envio" name="hora_envio" value="09:00" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enviar_email" name="enviar_email" checked>
                            <label class="form-check-label" for="enviar_email">
                                <i class="bi bi-envelope"></i> Enviar por Email
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enviar_whatsapp" name="enviar_whatsapp">
                            <label class="form-check-label" for="enviar_whatsapp">
                                <i class="bi bi-whatsapp"></i> Enviar por WhatsApp
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="mensaje_personalizado" class="form-label">Mensaje Personalizado (opcional):</label>
                        <textarea class="form-control" id="mensaje_personalizado" name="mensaje_personalizado" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarRecordatorio()">
                    <i class="bi bi-save-fill"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const familiarId = {{ $familiar->id }};

// Cargar datos al iniciar
document.addEventListener('DOMContentLoaded', function() {
    cargarRelaciones();
    cargarRecordatorios();
    cargarFamiliaresDisponibles();
});

// Cargar relaciones familiares
function cargarRelaciones() {
    fetch(`/familiares/${familiarId}/relaciones`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('relacionesContainer');
            if (data.success && data.relaciones.length > 0) {
                let html = '<div class="list-group">';
                data.relaciones.forEach(rel => {
                    html += `
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${rel.familiar.nombre}</strong>
                                <br><small class="text-muted">${rel.tipo}</small>
                                ${rel.descripcion ? `<br><small>${rel.descripcion}</small>` : ''}
                            </div>
                            <button class="btn btn-sm btn-danger" onclick="eliminarRelacion(${rel.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-diagram-3" style="font-size: 3rem; color: #cbd5e1;"></i>
                        <p class="text-muted mt-2 mb-0">No hay relaciones familiares registradas</p>
                    </div>
                `;
            }
        });
}

// Cargar recordatorios
function cargarRecordatorios() {
    fetch(`/familiares/${familiarId}/recordatorios`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('recordatoriosContainer');
            if (data.success && data.recordatorios.length > 0) {
                let html = '<div class="list-group">';
                data.recordatorios.forEach(rec => {
                    html += `
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${rec.dias_antes} días antes</strong>
                                    <br><small class="text-muted">A las ${rec.hora_envio}</small>
                                    ${rec.enviar_email ? '<i class="bi bi-envelope text-primary"></i>' : ''}
                                    ${rec.enviar_whatsapp ? '<i class="bi bi-whatsapp text-success"></i>' : ''}
                                    ${rec.activo ? '<span class="badge bg-success ms-2">Activo</span>' : '<span class="badge bg-secondary ms-2">Inactivo</span>'}
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-sm ${rec.activo ? 'btn-warning' : 'btn-success'}" 
                                        onclick="toggleRecordatorio(${rec.id})">
                                        <i class="bi bi-${rec.activo ? 'pause' : 'play'}-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="eliminarRecordatorio(${rec.id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-alarm" style="font-size: 3rem; color: #cbd5e1;"></i>
                        <p class="text-muted mt-2 mb-0">No hay recordatorios configurados</p>
                    </div>
                `;
            }
        });
}

// Cargar familiares disponibles para relaciones
function cargarFamiliaresDisponibles() {
    fetch('/api/familiares')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('familiar_relacionado_id');
                select.innerHTML = '<option value="">Seleccionar...</option>';
                
                data.familiares.forEach(familiar => {
                    // No mostrar el familiar actual en la lista
                    if (familiar.id != familiarId) {
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

// Guardar relación
function guardarRelacion() {
    const form = document.getElementById('formAgregarRelacion');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.familiar_id = familiarId;

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
            bootstrap.Modal.getInstance(document.getElementById('addRelacionModal')).hide();
            cargarRelaciones();
            form.reset();
        } else {
            showErrorAlert('Error', data.message);
        }
    });
}

// Eliminar relación
function eliminarRelacion(id) {
    showDeleteConfirmAlert('¿Eliminar esta relación?', 'Esta acción no se puede deshacer').then((result) => {
        if (result.isConfirmed) {
            fetch(`/relaciones-familiares/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessAlert('¡Eliminado!', data.message);
                    cargarRelaciones();
                } else {
                    showErrorAlert('Error', data.message);
                }
            });
        }
    });
}

// Guardar recordatorio
function guardarRecordatorio() {
    const form = document.getElementById('formAgregarRecordatorio');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Convertir checkboxes a boolean
    data.enviar_email = document.getElementById('enviar_email').checked;
    data.enviar_whatsapp = document.getElementById('enviar_whatsapp').checked;
    data.activo = true;

    fetch(`/familiares/${familiarId}/recordatorios`, {
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
            bootstrap.Modal.getInstance(document.getElementById('addRecordatorioModal')).hide();
            cargarRecordatorios();
            form.reset();
        } else {
            showErrorAlert('Error', data.message);
        }
    });
}

// Toggle recordatorio activo/inactivo
function toggleRecordatorio(id) {
    fetch(`/recordatorios/${id}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarRecordatorios();
        }
    });
}

// Eliminar recordatorio
function eliminarRecordatorio(id) {
    showDeleteConfirmAlert('¿Eliminar este recordatorio?', 'Esta acción no se puede deshacer').then((result) => {
        if (result.isConfirmed) {
    
            fetch(`/recordatorios/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessAlert('¡Eliminado!', data.message);
                    cargarRecordatorios();
                } else {
                    showErrorAlert('Error', data.message);
                }
            });
        }
    });
}

// Exportar a Google Calendar
function exportarGoogleCalendar(familiarId) {
    fetch(`/google-calendar/exportar/${familiarId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.enlace) {
                window.open(data.enlace, '_blank');
                showSuccessAlert('¡Exportado!', 'El cumpleaños se ha exportado a Google Calendar');
            } else {
                showErrorAlert('Error', data.message);
            }
        });
}

// Enviar WhatsApp
function enviarWhatsApp(familiarId) {
    const mensaje = prompt('Mensaje personalizado (opcional):');
    
    fetch(`/whatsapp/enviar/${familiarId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ mensaje: mensaje || null })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessAlert('¡Enviado!', data.message);
        } else {
            showErrorAlert('Error', data.message);
        }
    });
}

</script>
@endsection

