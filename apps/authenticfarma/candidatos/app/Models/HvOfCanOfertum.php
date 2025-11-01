<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class HvOfCanOfertum
 * 
 * @property int $idhvofcan_oferta
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_estado
 * @property int $id_hoja_vida
 * @property int $idofoferta_laboral
 * @property string|null $ai
 * 
 * @property-read HvHojaVida $hojaVida
 * @property-read OfOfertaLaboral $ofertaLaboral
 * @property-read EstadoControl $estado
 * @property-read HvCandidato $candidato
 *
 * @package App\Models
 */
class HvOfCanOfertum extends Model
{
	protected $table = 'hv_of_can_oferta';
	protected $primaryKey = 'idhvofcan_oferta';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'idhvofcan_oferta' => 'int',
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_estado' => 'int',
		'id_hoja_vida' => 'int',
		'idofoferta_laboral' => 'int'
	];

	protected $fillable = [
		'fecha_creacion',
		'fecha_modificacion',
		'usuario_creacion',
		'usuario_modificacion',
		'id_estado',
		'id_hoja_vida',
		'idofoferta_laboral',
		'ai'
	];

    /**
     * Obtiene la hoja de vida asociada a la postulación
     */
    public function hojaVida(): BelongsTo
    {
        return $this->belongsTo(HvHojaVida::class, 'id_hoja_vida', 'id_hoja_vida');
    }

    /**
     * Obtiene la oferta laboral a la que se postuló
     */
    public function ofertaLaboral(): BelongsTo
    {
        return $this->belongsTo(OfOfertaLaboral::class, 'idofoferta_laboral', 'idofoferta_laboral');
    }

    /**
     * Obtiene el estado de la postulación
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoControl::class, 'id_estado', 'id_estado');
    }

    /**
     * Obtiene el candidato asociado a la postulación
     * (Acceso como atributo, no relación Eloquent)
     */
    public function getCandidatoAttribute()
    {
        return $this->hojaVida && $this->hojaVida->candidato ? $this->hojaVida->candidato : null;
    }
}
