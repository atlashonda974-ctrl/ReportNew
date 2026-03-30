<?php

namespace App\Services;

use Carbon\Carbon;

class ReinsFilterService
{
    public function categoryMapping(): array
    {
        return [
            'Fire'          => 11,
            'Marine'        => 12,
            'Motor'         => 13,
            'Miscellaneous' => 14,
            'Health'        => 16,
        ];
    }

    public function buildCounts($collection, callable $dateOf): array
    {
        $today  = Carbon::today();
        $counts = ['all' => $collection->count(), '2days' => 0, '5days' => 0, '7days' => 0, '10days' => 0, '15days' => 0, '15plus' => 0];

        foreach ($collection as $r) {
            $date = $dateOf($r);
            if (!$date || !$date->isValid()) continue;
            $d = $date->diffInDays($today);

            if     ($d <= 2)  $counts['2days']++;
            elseif ($d <= 5)  $counts['5days']++;
            elseif ($d <= 7)  $counts['7days']++;
            elseif ($d <= 10) $counts['10days']++;
            elseif ($d <= 15) $counts['15days']++;
            else              $counts['15plus']++;
        }

        return $counts;
    }

    public function applyTimeFilter($collection, string $timeFilter, callable $dateOf)
    {
        if ($timeFilter === 'all') return $collection;

        $today = Carbon::today();

        return $collection->filter(function ($r) use ($timeFilter, $today, $dateOf) {
            $date = $dateOf($r);
            if (!$date || !$date->isValid()) return false;
            $d = $date->diffInDays($today);

            switch ($timeFilter) {
                case '2days':  return $d <= 2;
                case '5days':  return $d > 2  && $d <= 5;
                case '7days':  return $d > 5  && $d <= 7;
                case '10days': return $d > 7  && $d <= 10;
                case '15days': return $d > 10 && $d <= 15;
                case '15plus': return $d > 15;
                default:       return true;
            }
        });
    }

    public function stampDaysOld($collection, callable $dateOf)
    {
        $today = Carbon::today();

        return $collection->map(function ($r) use ($today, $dateOf) {
            $date        = $dateOf($r);
            $r->days_old = ($date && $date->isValid()) ? $date->diffInDays($today) : null;
            return $r;
        });
    }

    public function groupByAging($data): array
    {
        $now     = Carbon::now();
        $buckets = [
            '0-3 Days'   => [0,  3],
            '4-7 Days'   => [4,  7],
            '8-10 Days'  => [8,  10],
            '11-15 Days' => [11, 15],
            '16-20 Days' => [16, 20],
            '20+ Days'   => [21, PHP_INT_MAX],
        ];

        $grouped = [];
        foreach ($buckets as $label => [$min, $max]) {
            $grouped[$label] = $data->filter(function ($r) use ($now, $min, $max) {
                $date = ($r->GRH_DOCUMENTDATE ?? null)
                    ? Carbon::createFromFormat('d-M-y', $r->GRH_DOCUMENTDATE)
                    : null;
                if (!$date || !$date->isValid()) return false;
                $diff = $date->diffInDays($now);
                return $diff >= $min && $diff <= $max;
            });
        }

        return $grouped;
    }
}