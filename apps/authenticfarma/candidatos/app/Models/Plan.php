<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'products';

    protected $casts = [
        'price' => 'float',
    ];

	protected $fillable = [
		'name',
		'description',
		'price',
		'image_url',
        'status',
	];

    public function details()
    {
        return $this->hasMany(ProductDetail::class, 'product_id');
    }
}
