<?php

namespace App\Http\Controllers;

use App\Enums\DeleteAccountReasonType;
use App\Models\DeleteAccountReason;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteAccountReasonController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $type = $request->query('type');
        
        $query = DeleteAccountReason::query();
        
        if ($type) {
            $reasonType = DeleteAccountReasonType::tryFrom((int) $type);
            if ($reasonType) {
                $query->byType($reasonType);
            }
        }
        
        $reasons = $query->orderBy('reason')->get();
        
        return response()->json([
            'success' => true,
            'data' => $reasons->map(function ($reason) {
                return [
                    'id' => $reason->id,
                    'reason' => $reason->reason,
                    'type' => $reason->type->value,
                    'type_label' => $reason->type->label(),
                ];
            })
        ]);
    }
}
