<?php

namespace App\Services;

use Carbon\Carbon;

class BatchDurationService
{
    /**
     * Calculate end date based on course duration (total calendar days).
     * Course duration = working days + sundays. End = Start + (duration - 1) days.
     *
     * @param Carbon $startDate
     * @param int $courseDurationDays Total calendar days
     * @return Carbon
     */
    public static function calculateEndDate(Carbon $startDate, int $courseDurationDays): Carbon
    {
        return $startDate->copy()->addDays($courseDurationDays - 1);
    }
    
    /**
     * Calculate total days as working days + sundays
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    public static function calculateTotalDays(Carbon $startDate, Carbon $endDate): int
    {
        $workingDays = self::calculateWorkingDays($startDate, $endDate);
        $sundays = self::calculateSundays($startDate, $endDate);

        return $workingDays + $sundays;
    }
    
    /**
     * Calculate working days (excluding Sundays)
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    public static function calculateWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $workingDays = 0;
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            // Count only non-Sunday days
            if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
                $workingDays++;
            }
            $currentDate->addDay();
        }
        
        return $workingDays;
    }
    
    /**
     * Calculate Sundays between two dates
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    public static function calculateSundays(Carbon $startDate, Carbon $endDate): int
    {
        $sundays = 0;
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            if ($currentDate->dayOfWeek === Carbon::SUNDAY) {
                $sundays++;
            }
            $currentDate->addDay();
        }
        
        return $sundays;
    }
    
    /**
     * Get duration breakdown for display
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int $courseDurationDays
     * @return array
     */
    public static function getDurationBreakdown(Carbon $startDate, Carbon $endDate, int $courseDurationDays): array
    {
        $totalDays = self::calculateTotalDays($startDate, $endDate);
        $workingDays = self::calculateWorkingDays($startDate, $endDate);
        $sundays = self::calculateSundays($startDate, $endDate);
        
        return [
            'total_days' => $totalDays,
            'working_days' => $workingDays,
            'sundays' => $sundays,
            'course_duration' => $courseDurationDays,
            'is_accurate' => $totalDays === $courseDurationDays,
            'difference' => $totalDays - $courseDurationDays
        ];
    }
}
