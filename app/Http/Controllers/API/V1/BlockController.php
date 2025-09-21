<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\BlockRequest;
use App\Http\Resources\API\V1\BlockResource;
use App\Models\Block;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockController extends Controller
{
    /**
     * Block a provider/user
     */
    public function block(BlockRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        
        // Check if already blocked
        $existingBlock = Block::where('blocker_id', $user->id)
            ->where('blocked_id', $data['blocked_id'])
            ->first();
            
        if ($existingBlock) {
            return $this->errorResponse(__('User is already blocked'), 409);
        }
        
        // Check if trying to block yourself
        if ($user->id == $data['blocked_id']) {
            return $this->errorResponse(__('You cannot block yourself'), 400);
        }
        
        // Create the block
        $block = Block::create([
            'blocker_id' => $user->id,
            'blocked_id' => $data['blocked_id'],
        ]);
        
        if(!$block){
            return $this->errorResponse(__('Failed to block user'), 500);
        }
        return $this->successResponse([], __('User Blocked successfully'));
    }
    
    /**
     * Unblock a user
     */
    public function unblock(Request $request, $id)
    {
        $user = Auth::user();
        
        $block = Block::where('blocker_id', $user->id)
            ->where('blocked_id', $id)
            ->first();
            
        if (!$block) {
            return $this->errorResponse(__('User is not blocked'), 404);
        }
        
        $block->delete();
        
        return $this->successResponse([], __('User unblocked successfully'));
    }
    
    /**
     * Get list of blocked users
     */
    public function listBlocks(Request $request)
    {
        $user = Auth::user();
        
        $blocks = Block::where('blocker_id', $user->id)
            ->with(['blocked.provider.media', 'blocked.media'])
            ->orderBy('created_at', 'desc')
            ->get();

            if($blocks->isEmpty()){
                return $this->successResponse([], __('No blocked users found'));
            }
            
        return $this->successResponse(
             BlockResource::collection($blocks)
        , __('Blocked users retrieved successfully'));
    }
    
    /**
     * Check if a specific user is blocked
     */
    public function checkBlockStatus(Request $request, $id)
    {
        $user = Auth::user();
        
        $isBlocked = Block::where('blocker_id', $user->id)
            ->where('blocked_id', $id)
            ->exists();
            
        return $this->successResponse([
            'is_blocked' => $isBlocked
        ], __('Block status checked successfully'));
    }
}
