<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use App\Models\BranchesList;


class UWController extends Controller
{
    public function getRenewalData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Fetch data using the helper function
        $result = Helper::fetchRenewalData($startDate, $endDate);
        $data = collect($result['data'] ?? []);
        //dd( $data );

        // Default date range
        $defaultStartDate = $startDate ? Carbon::parse($startDate)->format('d-M-Y') : Carbon::now()->startOfYear()->format('d-M-Y');
        $defaultEndDate = $endDate ? Carbon::parse($endDate)->format('d-M-Y') : Carbon::now()->format('d-M-Y');

        // Category mapping
        $categoryMapping = [
            'Fire' => 11,
            'Marine' => 12,
            'Motor' => 13,
            'Miscellaneous' => 14,
            'Health' => 16,
        ];

        // Filter data based on selected category
        if ($request->filled('new_category')) {
            $selectedCategory = $request->new_category;
            if ($deptCode = $categoryMapping[$selectedCategory] ?? null) {
                $data = $data->filter(function($item) use ($deptCode) {
                    return Str::startsWith($item['PDP_DEPT_CODE'] ?? '', (string)$deptCode);
                });
            }
        }

        // Filter branches based on selected location category
        if ($request->filled('location_category')) {
            $selectedLocation = $request->location_category;
            $data = $data->filter(function($item) use ($selectedLocation) {
                return Str::contains($item['PLC_LOCADESC'], $selectedLocation);
            });
        }

        $uniqueCategories = $data->pluck('PLC_LOCADESC')->filter()->unique()->sort()->values();

        // Prepare date range for the API
        $apiDateRangeFrom = Carbon::parse($startDate);
        $apiDateRangeTo = Carbon::parse($endDate);
        
        $branches = BranchesList::all();

        // Return the view with necessary data
        return view('uw.renewal', [
            'data' => $data,
            'start_date' => $defaultStartDate,
            'end_date' => $defaultEndDate,
            'api_date_range' => [
                'from' => $apiDateRangeFrom->format('d-M-Y'),
                'to' => $apiDateRangeTo->format('d-M-Y')
            ],
            'uniqueCategories' => $uniqueCategories,
            'total_records' => count($result['data']),
            'branches' => $branches,
            'filtered_records' => $data->count()
        ]);
    }
}