<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Str;



class BrokerController extends Controller

{
//     public function index(Request $request)
// {
//     $data = Helper::fetchBrokerData();

//     if (isset($data['error'])) {
//         return response()->json(['error' => $data['error']], 500);
//     }

//     $data = collect($data);

//     // Date Filtering
//     if ($request->filled('start_date') && $request->filled('end_date')) {
//         $startDate = Carbon::parse($request->start_date);
//         $endDate = Carbon::parse($request->end_date);

//         $data = $data->filter(function ($item) use ($startDate, $endDate) {
//             $date = Carbon::createFromFormat('d-M-y', $item['GDH_ISSUEDATE']);
//             return $date->between($startDate, $endDate);
//         });
//     }

//     // Category Mapping
//     $categoryMapping = [
//         'Fire' => 11,
//         'Marine' => 12,
//         'Motor' => 13,
//         'Miscellaneous' => 14,
//         'Health' => 16,
//     ];

//     // PDP_DEPT_CODE Filtering
//     if ($request->filled('new_category')) {
//         $selectedNewCategory = $request->new_category;
//         $allowedNewCode = $categoryMapping[$selectedNewCategory] ?? null;

//         if ($allowedNewCode !== null) {
//             $data = $data->filter(function ($item) use ($allowedNewCode) {
//                 return isset($item['PDP_DEPT_CODE']) &&
//                     Str::startsWith($item['PDP_DEPT_CODE'], (string) $allowedNewCode);
//             });
//         }
//     }

//     return view('broker.index', compact('data'));
// }

// public function index(Request $request)
// {
//     // 1. Get all brokers for dropdown
//     $brokers = Helper::fetchBroker();
//     if (isset($brokers['error'])) {
//         return back()->with('error', $brokers['error']);
//     }

//     // Filter brokers
//     $brokers = collect($brokers)->filter(function ($broker) {
//         return isset($broker['PPS_STATUS']) && $broker['PPS_STATUS'] === 'A';
//     });

//     // 2. Get broker data
//     $data = Helper::fetchBrokerData();
//     if (isset($data['error'])) {
//         return response()->json(['error' => $data['error']], 500);
//     }

//     $data = collect($data['data']); // Collect only the relevant data

//     // Date Filtering
//     if ($request->filled('start_date') && $request->filled('end_date')) {
//         $startDate = Carbon::parse($request->start_date);
//         $endDate = Carbon::parse($request->end_date);

//         $data = $data->filter(function ($item) use ($startDate, $endDate) {
//             $date = Carbon::createFromFormat('d-M-y', $item['GDH_ISSUEDATE']);
//             return $date->between($startDate, $endDate);
//         });
//     }

//     // Category Mapping
//     $categoryMapping = [
//         'Fire' => 11,
//         'Marine' => 12,
//         'Motor' => 13,
//         'Miscellaneous' => 14,
//         'Health' => 16,
//     ];

//     // PDP_DEPT_CODE Filtering
//     if ($request->filled('new_category')) {
//         $selectedNewCategory = $request->new_category;
//         $allowedNewCode = $categoryMapping[$selectedNewCategory] ?? null;

//         if ($allowedNewCode !== null) {
//             $data = $data->filter(function ($item) use ($allowedNewCode) {
//                 return isset($item['PDP_DEPT_CODE']) &&
//                     Str::startsWith($item['PDP_DEPT_CODE'], (string) $allowedNewCode);
//             });
//         }
//     }

//     // 3. Get renewal data
//     $renewalResponse = Helper::fetchRenewal();
//     if (isset($renewalResponse['error'])) {
//         return back()->with('error', $renewalResponse['error']);
//     }

//     $currentBrokerCode = $renewalResponse['current_broker'] ?? null; // Handle null case
//     $renewalData = $renewalResponse['data'];

//     // Return the view with all necessary data
//     return view('broker.index', compact('brokers', 'data', 'currentBrokerCode', 'renewalData'));
// }

// public function index(Request $request)
// {
//     // 1. Get all brokers for dropdown
//     $brokers = Helper::fetchBroker();
//     if (isset($brokers['error'])) {
//         return back()->with('error', $brokers['error']);
//     }

//     // Filter brokers
//     $brokers = collect($brokers)->filter(function ($broker) {
//         return isset($broker['PPS_STATUS']) && $broker['PPS_STATUS'] === 'A';
//     });

//     // 2. Get broker data
//     $data = Helper::fetchBrokerData();
//     if (isset($data['error'])) {
//         return response()->json(['error' => $data['error']], 500);
//     }

//     $data = collect($data['data']); // Collect only the relevant data

//     // Filter by selected broker code if provided
//     if ($request->filled('broker_code')) {
//         $selectedBrokerCode = $request->broker_code;

//         $data = $data->filter(function ($item) use ($selectedBrokerCode) {
//             return isset($item['broker_code']) && $item['broker_code'] === $selectedBrokerCode;
//         });
//     }

//     // Date Filtering
//     if ($request->filled('start_date') && $request->filled('end_date')) {
//         $startDate = Carbon::parse($request->start_date);
//         $endDate = Carbon::parse($request->end_date);

//         $data = $data->filter(function ($item) use ($startDate, $endDate) {
//             $date = Carbon::createFromFormat('d-M-y', $item['GDH_ISSUEDATE']);
//             return $date->between($startDate, $endDate);
//         });
//     }

//     // Category Mapping
//     $categoryMapping = [
//         'Fire' => 11,
//         'Marine' => 12,
//         'Motor' => 13,
//         'Miscellaneous' => 14,
//         'Health' => 16,
//     ];

//     // PDP_DEPT_CODE Filtering
//     if ($request->filled('new_category')) {
//         $selectedNewCategory = $request->new_category;
//         $allowedNewCode = $categoryMapping[$selectedNewCategory] ?? null;

//         if ($allowedNewCode !== null) {
//             $data = $data->filter(function ($item) use ($allowedNewCode) {
//                 return isset($item['PDP_DEPT_CODE']) &&
//                     Str::startsWith($item['PDP_DEPT_CODE'], (string) $allowedNewCode);
//             });
//         }
//     }

//     // 3. Get renewal data
//     $renewalResponse = Helper::fetchRenewal();
//     if (isset($renewalResponse['error'])) {
//         return back()->with('error', $renewalResponse['error']);
//     }

//     $currentBrokerCode = $renewalResponse['current_broker'] ?? null; // Handle null case
//     $renewalData = $renewalResponse['data'];

//     // Return the view with all necessary data
//     return view('broker.index', compact('brokers', 'data', 'currentBrokerCode', 'renewalData'));
// }
// with filter record
// public function index(Request $request)
// {
//     // 1. Get all brokers for dropdown
//     $brokers = Helper::fetchBroker();
//     if (isset($brokers['error'])) {
//         return back()->with('error', $brokers['error']);
//     }

//     // Filter brokers
//     $brokers = collect($brokers)->filter(function ($broker) {
//         return isset($broker['PPS_STATUS']) && $broker['PPS_STATUS'] === 'A';
//     });

//     // 2. Get renewal data (kept out from filtering)
//     $renewalResponse = Helper::fetchRenewal();
//     if (isset($renewalResponse['error'])) {
//         return back()->with('error', $renewalResponse['error']);
//     }

//     $currentBrokerCode = $renewalResponse['current_broker'] ?? null; // Handle null case
//     $renewalData = $renewalResponse['data'];

//     // 3. Get broker data
//     $data = Helper::fetchBrokerData();
//     if (isset($data['error'])) {
//         return response()->json(['error' => $data['error']], 500);
//     }

//     $data = collect($data['data']); // Collect only the relevant data

//     // Filter by selected broker code if provided
//     if ($request->filled('broker_code')) {
//         $selectedBrokerCode = $request->broker_code;

//         $data = $data->filter(function ($item) use ($selectedBrokerCode) {
//             return isset($item['broker_code']) && $item['broker_code'] === $selectedBrokerCode;
//         });
//     }

//     // Date Filtering
//     if ($request->filled('start_date') && $request->filled('end_date')) {
//         $startDate = Carbon::parse($request->start_date);
//         $endDate = Carbon::parse($request->end_date);

//         $data = $data->filter(function ($item) use ($startDate, $endDate) {
//             $date = Carbon::createFromFormat('d-M-y', $item['GDH_ISSUEDATE']);
//             return $date->between($startDate, $endDate);
//         });
//     }

//     // Category Mapping
//     $categoryMapping = [
//         'Fire' => 11,
//         'Marine' => 12,
//         'Motor' => 13,
//         'Miscellaneous' => 14,
//         'Health' => 16,
//     ];

//     // PDP_DEPT_CODE Filtering
//     if ($request->filled('new_category')) {
//         $selectedNewCategory = $request->new_category;
//         $allowedNewCode = $categoryMapping[$selectedNewCategory] ?? null;

//         if ($allowedNewCode !== null) {
//             $data = $data->filter(function ($item) use ($allowedNewCode) {
//                 return isset($item['PDP_DEPT_CODE']) &&
//                     Str::startsWith($item['PDP_DEPT_CODE'], (string) $allowedNewCode);
//             });
//         }
//     }

//     // Return the view with all necessary data
//     return view('broker.index', compact('brokers', 'data', 'currentBrokerCode', 'renewalData'));
// }
// without filter record 
public function index(Request $request)
{
    // 1. Get all brokers for dropdown
    $brokers = Helper::fetchBroker();
    if (isset($brokers['error'])) {
        return back()->with('error', $brokers['error']);
    }
   // dd($brokers);

    // Filter brokers
    $brokers = collect($brokers)->filter(function ($broker) {
        return isset($broker['PPS_STATUS']) && $broker['PPS_STATUS'] === 'A';
    });

    // 2. Get renewal data (kept out from filtering)
    $renewalResponse = Helper::fetchRenewal();
    if (isset($renewalResponse['error'])) {
        return back()->with('error', $renewalResponse['error']);
    }
//dd($renewalResponse);
    $currentBrokerCode = $renewalResponse['current_broker'] ?? null; // Handle null case
    //dd($currentBrokerCode );
    $renewalData = $renewalResponse['data'];


    //dd($renewalData);
    // 3. Get broker data
    $data = Helper::fetchBrokerData();
    if (isset($data['error'])) {
        return response()->json(['error' => $data['error']], 500);
    }

    $data = collect($data['data']); // Collect only the relevant data

    // Note: No filtering by broker code here

    // Date Filtering
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $data = $data->filter(function ($item) use ($startDate, $endDate) {
            $date = Carbon::createFromFormat('d-M-y', $item['GDH_ISSUEDATE']);
            return $date->between($startDate, $endDate);
        });
    }

    // Category Mapping
    $categoryMapping = [
        'Fire' => 11,
        'Marine' => 12,
        'Motor' => 13,
        'Miscellaneous' => 14,
        'Health' => 16,
    ];

    // PDP_DEPT_CODE Filtering
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
    return view('broker.index', compact('brokers', 'data', 'currentBrokerCode', 'renewalData'));
}

     
}
