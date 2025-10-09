<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parentesco extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre_parentesco',
    ];

    /**
     * Obtiene los familiares que tienen este parentesco.
     */
    public function familiares(): HasMany
    {
        return $this->hasMany(Familiar::class);
    }
}

