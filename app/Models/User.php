<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activated_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
    protected $fillable = array('first_name', 'last_name', 'email', 'phone', 'city_id', 'is_active', 'lat', 'long', 'active_code', 'is_verified', 'address');

    public function sendActiveCode()
    {
        $this->active_code = env('APP_ENV') != 'production' ? 1234 : rand(1000,9999);
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
        // Example: Allow access for users with a specific role or condition
        return $this->hasRole('super_admin');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function customNotifications()
    {
        return $this->morphToMany('App\Models\CustomNotification', 'notifiable');
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
        return $this->morphMany('App\Models\Comment', 'commentable');
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
            return $this->provider->store_name;
        }
        return $this->first_name . ' ' . $this->last_name;
    }

}