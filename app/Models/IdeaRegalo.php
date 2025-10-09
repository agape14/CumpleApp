<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdeaRegalo extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'ideas_regalos';

    /**
     * Obtiene el nombre de la clave de ruta para el modelo.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'idea',
        'precio_estimado',
        'link_compra',
        'comprado',
        'familiar_id',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'precio_estimado' => 'decimal:2',
        'comprado' => 'boolean',
    ];

    /**
     * Obtiene el familiar al que pertenece esta idea de regalo.
     */
    public function familiar(): BelongsTo
    {
        return $this->belongsTo(Familiar::class);
    }
}

