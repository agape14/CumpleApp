@extends('layouts.app')

@section('title', 'Historial de Regalos - ' . $familiar->nombre)

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <a href="{{ route('familiares.show', $familiar) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver a {{ $familiar->nombre }}
        </a>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-gift-fill"></i> Historial de Regalos - {{ $familiar->nombre }}
                </h4>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarRegaloModal">
                    <i class="bi bi-plus-lg"></i> Agregar Regalo
                </button>
            </div>
            <div class="card-body">
                @if($regalos->isEmpty())
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill"></i>
                        No hay regalos registrados aún. ¡Agrega el primer regalo dado a {{ $familiar->nombre }}!
                    </div>
                @else
                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stat-card bg-purple">
                                <div class="stat-icon">
                                    <i class="bi bi-gift-fill"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number">{{ $regalos->count() }}</div>
                                    <div class="stat-label">Regalos Dados</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card bg-success">
                                <div class="stat-icon">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number">S/ {{ number_format($regalos->sum('precio'), 2) }}</div>
                                    <div class="stat-label">Total Gastado</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card bg-warning">
                                <div class="stat-icon">
                                    <i class="bi bi-calculator"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number">S/ {{ number_format($regalos->avg('precio') ?? 0, 2) }}</div>
                                    <div class="stat-label">Promedio</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card bg-info">
                                <div class="stat-icon">
                                    <i class="bi bi-calendar-event-fill"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number">{{ $regalos->first() ? $regalos->first()->fecha_entrega->format('Y') : '-' }}</div>
                                    <div class="stat-label">Último Regalo</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de regalos -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Regalo</th>
                                    <th>Fecha</th>
                                    <th>Ocasión</th>
                                    <th>Precio</th>
                                    <th>Lugar de Compra</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($regalos as $regalo)
                                <tr>
                                    <td>
                                        <strong>{{ $regalo->nombre_regalo }}</strong>
                                        @if($regalo->descripcion)
                                            <br><small class="text-muted">{{ Str::limit($regalo->descripcion, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar3"></i>
                                        {{ $regalo->fecha_entrega->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @php
                                            $badgeColors = [
                                                'cumpleaños' => 'primary',
                                                'navidad' => 'success',
                                                'aniversario' => 'danger',
                                                'graduacion' => 'warning',
                                                'otro' => 'secondary'
                                            ];
                                            $color = $badgeColors[$regalo->ocasion] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">
                                            {{ ucfirst($regalo->ocasion) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($regalo->precio)
                                            <strong class="text-success">S/ {{ number_format($regalo->precio, 2) }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $regalo->lugar_compra ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="verDetalle({{ $regalo->id }})" title="Ver detalle">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editarRegalo({{ $regalo->id }})" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('regalos-dados.destroy', $regalo) }}" method="POST" class="d-inline" id="deleteForm{{ $regalo->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" title="Eliminar"
                                                onclick="confirmDeleteWithElement(this, '¿Estás seguro de eliminar este regalo del historial?', 'Esta acción no se puede deshacer')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Regalo -->
<div class="modal fade" id="agregarRegaloModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-gift-fill"></i> Agregar Regalo Dado
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('regalos-dados.store', $familiar) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre_regalo" class="form-label">Nombre del Regalo: *</label>
                            <input type="text" class="form-control" id="nombre_regalo" name="nombre_regalo" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="fecha_entrega" class="form-label">Fecha de Entrega: *</label>
                            <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="ocasion" class="form-label">Ocasión: *</label>
                            <select class="form-select" id="ocasion" name="ocasion" required>
                                <option value="cumpleaños">🎂 Cumpleaños</option>
                                <option value="navidad">🎄 Navidad</option>
                                <option value="aniversario">💕 Aniversario</option>
                                <option value="graduacion">🎓 Graduación</option>
                                <option value="otro">🎁 Otro</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="precio" class="form-label">Precio:</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="lugar_compra" class="form-label">Lugar de Compra:</label>
                            <input type="text" class="form-control" id="lugar_compra" name="lugar_compra" 
                                placeholder="Ej: Amazon, Liverpool, etc.">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="foto" class="form-label">Foto del Regalo:</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            <small class="text-muted">Tamaño máximo: 2MB</small>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="descripcion" class="form-label">Descripción:</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="notas" class="form-label">Notas Adicionales:</label>
                            <textarea class="form-control" id="notas" name="notas" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Regalo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Detalle -->
<div class="modal fade" id="verDetalleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-gift-fill"></i> Detalle del Regalo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleContent">
                <!-- Se llenará con JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const regalosData = @json($regalos);

function verDetalle(regaloId) {
    const regalo = regalosData.find(r => r.id === regaloId);
    if (!regalo) return;

    const content = document.getElementById('detalleContent');
    content.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6><i class="bi bi-gift"></i> Nombre:</h6>
                <p>${regalo.nombre_regalo}</p>
            </div>
            <div class="col-md-6">
                <h6><i class="bi bi-calendar3"></i> Fecha:</h6>
                <p>${new Date(regalo.fecha_entrega).toLocaleDateString('es-ES')}</p>
            </div>
            <div class="col-md-6">
                <h6><i class="bi bi-tag"></i> Ocasión:</h6>
                <p>${regalo.ocasion}</p>
            </div>
            <div class="col-md-6">
                <h6><i class="bi bi-cash"></i> Precio:</h6>
                <p>${regalo.precio ? '$' + parseFloat(regalo.precio).toFixed(2) : 'No especificado'}</p>
            </div>
            <div class="col-md-12">
                <h6><i class="bi bi-shop"></i> Lugar de Compra:</h6>
                <p>${regalo.lugar_compra || 'No especificado'}</p>
            </div>
            <div class="col-md-12">
                <h6><i class="bi bi-card-text"></i> Descripción:</h6>
                <p>${regalo.descripcion || 'Sin descripción'}</p>
            </div>
            <div class="col-md-12">
                <h6><i class="bi bi-sticky"></i> Notas:</h6>
                <p>${regalo.notas || 'Sin notas'}</p>
            </div>
            ${regalo.foto ? `
                <div class="col-md-12">
                    <h6><i class="bi bi-image"></i> Foto:</h6>
                    <img src="/storage/${regalo.foto}" class="img-fluid rounded" alt="Foto del regalo">
                </div>
            ` : ''}
        </div>
    `;

    const modal = new bootstrap.Modal(document.getElementById('verDetalleModal'));
    modal.show();
}

function editarRegalo(regaloId) {
    // Implementar edición si se requiere
    showInfoAlert('Próximamente', 'La función de edición de regalos estará disponible pronto');
}

</script>
@endsection

