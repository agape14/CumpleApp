@extends('layouts.app')

@section('title', 'Configuración')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="bi bi-gear-fill"></i> Configuración de la Aplicación
                </h4>
            </div>
            <div class="card-body">
                <!-- Tabs de configuración -->
                <ul class="nav nav-tabs mb-4" id="configTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tema-tab" data-bs-toggle="tab" data-bs-target="#tema" type="button">
                            <i class="bi bi-palette-fill"></i> Temas
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="google-tab" data-bs-toggle="tab" data-bs-target="#google" type="button">
                            <i class="bi bi-calendar-check-fill"></i> Google Calendar
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="whatsapp-tab" data-bs-toggle="tab" data-bs-target="#whatsapp" type="button">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button">
                            <i class="bi bi-sliders"></i> General
                        </button>
                    </li>
                </ul>

                <!-- Contenido de tabs -->
                <div class="tab-content" id="configTabsContent">
                    <!-- Tab de Temas -->
                    <div class="tab-pane fade show active" id="tema" role="tabpanel">
                        <h5 class="mb-3">
                            <i class="bi bi-palette-fill"></i> Personalización de Temas
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tema_select" class="form-label">Tema de la aplicación:</label>
                                    <select class="form-select" id="tema_select" onchange="cambiarTema()">
                                        <option value="light" {{ ($configuraciones->where('clave', 'tema')->first()->valor ?? 'light') == 'light' ? 'selected' : '' }}>
                                            🌞 Claro
                                        </option>
                                        <option value="dark" {{ ($configuraciones->where('clave', 'tema')->first()->valor ?? '') == 'dark' ? 'selected' : '' }}>
                                            🌙 Oscuro
                                        </option>
                                        <option value="blue" {{ ($configuraciones->where('clave', 'tema')->first()->valor ?? '') == 'blue' ? 'selected' : '' }}>
                                            🔵 Azul
                                        </option>
                                        <option value="green" {{ ($configuraciones->where('clave', 'tema')->first()->valor ?? '') == 'green' ? 'selected' : '' }}>
                                            🟢 Verde
                                        </option>
                                        <option value="purple" {{ ($configuraciones->where('clave', 'tema')->first()->valor ?? '') == 'purple' ? 'selected' : '' }}>
                                            🟣 Púrpura
                                        </option>
                                        <option value="pink" {{ ($configuraciones->where('clave', 'tema')->first()->valor ?? '') == 'pink' ? 'selected' : '' }}>
                                            💗 Rosa
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="color_primario" class="form-label">Color primario personalizado:</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="color_primario" 
                                            value="{{ $configuraciones->where('clave', 'color_primario')->first()->valor ?? '#3B82F6' }}">
                                        <button class="btn btn-primary" onclick="aplicarColorPersonalizado()">
                                            <i class="bi bi-check"></i> Aplicar
                                        </button>
                                    </div>
                                </div>

                                <button class="btn btn-secondary" onclick="restablecerTemaDefault()">
                                    <i class="bi bi-arrow-counterclockwise"></i> Restablecer por defecto
                                </button>
                            </div>

                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill"></i>
                                    <strong>Información:</strong>
                                    <p class="mb-0 mt-2">Los temas cambian la apariencia general de la aplicación. Puedes elegir entre temas predefinidos o personalizar el color primario según tus preferencias.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab de Google Calendar -->
                    <div class="tab-pane fade" id="google" role="tabpanel">
                        <h5 class="mb-3">
                            <i class="bi bi-calendar-check-fill"></i> Integración con Google Calendar
                        </h5>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" id="google_calendar_enabled" 
                                        {{ ($configuraciones->where('clave', 'google_calendar_enabled')->first()->valor ?? 'false') == 'true' ? 'checked' : '' }}
                                        onchange="toggleGoogleCalendar()">
                                    <label class="form-check-label" for="google_calendar_enabled">
                                        Habilitar integración con Google Calendar
                                    </label>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <strong>Cómo usar:</strong>
                                    <ol class="mt-2 mb-0">
                                        <li>Habilita la integración con el interruptor de arriba</li>
                                        <li>Ve a la página de un familiar</li>
                                        <li>Haz clic en "Exportar a Google Calendar"</li>
                                        <li>Se abrirá Google Calendar con el evento prellenado</li>
                                        <li>Confirma la creación del evento en Google Calendar</li>
                                    </ol>
                                </div>

                                <div class="mt-3">
                                    <a href="{{ route('google-calendar.exportar-todos') }}" class="btn btn-success" target="_blank">
                                        <i class="bi bi-cloud-upload"></i> Exportar todos los cumpleaños
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab de WhatsApp -->
                    <div class="tab-pane fade" id="whatsapp" role="tabpanel">
                        <h5 class="mb-3">
                            <i class="bi bi-whatsapp"></i> Notificaciones por WhatsApp (Twilio)
                        </h5>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" id="whatsapp_enabled" 
                                        {{ ($configuraciones->where('clave', 'whatsapp_enabled')->first()->valor ?? 'false') == 'true' ? 'checked' : '' }}
                                        onchange="toggleWhatsApp()">
                                    <label class="form-check-label" for="whatsapp_enabled">
                                        Habilitar notificaciones por WhatsApp
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label for="twilio_account_sid" class="form-label">Twilio Account SID:</label>
                                    <input type="text" class="form-control" id="twilio_account_sid" 
                                        value="{{ $configuraciones->where('clave', 'twilio_account_sid')->first()->valor ?? '' }}"
                                        placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                                </div>

                                <div class="mb-3">
                                    <label for="twilio_auth_token" class="form-label">Twilio Auth Token:</label>
                                    <input type="password" class="form-control" id="twilio_auth_token" 
                                        value="{{ $configuraciones->where('clave', 'twilio_auth_token')->first()->valor ?? '' }}"
                                        placeholder="••••••••••••••••••••••••••••••••">
                                </div>

                                <div class="mb-3">
                                    <label for="twilio_whatsapp_number" class="form-label">Número de WhatsApp de Twilio:</label>
                                    <input type="text" class="form-control" id="twilio_whatsapp_number" 
                                        value="{{ $configuraciones->where('clave', 'twilio_whatsapp_number')->first()->valor ?? '' }}"
                                        placeholder="+14155238886">
                                    <small class="text-muted">Formato: +[código país][número] (ej: +14155238886)</small>
                                </div>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary" onclick="guardarConfigWhatsApp()">
                                        <i class="bi bi-save"></i> Guardar Configuración
                                    </button>
                                    <button class="btn btn-success" onclick="probarWhatsApp()">
                                        <i class="bi bi-send"></i> Probar Conexión
                                    </button>
                                </div>

                                <div class="alert alert-info mt-3">
                                    <i class="bi bi-info-circle-fill"></i>
                                    <strong>¿Cómo obtener las credenciales de Twilio?</strong>
                                    <ol class="mt-2 mb-0">
                                        <li>Regístrate en <a href="https://www.twilio.com" target="_blank">Twilio.com</a></li>
                                        <li>Ve a tu Dashboard y copia el Account SID y Auth Token</li>
                                        <li>Activa el servicio de WhatsApp en Twilio</li>
                                        <li>Obtén tu número de WhatsApp de Twilio</li>
                                        <li>Pega las credenciales aquí y prueba la conexión</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab General -->
                    <div class="tab-pane fade" id="general" role="tabpanel">
                        <h5 class="mb-3">
                            <i class="bi bi-sliders"></i> Configuración General
                        </h5>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <strong>¡Atención!</strong>
                                    <p class="mb-0 mt-2">Esta acción restablecerá todas las configuraciones a sus valores por defecto.</p>
                                </div>

                                <form action="{{ route('configuracion.restablecer') }}" method="POST" id="resetConfigForm">
                                    @csrf
                                    <button type="button" class="btn btn-danger" 
                                            onclick="confirmDeleteWithElement(this, '¿Estás seguro de restablecer todas las configuraciones?', 'Esta acción no se puede deshacer')">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restablecer todas las configuraciones
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para probar WhatsApp -->
<div class="modal fade" id="probarWhatsAppModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-whatsapp"></i> Probar Conexión WhatsApp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="telefono_prueba" class="form-label">Número de teléfono para prueba:</label>
                    <input type="text" class="form-control" id="telefono_prueba" 
                        placeholder="+52XXXXXXXXXX">
                    <small class="text-muted">Formato: +[código país][número] (ej: +5215512345678)</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="enviarPruebaWhatsApp()">
                    <i class="bi bi-send"></i> Enviar Prueba
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Cambiar tema
function cambiarTema() {
    const tema = document.getElementById('tema_select').value;
    
    fetch('/configuracion/tema', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ tema: tema })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje('success', data.message);
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje('danger', 'Error al cambiar el tema');
    });
}

// Aplicar color personalizado
function aplicarColorPersonalizado() {
    const color = document.getElementById('color_primario').value;
    const tema = document.getElementById('tema_select').value;
    
    fetch('/configuracion/tema', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            tema: tema,
            color_primario: color 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje('success', '¡Color aplicado exitosamente!');
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje('danger', 'Error al aplicar el color');
    });
}

// Restablecer tema default
function restablecerTemaDefault() {
    showConfirmAlert('¿Restablecer tema?', '¿Deseas restablecer el tema a los valores por defecto?').then((result) => {
        if (result.isConfirmed) {
            document.getElementById('tema_select').value = 'light';
            document.getElementById('color_primario').value = '#3B82F6';
            cambiarTema();
            showSuccessAlert('¡Restablecido!', 'El tema se ha restablecido a los valores por defecto');
        }
    });
}


// Toggle Google Calendar
function toggleGoogleCalendar() {
    const enabled = document.getElementById('google_calendar_enabled').checked;
    
    fetch('/configuracion/google-calendar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ enabled: enabled })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje('success', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje('danger', 'Error al actualizar la configuración');
    });
}

// Toggle WhatsApp
function toggleWhatsApp() {
    const enabled = document.getElementById('whatsapp_enabled').checked;
    
    fetch('/configuracion/whatsapp', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ enabled: enabled })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje('success', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje('danger', 'Error al actualizar la configuración');
    });
}

// Guardar configuración WhatsApp
function guardarConfigWhatsApp() {
    const accountSid = document.getElementById('twilio_account_sid').value;
    const authToken = document.getElementById('twilio_auth_token').value;
    const whatsappNumber = document.getElementById('twilio_whatsapp_number').value;
    const enabled = document.getElementById('whatsapp_enabled').checked;
    
    fetch('/configuracion/whatsapp', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            enabled: enabled,
            account_sid: accountSid,
            auth_token: authToken,
            whatsapp_number: whatsappNumber
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje('success', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje('danger', 'Error al guardar la configuración');
    });
}

// Probar WhatsApp
function probarWhatsApp() {
    const modal = new bootstrap.Modal(document.getElementById('probarWhatsAppModal'));
    modal.show();
}

// Enviar prueba WhatsApp
function enviarPruebaWhatsApp() {
    const telefono = document.getElementById('telefono_prueba').value;
    
    if (!telefono) {
        showWarningAlert('Teléfono requerido', 'Por favor ingresa un número de teléfono');
        return;
    }
    
    fetch('/whatsapp/probar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ telefono: telefono })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje('success', data.message);
            bootstrap.Modal.getInstance(document.getElementById('probarWhatsAppModal')).hide();
        } else {
            mostrarMensaje('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje('danger', 'Error al enviar la prueba');
    });
}

// Mostrar mensaje
function mostrarMensaje(tipo, mensaje) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${tipo} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.main-container');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
@endsection

