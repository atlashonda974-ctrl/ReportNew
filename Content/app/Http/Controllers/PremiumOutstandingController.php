<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Str;



class PremiumOutstandingController extends Controller

{

public function index(Request $request)
{
    // 1. Get all brokers for dropdown
    $brokers = Helper::fetchBroker();
    if (isset($brokers['error'])) {
        return back()->with('error', $brokers['error']);
    }

    // Filter brokers
    $brokers = collect($brokers)->filter(function ($broker) {
        return isset($broker['PPS_STATUS']) && $broker['PPS_STATUS'] === 'A';
    });

    // 2. Get renewal data
    $renewalResponse = Helper::fetchRenewal();
    if (isset($renewalResponse['error'])) {
        return back()->with('error', $renewalResponse['error']);
    }

    $currentBrokerCode = $renewalResponse['current_broker'] ?? null; // Handle null case
    $renewalData = $renewalResponse['data'];

    // Set default dates if not provided
    $startDate = '2020-01-01'; // Fixed start date
    $endDate = $request->input('end_date', date('Y-m-d'));

    // Fetch data from the premium API with date filtering at API level
    $data = Helper::fetchPremiumData($startDate, $endDate);
    //dd($data);
    if (isset($data['error'])) {
        return response()->json(['error' => $data['error'], 'current_broker' => $currentBrokerCode], 500);
    }

    $data = collect($data['data']); // Collect only the relevant data

    // Calculate outstanding premium for all records
    $data = $data->map(function ($item) {
        $netPremium = is_numeric(str_replace(',', '', $item['GDH_NETPREMIUM'] ?? 0)) ? 
                     floatval(str_replace(',', '', $item['GDH_NETPREMIUM'])) : 0;
        $collectionAmount = is_numeric(str_replace(',', '', $item['KNOCKOFFAMOUNT'] ?? 0)) ? 
                          floatval(str_replace(',', '', $item['KNOCKOFFAMOUNT'])) : 0;

        $item['OUTSTANDING_PREMIUM'] = $netPremium - $collectionAmount;
        return $item;
    });
    //dd($data);

    // Category Mapping
    $categoryMapping = [
        'Fire' => 11,
        'Marine' => 12,
        'Motor' => 13,
        'Miscellaneous' => 14,
        'Health' => 16,
    ];

    // PDP_DEPT_CODE Filtering (client-side)
    if ($request->filled('new_category')) {
        $selectedNewCategory = $request->new_category;
        $allowedNewCode = $categoryMapping[$selectedNewCategory] ?? null;

        if ($allowedNewCode !== null) {
            $data = $data->filter(function ($item) use ($allowedNewCode) {
                return isset($item['PDP_DEPT_CODE']) &&
                    Str::startsWith($item['PDP_DEPT_CODE'], (string) $allowedNewCode);
            });
        }
    }

    // Default: Show outstanding records
    $outstandingOnly = true;

    // Outstanding Premium Filtering
    if ($request->filled('outstanding_filter')) {
        if ($request->outstanding_filter === 'outstanding') {
            $data = $data->filter(function ($item) {
                return ($item['OUTSTANDING_PREMIUM'] ?? 0) > 0;
            });
        } elseif ($request->outstanding_filter === 'all') {
            $outstandingOnly = false;
        }
    } else {
        $data = $data->filter(function ($item) {
            return ($item['OUTSTANDING_PREMIUM'] ?? 0) > 0;
        });
    }

    return view('premium.index', compact('data', 'brokers', 'renewalData', 'currentBrokerCode'));
}

     
}
