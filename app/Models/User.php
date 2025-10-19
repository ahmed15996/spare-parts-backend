<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Modules\Chat\Models\Conversation;
use Spatie\Permission\Traits\HasRoles;
use Filament\Panel;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Spatie\MediaLibrary\InteractsWithMedia;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class User extends Model implements FilamentUser, HasMedia, Authenticatable, AuthorizableContract
{

    use HasFactory, Notifiable, HasRoles, InteractsWithMedia, HasApiTokens, AuthenticatableTrait, Authorizable;

    protected $table = 'users';
    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activated_at' => 'datetime',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }
    protected $fillable = array('first_name', 'last_name', 'email', 'password', 'phone', 'city_id', 'is_active', 'lat', 'long', 'active_code', 'is_verified', 'address');

    public function sendActiveCode()
    {
        // $this->active_code = env('APP_ENV') != 'production' ? 1234 : rand(1000,9999);
        $this->active_code = 1234;
        $this->save();
    }
    public function provider()
    {
        return $this->hasOne(Provider::class);
    }
    public function getFilamentName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    public function canAccessPanel(Panel $panel): bool
    {
        // Allow users with any role EXCEPT 'client' or 'provider' (app users)
        // This allows super_admin, admin, and any other dashboard roles
        if ($this->hasRole(['client', 'provider'])) {
            return false;
        }
        
        // Allow if user has any other role (dashboard users)
        return $this->roles()->where('guard_name', 'web')->exists();
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function customNotifications()
    {
        return $this->morphMany('App\Models\CustomNotification', 'notifiable');
    }

    public function reports()
    {
        return $this->hasMany('App\Models\Report', 'reporter_id');
    }

    public function posts()
    {
        return $this->morphMany('App\Models\Post', 'author');
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'author');
    }

    public function adminRequests()
    {
        return $this->morphMany('App\Models\AdminRequest','requestable');
    }

    public function cars()
    {
        return $this->hasMany('App\Models\Car');
    }

    public function requests()
    {
        return $this->hasMany('App\Models\Request');
    }

    public function fcmTokens()
    {
        return $this->hasMany('App\Models\FcmToken');
    }
    public function getFCMTokens()
    {
        return $this->fcmTokens()->pluck('token')->toArray();
    }
    public function routeNotificationForFcm()
    {
        return $this->getFCMTokens();
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    /**
     * Get users that this user has blocked
     */
    public function blockedUsers()
    {
        return $this->hasMany(Block::class, 'blocker_id');
    }

    /**
     * Get users that have blocked this user
     */
    public function blockedByUsers()
    {
        return $this->hasMany(Block::class, 'blocked_id');
    }

    /**
     * Check if this user has blocked another user
     */
    public function hasBlocked(int $userId): bool
    {
        return $this->blockedUsers()->where('blocked_id', $userId)->exists();
    }

    /**
     * Check if this user is blocked by another user
     */
    public function isBlockedBy(int $userId): bool
    {
        return $this->blockedByUsers()->where('blocker_id', $userId)->exists();
    }

    public static function getAdmins(){
        return self::whereHas('roles', function($query){
            $query->whereIn('name', ['super_admin', 'admin']);
        })->get();
    }

    public function getNameAttribute(){
        if($this->hasRole('client')){
            return $this->first_name . ' ' . $this->last_name;
        }
        if($this->hasRole('provider')){
            // Check if provider relationship exists and has store_name
            if($this->provider && $this->provider->store_name) {
                return $this->provider->store_name;
            }
            // Fallback to full name if provider doesn't exist or has no store_name
            return $this->first_name . ' ' . $this->last_name;
        }
        return $this->first_name . ' ' . $this->last_name;
    }
    public function conversations(){
        return $this->belongsToMany(Conversation::class, 'conversation_user');
    }

}