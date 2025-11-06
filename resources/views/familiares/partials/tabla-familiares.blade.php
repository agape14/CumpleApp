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
            @foreach($familiares_filtrados as $familiar)
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
                        <button type="button" 
                                class="btn btn-sm btn-success" 
                                title="Agregar Relación"
                                onclick="abrirModalRelacion({{ $familiar->id }}, '{{ $familiar->nombre }}')">
                            <i class="bi bi-diagram-3"></i>
                        </button>
                        <a href="{{ route('familiares.edit', $familiar) }}" 
                           class="btn btn-sm btn-warning" 
                           title="Editar">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <form action="{{ route('familiares.destroy', $familiar) }}" 
                              method="POST" 
                              class="d-inline"
                              id="deleteForm{{ $familiar->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" 
                                    class="btn btn-sm btn-danger" 
                                    title="Eliminar"
                                    onclick="confirmDeleteWithElement(this, '¿Estás seguro de eliminar a {{ $familiar->nombre }}?', 'Esta acción no se puede deshacer')">
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

