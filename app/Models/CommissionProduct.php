<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionProduct extends Model
{
    protected $table = 'commission_products';

    protected $fillable = [
        'commission_id',
        'product_id',
        'pieces',
        'value',
    ];

    public function commission()
    {
        return $this->belongsTo(Commission::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}


