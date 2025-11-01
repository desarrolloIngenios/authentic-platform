<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UsuariosRole
 * 
 * @property int $idusuario
 * @property int $idrole
 *
 * @package App\Models
 */
class UsuariosRole extends Model
{
	protected $table = 'usuarios_roles';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'idusuario' => 'int',
		'idrole' => 'int'
	];
}
