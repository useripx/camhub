<?php

declare(strict_types=1);

namespace App\Services;

class RentalCalculatorService
{
    public const ESCROW_FEE = 5000.00; // Rp 5.000 Biaya Layanan Rekber Escrow

    /**
     * Calculate rental pricing estimate
     *
     * @param float $dailyPrice Tarif sewa per hari
     * @param int $durationDays Jumlah hari sewa (minimal 1)
     * @return array Rincian estimasi biaya sewa
     */
    public function calculate(float $dailyPrice, int $durationDays): array
    {
        $durationDays = max(1, $durationDays);
        $baseRentalFee = $dailyPrice * $durationDays;

        // Tiered Duration Discounts
        $discountRate = 0.0;
        if ($durationDays >= 14) {
            $discountRate = 0.15; // 15% discount for >= 14 days
        } elseif ($durationDays >= 7) {
            $discountRate = 0.10; // 10% discount for >= 7 days
        } elseif ($durationDays >= 3) {
            $discountRate = 0.05; // 5% discount for >= 3 days
        }

        $discountAmount = round($baseRentalFee * $discountRate);
        $subtotal = $baseRentalFee - $discountAmount;
        $escrowFee = self::ESCROW_FEE;
        $totalPrice = $subtotal + $escrowFee;

        return [
            'duration_days'          => $durationDays,
            'daily_price'            => $dailyPrice,
            'formatted_daily_price'  => 'Rp ' . number_format($dailyPrice, 0, ',', '.'),
            'base_rental_fee'        => $baseRentalFee,
            'formatted_base_fee'     => 'Rp ' . number_format($baseRentalFee, 0, ',', '.'),
            'discount_percent'       => ($discountRate * 100),
            'discount_amount'        => $discountAmount,
            'formatted_discount'     => 'Rp ' . number_format($discountAmount, 0, ',', '.'),
            'subtotal'               => $subtotal,
            'formatted_subtotal'     => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            'escrow_fee'             => $escrowFee,
            'formatted_escrow_fee'   => 'Rp ' . number_format($escrowFee, 0, ',', '.'),
            'total_price'            => $totalPrice,
            'formatted_total_price'  => 'Rp ' . number_format($totalPrice, 0, ',', '.'),
        ];
    }

    /**
     * Calculate duration days between two date strings (YYYY-MM-DD)
     */
    public function calculateDaysBetween(string $startDate, string $endDate): int
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);

        if (!$start || !$end || $end < $start) {
            return 1;
        }

        $diffSeconds = $end - $start;
        $days = (int) ceil($diffSeconds / 86400);

        // Same day rental counts as 1 day
        return max(1, $days);
    }
}
