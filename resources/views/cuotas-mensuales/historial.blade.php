@extends('layouts.app')

@section('title', 'Historial de Cuotas')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="text-white">
                <i class="bi bi-clock-history"></i> Historial de Cuotas Mensuales
            </h2>
            <a href="{{ route('cuotas-mensuales.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver a Cuotas Actuales
            </a>
        </div>
    </div>

    <!-- Selector de Año -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('cuotas-mensuales.historial') }}" class="row g-3">
                    <div class="col-md-6">
                        <label for="anio" class="form-label">
                            <i class="bi bi-calendar"></i> Año:
                        </label>
                        <select class="form-select" id="anio" name="anio" onchange="this.form.submit()">
                            @for($a = now()->year; $a >= 2020; $a--)
                                <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>
                                    {{ $a }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="button" class="btn btn-info" onclick="mostrarEstadisticas()">
                            <i class="bi bi-bar-chart"></i> Ver Estadísticas del Año
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Estadísticas Anuales -->
    <div class="col-md-4 mb-4">
        <div class="stat-card bg-success">
            <div class="stat-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $cuotasPagadasAnual }}</div>
                <div class="stat-label">Cuotas Pagadas en {{ $anio }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="stat-card bg-danger">
            <div class="stat-icon">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $cuotasPendientesAnual }}</div>
                <div class="stat-label">Cuotas Pendientes</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="stat-card bg-info">
            <div class="stat-icon">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">S/ {{ number_format($totalRecaudadoAnual, 2) }}</div>
                <div class="stat-label">Total Recaudado en {{ $anio }}</div>
            </div>
        </div>
    </div>

    <!-- Historial por Mes -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-range"></i> Historial de Cuotas {{ $anio }}
                </h5>
            </div>
            <div class="card-body">
                @if($cuotasPorMes->isEmpty())
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        No hay cuotas registradas para el año {{ $anio }}.
                    </div>
                @else
                    @foreach($cuotasPorMes as $mes => $cuotasMes)
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-calendar-month"></i> 
                                {{ Carbon\Carbon::create(null, $mes, 1)->locale('es')->monthName }} {{ $anio }}
                                <span class="badge bg-success ms-2">
                                    {{ $cuotasMes->where('estado', 'pagado')->count() }} / {{ $hermanos->count() }} Pagadas
                                </span>
                            </h6>

                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Hermano</th>
                                            <th>Monto</th>
                                            <th>Estado</th>
                                            <th>Fecha Pago</th>
                                            <th>Comprobante</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($hermanos as $hermano)
                                            @php
                                                $cuota = $cuotasMes->firstWhere('hermano_id', $hermano->id);
                                            @endphp
                                            <tr>
                                                <td>{{ $hermano->nombre }}</td>
                                                <td>
                                                    @if($cuota)
                                                        S/ {{ number_format($cuota->monto, 2) }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($cuota)
                                                        @if($cuota->estado == 'pagado')
                                                            <span class="badge bg-success">Pagado</span>
                                                        @elseif($cuota->estado == 'parcial')
                                                            <span class="badge bg-warning">Parcial</span>
                                                        @else
                                                            <span class="badge bg-danger">Pendiente</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">Sin registro</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($cuota && $cuota->fecha_pago)
                                                        {{ $cuota->fecha_pago->format('d/m/Y') }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($cuota && $cuota->comprobante)
                                                        <a href="{{ asset('storage/' . $cuota->comprobante) }}" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="bi bi-file-earmark"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <td><strong>Total del Mes:</strong></td>
                                            <td><strong>S/ {{ number_format($cuotasMes->where('estado', 'pagado')->sum('monto'), 2) }}</strong></td>
                                            <td colspan="3">
                                                <span class="badge bg-success">
                                                    {{ $cuotasMes->where('estado', 'pagado')->count() }} Pagadas
                                                </span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Gráfico de Estadísticas (opcional) -->
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up"></i> Resumen Visual del Año {{ $anio }}
                </h5>
            </div>
            <div class="card-body">
                <canvas id="chartCuotas" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Mostrar estadísticas
function mostrarEstadisticas() {
    const anio = document.getElementById('anio').value;
    
    fetch(`/cuotas-mensuales/estadisticas?anio=${anio}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarGrafico(data.estadisticas);
            }
        });
}

// Renderizar gráfico
function renderizarGrafico(estadisticas) {
    const ctx = document.getElementById('chartCuotas');
    
    const labels = estadisticas.map(e => e.nombre_mes);
    const pagadas = estadisticas.map(e => e.pagadas);
    const pendientes = estadisticas.map(e => e.pendientes);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pagadas',
                    data: pagadas,
                    backgroundColor: '#10b981',
                },
                {
                    label: 'Pendientes',
                    data: pendientes,
                    backgroundColor: '#ef4444',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Cargar gráfico al iniciar
document.addEventListener('DOMContentLoaded', function() {
    mostrarEstadisticas();
});
</script>
@endsection

