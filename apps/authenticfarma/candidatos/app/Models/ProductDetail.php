<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    protected $table = 'product_details';

    protected $fillable = [
        'product_id',
        'description',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'product_id');
    }
} 