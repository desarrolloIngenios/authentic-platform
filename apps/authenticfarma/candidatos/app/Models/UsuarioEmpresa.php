<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UsuarioEmpresa
 * 
 * @property int $idusuario
 * @property int $idempresa
 *
 * @package App\Models
 */
class UsuarioEmpresa extends Model
{
	protected $table = 'usuario_empresa';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'idusuario' => 'int',
		'idempresa' => 'int'
	];
}
