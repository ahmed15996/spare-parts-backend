<?php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'posts';
    public $timestamps = true;
    protected $fillable = array('author_id', 'author_type', 'content', 'status', 'rejection_reason', 'accepted_at');

    public function author()
    {
        return $this->morphTo();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function getAuthorDisplayNameAttribute(): ?string
    {
        if (!$this->author) {
            return null;
        }
        if (method_exists($this->author, 'hasRole')) {
            if ($this->author->hasRole('client')) {
                return trim(($this->author->first_name ?? '') . ' ' . ($this->author->last_name ?? '')) ?: null;
            }
            if ($this->author->hasRole('provider')) {
                return $this->author->store_name ?? null;
            }
        }
        return $this->author->name ?? null;
    }

    public function getAuthorAvatarUrlAttribute(): ?string
    {
        if (!$this->author || !method_exists($this->author, 'getFirstMediaUrl')) {
            return null;
        }
        return $this->author->getFirstMediaUrl('avatar') ?: null;
    }

    public function getFirstPostMediaUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('posts') ?: null;
    }

    public function getAuthorPhoneAttribute(): ?string
    {
        if (!$this->author) {
            return null;
        }
        if (method_exists($this->author, 'hasRole') && $this->author->hasRole('provider')) {
            return optional($this->author->user)->phone;
        }
        return $this->author->phone ?? null;
    }

}