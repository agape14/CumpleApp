<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CuotaMensual extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'cuotas_mensuales';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hermano_id',
        'tipo_cuota',
        'concepto',
        'meta_total',
        'fecha_limite',
        'anio',
        'mes',
        'monto',
        'estado',
        'fecha_pago',
        'comprobante',
        'notas',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_pago' => 'date',
        'fecha_limite' => 'date',
        'monto' => 'decimal:2',
        'meta_total' => 'decimal:2',
        'mes' => 'integer',
    ];

    /**
     * Obtiene el hermano (familiar) asociado a la cuota.
     */
    public function hermano(): BelongsTo
    {
        return $this->belongsTo(Familiar::class, 'hermano_id');
    }

    /**
     * Obtiene el nombre del mes.
     */
    public function getNombreMesAttribute(): string
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        return $meses[$this->mes] ?? '';
    }

    /**
     * Obtiene el período completo (mes/año).
     */
    public function getPeriodoAttribute(): string
    {
        return $this->nombre_mes . ' ' . $this->anio;
    }

    /**
     * Scope para filtrar por año.
     */
    public function scopeDelAnio($query, int $anio)
    {
        return $query->where('anio', $anio);
    }

    /**
     * Scope para filtrar por mes.
     */
    public function scopeDelMes($query, int $mes)
    {
        return $query->where('mes', $mes);
    }

    /**
     * Scope para filtrar por período (mes y año).
     */
    public function scopeDelPeriodo($query, int $mes, int $anio)
    {
        return $query->where('mes', $mes)->where('anio', $anio);
    }

    /**
     * Scope para filtrar solo cuotas pagadas.
     */
    public function scopePagadas($query)
    {
        return $query->where('estado', 'pagado');
    }

    /**
     * Scope para filtrar solo cuotas pendientes.
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para filtrar cuotas mensuales.
     */
    public function scopeMensuales($query)
    {
        return $query->where('tipo_cuota', 'mensual');
    }

    /**
     * Scope para filtrar colectas especiales.
     */
    public function scopeEspeciales($query)
    {
        return $query->where('tipo_cuota', 'especial');
    }

    /**
     * Scope para filtrar por concepto.
     */
    public function scopePorConcepto($query, string $concepto)
    {
        return $query->where('concepto', $concepto);
    }

    /**
     * Obtiene el porcentaje de avance de la meta (para colectas especiales).
     */
    public function getPorcentajeMetaAttribute(): ?float
    {
        if (!$this->meta_total || $this->meta_total == 0) {
            return null;
        }

        return ($this->monto / $this->meta_total) * 100;
    }
}

