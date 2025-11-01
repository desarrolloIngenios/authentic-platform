<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Usuario;

/**
 * Class Role
 * 
 * @property int $idrole
 * @property string|null $nombre
 *
 * @package App\Models
 */
class Role extends Model
{
	protected $table = 'roles';
	protected $primaryKey = 'idrole';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'idrole' => 'int'
	];

	protected $fillable = [
		'nombre'
	];

	public function usuarios()
	{
		return $this->belongsToMany(Usuario::class, 'usuarios_roles', 'idrole', 'idusuario');
	}
}
