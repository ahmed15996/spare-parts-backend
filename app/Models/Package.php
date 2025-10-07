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

    public function getFinalPriceAttribute()
    {
        $finalPrice = $this->price;
        if($this->discount && $this->discount > 0){
            $finalPrice = $this->price - $this->discount;
        }
        if(setting('general', 'packages_discount') && setting('general', 'packages_discount') > 0){
            $finalPrice = $finalPrice - ( $this->price * setting('general', 'packages_discount') / 100);
        }
        return $finalPrice;
    }
}