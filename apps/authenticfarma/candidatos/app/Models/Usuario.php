<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Empresa;
use App\Models\Role;

/**
 * Class Usuario
 * 
 * @property int $id
 * @property string|null $apellido
 * @property string|null $confirmation_token
 * @property string|null $email
 * @property bool|null $enabled
 * @property string|null $nombre
 * @property string|null $password
 * @property string|null $reset_token
 * @property string|null $username
 * @property int|null $id_empresa
 *
 * @package App\Models
 */


class Usuario extends Authenticatable

{
	protected $table = 'usuarios';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'enabled' => 'bool',
		'id_empresa' => 'int'
	];

	protected $hidden = [
		'confirmation_token',
		'password',
		'reset_token'
	];

	protected $fillable = [
		'apellido',
		'confirmation_token',
		'email',
		'enabled',
		'nombre',
		'password',
		'reset_token',
		'username',
		'id_empresa',
		'google_id'
	];

	public function empresa()
	{
		return $this->belongsTo(Empresa::class, 'id_empresa');
	}

	public function roles()
	{
		return $this->belongsToMany(Role::class, 'usuarios_roles', 'idusuario', 'idrole');
	}
	public function hojaVida()
	{
		return $this->hasOne(HvHojaVida::class, 'id_usuario');
	}
}
