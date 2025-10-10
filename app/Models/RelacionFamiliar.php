<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelacionFamiliar extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'relaciones_familiares';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'familiar_id',
        'familiar_relacionado_id',
        'tipo_relacion',
        'descripcion',
    ];

    /**
     * Obtiene el familiar principal de la relación.
     */
    public function familiar(): BelongsTo
    {
        return $this->belongsTo(Familiar::class, 'familiar_id');
    }

    /**
     * Obtiene el familiar relacionado.
     */
    public function familiarRelacionado(): BelongsTo
    {
        return $this->belongsTo(Familiar::class, 'familiar_relacionado_id');
    }

    /**
     * Obtiene el tipo de relación inversa.
     */
    public function getTipoRelacionInversaAttribute(): ?string
    {
        $inversas = [
            'padre' => 'hijo',
            'madre' => 'hijo',
            'hijo' => 'padre',
            'hija' => 'padre',
            'esposo' => 'esposa',
            'esposa' => 'esposo',
            'pareja' => 'pareja',
            'hermano' => 'hermano',
            'hermana' => 'hermana',
            'abuelo' => 'nieto',
            'abuela' => 'nieto',
            'nieto' => 'abuelo',
            'nieta' => 'abuela',
            'tio' => 'sobrino',
            'tia' => 'sobrino',
            'sobrino' => 'tio',
            'sobrina' => 'tia',
            'primo' => 'primo',
            'prima' => 'prima',
            'otro' => 'otro',
        ];

        return $inversas[$this->tipo_relacion] ?? null;
    }
}
