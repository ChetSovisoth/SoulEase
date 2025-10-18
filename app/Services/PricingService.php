<?php

namespace App\Services;

use App\Models\TherapySession;
use App\Models\User;

class PricingService
{
    /**
     * Calculate session price based on duration
     * Note: All sessions are priced at $100/hour standard rate
     */
    public function calculateSessionPrice(User $therapist, int $durationMinutes): float
    {
        // Standardized hourly rate for all therapists
        $hourlyRate = 100;

        // Calculate base price based on duration
        $basePrice = ($hourlyRate / 60) * $durationMinutes;

        // Apply duration-based multipliers
        $multiplier = $this->getDurationMultiplier($durationMinutes);

        return round($basePrice * $multiplier, 2);
    }

    /**
     * Get pricing multiplier based on session duration
     * Longer sessions may get discounts or premiums
     */
    private function getDurationMultiplier(int $durationMinutes): float
    {
        return match (true) {
            $durationMinutes <= 30 => 1.0,      // 30-min sessions: standard rate
            $durationMinutes <= 60 => 1.0,      // 60-min sessions: standard rate
            $durationMinutes <= 90 => 0.95,     // 90-min sessions: 5% discount
            $durationMinutes <= 120 => 0.90,    // 120-min sessions: 10% discount
            default => 0.85,                     // 120+ min sessions: 15% discount
        };
    }

    /**
     * Get available session duration options
     */
    public function getAvailableDurations(): array
    {
        return [
            ['value' => 30, 'label' => '30 minutes'],
            ['value' => 60, 'label' => '1 hour'],
            ['value' => 90, 'label' => '1.5 hours'],
            ['value' => 120, 'label' => '2 hours'],
        ];
    }

    /**
     * Calculate price breakdown for display
     * Note: All sessions are priced at $100/hour standard rate
     */
    public function getPriceBreakdown(User $therapist, int $durationMinutes): array
    {
        // Standardized hourly rate for all therapists
        $hourlyRate = 100;
        $basePrice = ($hourlyRate / 60) * $durationMinutes;
        $multiplier = $this->getDurationMultiplier($durationMinutes);
        $finalPrice = round($basePrice * $multiplier, 2);
        $discount = round($basePrice - $finalPrice, 2);

        return [
            'hourly_rate' => $hourlyRate,
            'duration_minutes' => $durationMinutes,
            'duration_hours' => round($durationMinutes / 60, 2),
            'base_price' => round($basePrice, 2),
            'multiplier' => $multiplier,
            'discount' => $discount,
            'final_price' => $finalPrice,
            'currency' => 'USD',
        ];
    }
}
