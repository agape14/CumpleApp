<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegaloDado extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'regalos_dados';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'familiar_id',
        'nombre_regalo',
        'descripcion',
        'precio',
        'fecha_entrega',
        'ocasion',
        'lugar_compra',
        'notas',
        'foto',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_entrega' => 'date',
        'precio' => 'decimal:2',
    ];

    /**
     * Obtiene el familiar al que se le dio el regalo.
     */
    public function familiar(): BelongsTo
    {
        return $this->belongsTo(Familiar::class);
    }
}
