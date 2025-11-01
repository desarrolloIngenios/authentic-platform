<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Genero;
use App\Models\TipoDocumento;
use App\Models\HvCanCorreo;
use App\Models\HvCanExpLab;

/**
 * Class HvCandidato
 * 
 * @property int $id_candidato
 * @property string|null $apellidos
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property Carbon|null $fecha_nacimiento
 * @property string|null $nombres
 * @property string|null $numero_documento
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_estado
 * @property int $id_genero
 * @property int $id_tipo_documento
 *
 * @package App\Models
 */
class HvCandidato extends Model
{
	protected $table = 'hv_candidato';
	protected $primaryKey = 'id_candidato';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'fecha_nacimiento' => 'datetime',
		'id_estado' => 'int',
		'id_genero' => 'int',
		'id_tipo_documento' => 'int'
	];

	protected $fillable = [
		'apellidos',
		'fecha_creacion',
		'fecha_modificacion',
		'fecha_nacimiento',
		'nombres',
		'numero_documento',
		'usuario_creacion',
		'usuario_modificacion',
		'id_estado',
		'id_genero',
		'id_tipo_documento'
	];

	public function genero()
	{
		return $this->belongsTo(Genero::class, 'id_genero');
	}

	public function tipoDocumento()
	{
		return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
	}

	public function correo()
	{
		return $this->hasOne(HvCanCorreo::class, 'id_candidato');
	}

	public function experienciasLaborales()
	{
		return $this->hasMany(HvCanExpLab::class, 'id_candidato');
	}

	public function perfil()
	{
		return $this->hasOne(HvCanPerfil::class, 'id_candidato');
	}

	public function sector()
	{
		return $this->hasMany(HvCanPerSector::class, 'id_candidato');
	}

	/**
	 * Obtiene las áreas preferidas del candidato
	 */
	public function areasPreferidas()
	{
		return $this->hasMany(HvCanPerArea::class, 'id_candidato');
	}

	public function skills()
	{
		return $this->hasMany(HvCanSkill::class, 'id_candidato');
	}

	public function formacionacademica()
	{
		return $this->hasMany(HvCanFormAc::class, 'id_candidato');
	}
	public function formacionacademicaad()
	{
		return $this->hasMany(HvCanFormAd::class, 'id_candidato');
	}

	public function HvCanIdioma()
	{
		return $this->hasMany(HvCanIdioma::class, 'id_candidato');
	}
	public function telefono()
	{
		return $this->hasOne(HvCanTelefono::class, 'id_candidato');
	}
	public function ubicacion()
	{
		return $this->hasOne(HvCanUbicacion::class, 'id_candidato');
	}

	public function hojaVida()
	{
		return $this->hasOne(HvHojaVida::class, 'id_candidato');
	}

	public function nuevoTrabajo()
	{
		return $this->hasOne(HvCanNewjob::class, 'id_candidato');
	}
}
