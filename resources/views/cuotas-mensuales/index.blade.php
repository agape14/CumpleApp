@extends('layouts.app')

@section('title', 'Cuotas Mensuales')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="text-white">
                <i class="bi bi-cash-coin"></i> Cuotas Mensuales para los Papás
            </h2>
            <div class="btn-group">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generarCuotasModal">
                    <i class="bi bi-plus-circle"></i> Generar Cuotas del Mes
                </button>
                <a href="{{ route('cuotas-mensuales.colectas-especiales') }}" class="btn btn-success">
                    <i class="bi bi-piggy-bank"></i> Colectas Especiales
                </a>
                <a href="{{ route('cuotas-mensuales.historial') }}" class="btn btn-info">
                    <i class="bi bi-clock-history"></i> Ver Historial
                </a>
            </div>
        </div>
    </div>

    <!-- Selector de Período -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('cuotas-mensuales.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="mes" class="form-label">
                            <i class="bi bi-calendar-month"></i> Mes:
                        </label>
                        <select class="form-select" id="mes" name="mes" onchange="this.form.submit()">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $mesActual == $m ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create(null, $m, 1)->locale('es')->monthName }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="anio" class="form-label">
                            <i class="bi bi-calendar"></i> Año:
                        </label>
                        <select class="form-select" id="anio" name="anio" onchange="this.form.submit()">
                            @for($a = now()->year; $a >= 2020; $a--)
                                <option value="{{ $a }}" {{ $anioActual == $a ? 'selected' : '' }}>
                                    {{ $a }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#registrarCuotaModal">
                            <i class="bi bi-plus"></i> Registrar Cuota Individual
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Estadísticas del Mes -->
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-purple">
            <div class="stat-icon">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $totalHermanos }}</div>
                <div class="stat-label">Total Hermanos</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-success">
            <div class="stat-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $cuotasPagadas }}</div>
                <div class="stat-label">Cuotas Pagadas</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-danger">
            <div class="stat-icon">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $cuotasPendientes }}</div>
                <div class="stat-label">Cuotas Pendientes</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-warning">
            <div class="stat-icon">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">S/ {{ number_format($totalRecaudado, 2) }}</div>
                <div class="stat-label">Total Recaudado</div>
            </div>
        </div>
    </div>

    <!-- Listado de Cuotas -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-list-check"></i> Cuotas de {{ Carbon\Carbon::create(null, $mesActual, 1)->locale('es')->monthName }} {{ $anioActual }}
                </h5>
            </div>
            <div class="card-body">
                @if($hermanos->isEmpty())
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>No hay hermanos registrados.</strong>
                        <p class="mb-2">Para usar esta funcionalidad, necesitas:</p>
                        <ol class="mb-0">
                            <li>Tener familiares registrados con parentesco "Hermano" o "Hermana", O</li>
                            <li>Crear relaciones de tipo "hermano/hermana" entre familiares</li>
                        </ol>
                        <hr>
                        <div class="mt-2">
                            <a href="{{ route('familiares.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-people"></i> Ir a Familiares
                            </a>
                            <a href="{{ route('arbol-genealogico.index') }}" class="btn btn-sm btn-info">
                                <i class="bi bi-diagram-3"></i> Crear Relaciones
                            </a>
                        </div>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Hermano</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Fecha de Pago</th>
                                    <th>Comprobante</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hermanos as $hermano)
                                    @php
                                        $cuota = $cuotas->firstWhere('hermano_id', $hermano->id);
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $hermano->nombre }}</strong>
                                            @if($hermano->telefono)
                                                <br><small class="text-muted">
                                                    <i class="bi bi-telephone"></i> {{ $hermano->telefono }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cuota)
                                                <strong class="text-success">S/ {{ number_format($cuota->monto, 2) }}</strong>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cuota)
                                                @if($cuota->estado == 'pagado')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Pagado
                                                    </span>
                                                @elseif($cuota->estado == 'parcial')
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-exclamation-circle"></i> Parcial
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle"></i> Pendiente
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Sin registrar</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cuota && $cuota->fecha_pago)
                                                <i class="bi bi-calendar-check"></i>
                                                {{ $cuota->fecha_pago->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cuota && $cuota->comprobante)
                                                <a href="{{ asset('storage/' . $cuota->comprobante) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="bi bi-file-earmark-check"></i> Ver
                                                </a>
                                            @else
                                                <span class="text-muted">Sin comprobante</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cuota)
                                                @if($cuota->estado != 'pagado')
                                                    <button class="btn btn-sm btn-success" 
                                                            onclick="marcarPagada({{ $cuota->id }})">
                                                        <i class="bi bi-check"></i> Marcar Pagada
                                                    </button>
                                                @endif
                                                <button class="btn btn-sm btn-warning" 
                                                        onclick="editarCuota({{ $cuota->id }})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="{{ route('cuotas-mensuales.destroy', $cuota) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="confirmDeleteWithElement(this, '¿Eliminar esta cuota?', 'Esta acción no se puede deshacer')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-sm btn-primary" 
                                                        onclick="registrarCuotaRapida({{ $hermano->id }})">
                                                    <i class="bi bi-plus"></i> Registrar
                                                </button>
                                            @endif
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

<!-- Modal Generar Cuotas del Mes -->
<div class="modal fade" id="generarCuotasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-plus"></i> Generar Cuotas del Mes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('cuotas-mensuales.generar-mes') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Esta función creará cuotas para todos los hermanos ({{ $totalHermanos }}) para el mes seleccionado.
                    </div>
                    
                    <div class="mb-3">
                        <label for="gen_mes" class="form-label">Mes:</label>
                        <select class="form-select" id="gen_mes" name="mes" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $mesActual == $m ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create(null, $m, 1)->locale('es')->monthName }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="gen_anio" class="form-label">Año:</label>
                        <select class="form-select" id="gen_anio" name="anio" required>
                            @for($a = now()->year; $a >= 2020; $a--)
                                <option value="{{ $a }}" {{ $anioActual == $a ? 'selected' : '' }}>
                                    {{ $a }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="gen_monto" class="form-label">Monto de la Cuota:</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="gen_monto" name="monto" 
                                   step="0.01" min="0" required value="500.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Generar Cuotas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Registrar Cuota Individual -->
<div class="modal fade" id="registrarCuotaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-cash-coin"></i> Registrar Cuota Individual
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('cuotas-mensuales.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="hermano_id" class="form-label">Hermano: *</label>
                            <select class="form-select" id="hermano_id" name="hermano_id" required>
                                <option value="">Seleccionar hermano...</option>
                                @forelse($hermanos as $h)
                                    <option value="{{ $h->id }}">{{ $h->nombre }}</option>
                                @empty
                                    <option value="" disabled>No hay hermanos disponibles</option>
                                @endforelse
                            </select>
                            @if($hermanos->isEmpty())
                                <small class="text-danger">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    No hay hermanos. Agrega familiares con parentesco "Hermano" o crea relaciones de tipo "hermano/hermana".
                                </small>
                            @endif
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="reg_mes" class="form-label">Mes: *</label>
                            <select class="form-select" id="reg_mes" name="mes" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $mesActual == $m ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::create(null, $m, 1)->locale('es')->monthName }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="reg_anio" class="form-label">Año: *</label>
                            <select class="form-select" id="reg_anio" name="anio" required>
                                @for($a = now()->year; $a >= 2020; $a--)
                                    <option value="{{ $a }}" {{ $anioActual == $a ? 'selected' : '' }}>
                                        {{ $a }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="monto" class="form-label">Monto: *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="monto" name="monto" 
                                       step="0.01" min="0" required value="500.00">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="estado" class="form-label">Estado: *</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="pagado" selected>Pagado</option>
                                <option value="parcial">Parcial</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="fecha_pago" class="form-label">Fecha de Pago:</label>
                            <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" value="{{ now()->toDateString() }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="comprobante" class="form-label">Comprobante:</label>
                            <input type="file" class="form-control" id="comprobante" name="comprobante" 
                                   accept="image/*,.pdf">
                            <small class="text-muted">JPG, PNG o PDF. Máximo 5MB</small>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="notas" class="form-label">Notas:</label>
                            <textarea class="form-control" id="notas" name="notas" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Guardar Cuota
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Marcar como Pagada -->
<div class="modal fade" id="marcarPagadaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle"></i> Marcar Cuota como Pagada
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formMarcarPagada" enctype="multipart/form-data">
                    <input type="hidden" id="cuota_id" name="cuota_id">
                    
                    <div class="mb-3">
                        <label for="fecha_pago_modal" class="form-label">Fecha de Pago:</label>
                        <input type="date" class="form-control" id="fecha_pago_modal" name="fecha_pago" 
                               value="{{ now()->toDateString() }}">
                    </div>

                    <div class="mb-3">
                        <label for="comprobante_modal" class="form-label">Comprobante de Pago:</label>
                        <input type="file" class="form-control" id="comprobante_modal" name="comprobante" 
                               accept="image/*,.pdf">
                        <small class="text-muted">JPG, PNG o PDF. Máximo 5MB</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="guardarPagada()">
                    <i class="bi bi-check-circle"></i> Marcar como Pagada
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let cuotaActualId = null;

// Registrar cuota rápida
function registrarCuotaRapida(hermanoId) {
    document.getElementById('hermano_id').value = hermanoId;
    const modal = new bootstrap.Modal(document.getElementById('registrarCuotaModal'));
    modal.show();
}

// Marcar como pagada
function marcarPagada(cuotaId) {
    cuotaActualId = cuotaId;
    const modal = new bootstrap.Modal(document.getElementById('marcarPagadaModal'));
    modal.show();
}

// Guardar cuota como pagada
function guardarPagada() {
    const form = document.getElementById('formMarcarPagada');
    const formData = new FormData(form);

    fetch(`/cuotas-mensuales/${cuotaActualId}/marcar-pagada`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessAlert('¡Éxito!', data.message);
            bootstrap.Modal.getInstance(document.getElementById('marcarPagadaModal')).hide();
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showErrorAlert('Error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorAlert('Error', 'Error al marcar la cuota como pagada');
    });
}

// Editar cuota
function editarCuota(cuotaId) {
    showInfoAlert('Próximamente', 'La función de editar cuota estará disponible pronto');
}
</script>
@endsection

