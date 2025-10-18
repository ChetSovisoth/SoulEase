<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PaddlePriceMapper
{
    private array $priceMap;

    /**
     * Initialize price map from config
     */
    public function __construct()
    {
        // Load price IDs from config (which reads from .env)
        $this->priceMap = [
            // Standard durations (required)
            30 => config('services.paddle.prices.30min', 'pri_30min_placeholder'),   // $50.00
            60 => config('services.paddle.prices.60min', 'pri_60min_placeholder'),   // $100.00
            90 => config('services.paddle.prices.90min', 'pri_90min_placeholder'),   // $142.50
            120 => config('services.paddle.prices.120min', 'pri_120min_placeholder'), // $180.00

            // Optional additional durations (uncomment in config if needed)
            // 45 => config('services.paddle.prices.45min', 'pri_45min_placeholder'),   // $75.00
            // 150 => config('services.paddle.prices.150min', 'pri_150min_placeholder'), // $212.50
            // 180 => config('services.paddle.prices.180min', 'pri_180min_placeholder'), // $255.00
        ];
    }

    /**
     * Get Paddle price ID for a given duration
     */
    public function getPriceIdForDuration(int $durationMinutes): ?string
    {
        $priceId = $this->priceMap[$durationMinutes] ?? null;

        if (!$priceId || str_contains($priceId, 'placeholder')) {
            Log::warning('Paddle price ID not configured for duration', [
                'duration' => $durationMinutes,
            ]);
            return null;
        }

        return $priceId;
    }

    /**
     * Get all available durations with their price IDs
     */
    public function getAllPrices(): array
    {
        $prices = [];

        foreach ($this->priceMap as $duration => $priceId) {
            if ($priceId && !str_contains($priceId, 'placeholder')) {
                $prices[] = [
                    'duration_minutes' => $duration,
                    'price_id' => $priceId,
                ];
            }
        }

        return $prices;
    }

    /**
     * Check if price IDs are configured
     */
    public function isPriceConfigured(int $durationMinutes): bool
    {
        $priceId = $this->priceMap[$durationMinutes] ?? null;
        return $priceId && !str_contains($priceId, 'placeholder');
    }
}
