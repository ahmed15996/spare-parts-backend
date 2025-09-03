<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Provider;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Actions\Action;

class ReportService extends BaseService
{
    public function __construct(Report $report)
    {
        parent::__construct($report);
    }

    /**
     * Create a report for a given target based on type.
     * $type: 0 => Comment, 1 => Provider
     */
    public function createReport(User $reporter, int $targetId, int $type, ?string $reason = null): Report
    {
        $reportable = match ($type) {
            0 => Comment::query()->find($targetId),
            1 => Provider::query()->find($targetId),
            default => null,
        };

        if (!$reportable) {
            $key = $type === 0 ? 'comment_id' : ($type === 1 ? 'provider_id' : 'model_id');
            throw ValidationException::withMessages([
                $key => __('The selected target was not found.'),
            ]);
        }

        return DB::transaction(function () use ($reporter, $reportable, $reason) {
            /** @var Report $report */
            $report = new Report([
                'reporter_id' => $reporter->id,
                'reason' => $reason,
            ]);
            $reportable->reports()->save($report);
            $this->afterCreate($report);
            return $report->fresh();
        });
    }

    protected function afterCreate(Report $report): void
    {
        // TODO: Send notification to admin

        $this->sendAdminNotification(__('New report'), __('A new report has been received'), [
            Action::make('view')
                ->url(route('filament.admin.resources.reports.view', $report->id))
                ->label(__('Let\'s review it'))
        ]);
    }
}


