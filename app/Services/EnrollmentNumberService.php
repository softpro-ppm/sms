<?php

namespace App\Services;

use App\Models\Enrollment;

class EnrollmentNumberService
{
    /**
     * Generate unique enrollment number in format: SP20253000
     */
    public static function generateEnrollmentNumber(): string
    {
        $currentYear = date('Y');
        $prefix = "SP{$currentYear}";
        
        // Get the last enrollment number for this year
        $lastEnrollment = Enrollment::where('enrollment_number', 'like', "{$prefix}%")
            ->orderBy('enrollment_number', 'desc')
            ->first();
        
        if ($lastEnrollment) {
            // Extract the number part and increment
            $lastNumber = (int) substr($lastEnrollment->enrollment_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            // First enrollment for this year
            $nextNumber = 3000;
        }
        
        // Format with leading zeros
        $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}{$formattedNumber}";
    }
    
    /**
     * Validate enrollment number format
     */
    public static function isValidFormat(string $enrollmentNumber): bool
    {
        // Format: SP followed by 4-digit year and 4-digit number
        return preg_match('/^SP\d{8}$/', $enrollmentNumber);
    }
    
    /**
     * Get year from enrollment number
     */
    public static function getYearFromEnrollmentNumber(string $enrollmentNumber): ?int
    {
        if (self::isValidFormat($enrollmentNumber)) {
            return (int) substr($enrollmentNumber, 2, 4);
        }
        
        return null;
    }
}