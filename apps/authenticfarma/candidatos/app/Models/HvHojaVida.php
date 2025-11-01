<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class HvHojaVida
 * 
 * @property int $id_hoja_vida
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $foto
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int|null $id_candidato
 * @property int $id_estado
 * @property int $id_usuario
 * @property int|null $id_empresa
 * @property string $json_skills
 * @property string $json_profile
 * @property-read \Illuminate\Database\Eloquent\Collection|HvOfCanOfertaFavorito[] $ofertasFavoritas
 * @property-read \Illuminate\Database\Eloquent\Collection|HvOfCanOfertum[] $postulaciones
 *
 * @package App\Models
 */
class HvHojaVida extends Model
{
	protected $table = 'hv_hoja_vida';
	protected $primaryKey = 'id_hoja_vida';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_candidato' => 'int',
		'id_estado' => 'int',
		'id_usuario' => 'int',
		'id_empresa' => 'int'
	];

	protected $fillable = [
		'fecha_creacion',
		'fecha_modificacion',
		'foto',
		'usuario_creacion',
		'usuario_modificacion',
		'id_candidato',
		'id_estado',
		'id_usuario',
		'id_empresa',
		'json_skills',
		'json_profile'
	];

	public function candidato()
	{
		return $this->belongsTo(HvCandidato::class, 'id_candidato');
	}
	public function usuario()
	{
		return $this->belongsTo(Usuario::class, 'id_usuario');
	}
	public function empresa()
	{
		return $this->belongsTo(Empresa::class, 'id_empresa');
	}

	public function OfHvSelecionados()
	{
		return $this->belongsToMany(OfOfertaLaboral::class, 'oferta_and_hoja_vida_seleccionados', 'oferta_laboral_id', 'hoja_vida_id');
	}

	/**
	 * Obtiene las ofertas laborales marcadas como favoritas por este candidato
	 */
	public function ofertasFavoritas(): HasMany
	{
		return $this->hasMany(HvOfCanOfertaFavorito::class, 'id_hoja_vida', 'id_hoja_vida');
	}

	/**
	 * Obtiene todas las postulaciones realizadas con esta hoja de vida
	 */
	public function postulaciones(): HasMany
	{
		return $this->hasMany(HvOfCanOfertum::class, 'id_hoja_vida', 'id_hoja_vida');
	}
}
