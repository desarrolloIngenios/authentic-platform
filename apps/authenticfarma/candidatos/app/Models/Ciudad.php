<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Ciudad
 * 
 * @property int $id
 * @property string|null $descripcion
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $nombre
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_departamento
 * @property int $id_estado
 *
 * @package App\Models
 */
class Ciudad extends Model
{
	protected $table = 'ciudad';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_departamento' => 'int',
		'id_estado' => 'int'
	];

	protected $fillable = [
		'descripcion',
		'fecha_creacion',
		'fecha_modificacion',
		'nombre',
		'usuario_creacion',
		'usuario_modificacion',
		'id_departamento',
		'id_estado'
	];
	public function ofertasLaborales()
	{
		return $this->hasMany(OfOfertaLaboral::class, 'id_ciudad');
	}
	public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento', 'id');
    }

    public function getCiudadDepartamentoPaisAttribute()
    {
        return $this->nombre.' - '.$this->departamento->nombre.' - '.$this->departamento->pais->nombre;
    }
}
