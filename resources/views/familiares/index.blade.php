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
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th><i class="bi bi-person"></i> Nombre</th>
                                <th><i class="bi bi-heart"></i> Parentesco</th>
                                <th><i class="bi bi-cake2"></i> Fecha Nacimiento</th>
                                <th><i class="bi bi-calendar"></i> Edad</th>
                                <th><i class="bi bi-clock"></i> Próximo Cumpleaños</th>
                                <th><i class="bi bi-bell"></i> Notificar</th>
                                <th class="text-center"><i class="bi bi-gear"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($familiares as $familiar)
                            <tr>
                                <td>
                                    <strong>{{ $familiar->nombre }}</strong>
                                    @if($familiar->zodiac_sign)
                                    <br>
                                    <span class="zodiac-sign">
                                        <i class="bi bi-star-fill"></i> {{ $familiar->zodiac_sign }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $familiar->parentesco->nombre_parentesco }}
                                    </span>
                                </td>
                                <td>{{ $familiar->fecha_nacimiento->format('d/m/Y') }}</td>
                                <td>
                                    <strong class="text-primary">{{ $familiar->age }} años</strong>
                                </td>
                                <td>
                                    @if($familiar->days_until_birthday == 0)
                                        <span class="badge bg-danger">
                                            <i class="bi bi-gift-fill"></i> ¡Hoy!
                                        </span>
                                    @elseif($familiar->days_until_birthday <= 7)
                                        <span class="badge bg-warning">
                                            En {{ $familiar->days_until_birthday }} días
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            En {{ $familiar->days_until_birthday }} días
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($familiar->notificar)
                                        <i class="bi bi-bell-fill text-success" title="Notificaciones activadas"></i>
                                    @else
                                        <i class="bi bi-bell-slash-fill text-muted" title="Notificaciones desactivadas"></i>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('familiares.show', $familiar) }}" 
                                           class="btn btn-sm btn-info text-white" 
                                           title="Ver">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('familiares.edit', $familiar) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('familiares.destroy', $familiar) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('¿Estás seguro de eliminar a {{ $familiar->nombre }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Eliminar">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #cbd5e1;"></i>
                    <h4 class="mt-3 text-muted">No hay familiares registrados</h4>
                    <p class="text-muted">Comienza agregando tu primer familiar</p>
                    <a href="{{ route('familiares.create') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle-fill"></i> Agregar Familiar
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

