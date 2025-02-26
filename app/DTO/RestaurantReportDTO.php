<?php

namespace App\DTO;

final class RestaurantReportDTO
{
    public function __construct(
        private readonly string $reportNumber,
        private readonly string $timeSpanStartDate,
        private readonly string $timeSpanEndDate,
        private readonly string $generateAt,
        private readonly object $bills,
        private readonly array $totals,
        private readonly string $totalSales,
        private readonly array $restaurant
    ) {
    }

    public function toArray(): array
    {
        return [
            'reportNumber' => $this->reportNumber,
            'timeSpanStartDate' => $this->timeSpanStartDate,
            'timeSpanEndDate' => $this->timeSpanEndDate,
            'generateAt' => $this->generateAt,
            'bills' => $this->bills,
            'totals' => $this->totals,
            'totalSales' => $this->totalSales,
            'restaurant' => $this->restaurant,
        ];
    }

    public function getReportNumber(): string
    {
        return $this->reportNumber;
    }
}
