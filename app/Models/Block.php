<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model 
{
    protected $table = 'user_blocks';
    public $timestamps = true;
    protected $fillable = ['blocker_id', 'blocked_id'];

    /**
     * Get the user who created the block
     */
    public function blocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }

    /**
     * Get the user who is blocked
     */
    public function blocked(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_id');
    }

    /**
     * Check if a user is blocked by another user
     */
    public static function isBlocked(int $blockerId, int $blockedId): bool
    {
        return static::where('blocker_id', $blockerId)
            ->where('blocked_id', $blockedId)
            ->exists();
    }

    /**
     * Get all users blocked by a specific user
     */
    public static function getBlockedUsers(int $blockerId)
    {
        return static::where('blocker_id', $blockerId)
            ->with('blocked')
            ->get();
    }
}