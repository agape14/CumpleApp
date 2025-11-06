<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'CumpleApp') - Recordatorios de Cumpleaños</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #ec4899;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
        }

        .navbar-brand i {
            color: var(--secondary-color);
        }

        .main-container {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.25rem;
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: var(--success-color);
            border: none;
            border-radius: 25px;
        }

        .btn-warning {
            background: var(--warning-color);
            border: none;
            border-radius: 25px;
        }

        .btn-danger {
            background: var(--danger-color);
            border: none;
            border-radius: 25px;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 500;
        }

        .table {
            background: white;
            border-radius: 10px;
        }

        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .table thead th {
            border: none;
            padding: 1rem;
        }

        .table tbody tr {
            transition: background-color 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.5rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            padding: 2rem 1.5rem;
            text-align: left;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        /* Ícono de fondo grande y semi-transparente */
        .stat-card .stat-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 5rem;
            opacity: 0.2;
            color: white;
            z-index: 1;
        }

        .stat-card .stat-content {
            position: relative;
            z-index: 2;
        }

        .stat-card .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white;
            line-height: 1;
        }

        .stat-card .stat-label {
            color: rgba(255, 255, 255, 0.95);
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-change {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .stat-card .stat-change i {
            font-size: 0.75rem;
        }

        /* Colores sólidos como la imagen */
        .stat-card.bg-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card.bg-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .stat-card.bg-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .stat-card.bg-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .stat-card.bg-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .stat-card.bg-purple {
            background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%);
        }

        .stat-card.bg-pink {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
        }

        .stat-card.bg-indigo {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        }

        /* Variantes glass - mantener compatibilidad */
        .stat-card.glass-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card.glass-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .stat-card.glass-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .stat-card.glass-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .birthday-card {
            background: white;
            border-left: 4px solid var(--secondary-color);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .birthday-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .zodiac-sign {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        footer {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem 0;
            margin-top: 3rem;
            box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>

    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-cake2-fill"></i> CumpleApp
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-house-fill"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('familiares.*') ? 'active fw-bold' : '' }}" href="{{ route('familiares.index') }}">
                            <i class="bi bi-people-fill"></i> Familiares
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('arbol-genealogico.*') ? 'active fw-bold' : '' }}" href="{{ route('arbol-genealogico.index') }}">
                            <i class="bi bi-diagram-3-fill"></i> Árbol Genealógico
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cuotas-mensuales.*') ? 'active fw-bold' : '' }}" href="{{ route('cuotas-mensuales.index') }}">
                            <i class="bi bi-cash-coin"></i> Cuotas Mensuales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('configuracion.*') ? 'active fw-bold' : '' }}" href="{{ route('configuracion.index') }}">
                            <i class="bi bi-gear-fill"></i> Configuración
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ Session::get('familiar_nombre', 'Usuario') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-person-vcard"></i> DNI: {{ Session::get('familiar_dni', '-') }}
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container main-container">
        <!-- Mensajes de alerta -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>¡Ups! Hay algunos errores:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Contenido de la página -->
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p class="mb-0 text-muted">
                <i class="bi bi-heart-fill text-danger"></i> 
                CumpleApp &copy; {{ date('Y') }} - Nunca olvides un cumpleaños
            </p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 Helper Functions -->
    <script>
        // Función helper para alertas de éxito
        function showSuccessAlert(title, text = '') {
            return Swal.fire({
                icon: 'success',
                title: title,
                text: text,
                confirmButtonText: '¡Entendido!',
                confirmButtonColor: '#10b981'
            });
        }

        // Función helper para alertas de error
        function showErrorAlert(title, text = '') {
            return Swal.fire({
                icon: 'error',
                title: title,
                text: text,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#ef4444'
            });
        }

        // Función helper para alertas de información
        function showInfoAlert(title, text = '') {
            return Swal.fire({
                icon: 'info',
                title: title,
                text: text,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#3b82f6'
            });
        }

        // Función helper para alertas de advertencia
        function showWarningAlert(title, text = '') {
            return Swal.fire({
                icon: 'warning',
                title: title,
                text: text,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#f59e0b'
            });
        }

        // Función helper para confirmaciones
        function showConfirmAlert(title, text = '', confirmButtonText = 'Sí, eliminar') {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Cancelar'
            });
        }

        // Función helper para confirmaciones de eliminación
        function showDeleteConfirmAlert(title, text = '') {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });
        }

        // Función específica para formularios (sincrónica)
        function confirmDeleteSync(title, text = '') {
            // Mostrar el alert y esperar la respuesta
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                // Si se confirma, enviar el formulario
                if (result.isConfirmed) {
                    // Encontrar el formulario usando el evento actual
                    const form = event.target.closest('form');
                    if (form) {
                        form.submit();
                    }
                }
            });
            
            // Retornar false para prevenir el envío inmediato
            return false;
        }

        // Función alternativa que funciona con onclick
        function confirmDeleteWithElement(element, title, text = '') {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = element.closest('form');
                    if (form) {
                        form.submit();
                    }
                }
            });
        }
    </script>

    @yield('scripts')
</body>
</html>

