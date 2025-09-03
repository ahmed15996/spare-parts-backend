<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\StoreReportRequest;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService)
    {
    }

    public function store(StoreReportRequest $request): JsonResponse
    {
        $data = $request->validated();
        $report = $this->reportService->createReport(
            Auth::user(),
            (int) $data['model_id'],
            (int) $data['model_type'],
            $data['reason'] ?? null
        );

        return $this->successResponse([], __('Report submitted successfully'));
    }
}


