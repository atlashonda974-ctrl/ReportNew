<?php
namespace App\Services;

class DepreciationService
{
    public function calculateTotals(array $data): array
    {
        $totals = [
            'totalOriginalCost' => 0,
            'totalAccumulatedDepreciation' => 0,
            'totalSaleProceed' => 0,
            'totalNetBookValue' => 0,
            'totalGainLoss' => 0,
        ];

        foreach ($data as $record) {
            $originalCost = $record['Original Cost'] ?? 0;
            $accumulatedDepreciation = $record['Accumulated Depreciation'] ?? 0;
            $saleProceed = $record['Sale Proceed'] ?? 0;
            
            $netBookValue = $originalCost - $accumulatedDepreciation;

            $totals['totalOriginalCost'] += $originalCost;
            $totals['totalAccumulatedDepreciation'] += $accumulatedDepreciation;
            $totals['totalSaleProceed'] += $saleProceed;
            $totals['totalNetBookValue'] += $netBookValue;
            $totals['totalGainLoss'] += $netBookValue - $saleProceed;
        }

        return $totals;
    }
}