@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Título de bienvenida -->
    <div class="col-12 mb-4">
        <h1 class="text-white text-center">
            <i class="bi bi-balloon-heart-fill"></i> 
            ¡Bienvenido a CumpleApp!
        </h1>
        <p class="text-white text-center lead">Gestiona y recuerda los cumpleaños de tus seres queridos</p>
    </div>

    <!-- Estadísticas principales -->
    <div class="col-md-4 mb-4">
        <div class="stat-card">
            <div class="stat-icon text-primary">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-number text-primary">{{ $totalFamiliares }}</div>
            <div class="stat-label">Total Familiares</div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="stat-card">
            <div class="stat-icon text-success">
                <i class="bi bi-calendar-event-fill"></i>
            </div>
            <div class="stat-number text-success">{{ $cumpleanosMesActual->count() }}</div>
            <div class="stat-label">Cumpleaños este mes</div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="stat-card">
            <div class="stat-icon text-danger">
                <i class="bi bi-gift-fill"></i>
            </div>
            <div class="stat-number text-danger">{{ $cumpleanosHoy->count() }}</div>
            <div class="stat-label">Cumpleaños hoy</div>
        </div>
    </div>

    <!-- Cumpleaños de hoy -->
    @if($cumpleanosHoy->count() > 0)
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-cake2-fill"></i> ¡Cumpleaños de Hoy!
            </div>
            <div class="card-body">
                @foreach($cumpleanosHoy as $familiar)
                <div class="alert alert-warning mb-3" role="alert">
                    <h4 class="alert-heading">
                        <i class="bi bi-balloon-fill"></i> 
                        ¡Hoy es el cumpleaños de {{ $familiar->nombre }}!
                    </h4>
                    <p class="mb-0">
                        Cumple {{ $familiar->age }} años. 
                        <strong>¡No olvides saludarlo!</strong>
                    </p>
                    <hr>
                    <div class="d-flex gap-2">
                        @if($familiar->telefono)
                        <a href="tel:{{ $familiar->telefono }}" class="btn btn-success btn-sm">
                            <i class="bi bi-telephone-fill"></i> Llamar
                        </a>
                        @endif
                        @if($familiar->email)
                        <a href="mailto:{{ $familiar->email }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-envelope-fill"></i> Enviar Email
                        </a>
                        @endif
                        <a href="{{ route('familiares.show', $familiar) }}" class="btn btn-info btn-sm text-white">
                            <i class="bi bi-eye-fill"></i> Ver Perfil
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Próximo cumpleaños -->
    @if($proximoCumpleanos)
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar-check-fill"></i> Próximo Cumpleaños
            </div>
            <div class="card-body">
                <div class="birthday-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">
                                <i class="bi bi-person-fill text-primary"></i>
                                {{ $proximoCumpleanos->nombre }}
                            </h5>
                            <p class="text-muted mb-1">
                                <i class="bi bi-cake2"></i> 
                                {{ $proximoCumpleanos->fecha_nacimiento->format('d/m/Y') }}
                            </p>
                            <p class="text-muted mb-0">
                                <small>{{ $proximoCumpleanos->parentesco->nombre_parentesco }}</small>
                            </p>
                        </div>
                        <div class="text-end">
                            <h3 class="text-primary mb-0">{{ $proximoCumpleanos->days_until_birthday }}</h3>
                            <small class="text-muted">días</small>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('familiares.show', $proximoCumpleanos) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-eye-fill"></i> Ver Detalles
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Próximos 5 cumpleaños -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-ul"></i> Próximos Cumpleaños
            </div>
            <div class="card-body">
                @if($proximosCumpleanos->count() > 0)
                    @foreach($proximosCumpleanos as $familiar)
                    <div class="birthday-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $familiar->nombre }}</h6>
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> 
                                    {{ $familiar->next_birthday->format('d/m/Y') }}
                                </small>
                            </div>
                            <div>
                                <span class="badge bg-primary">
                                    {{ $familiar->days_until_birthday }} días
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted text-center mb-0">
                        <i class="bi bi-inbox"></i> No hay cumpleaños registrados
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Gráfico de cumpleaños por mes -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart-fill"></i> Distribución de Cumpleaños por Mes
            </div>
            <div class="card-body">
                <canvas id="birthdayChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- Botón de acción rápida -->
    <div class="col-12 text-center mb-4">
        <a href="{{ route('familiares.create') }}" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-circle-fill"></i> Agregar Nuevo Familiar
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Datos para el gráfico
    const cumpleanosPorMes = @json(array_values($cumpleanosPorMes));
    
    // Configuración del gráfico
    const ctx = document.getElementById('birthdayChart').getContext('2d');
    const birthdayChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                     'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            datasets: [{
                label: 'Cumpleaños',
                data: cumpleanosPorMes,
                backgroundColor: 'rgba(102, 126, 234, 0.5)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 2,
                borderRadius: 10,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endsection

