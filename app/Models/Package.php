<?php

namespace App\Models;

use App\Enums\BannerType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;
use function App\Helpers\setting;

class Package extends Model 
{

    use HasTranslations;
    protected $table = 'packages';
    public $timestamps = true;
    protected $fillable = array('name', 'description', 'price', 'banner_type', 'duration', 'discount');

    public $translatable = ['name', 'description'];
    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'banner_type' => BannerType::class,
        'price' => 'decimal:2',
        'discount' => 'integer',
    ];

    public static function boot()
    {
        parent::boot();
        //after create or update,delete the cache
        static::saved(function () {
            Cache::forget('packages');
        });
        static::deleted(function () {
            Cache::forget('packages');
        });
    }

    public function getFinalPriceAttribute(): float
    {
        $finalPrice = (float) $this->price;
        
        // Apply package-specific discount
        if($this->discount && $this->discount > 0){
            $finalPrice = $finalPrice - (float) $this->discount;
        }
        
        // Apply global packages discount percentage
        $globalDiscount = setting('general', 'packages_discount');
        if($globalDiscount && $globalDiscount > 0){
            $finalPrice = $finalPrice - ($finalPrice * (float) $globalDiscount / 100);
        }
        
        // Ensure price never goes below 0
        return max(0, round($finalPrice, 2));
    }
}