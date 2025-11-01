<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class HvOfCanOfertaFavorito
 * 
 * @property int $idhvofcan_oferta_favorito
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_estado
 * @property int $id_hoja_vida
 * @property int $idofoferta_laboral
 * 
 * @property HvHojaVida $hojaVida
 * @property OfOfertaLaboral $ofertaLaboral
 * @property EstadoControl $estado
 *
 * @package App\Models
 */
class HvOfCanOfertaFavorito extends Model
{
	protected $table = 'hv_of_can_oferta_favorito';
	protected $primaryKey = 'idhvofcan_oferta_favorito';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'idhvofcan_oferta_favorito' => 'int',
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
		'idofoferta_laboral'
	];

	/**
	 * Obtiene la hoja de vida asociada al favorito
	 */
	public function hojaVida(): BelongsTo
	{
		return $this->belongsTo(HvHojaVida::class, 'id_hoja_vida', 'idhv_hoja_vida');
	}

	/**
	 * Obtiene la oferta laboral marcada como favorita
	 */
	public function ofertaLaboral(): BelongsTo
	{
		return $this->belongsTo(OfOfertaLaboral::class, 'idofoferta_laboral', 'idof_oferta_laboral');
	}

	/**
	 * Obtiene el estado del registro
	 */
	public function estado(): BelongsTo
	{
		return $this->belongsTo(EstadoControl::class, 'id_estado', 'id_estado');
	}
}
