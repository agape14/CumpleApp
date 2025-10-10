<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionUsuario extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'configuracion_usuario';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
    ];

    /**
     * Obtiene el valor de una configuración específica.
     */
    public static function obtener(string $clave, ?string $default = null): ?string
    {
        $config = self::where('clave', $clave)->first();
        return $config ? $config->valor : $default;
    }

    /**
     * Actualiza o crea una configuración.
     */
    public static function establecer(string $clave, string $valor, ?string $descripcion = null): void
    {
        self::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $valor, 'descripcion' => $descripcion]
        );
    }

    /**
     * Obtiene todas las configuraciones como array clave => valor.
     */
    public static function obtenerTodas(): array
    {
        return self::pluck('valor', 'clave')->toArray();
    }
}
