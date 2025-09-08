<?php

namespace App\Models;

use App\Enums\CommissionType;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $table = 'commissions';

    protected $fillable = [
        'type',
        'value',
        'amount',
        'payed',
        'product_id',
        'user_id',
    ];

    protected $casts = [
        'payed' => 'boolean',
        'type' => CommissionType::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CommissionProduct::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'commission_products')
            ->withPivot(['pieces'])
            ->withTimestamps();
    }
}


