<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PassTem
 * 
 * @property int $id
 * @property int $id_user
 * @property string $password_tem
 * @property string $password_real
 * @property bool $enable
 *
 * @package App\Models
 */
class PassTem extends Model
{
	protected $table = 'pass_tem';
	public $timestamps = false;

	protected $casts = [
		'id_user' => 'int',
		'enable' => 'bool'
	];

	protected $fillable = [
		'id_user',
		'password_tem',
		'password_real',
		'enable'
	];
}
