<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recordatorio extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'recordatorios';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'familiar_id',
        'dias_antes',
        'enviar_email',
        'enviar_whatsapp',
        'activo',
        'hora_envio',
        'mensaje_personalizado',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'enviar_email' => 'boolean',
        'enviar_whatsapp' => 'boolean',
        'activo' => 'boolean',
    ];

    /**
     * Obtiene el familiar asociado al recordatorio.
     */
    public function familiar(): BelongsTo
    {
        return $this->belongsTo(Familiar::class);
    }
}
