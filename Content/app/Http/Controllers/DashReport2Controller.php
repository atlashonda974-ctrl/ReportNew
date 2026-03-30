<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use App\Models\BranchesList;


class DashReport2Controller extends Controller
{

 public function getOutDoData(Request $request)
{
    
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $formStartDate = $startDate ?? Carbon::now()->startOfYear()->format('Y-m-d');
    $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

    // 2. Get data from helper
    $result = Helper::getDoReport($formStartDate, $formEndDate);
    $data = collect($result['data'] ?? []);
    //dd($data);

    $apiDateFrom = Carbon::parse($formStartDate)->format('d-M-Y');
    $apiDateTo = Carbon::parse($formEndDate)->format('d-M-Y');


    
        
    
   
   // dd($uniqueCategories);

   
    $branchCode = $request->input('location_category');
    $matchedBranch = BranchesList::where('fbracode', $branchCode)->first();

    $branches = $matchedBranch ? collect([$matchedBranch]) : BranchesList::all();

 
    return view('do.index', [
        'data' => $data,
        'start_date' => $formStartDate,
        'end_date' => $formEndDate,
       
        'api_date_range' => [
            'from' => $apiDateFrom,
            'to' => $apiDateTo,
        ],
        'branches' => $branches,
    ]);
}

 public function getOutstandingData(Request $request)
{
    // 1. Get dates from request or set defaults
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $formStartDate = $startDate ?? Carbon::now()->startOfYear()->format('Y-m-d');
    $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

    // 2. Get data from helper
    $result = Helper::getOutstandingData($formStartDate, $formEndDate);
    $data = collect($result['data'] ?? []);

    // 3. Format dates for display
    $apiDateFrom = Carbon::parse($formStartDate)->format('d-M-Y');
    $apiDateTo = Carbon::parse($formEndDate)->format('d-M-Y');

    // 4. Category filtering (by new_category)
    $categoryMapping = [
        'Fire' => 11,
        'Marine' => 12,
        'Motor' => 13,
        'Miscellaneous' => 14,
        'Health' => 16,
    ];

    if ($request->filled('new_category')) {
        $selectedCategory = $request->new_category;
        if ($deptCode = $categoryMapping[$selectedCategory] ?? null) {
            $data = $data->filter(function ($item) use ($deptCode) {
                return Str::startsWith($item['PDP_DEPT_CODE'] ?? '', (string) $deptCode);
            });
        }
    }

    // 5. Branch filtering by PLC_LOCADESC (dropdown)
    if ($request->filled('location_category')) {
        $selectedLocation = $request->location_category;
        $data = $data->filter(function ($item) use ($selectedLocation) {
            return Str::contains($item['PLC_LOCADESC'], $selectedLocation);
        });
    }

    // 6. Extract unique categories from filtered data
    $uniqueCategories = $data->pluck('PLC_LOCADESC')->filter()->unique()->sort()->values();

    // 7. Fetch branch info from branches_list table by fbracode
    $branchCode = $request->input('location_category');
    $matchedBranch = BranchesList::where('fbracode', $branchCode)->first();

    // . Set branches for dropdown: single or all
    $branches = $matchedBranch ? collect([$matchedBranch]) : BranchesList::all();

    // 10. Return view
    return view('os.index', [
        'data' => $data,
        'start_date' => $formStartDate,
        'end_date' => $formEndDate,
        'uniqueCategories' => $uniqueCategories,
        'api_date_range' => [
            'from' => $apiDateFrom,
            'to' => $apiDateTo,
        ],
        'branches' => $branches,
    ]);
}
public function getOutstandingGroupData(Request $request)
{

    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $formStartDate = $startDate ?? Carbon::now()->subYears(3)->startOfYear()->format('Y-m-d');
    $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

    $result = Helper::getOutstandingGroupData($formStartDate, $formEndDate);
    $data = collect($result['data'] ?? []);

    

    // Grouping data by insured description and calculating totals
    $groupedData = $data->groupBy('PPS_DESC')->map(function ($group) {
        return [
            'PPS_DESC' => $group->first()['PPS_DESC'], 
            'PPS_PARTY_CODE' => $group->first()['PPS_PARTY_CODE'], 
            // Outstanding amounts
            '2025_amt' => $group->where('GDH_YEAR', 2025)->sum(function($item) {
                return $item['TOT_PRE'] - $item['TOT_COL'];
            }),
            '2024_amt' => $group->where('GDH_YEAR', 2024)->sum(function($item) {
                return $item['TOT_PRE'] - $item['TOT_COL']; 
            }),
            '2023_amt' => $group->where('GDH_YEAR', 2023)->sum(function($item) {
                return $item['TOT_PRE'] - $item['TOT_COL']; 
            }),
            '2022_amt' => $group->where('GDH_YEAR', 2022)->sum(function($item) {
                return $item['TOT_PRE'] - $item['TOT_COL']; 
            }),
            // Document counts
            '2025_count' => $group->where('GDH_YEAR', 2025)->count(),
            '2024_count' => $group->where('GDH_YEAR', 2024)->count(),
            '2023_count' => $group->where('GDH_YEAR', 2023)->count(),
            '2022_count' => $group->where('GDH_YEAR', 2022)->count(),
            // Totals
            'Outstanding' => $group->sum(function($item) {
                return $item['TOT_PRE'] - $item['TOT_COL'];
            }),
            'DocumentCount' => $group->count(),
        ];
    })->values();

    $branchCode = $request->input('location_category');
    $matchedBranch = BranchesList::where('fbracode', $branchCode)->first();
    $branches = $matchedBranch ? collect([$matchedBranch]) : BranchesList::all();
    //dd($branches);

    return view('os.group', [
        'data' => $groupedData,
        'start_date' => $formStartDate,
        'end_date' => $formEndDate,
        'branches' => $branches,
    ]);
}


public function getOutstandingGroupBranchData(Request $request)
{

    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $formStartDate = $startDate ?? Carbon::now()->subYears(3)->startOfYear()->format('Y-m-d');
    $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

    $result = Helper::getOutstandingGroupBranchData($formStartDate, $formEndDate);
    $data = collect($result['data'] ?? []);

    

    // Grouping data by insured description and calculating totals
    $groupedData = $data->groupBy('PLC_LOCADESC')->map(function ($group) {
        return [
            'PLC_LOCADESC' => $group->first()['PLC_LOCADESC'], 
            'PLC_LOC_CODE' => $group->first()['PLC_LOC_CODE'], 
            // Outstanding amounts
            '2025_amt' => $group->where('GDH_YEAR', 2025)->sum(function($item) {
                return $item['TOT_PRE'] - $item['TOT_COL'];
            }),
            '2024_amt' => $group->where('GDH_YEAR', 2024)->sum(function($item) {
                return $item['TOT_PRE'] - $item['TOT_COL']; 
            }),
            '2023_amt' => $group->where('GDH_YEAR', 2023)->sum(function($item) {
                return $item['TOT_PRE'] - $item['TOT_COL']; 
            }),
            '2022_amt' => $group->where('GDH_YEAR', 2022)->sum(function($item) {
                return $item['TOT_PRE'] - $item['TOT_COL']; 
            }),
            // Document counts
            '2025_count' => $group->where('GDH_YEAR', 2025)->count(),
            '2024_count' => $group->where('GDH_YEAR', 2024)->count(),
            '2023_count' => $group->where('GDH_YEAR', 2023)->count(),
            '2022_count' => $group->where('GDH_YEAR', 2022)->count(),
            // Totals
            'Outstanding' => $group->sum(function($item) {
                return $item['TOT_PRE'] - $item['TOT_COL'];
            }),
            'DocumentCount' => $group->count(),
        ];
    })->values();
   // dd($groupedData);

    $branchCode = $request->input('location_category');
    $matchedBranch = BranchesList::where('fbracode', $branchCode)->first();
    $branches = $matchedBranch ? collect([$matchedBranch]) : BranchesList::all();
    //dd($branches);

    return view('os.branch', [
        'data' => $groupedData,
        'start_date' => $formStartDate,
        'end_date' => $formEndDate,
        'branches' => $branches,
    ]);
}

public function getOutstandingTimelineData(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Default date values
    $formStartDate = $startDate ?? Carbon::now()->subYears(3)->startOfYear()->format('Y-m-d');
    $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

    // Fetch data
    $result = Helper::getOutstandingTimeline($formStartDate, $formEndDate);
    $data = collect($result['data'] ?? []);

    // Grouping data by insured description and calculating totals
    $groupedData = $data->groupBy('PPS_DESC')->map(function ($group) {
        $counts = [
            '0-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '91-120' => 0,
            '121-180' => 0,
            '180+' => 0,
            'Total' => 0,
        ];

        $amounts = [
            '0-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '91-120' => 0,
            '121-180' => 0,
            '180+' => 0,
        ];

        foreach ($group as $item) {
            // Calculate the number of days since the issue date
            $days = Carbon::now()->diffInDays(Carbon::parse($item['GDH_ISSUEDATE']));
            if ($days <= 30) {
                $counts['0-30']++;
                $amounts['0-30'] += $item['TOT_PRE'] - $item['TOT_COL'];
            } elseif ($days <= 60) {
                $counts['31-60']++;
                $amounts['31-60'] += $item['TOT_PRE'] - $item['TOT_COL'];
            } elseif ($days <= 90) {
                $counts['61-90']++;
                $amounts['61-90'] += $item['TOT_PRE'] - $item['TOT_COL'];
            } elseif ($days <= 120) {
                $counts['91-120']++;
                $amounts['91-120'] += $item['TOT_PRE'] - $item['TOT_COL'];
            } elseif ($days <= 180) {
                $counts['121-180']++;
                $amounts['121-180'] += $item['TOT_PRE'] - $item['TOT_COL'];
            } else {
                $counts['180+']++;
                $amounts['180+'] += $item['TOT_PRE'] - $item['TOT_COL'];
            }
            $counts['Total']++;
        }

        return [
            'PPS_DESC' => $group->first()['PPS_DESC'],
            'PPS_PARTY_CODE' => $group->first()['PPS_PARTY_CODE'],
            'Counts' => $counts,
            'Amounts' => $amounts,
            'Outstanding' => array_sum($amounts), 
            'DocumentCount' => $group->count(),
        ];
    })->values();

    // Get branch data
    $branchCode = $request->input('location_category');
    $matchedBranch = BranchesList::where('fbracode', $branchCode)->first();
    $branches = $matchedBranch ? collect([$matchedBranch]) : BranchesList::all();

    // Return view with data
    return view('os.timeline', [
        'data' => $groupedData,
        'start_date' => $formStartDate,
        'end_date' => $formEndDate,
        'branches' => $branches,
    ]);
}
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


  
public function getBranchReport(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $branch = $request->input('location_category', '20110');

    $result = Helper::getBranchReport($startDate, $endDate, $branch);

    $defaultStartDate = $startDate ?: Carbon::now()->startOfYear()->format('Y-m-d');
    $defaultEndDate = $endDate ?: Carbon::now()->format('Y-m-d');

    $dataCollection = collect($result['data'] ?? []);

    if ($request->filled('takaful')) {
        $selectedTakaful = $request->takaful;
        $dataCollection = $dataCollection->filter(function($item) use ($selectedTakaful) {
            return isset($item['PLC_LOC_CODE']) && 
                   $item['PLC_LOC_CODE'] === $selectedTakaful;
        });
    }

     $branchCode = $request->input('location_category');
    $matchedBranch = BranchesList::where('fbracode', $branchCode)->first();

    // . Set branches for dropdown: single or all
    $branches = $matchedBranch ? collect([$matchedBranch]) : BranchesList::all();


    return view('cr.index', [
        'data' => $dataCollection->all(),
        'api_url' => $result['api_url'] ?? '',
        'params' => $result['params'] ?? [],
        'branches' => $branches,
        'start_date' => $defaultStartDate,
        'end_date' => $defaultEndDate,
        'selected_branch' => $branch,
       
    ]);
}
 
  


}