@extends('layouts.app')

@section('title', 'Colectas Especiales')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="text-white">
                <i class="bi bi-piggy-bank"></i> Colectas Especiales
            </h2>
            <div class="btn-group">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearColectaModal">
                    <i class="bi bi-plus-circle"></i> Nueva Colecta
                </button>
                <a href="{{ route('cuotas-mensuales.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a Cuotas Mensuales
                </a>
            </div>
        </div>
    </div>

    @if($colectas->isEmpty())
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-piggy-bank" style="font-size: 4rem; color: #cbd5e1;"></i>
                    <h4 class="mt-3 text-muted">No hay colectas especiales activas</h4>
                    <p class="text-muted">Las colectas especiales son para eventos específicos como Navidad, Cumpleaños de Papás, etc.</p>
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#crearColectaModal">
                        <i class="bi bi-plus-circle"></i> Crear Primera Colecta
                    </button>
                </div>
            </div>
        </div>
    @else
        @foreach($colectas as $concepto => $cuotasColecta)
            @php
                // Total recaudado (solo pagos realizados)
                $totalRecaudado = $cuotasColecta->where('estado', 'pagado')->sum('monto');
                
                // Meta total (objetivo general de la colecta)
                $metaTotal = $cuotasColecta->first()->meta_total ?? 0;
                
                // Total asignado: obtener solo la cuota original de cada hermano (no los pagos parciales)
                // Agrupar por hermano_id y tomar solo la cuota más antigua (la asignada originalmente)
                $cuotasOriginales = $cuotasColecta->groupBy('hermano_id')->map(function($cuotasHermano) {
                    // Ordenar por created_at y tomar la primera (la original)
                    return $cuotasHermano->sortBy('created_at')->first();
                });
                $totalAsignado = $cuotasOriginales->sum('monto');
                
                // Porcentaje basado en el total asignado (no en la meta)
                $porcentaje = $totalAsignado > 0 ? ($totalRecaudado / $totalAsignado) * 100 : 0;
                
                $fechaLimite = $cuotasColecta->first()->fecha_limite;
                $diasRestantes = $fechaLimite ? now()->diffInDays($fechaLimite, false) : null;
            @endphp

            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-gift"></i> {{ $concepto }}
                            </h5>
                            <div>
                                @if($diasRestantes !== null)
                                    @if($diasRestantes > 0)
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-clock"></i> {{ $diasRestantes }} días restantes
                                        </span>
                                    @elseif($diasRestantes == 0)
                                        <span class="badge bg-warning">
                                            <i class="bi bi-exclamation-triangle"></i> ¡Vence hoy!
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Vencida
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Barra de Progreso -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <span><strong>Meta General:</strong> S/ {{ number_format($metaTotal, 2) }}</span>
                                    <br>
                                    <small class="text-muted">Total Asignado: S/ {{ number_format($totalAsignado, 2) }}</small>
                                </div>
                                <div class="text-end">
                                    <span><strong>Recaudado:</strong> S/ {{ number_format($totalRecaudado, 2) }}</span>
                                    <br>
                                    <small class="text-muted">Pendiente: S/ {{ number_format(max(0, $totalAsignado - $totalRecaudado), 2) }}</small>
                                </div>
                            </div>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar {{ $porcentaje >= 100 ? 'bg-success' : ($porcentaje >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                     role="progressbar" 
                                     style="width: {{ min($porcentaje, 100) }}%">
                                    {{ number_format(min($porcentaje, 100), 1) }}%
                                </div>
                            </div>
                            @if($fechaLimite)
                                <small class="text-muted">
                                    <i class="bi bi-calendar-event"></i> 
                                    Fecha límite: {{ $fechaLimite->format('d/m/Y') }}
                                </small>
                            @endif
                        </div>

                        <!-- Tabla de Aportes -->
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Hermano</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Comprobante</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hermanos as $hermano)
                                        @php
                                            // Obtener todas las cuotas/pagos de este hermano para esta colecta
                                            $cuotasHermano = $cuotasColecta->where('hermano_id', $hermano->id);
                                            
                                            // Cuota original (la primera creada)
                                            $cuotaOriginal = $cuotasHermano->sortBy('created_at')->first();
                                            
                                            // Total pagado (sumar todos los pagos con estado 'pagado')
                                            $totalPagado = $cuotasHermano->where('estado', 'pagado')->sum('monto');
                                            
                                            // Monto asignado
                                            $montoAsignado = $cuotaOriginal ? $cuotaOriginal->monto : 0;
                                            
                                            // Estado: completado si el total pagado >= monto asignado
                                            $completado = $totalPagado >= $montoAsignado;
                                            
                                            // Último pago realizado
                                            $ultimoPago = $cuotasHermano->where('estado', 'pagado')->sortByDesc('fecha_pago')->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $hermano->nombre }}</td>
                                            <td>
                                                @if($cuotaOriginal)
                                                    <div>
                                                        <strong>S/ {{ number_format($montoAsignado, 2) }}</strong>
                                                    </div>
                                                    @if($totalPagado > 0 && !$completado)
                                                        <small class="text-muted">
                                                            Pagado: S/ {{ number_format($totalPagado, 2) }}
                                                        </small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($cuotaOriginal)
                                                    @if($completado)
                                                        <span class="badge bg-success">Pagado</span>
                                                    @elseif($totalPagado > 0)
                                                        <span class="badge bg-warning">Parcial</span>
                                                    @else
                                                        <span class="badge bg-danger">Pendiente</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Sin asignar</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ultimoPago && $ultimoPago->fecha_pago)
                                                    {{ $ultimoPago->fecha_pago->format('d/m/Y') }}
                                                    @if($cuotasHermano->where('estado', 'pagado')->count() > 1)
                                                        <br><small class="text-muted">({{ $cuotasHermano->where('estado', 'pagado')->count() }} pagos)</small>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($ultimoPago && $ultimoPago->comprobante)
                                                    <a href="{{ asset('storage/' . $ultimoPago->comprobante) }}" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="bi bi-file-earmark"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$completado)
                                                    <button class="btn btn-sm btn-success" 
                                                            onclick="registrarAporte('{{ $concepto }}', {{ $hermano->id }}, '{{ $hermano->nombre }}')">
                                                        <i class="bi bi-plus"></i> Aportar
                                                    </button>
                                                @endif
                                                @if($cuotaOriginal)
                                                    <button type="button" class="btn btn-sm btn-info" 
                                                            onclick="verHistorial({{ $hermano->id }}, '{{ $hermano->nombre }}', '{{ $concepto }}')"
                                                            title="Ver historial de pagos">
                                                        <i class="bi bi-clock-history"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    @php
                                        // Contar hermanos que completaron su cuota
                                        $hermanosCompletados = 0;
                                        $totalPagosRealizados = 0;
                                        foreach($hermanos as $hermano) {
                                            $cuotasHermano = $cuotasColecta->where('hermano_id', $hermano->id);
                                            $cuotaOriginal = $cuotasHermano->sortBy('created_at')->first();
                                            $totalPagadoHermano = $cuotasHermano->where('estado', 'pagado')->sum('monto');
                                            $montoAsignado = $cuotaOriginal ? $cuotaOriginal->monto : 0;
                                            
                                            if ($totalPagadoHermano >= $montoAsignado && $montoAsignado > 0) {
                                                $hermanosCompletados++;
                                            }
                                            
                                            $totalPagosRealizados += $cuotasHermano->where('estado', 'pagado')->count();
                                        }
                                    @endphp
                                    <tr class="table-active">
                                        <td><strong>Total Recaudado:</strong></td>
                                        <td><strong>S/ {{ number_format($totalRecaudado, 2) }}</strong></td>
                                        <td colspan="4">
                                            <span class="badge bg-success">
                                                {{ $hermanosCompletados }} de {{ $hermanos->count() }} completaron
                                            </span>
                                            @if($totalPagosRealizados > 0)
                                                <span class="badge bg-info">
                                                    {{ $totalPagosRealizados }} pago(s) realizados
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<!-- Modal para Crear Nueva Colecta -->
<div class="modal fade" id="crearColectaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-piggy-bank"></i> Crear Nueva Colecta Especial
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('cuotas-mensuales.crear-colecta') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Las colectas especiales son para eventos únicos (Navidad, Cumpleaños, etc.)
                    </div>

                    <div class="mb-3">
                        <label for="concepto" class="form-label">
                            <i class="bi bi-tag"></i> Concepto/Motivo: *
                        </label>
                        <select class="form-select" id="concepto" name="concepto" onchange="actualizarConcepto()" required>
                            <option value="">Seleccionar concepto...</option>
                            <option value="🎄 Navidad {{ now()->year }}">🎄 Navidad {{ now()->year }}</option>
                            <option value="🎂 Cumpleaños de Mamá {{ now()->year }}">🎂 Cumpleaños de Mamá {{ now()->year }}</option>
                            <option value="🎂 Cumpleaños de Papá {{ now()->year }}">🎂 Cumpleaños de Papá {{ now()->year }}</option>
                            <option value="🏥 Gastos Médicos">🏥 Gastos Médicos</option>
                            <option value="🏠 Reparaciones de Casa">🏠 Reparaciones de Casa</option>
                            <option value="🎊 Aniversario de Bodas">🎊 Aniversario de Bodas</option>
                            <option value="otro">✏️ Otro (personalizado)</option>
                        </select>
                        <input type="text" class="form-control mt-2" id="concepto_personalizado" 
                               placeholder="Escribe el concepto personalizado" style="display: none;">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="meta_total" class="form-label">
                                <i class="bi bi-bullseye"></i> Meta Total: *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" class="form-control" id="meta_total" name="meta_total" 
                                       step="0.01" min="0" required placeholder="5000.00">
                            </div>
                            <small class="text-muted">Total que se necesita recaudar</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="monto_individual" class="form-label">
                                <i class="bi bi-cash"></i> Aporte por Hermano: *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" class="form-control" id="monto_individual" name="monto_individual" 
                                       step="0.01" min="0" required placeholder="500.00">
                            </div>
                            <small class="text-muted">Sugerido: Meta ÷ {{ $hermanos->count() }} hermanos</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_limite" class="form-label">
                            <i class="bi bi-calendar-event"></i> Fecha Límite: *
                        </label>
                        <input type="date" class="form-control" id="fecha_limite" name="fecha_limite" required>
                    </div>

                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i>
                        Se creará una cuota de <strong id="preview_monto">S/ 0.00</strong> para cada uno de los <strong>{{ $hermanos->count() }}</strong> hermanos.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Crear Colecta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Registrar Aporte -->
<div class="modal fade" id="registrarAporteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-cash-coin"></i> Registrar Aporte
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formRegistrarAporte" enctype="multipart/form-data">
                    <input type="hidden" id="aporte_hermano_id" name="hermano_id">
                    <input type="hidden" id="aporte_concepto" name="concepto">

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Hermano:</strong> <span id="aporte_hermano_nombre"></span><br>
                        <strong>Colecta:</strong> <span id="aporte_colecta_nombre"></span>
                    </div>

                    <div class="alert alert-warning" id="aporte_info_monto" style="display: none;">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Monto Asignado:</strong> S/ <span id="monto_asignado">0.00</span><br>
                        <strong>Ya Pagado:</strong> S/ <span id="monto_pagado">0.00</span><br>
                        <strong>Pendiente:</strong> S/ <span id="monto_pendiente">0.00</span>
                    </div>

                    <div class="mb-3">
                        <label for="aporte_monto" class="form-label">
                            <i class="bi bi-cash"></i> Monto a Aportar: *
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">S/</span>
                            <input type="number" class="form-control" id="aporte_monto" name="monto" 
                                   step="0.01" min="0.01" required>
                        </div>
                        <small class="text-muted">Puedes hacer pagos parciales hasta completar tu cuota</small>
                    </div>

                    <div class="mb-3">
                        <label for="aporte_comprobante" class="form-label">
                            <i class="bi bi-file-earmark"></i> Comprobante de Pago:
                        </label>
                        <input type="file" class="form-control" id="aporte_comprobante" name="comprobante" 
                               accept="image/*,.pdf">
                        <small class="text-muted">JPG, PNG o PDF. Máximo 5MB</small>
                    </div>

                    <div class="mb-3">
                        <label for="aporte_notas" class="form-label">
                            <i class="bi bi-sticky"></i> Notas (opcional):
                        </label>
                        <textarea class="form-control" id="aporte_notas" name="notas" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="guardarAporte()">
                    <i class="bi bi-check-circle"></i> Registrar Aporte
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Actualizar concepto personalizado
function actualizarConcepto() {
    const select = document.getElementById('concepto');
    const inputPersonalizado = document.getElementById('concepto_personalizado');
    
    if (select.value === 'otro') {
        inputPersonalizado.style.display = 'block';
        inputPersonalizado.required = true;
        select.name = '';
        inputPersonalizado.name = 'concepto';
    } else {
        inputPersonalizado.style.display = 'none';
        inputPersonalizado.required = false;
        select.name = 'concepto';
        inputPersonalizado.name = '';
    }
}

// Actualizar preview del monto
document.getElementById('monto_individual')?.addEventListener('input', function() {
    const monto = this.value || 0;
    document.getElementById('preview_monto').textContent = 'S/ ' + parseFloat(monto).toFixed(2);
});

// Calcular monto sugerido
document.getElementById('meta_total')?.addEventListener('input', function() {
    const meta = parseFloat(this.value) || 0;
    const hermanos = {{ $hermanos->count() }};
    const sugerido = meta / hermanos;
    document.getElementById('monto_individual').value = sugerido.toFixed(2);
    document.getElementById('preview_monto').textContent = 'S/ ' + sugerido.toFixed(2);
});

// Registrar aporte
function registrarAporte(concepto, hermanoId, hermanoNombre) {
    document.getElementById('aporte_hermano_id').value = hermanoId;
    document.getElementById('aporte_concepto').value = concepto;
    document.getElementById('aporte_hermano_nombre').textContent = hermanoNombre;
    document.getElementById('aporte_colecta_nombre').textContent = concepto;
    
    // Obtener información de la cuota y calcular pendiente
    const cuotasData = @json($colectas);
    const colecta = cuotasData[concepto];
    
    if (colecta) {
        // Buscar la cuota del hermano
        const cuotaHermano = colecta.find(c => c.hermano_id == hermanoId);
        
        if (cuotaHermano) {
            const montoAsignado = parseFloat(cuotaHermano.monto) || 0;
            
            // Calcular el monto ya pagado (sumar todos los pagos del hermano para esta colecta)
            const montoPagado = colecta
                .filter(c => c.hermano_id == hermanoId && c.estado === 'pagado')
                .reduce((sum, c) => sum + parseFloat(c.monto || 0), 0);
            
            const montoPendiente = montoAsignado - montoPagado;
            
            // Mostrar información
            document.getElementById('monto_asignado').textContent = montoAsignado.toFixed(2);
            document.getElementById('monto_pagado').textContent = montoPagado.toFixed(2);
            document.getElementById('monto_pendiente').textContent = montoPendiente.toFixed(2);
            document.getElementById('aporte_info_monto').style.display = 'block';
            
            // Establecer el valor máximo del input al monto pendiente
            document.getElementById('aporte_monto').max = montoPendiente.toFixed(2);
            document.getElementById('aporte_monto').value = montoPendiente.toFixed(2);
        }
    }
    
    const modal = new bootstrap.Modal(document.getElementById('registrarAporteModal'));
    modal.show();
}

// Guardar aporte
function guardarAporte() {
    const form = document.getElementById('formRegistrarAporte');
    const formData = new FormData(form);

    fetch('/cuotas-mensuales/registrar-aporte', {
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
            bootstrap.Modal.getInstance(document.getElementById('registrarAporteModal')).hide();
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showErrorAlert('Error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorAlert('Error', 'Error al registrar el aporte');
    });
}

// Ver historial de pagos
function verHistorial(hermanoId, hermanoNombre, concepto) {
    const cuotasData = @json($colectas);
    const colecta = cuotasData[concepto];
    
    if (!colecta) {
        showErrorAlert('Error', 'No se encontraron datos de la colecta');
        return;
    }
    
    // Filtrar todos los pagos y cuota original de este hermano
    const cuotasHermano = colecta.filter(c => c.hermano_id == hermanoId);
    const cuotaOriginal = cuotasHermano.sort((a, b) => new Date(a.created_at) - new Date(b.created_at))[0];
    const pagosHermano = cuotasHermano.filter(c => c.estado === 'pagado');
    
    if (!cuotaOriginal) {
        showErrorAlert('Error', 'No se encontró información de la cuota para este hermano.');
        return;
    }
    
    const montoAsignado = parseFloat(cuotaOriginal.monto) || 0;
    
    // Construir HTML de la tabla de historial
    let html = `
        <div class="modal fade" id="modalHistorial" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-clock-history"></i> Historial de Pagos - ${hermanoNombre}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Colecta:</strong> ${concepto}<br>
                            <strong>Cuota Asignada:</strong> S/ ${montoAsignado.toFixed(2)}
                        </div>
                        
                        ${pagosHermano.length === 0 ? '<div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> Aún no hay pagos registrados.</div>' : ''}
                        
                        ${pagosHermano.length > 0 ? `<table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha y Hora</th>
                                    <th>Monto</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>
                            <tbody>` : ''}`;
    
    let totalPagado = 0;
    pagosHermano.forEach((pago, index) => {
        // Formatear fecha a dd/mm/yyyy HH:mm
        let fechaFormateada = '-';
        if (pago.fecha_pago) {
            const fechaObj = new Date(pago.fecha_pago);
            const dia = String(fechaObj.getDate()).padStart(2, '0');
            const mes = String(fechaObj.getMonth() + 1).padStart(2, '0');
            const anio = fechaObj.getFullYear();
            const horas = String(fechaObj.getHours()).padStart(2, '0');
            const minutos = String(fechaObj.getMinutes()).padStart(2, '0');
            fechaFormateada = `${dia}/${mes}/${anio} ${horas}:${minutos}`;
        }
        
        const monto = parseFloat(pago.monto || 0);
        totalPagado += monto;
        const notas = pago.notas || '-';
        
        html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${fechaFormateada}</td>
                                    <td><strong>S/ ${monto.toFixed(2)}</strong></td>
                                    <td>${notas}</td>
                                </tr>`;
    });
    
    const pendiente = montoAsignado - totalPagado;
    const completado = pendiente <= 0;
    
    html += pagosHermano.length > 0 ? `
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="2"><strong>Total Pagado:</strong></td>
                                    <td colspan="2"><strong>S/ ${totalPagado.toFixed(2)}</strong></td>
                                </tr>
                                ${pendiente > 0 ? `
                                <tr class="table-warning">
                                    <td colspan="2"><strong>Pendiente:</strong></td>
                                    <td colspan="2"><strong>S/ ${pendiente.toFixed(2)}</strong></td>
                                </tr>` : `
                                <tr class="table-success">
                                    <td colspan="4" class="text-center">
                                        <i class="bi bi-check-circle"></i> <strong>¡Cuota completada!</strong>
                                    </td>
                                </tr>`}
                            </tfoot>
                        </table>` : '';
    
    html += `
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>`;
    
    // Eliminar modal existente si hay
    const modalExistente = document.getElementById('modalHistorial');
    if (modalExistente) {
        modalExistente.remove();
    }
    
    // Agregar el modal al body
    document.body.insertAdjacentHTML('beforeend', html);
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalHistorial'));
    modal.show();
    
    // Limpiar el modal después de cerrarlo
    document.getElementById('modalHistorial').addEventListener('hidden.bs.modal', function () {
        this.remove();
    });
}
</script>
@endsection

