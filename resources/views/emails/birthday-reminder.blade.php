<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio de Cumpleaños</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header .icon {
            font-size: 60px;
            margin-bottom: 10px;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .content h2 {
            color: #667eea;
            margin-bottom: 20px;
        }
        .birthday-info {
            background: #f8f9fa;
            border-left: 4px solid #ec4899;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
            border-radius: 10px;
        }
        .birthday-info p {
            margin: 10px 0;
            font-size: 16px;
        }
        .birthday-info strong {
            color: #667eea;
        }
        .balloon {
            font-size: 40px;
            display: inline-block;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🎂</div>
            <h1>¡Recordatorio de Cumpleaños!</h1>
        </div>

        <div class="content">
            <div class="balloon">🎈 🎉 🎁</div>
            
            <h2>¡Hoy es un día especial!</h2>

            <div class="birthday-info">
                <p><strong>Nombre:</strong> {{ $familiar->nombre }}</p>
                <p><strong>Parentesco:</strong> {{ $familiar->parentesco->nombre_parentesco }}</p>
                <p><strong>Edad:</strong> {{ $familiar->age }} años</p>
                <p><strong>Fecha de Nacimiento:</strong> {{ $familiar->fecha_nacimiento->format('d/m/Y') }}</p>
                @if($familiar->zodiac_sign)
                <p><strong>Signo Zodiacal:</strong> {{ $familiar->zodiac_sign }}</p>
                @endif
            </div>

            <p style="font-size: 18px; color: #374151;">
                <strong>¡No olvides saludarlo!</strong>
            </p>

            @if($familiar->telefono || $familiar->email)
            <p style="margin-top: 20px; color: #6b7280;">
                Contacta a {{ $familiar->nombre }}:
            </p>
            <div style="margin-top: 10px;">
                @if($familiar->telefono)
                <p style="margin: 5px 0;">
                    📱 Teléfono: {{ $familiar->telefono }}
                </p>
                @endif
                @if($familiar->email)
                <p style="margin: 5px 0;">
                    📧 Email: {{ $familiar->email }}
                </p>
                @endif
            </div>
            @endif

            @if($familiar->ideasRegalos->count() > 0)
            <div class="birthday-info" style="margin-top: 30px;">
                <p><strong>💡 Ideas de Regalos:</strong></p>
                <ul style="text-align: left; margin: 10px 0;">
                    @foreach($familiar->ideasRegalos->take(3) as $idea)
                    <li>{{ $idea->idea }} @if($idea->precio_estimado) - S/ {{ number_format($idea->precio_estimado, 2) }} @endif</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="balloon">🎊 🎈 🎁</div>
        </div>

        <div class="footer">
            <p>Este es un recordatorio automático de <strong>CumpleApp</strong></p>
            <p>Nunca olvides un cumpleaños ❤️</p>
        </div>
    </div>
</body>
</html>

