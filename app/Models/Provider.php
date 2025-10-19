<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Provider extends Model implements HasMedia 
{
    use InteractsWithMedia, HasTranslations;

    protected $table = 'providers';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = array('user_id', 'store_name', 'description', 'commercial_number', 'location', 'category_id', 'city_id', 'slug');
    public $translatable = ['store_name'];
    
    protected $casts = [
        'store_name' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_provider','provider_id','brand_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product','provider_id');
    }

    public function posts()
    {
        return $this->morphMany('App\Models\Post', 'author');
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'author');
    }

    public function days()
    {
        return $this->hasMany('App\Models\DayProvider', 'provider_id','id');
    }

    public function adminRequests()
    {
        return $this->morphMany('App\Models\AdminRequest', 'requestable');
    }

    public function offers()
    {
        return $this->hasMany('App\Models\Offer');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Models\Subscription','provider_id');
    }

    public function banners()
    {
        return $this->hasMany('App\Models\Banner');
    }

    public function activeProfileBanners()
    {
        return $this->hasMany('App\Models\Banner')->where('status', 2)
        ->whereIn('type', [2,3]);
    }

    public function hasActiveSubscription()
    {
        return $this->subscriptions->where('is_active', true)->first() ? true : false;
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    /**
     * Get the hidden requests for this provider.
     */
    public function hiddenRequests()
    {
        return $this->hasMany(ProviderHiddenRequest::class);
    }

    /**
     * Get the requests that this provider has hidden.
     */
    public function hiddenRequestsList()
    {
        return $this->belongsToMany(Request::class, 'provider_hidden_requests');
    }

    /**
     * Get the average rating for this provider (last 3 months)
     */
    public function getAverageRating(): float
    {
        return Review::calculateProviderRating($this->id);
    }

    /**
     * Get the total number of reviews for this provider (last 3 months)
     */
    public function getReviewCount(): int
    {
        return Review::getProviderReviewCount($this->id);
    }

    /**
     * Check if provider is currently open and return status with closing time
     */
    public function isCurrentlyOpen(): array
    {
        $now = now();
        $currentDay = $now->format('N'); // 1 (Monday) through 7 (Sunday)
        
        // Convert to match your day IDs (1=Saturday, 2=Sunday, 3=Monday, etc.)
        $dayId = $currentDay == 6 ? 1 : ($currentDay == 7 ? 2 : $currentDay + 2);
        
        $dayProvider = $this->days()
            ->where('day_id', $dayId)
            ->first();

        if (!$dayProvider || $dayProvider->is_closed) {
            return [
                'is_open' => false,
                'text' => __('closed now'),
                'to' => null,
            ];
        }

        // Check if current time is within working hours
        $currentTime = $now->format('H:i');
        $isOpen = $currentTime >= $dayProvider->from && $currentTime <= $dayProvider->to;
        
        return [
            'is_open' => $isOpen,
            'text' => __('open now'),
            'to' => $isOpen ? Carbon::parse($dayProvider->to)->format('H:i') : null,
        ];
    }

    /**
     * Get working hours for a specific day
     */
    public function getWorkingHours(int $dayId): ?array
    {
        $dayProvider = $this->days()
            ->where('day_id', $dayId)
            ->first();

        if (!$dayProvider || $dayProvider->is_closed) {
            return null;
        }

        return [
            'from' => $dayProvider->from,
            'to' => $dayProvider->to,
            'is_closed' => false,
        ];
    }

    public static function boot(){
        parent::boot();
        static::created(function($provider){
            $days = Day::all();
            foreach($days as $day){
                $provider->days()->create([
                    'day_id' => $day->id,
                    'is_closed' => true,
                    'from'=>'02:00',
                    'to'=>"15:20"
                ]);
            }
        });
    }
}