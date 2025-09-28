<?php

namespace App\Services;

use App\Models\Provider;
use App\Models\DayProvider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProviderDaysService
{
    /**
     * Get working days for a provider
     */
    public function getProviderDays(Provider $provider): Collection
    {
        return $provider->days()->with('day')->orderBy('day_id')->get();
    }

    /**
     * Update working days for a provider
     */
    public function updateProviderDays(Provider $provider, array $daysData): bool
    {
        try {
            DB::beginTransaction();

            foreach ($daysData as $dayData) {
                $dayProvider = $provider->days()
                    ->where('day_id', $dayData['day_id'])
                    ->first();

                if ($dayProvider) {
                    $updateData = [
                        'is_closed' => $dayData['is_closed'],
                    ];

                    $updateData['from'] = $dayData['from'];
                    $updateData['to'] = $dayData['to'];
                    $updateData['is_closed'] = $dayData['is_closed'];
                    

                    $dayProvider->update($updateData);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reset provider days to default (all closed)
     */
    public function resetProviderDays(Provider $provider): bool
    {
        try {
            $provider->days()->update([
                'is_closed' => true,
                'from' => null,
                'to' => null,
            ]);
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Check if provider is currently open
     */
    public function isProviderOpen(Provider $provider): bool
    {
        $now = now();
        $currentDay = $now->format('N'); // 1 (Monday) through 7 (Sunday)
        
        // Convert to match your day IDs (assuming 1=Sunday, 2=Monday, etc.)
        $dayId = $currentDay == 7 ? 1 : $currentDay + 1;
        
        $dayProvider = $provider->days()
            ->where('day_id', $dayId)
            ->first();

        if (!$dayProvider || $dayProvider->is_closed) {
            return false;
        }

        // Check if current time is within working hours
        $currentTime = $now->format('H:i');
        return $currentTime >= $dayProvider->from && $currentTime <= $dayProvider->to;
    }

    /**
     * Get next opening time for provider
     */
    public function getNextOpeningTime(Provider $provider): ?string
    {
        $now = now();
        $currentDay = $now->format('N');
        $dayId = $currentDay == 7 ? 1 : $currentDay + 1;
        
        // Check current day first
        $dayProvider = $provider->days()
            ->where('day_id', $dayId)
            ->first();

        if ($dayProvider && !$dayProvider->is_closed) {
            $currentTime = $now->format('H:i');
            if ($currentTime < $dayProvider->from) {
                return $dayProvider->from;
            }
        }

        // Check next 7 days for next opening
        for ($i = 1; $i <= 7; $i++) {
            $nextDayId = ($dayId + $i - 1) % 7;
            if ($nextDayId == 0) $nextDayId = 7;
            
            $dayProvider = $provider->days()
                ->where('day_id', $nextDayId)
                ->first();

            if ($dayProvider && !$dayProvider->is_closed) {
                return $dayProvider->from;
            }
        }

        return null;
    }
}
