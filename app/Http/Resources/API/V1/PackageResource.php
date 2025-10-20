<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\setting;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $durationDays = (int) ($this->duration ?? 0);
        $locale = app()->getLocale();

        if ($locale === 'ar') {
            if ($durationDays % 30 === 0 && $durationDays > 0) {
                $months = (int) ($durationDays / 30);
                if ($months === 1) {
                    $durationText = 'شهريا';
                } elseif ($months === 2) {
                    $durationText = 'شهرين';
                } elseif ($months >= 3 && $months <= 10) {
                    $durationText = $months . ' أشهر';
                } else { // 11-12 and above
                    $durationText = $months . ' شهر';
                }
            } else {
                $days = $durationDays;
                if ($days === 1) {
                    $durationText = 'يوم';
                } elseif ($days === 2) {
                    $durationText = 'يومين';
                } elseif ($days >= 3 && $days <= 10) {
                    $durationText = $days . ' أيام';
                } else { // 11-29 and any other non-multiple of 30
                    $durationText = $days . ' يوم';
                }
            }
        } else {
            if ($durationDays % 30 === 0 && $durationDays > 0) {
                $months = (int) ($durationDays / 30);
                if ($months === 1) {
                    $durationText = 'monthly';
                } else {
                    $durationText = $months . ' ' . ($months === 1 ? 'month' : 'months');
                }
            } else {
                $days = $durationDays;
                $durationText = $days . ' ' . ($days === 1 ? 'day' : 'days');
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->finalPrice,
            'banner_type' => $this->banner_type->label(),
            'discount' => setting('general', 'packages_discount')??0,
            'duration' => $durationText,
        ];
    }
}
