<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pais;
use App\Models\Sector;
use App\Models\Usuario;

/**
 * Class Empresa
 * 
 * @property int $id
 * @property bool|null $confidencial
 * @property string|null $descripcion
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $logourl
 * @property string|null $nit
 * @property string|null $nombre
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_estado
 * @property int $id_pais
 * @property int $id_sector
 * @property string|null $correo
 * @property string|null $direccion
 * @property string|null $telefono
 *
 * @package App\Models
 */
class Empresa extends Model
{
	protected $table = 'empresa';
	public $timestamps = false;

	protected $casts = [
		'confidencial' => 'bool',
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_estado' => 'int',
		'id_pais' => 'int',
		'id_sector' => 'int'
	];

	protected $fillable = [
		'confidencial',
		'descripcion',
		'fecha_creacion',
		'fecha_modificacion',
		'logourl',
		'nit',
		'nombre',
		'usuario_creacion',
		'usuario_modificacion',
		'id_estado',
		'id_pais',
		'id_sector',
		'correo',
		'direccion',
		'telefono'
	];

	public function pais()
	{
		return $this->belongsTo(Pais::class, 'id_pais');
	}

	public function sector()
	{
		return $this->belongsTo(Sector::class, 'id_sector');
	}

	public function usuarios()
	{
		return $this->hasMany(Usuario::class, 'id_empresa');
	}

	public function ofertasLaborales()
	{
		return $this->hasMany(OfOfertaLaboral::class, 'id_empresa');
	}
}
