<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use App\Models\BranchesList;


class OutstandingReportController extends Controller
{
//    public function getOutstandingData(Request $request)
// {
//     // Get dates from request or set defaults
//     $startDate = $request->input('start_date');
//     $endDate = $request->input('end_date');

//     // Ensure default values for form (input type="date" expects Y-m-d)
//     $formStartDate = $startDate ?? Carbon::now()->startOfYear()->format('Y-m-d');
//     //$formStartDate = $startDate ?? Carbon::now()->subYears(3)->startOfYear()->format('Y-m-d');

//     $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

//     // Get the data from helper
//     $result = Helper::getOutstandingData($formStartDate, $formEndDate);
//     $data = collect($result['data'] ?? []);

//     // Prepare formatted dates for display/API info
//     $apiDateFrom = Carbon::parse($formStartDate)->format('d-M-Y');
//     $apiDateTo = Carbon::parse($formEndDate)->format('d-M-Y');

//     // Filter by category if provided
//     $categoryMapping = [
//         'Fire' => 11,
//         'Marine' => 12,
//         'Motor' => 13,
//         'Miscellaneous' => 14,
//         'Health' => 16,
//     ];

//     if ($request->filled('new_category')) {
//         $selectedCategory = $request->new_category;
//         if ($deptCode = $categoryMapping[$selectedCategory] ?? null) {
//             $data = $data->filter(function($item) use ($deptCode) {
//                 return Str::startsWith($item['PDP_DEPT_CODE'] ?? '', (string)$deptCode);
//             });
//         }
//     }

   

//     // Filter by branch if provided
//     if ($request->filled('branch')) {
//         $branch = $request->branch;
//         $data = $data->filter(function($item) use ($branch) {
//             return isset($item['PLC_LOCADESC']) && $item['PLC_LOCADESC'] == $branch;
//         });
//     }

//     // Prepare unique options for dropdowns
//     $branchOptions = $data->pluck('PLC_LOCADESC')->unique()->filter()->values()->toArray();

//     // Return view with data and form values
//     return view('os.index', [
//         'data' => $data,
//         'start_date' => $formStartDate,
//         'end_date' => $formEndDate,
//         'api_date_range' => [
//             'from' => $apiDateFrom,
//             'to' => $apiDateTo,
//         ],
       
//         'branchOptions' => $branchOptions,
//     ]);
// }

//  public function getOutstandingGroupData(Request $request)
// {
//     // Get dates from request or set defaults
//     $startDate = $request->input('start_date');
//     $endDate = $request->input('end_date');

//     $formStartDate = $startDate ?? Carbon::now()->subYears(3)->startOfYear()->format('Y-m-d');
//     $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

//     $result = Helper::getOutstandingGroupData($formStartDate, $formEndDate);
//     $data = collect($result['data'] ?? []);

//     // Filter by branch if provided
//     if ($request->filled('branch')) {
//         $branch = $request->branch;
//         $data = $data->filter(function($item) use ($branch) {
//             return isset($item['PLC_LOCADESC']) && $item['PLC_LOCADESC'] == $branch;
//         });
//     }

//     // Grouping data by insured description and calculating totals
//     $groupedData = $data->groupBy('PPS_DESC')->map(function ($group) {
//         return [
//             'PPS_DESC' => $group->first()['PPS_DESC'],
//             'PPS_PARTY_CODE' => $group->first()['PPS_PARTY_CODE'],
//             '2025' => $group->where('GDH_YEAR', 2025)->sum('TOT_PRE'),
//             '2024' => $group->where('GDH_YEAR', 2024)->sum('TOT_PRE'),
//             '2023' => $group->where('GDH_YEAR', 2023)->sum('TOT_PRE'),
//             '2022' => $group->where('GDH_YEAR', 2022)->sum('TOT_PRE'),
//             'Outstanding' => $group->sum('TOT_PRE') // Total outstanding for all years
//         ];
//     })->values();

//     // Prepare unique options for dropdowns
//     $branchOptions = $data->pluck('PLC_LOCADESC')->unique()->filter()->values()->toArray();

//     // Return view with data and form values
//     return view('os.group', [
//         'data' => $groupedData,
//         'start_date' => $formStartDate,
//         'end_date' => $formEndDate,
//         'branchOptions' => $branchOptions,
//     ]);
// }
// public function getOutstandingGroupData(Request $request)
// {
//     // Get dates from request or set defaults
//     $startDate = $request->input('start_date');
//     $endDate = $request->input('end_date');

//     $formStartDate = $startDate ?? Carbon::now()->subYears(3)->startOfYear()->format('Y-m-d');
//     $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

//     $result = Helper::getOutstandingGroupData($formStartDate, $formEndDate);
//     $data = collect($result['data'] ?? []);

//     // Filter by branch if provided
//     if ($request->filled('branch')) {
//         $branch = $request->branch;
//         $data = $data->filter(function($item) use ($branch) {
//             return isset($item['PLC_LOCADESC']) && $item['PLC_LOCADESC'] == $branch;
//         });
//     }

//     // Grouping data by insured description and calculating totals
//     $groupedData = $data->groupBy('PPS_DESC')->map(function ($group) {
//         return [
//             'PPS_DESC' => $group->first()['PPS_DESC'], // Insured description
//             'PPS_PARTY_CODE' => $group->first()['PPS_PARTY_CODE'], // Party code
//             '2025' => $group->where('GDH_YEAR', 2025)->sum(function($item) {
//                 return $item['TOT_PRE'] - $item['TOT_COL']; // Calculate outstanding for 2025
//             }),
//             '2024' => $group->where('GDH_YEAR', 2024)->sum(function($item) {
//                 return $item['TOT_PRE'] - $item['TOT_COL']; // Calculate outstanding for 2024
//             }),
//             '2023' => $group->where('GDH_YEAR', 2023)->sum(function($item) {
//                 return $item['TOT_PRE'] - $item['TOT_COL']; // Calculate outstanding for 2023
//             }),
//             '2022' => $group->where('GDH_YEAR', 2022)->sum(function($item) {
//                 return $item['TOT_PRE'] - $item['TOT_COL']; // Calculate outstanding for 2022
//             }),
//             'Outstanding' => $group->sum(function($item) {
//                 return $item['TOT_PRE'] - $item['TOT_COL']; // Total outstanding across all years
//             }),
//         ];
//     })->values();

//     // Prepare unique options for dropdowns
//     $branchOptions = $data->pluck('PLC_LOCADESC')->unique()->filter()->values()->toArray();

//     // Return view with data and form values
//     return view('os.group', [
//         'data' => $groupedData,
//         'start_date' => $formStartDate,
//         'end_date' => $formEndDate,
//         'branchOptions' => $branchOptions,
//     ]);
// } .... shi
// public function getOutstandingGroupData(Request $request)
// {

//     $startDate = $request->input('start_date');
//     $endDate = $request->input('end_date');

//     $formStartDate = $startDate ?? Carbon::now()->subYears(3)->startOfYear()->format('Y-m-d');
//     $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

//     $result = Helper::getOutstandingGroupData($formStartDate, $formEndDate);
//     $data = collect($result['data'] ?? []);

//     // Filter by branch if provided
//     if ($request->filled('branch')) {
//         $branch = $request->branch;
//         $data = $data->filter(function($item) use ($branch) {
//             return isset($item['PLC_LOCADESC']) && $item['PLC_LOCADESC'] == $branch;
//         });
//     }

//     // Grouping data by insured description and calculating totals
//     $groupedData = $data->groupBy('PPS_DESC')->map(function ($group) {
//         return [
//             'PPS_DESC' => $group->first()['PPS_DESC'], 
//             'PPS_PARTY_CODE' => $group->first()['PPS_PARTY_CODE'], 
//             '2025' => $group->where('GDH_YEAR', 2025)->sum(function($item) {
//                 return $item['TOT_PRE'] - $item['TOT_COL'];
//             }),
//             '2024' => $group->where('GDH_YEAR', 2024)->sum(function($item) {
//                 return $item['TOT_PRE'] - $item['TOT_COL']; 
//             }),
//             '2023' => $group->where('GDH_YEAR', 2023)->sum(function($item) {
//                 return $item['TOT_PRE'] - $item['TOT_COL']; 
//             }),
//             '2022' => $group->where('GDH_YEAR', 2022)->sum(function($item) {
//                 return $item['TOT_PRE'] - $item['TOT_COL']; 
//             }),
//             'Outstanding' => $group->sum(function($item) {
//                 return $item['TOT_PRE'] - $item['TOT_COL'];
//             }),
//             'DocumentCount' => $group->count(),
//         ];
//     })->values();

//     $branchOptions = $data->pluck('PLC_LOCADESC')->unique()->filter()->values()->toArray();
//     return view('os.group', [
//         'data' => $groupedData,
//         'start_date' => $formStartDate,
//         'end_date' => $formEndDate,
//         'branchOptions' => $branchOptions,
//     ]);
// } without branches model;complete
// public function getOutstandingData(Request $request)
// {
//     // Get dates from request or set defaults
//     $startDate = $request->input('start_date');
//     $endDate = $request->input('end_date');

//     $formStartDate = $startDate ?? Carbon::now()->startOfYear()->format('Y-m-d');
//     $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

//     // Get the data from helper
//     $result = Helper::getOutstandingData($formStartDate, $formEndDate);
//     $data = collect($result['data'] ?? []);

//     // Format dates for API/info display
//     $apiDateFrom = Carbon::parse($formStartDate)->format('d-M-Y');
//     $apiDateTo = Carbon::parse($formEndDate)->format('d-M-Y');

//     // Category filtering
//     $categoryMapping = [
//         'Fire' => 11,
//         'Marine' => 12,
//         'Motor' => 13,
//         'Miscellaneous' => 14,
//         'Health' => 16,
//     ];

//     if ($request->filled('new_category')) {
//         $selectedCategory = $request->new_category;
//         if ($deptCode = $categoryMapping[$selectedCategory] ?? null) {
//             $data = $data->filter(function ($item) use ($deptCode) {
//                 return Str::startsWith($item['PDP_DEPT_CODE'] ?? '', (string) $deptCode);
//             });
//         }
//     }

//     // Branch filtering
//    // Filter branches based on selected location category
//         if ($request->filled('location_category')) {
//             $selectedLocation = $request->location_category;
//             $data = $data->filter(function($item) use ($selectedLocation) {
//                 return Str::contains($item['PLC_LOCADESC'], $selectedLocation);
//             });
//         }

//         $uniqueCategories = $data->pluck('PLC_LOCADESC')->filter()->unique()->sort()->values();
//     // ✅ Fetch all branches for full dropdown
//     $branchCode = $request->input('location_category'); // From the select box
// $singleBranch = BranchesList::where('fbracode', $branchCode)->first();

// $takaful = optional($singleBranch)->fbratak;

// // Optional: just send the matched branch for dropdown (if you only want one shown)
// $branches = $singleBranch ? collect([$singleBranch]) : BranchesList::all();

//     //dd($branches );

//     // Return view
//     return view('os.index', [
//         'data' => $data,
//         'start_date' => $formStartDate,
//         'end_date' => $formEndDate,
//         'uniqueCategories' => $uniqueCategories,
//         'api_date_range' => [
//             'from' => $apiDateFrom,
//             'to' => $apiDateTo,
//         ],
//         'branches' => $branches,           // From table for full dropdown
//     ]);
// }
//  public function getOutstandingData(Request $request)
// {
//     // 1. Get dates from request or set defaults
//     $startDate = $request->input('start_date');
//     $endDate = $request->input('end_date');

//     $formStartDate = $startDate ?? Carbon::now()->startOfYear()->format('Y-m-d');
//     $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

//     // 2. Get data from helper
//     $result = Helper::getOutstandingData($formStartDate, $formEndDate);
//     $data = collect($result['data'] ?? []);

//     // 3. Format dates for display
//     $apiDateFrom = Carbon::parse($formStartDate)->format('d-M-Y');
//     $apiDateTo = Carbon::parse($formEndDate)->format('d-M-Y');

//     // 4. Category filtering (by new_category)
//     $categoryMapping = [
//         'Fire' => 11,
//         'Marine' => 12,
//         'Motor' => 13,
//         'Miscellaneous' => 14,
//         'Health' => 16,
//     ];

//     if ($request->filled('new_category')) {
//         $selectedCategory = $request->new_category;
//         if ($deptCode = $categoryMapping[$selectedCategory] ?? null) {
//             $data = $data->filter(function ($item) use ($deptCode) {
//                 return Str::startsWith($item['PDP_DEPT_CODE'] ?? '', (string) $deptCode);
//             });
//         }
//     }

//     // 5. Branch filtering by PLC_LOCADESC (dropdown)
//     if ($request->filled('location_category')) {
//         $selectedLocation = $request->location_category;
//         $data = $data->filter(function ($item) use ($selectedLocation) {
//             return Str::contains($item['PLC_LOCADESC'], $selectedLocation);
//         });
//     }

//     // 6. Extract unique categories from filtered data
//     $uniqueCategories = $data->pluck('PLC_LOCADESC')->filter()->unique()->sort()->values();

//     // 7. Fetch branch info from branches_list table by fbracode
//     $branchCode = $request->input('location_category');
//     $matchedBranch = BranchesList::where('fbracode', $branchCode)->first();

//     // . Set branches for dropdown: single or all
//     $branches = $matchedBranch ? collect([$matchedBranch]) : BranchesList::all();

//     // 10. Return view
//     return view('os.index', [
//         'data' => $data,
//         'start_date' => $formStartDate,
//         'end_date' => $formEndDate,
//         'uniqueCategories' => $uniqueCategories,
//         'api_date_range' => [
//             'from' => $apiDateFrom,
//             'to' => $apiDateTo,
//         ],
//         'branches' => $branches,
//     ]);
// } without dept dynamic
  public function getOutstandingData(Request $request)
    {
        // 1. Get dates from request or set defaults
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $formStartDate = $startDate ?? Carbon::now()->startOfYear()->format('Y-m-d');
        $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

        // 2. Get department from request
        $categoryMapping = [
            'Fire' => 11,
            'Marine' => 12,
            'Motor' => 13,
            'Miscellaneous' => 14,
            'Health' => 16,
        ];
        
        $selectedCategory = $request->input('new_category');
        $deptCode = $selectedCategory && isset($categoryMapping[$selectedCategory]) ? $categoryMapping[$selectedCategory] : 'All';

        // 3. Get data from helper
        $result = Helper::getOutstandingData($formStartDate, $formEndDate, $deptCode);
        $data = collect($result['data'] ?? []);

        // 4. Format dates for display
        $apiDateFrom = Carbon::parse($formStartDate)->format('d-M-Y');
        $apiDateTo = Carbon::parse($formEndDate)->format('d-M-Y');

        // 5. Category filtering (by new_category)
        if ($request->filled('new_category') && $deptCode !== 'All') {
            $data = $data->filter(function ($item) use ($deptCode) {
                return Str::startsWith($item['PDP_DEPT_CODE'] ?? '', (string) $deptCode);
            });
        }

        // 6. Branch filtering by PLC_LOCADESC (dropdown)
        if ($request->filled('location_category')) {
            $selectedLocation = $request->location_category;
            $data = $data->filter(function ($item) use ($selectedLocation) {
                return Str::contains($item['PLC_LOCADESC'], $selectedLocation);
            });
        }

        // 7. Extract unique categories from filtered data
        $uniqueCategories = $data->pluck('PLC_LOCADESC')->filter()->unique()->sort()->values();

        // 8. Fetch branch info from branches_list table by fbracode
        $branchCode = $request->input('location_category');
        $matchedBranch = BranchesList::where('fbracode', $branchCode)->first();

        // 9. Set branches for dropdown: single or all
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
// public function getOutstandingTimelineData(Request $request)
// {
//     $startDate = $request->input('start_date');
//     $endDate = $request->input('end_date');

//     // Default date values
//     $formStartDate = $startDate ?? Carbon::now()->subYears(3)->startOfYear()->format('Y-m-d');
//     $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

//     // Fetch data
//     $result = Helper::getOutstandingTimeline($formStartDate, $formEndDate);
//     $data = collect($result['data'] ?? []);

//     $groupedData = $data->groupBy('PPS_DESC')->map(function ($group) {
//         $counts = [
//             '0-30' => 0,
//             '31-60' => 0,
//             '61-90' => 0,
//             '91-120' => 0,
//             '121-180' => 0,
//             '180+' => 0,
//             'Total' => 0,
//         ];

//         $yearCounts = [
//             '2025_count' => 0,
//             '2024_count' => 0,
//             '2023_count' => 0,
//             '2022_count' => 0,
//         ];

//         foreach ($group as $item) {
//             $days = Carbon::now()->diffInDays(Carbon::parse($item['GDH_ISSUEDATE']));
//             if ($days <= 30) {
//                 $counts['0-30']++;
//             } elseif ($days <= 60) {
//                 $counts['31-60']++;
//             } elseif ($days <= 90) {
//                 $counts['61-90']++;
//             } elseif ($days <= 120) {
//                 $counts['91-120']++;
//             } elseif ($days <= 180) {
//                 $counts['121-180']++;
//             } else {
//                 $counts['180+']++;
//             }
//             $counts['Total']++;

//             // Count documents for each year
//             if ($item['GDH_YEAR'] == 2025) {
//                 $yearCounts['2025_count']++;
//             } elseif ($item['GDH_YEAR'] == 2024) {
//                 $yearCounts['2024_count']++;
//             } elseif ($item['GDH_YEAR'] == 2023) {
//                 $yearCounts['2023_count']++;
//             } elseif ($item['GDH_YEAR'] == 2022) {
//                 $yearCounts['2022_count']++;
//             }
//         }

//         return [
//             'PPS_DESC' => $group->first()['PPS_DESC'],
//             'PPS_PARTY_CODE' => $group->first()['PPS_PARTY_CODE'],
//             'Counts' => $counts,
//             // Outstanding amounts for each year
//             '2025_amt' => $group->where('GDH_YEAR', 2025)->sum(function ($item) {
//                 return $item['TOT_PRE'] - $item['TOT_COL'];
//             }),
//             '2024_amt' => $group->where('GDH_YEAR', 2024)->sum(function ($item) {
//                 return $item['TOT_PRE'] - $item['TOT_COL'];
//             }),
//             '2023_amt' => $group->where('GDH_YEAR', 2023)->sum(function ($item) {
//                 return $item['TOT_PRE'] - $item['TOT_COL'];
//             }),
//             '2022_amt' => $group->where('GDH_YEAR', 2022)->sum(function ($item) {
//                 return $item['TOT_PRE'] - $item['TOT_COL'];
//             }),
//             'Outstanding' => $group->sum(function ($item) {
//                 return $item['TOT_PRE'] - $item['TOT_COL'];
//             }),
//             'DocumentCount' => $group->count(),
//             // Merge year counts
//             '2025_count' => $yearCounts['2025_count'],
//             '2024_count' => $yearCounts['2024_count'],
//             '2023_count' => $yearCounts['2023_count'],
//             '2022_count' => $yearCounts['2022_count'],
//         ];
//     })->values();

//     // Get branch data
//     $branchCode = $request->input('location_category');
//     $matchedBranch = BranchesList::where('fbracode', $branchCode)->first();
//     $branches = $matchedBranch ? collect([$matchedBranch]) : BranchesList::all();

//     // Return view with data
//     return view('os.timeline', [
//         'data' => $groupedData,
//         'start_date' => $formStartDate,
//         'end_date' => $formEndDate,
//         'branches' => $branches,
//     ]);
// }
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
            'Outstanding' => array_sum($amounts), // Total outstanding amount
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

}