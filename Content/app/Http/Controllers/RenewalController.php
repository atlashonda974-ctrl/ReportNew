<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\Helper;
use Illuminate\Support\Str;



class RenewalController extends Controller

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

    $currentBrokerCode = $renewalResponse['current_broker'] ?? null;
    $renewalData = $renewalResponse['data'];

    // 3. Basic filtering
    $data = collect($renewalData)->filter(function ($item) {
        return isset($item['PDT_DOCTYPE']) && $item['PDT_DOCTYPE'] === 'P';
    });

    // 4. Date filtering
    $startDate = $request->filled('start_date')
        ? date('Y-m-d', strtotime($request->start_date))
        : date('Y-m-d', strtotime('-10 days'));
    
    $endDate = $request->filled('end_date')
        ? date('Y-m-d', strtotime($request->end_date))
        : date('Y-m-d', strtotime('+30 days'));

    $data = $data->filter(function ($item) use ($startDate, $endDate) {
        $date = date('Y-m-d', strtotime($item['GDH_EXPIRYDATE'] ?? ''));
        return $date >= $startDate && $date <= $endDate;
    });

    // 5. Category filtering
    $categoryMapping = [
        'Fire' => 11,
        'Marine' => 12,
        'Motor' => 13,
        'Miscellaneous' => 14,
        'Health' => 16,
    ];

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
    // 6. Return view with all data
    return view('renewal.index', [
        'data' => $data,
        'brokers' => $brokers,
        'currentBrokerCode' => $currentBrokerCode,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'selectedCategory' => $request->new_category,
    ]);
}
     
}
