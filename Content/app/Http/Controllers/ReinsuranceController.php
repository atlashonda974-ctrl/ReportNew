<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\Helper;


class ReinsuranceController extends Controller

{
    public function index(Request $request)
    {
        $queryParams = [];
        $validationError = null;
        $startInput = $request->start_date;
        $endInput = $request->end_date;
        $apiDateRange = null;
    
        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $start = Carbon::createFromFormat('Y-m-d', $startInput);
                $end = Carbon::createFromFormat('Y-m-d', $endInput);
    
                if ($start->diffInDays($end) > 60) {
                    $validationError = 'The date range should not exceed 60 days.';
                    return view('reinsurance.index', compact(
                        'validationError', 'startInput', 'endInput', 'apiDateRange'
                    ))->with('data', [])->with('recordCount', 0);
                }
    
                $queryParams['start_date'] = $start->format('d-m-y');
                $queryParams['end_date'] = $end->format('d-m-y');
                $apiDateRange = 'Date Range: ' . $start->format('d-M-y') . ' to ' . $end->format('d-M-y');
    
            } catch (\Exception $e) {
                Log::error('Date parsing error: ' . $e->getMessage());
                return view('reinsurance.index', [
                    'data' => [],
                    'error' => 'Invalid date format.',
                    'startInput' => null,
                    'endInput' => null,
                    'apiDateRange' => null,
                    'recordCount' => 0,
                ]);
            }
        }
    
        // 🔥 Fetch from API
        $result = Helper::fetchReinsurance($queryParams);
    
        // 🔥 If no input dates given, get them from API response
        // Only extract from API if user didn't input dates
        if (!$startInput && !$endInput && isset($result['raw_response'])) {
            // Extract dates from the API response
            if (
                preg_match('/Input dates:\s*(\d{2}-\d{2}-\d{2})\s*to\s*(\d{2}-\d{2}-\d{2})/', $result['raw_response'], $matches) &&
                preg_match('/Date Range:\s*(\d{2}-[A-Za-z]{3}-\d{2})\s*to\s*(\d{2}-[A-Za-z]{3}-\d{2})/', $result['raw_response'], $rangeMatch)
            ) {
                $startInput = Carbon::createFromFormat('d-m-y', $matches[1])->format('Y-m-d');
                $endInput = Carbon::createFromFormat('d-m-y', $matches[2])->format('Y-m-d');
                $apiDateRange = 'Date Range: ' . $rangeMatch[1] . ' to ' . $rangeMatch[2];
            }
        }

    
        return view('reinsurance.index', [
            'data' => $result['data'] ?? [],
            'recordCount' => count($result['data'] ?? []),
            'error' => $result['success'] ? $validationError : $result['message'],
            'startInput' => $startInput,
            'endInput' => $endInput,
            'apiDateRange' => $apiDateRange,
        ]);
    }
    


    
}
