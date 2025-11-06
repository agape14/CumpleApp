<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Familiar extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'familiares';

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
        'nombre',
        'dni',
        'puede_acceder',
        'fecha_nacimiento',
        'telefono',
        'email',
        'notificar',
        'notas',
        'parentesco_id',
        'created_by',
        'updated_by',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_nacimiento' => 'date',
        'notificar' => 'boolean',
    ];

    /**
     * Obtiene el parentesco del familiar.
     */
    public function parentesco(): BelongsTo
    {
        return $this->belongsTo(Parentesco::class);
    }

    /**
     * Obtiene las ideas de regalos del familiar.
     */
    public function ideasRegalos(): HasMany
    {
        return $this->hasMany(IdeaRegalo::class);
    }

    /**
     * Obtiene las relaciones familiares donde este familiar es el principal.
     */
    public function relaciones(): HasMany
    {
        return $this->hasMany(RelacionFamiliar::class, 'familiar_id');
    }

    /**
     * Obtiene las relaciones familiares donde este familiar es el relacionado.
     */
    public function relacionesInversas(): HasMany
    {
        return $this->hasMany(RelacionFamiliar::class, 'familiar_relacionado_id');
    }

    /**
     * Obtiene todos los hijos del familiar.
     */
    public function hijos()
    {
        return $this->relaciones()
            ->whereIn('tipo_relacion', ['hijo', 'hija'])
            ->with('familiarRelacionado');
    }

    /**
     * Obtiene la pareja/esposo(a) del familiar.
     */
    public function pareja()
    {
        return $this->relaciones()
            ->whereIn('tipo_relacion', ['esposo', 'esposa', 'pareja'])
            ->with('familiarRelacionado')
            ->first();
    }

    /**
     * Obtiene los padres del familiar.
     */
    public function padres()
    {
        return $this->relacionesInversas()
            ->whereIn('tipo_relacion', ['hijo', 'hija'])
            ->with('familiar');
    }

    /**
     * Obtiene los regalos dados a este familiar.
     */
    public function regalosDados(): HasMany
    {
        return $this->hasMany(RegaloDado::class);
    }

    /**
     * Obtiene los recordatorios configurados para este familiar.
     */
    public function recordatorios(): HasMany
    {
        return $this->hasMany(Recordatorio::class);
    }

    /**
     * Obtiene las cuotas mensuales del hermano.
     */
    public function cuotasMensuales(): HasMany
    {
        return $this->hasMany(CuotaMensual::class, 'hermano_id');
    }

    /**
     * Obtiene el familiar que creó este registro.
     */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(Familiar::class, 'created_by');
    }

    /**
     * Obtiene el familiar que actualizó este registro por última vez.
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(Familiar::class, 'updated_by');
    }

    /**
     * Accesor para obtener la edad actual del familiar.
     */
    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->fecha_nacimiento ? Carbon::parse($this->fecha_nacimiento)->age : null,
        );
    }

    /**
     * Accesor para obtener el signo zodiacal del familiar.
     */
    protected function zodiacSign(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->fecha_nacimiento) {
                    return null;
                }

                $day = $this->fecha_nacimiento->day;
                $month = $this->fecha_nacimiento->month;

                $zodiacSigns = [
                    ['sign' => 'Capricornio', 'start' => [12, 22], 'end' => [1, 19]],
                    ['sign' => 'Acuario', 'start' => [1, 20], 'end' => [2, 18]],
                    ['sign' => 'Piscis', 'start' => [2, 19], 'end' => [3, 20]],
                    ['sign' => 'Aries', 'start' => [3, 21], 'end' => [4, 19]],
                    ['sign' => 'Tauro', 'start' => [4, 20], 'end' => [5, 20]],
                    ['sign' => 'Géminis', 'start' => [5, 21], 'end' => [6, 20]],
                    ['sign' => 'Cáncer', 'start' => [6, 21], 'end' => [7, 22]],
                    ['sign' => 'Leo', 'start' => [7, 23], 'end' => [8, 22]],
                    ['sign' => 'Virgo', 'start' => [8, 23], 'end' => [9, 22]],
                    ['sign' => 'Libra', 'start' => [9, 23], 'end' => [10, 22]],
                    ['sign' => 'Escorpio', 'start' => [10, 23], 'end' => [11, 21]],
                    ['sign' => 'Sagitario', 'start' => [11, 22], 'end' => [12, 21]],
                ];

                foreach ($zodiacSigns as $zodiac) {
                    [$startMonth, $startDay] = $zodiac['start'];
                    [$endMonth, $endDay] = $zodiac['end'];

                    if (($month == $startMonth && $day >= $startDay) || ($month == $endMonth && $day <= $endDay)) {
                        return $zodiac['sign'];
                    }
                }

                return null;
            }
        );
    }

    /**
     * Obtiene el próximo cumpleaños del familiar.
     */
    public function getNextBirthdayAttribute()
    {
        if (!$this->fecha_nacimiento) {
            return null;
        }

        $today = Carbon::today();
        $birthday = Carbon::create($today->year, $this->fecha_nacimiento->month, $this->fecha_nacimiento->day);

        if ($birthday->isPast()) {
            $birthday->addYear();
        }

        return $birthday;
    }

    /**
     * Obtiene los días restantes hasta el próximo cumpleaños.
     */
    public function getDaysUntilBirthdayAttribute()
    {
        if (!$this->next_birthday) {
            return null;
        }

        return Carbon::today()->diffInDays($this->next_birthday);
    }
}

