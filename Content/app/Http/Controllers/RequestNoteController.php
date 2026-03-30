<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use App\Models\BranchesList;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ReinsLog;
use App\Models\EmailLog;
use App\Models\ReqnoteMark;
use App\Models\VerifyLog; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReinsuranceRequestEmail;
use App\Models\User;
use App\Services\ReinsFilterService;


class RequestNoteController extends Controller

{
    protected $reinsFilter;

    public function __construct(ReinsFilterService $reinsFilter)
    {
        $this->reinsFilter = $reinsFilter;
    }

public function index(Request $request)
    {
        // Get user-submitted parameters
        $userStartDate = $request->query('start_date');
        $userEndDate = $request->query('end_date');
        $req_note = $request->query('req_note');
        $documentNumber = $request->query('uw_doc');

        // Set default dates from today to next 30 days
    if (!$userStartDate || !$userEndDate) {
        $userStartDate = Carbon::now()->format('Y-m-d');
        $userEndDate = Carbon::now()->addDays(30)->format('Y-m-d');
    }
          
    
        // Fetch data
        $result = Helper::fetchReinsuranceNotesData($userStartDate, $userEndDate, $req_note);
       //dd($result);
        // Handle API errors
        if ($result['status'] === 'error') {
            return response()->json($result, 500);
        }

        // Convert to collection
        $data = collect($result['data'] ?? []);
       // dd($data->values()->get());
        //dd($data->values()->get(93));

        //dd($request->all());
        // Format API date range for display
           // Format API date range
    $apiDateRangeFrom = Carbon::createFromFormat('d-M-Y', $result['api_expiry_from']);
    $apiDateRangeTo = Carbon::createFromFormat('d-M-Y', $result['api_expiry_to']);

    // Use either user dates or API fallback
    $defaultStartDate = $userStartDate ?? $apiDateRangeFrom->format('Y-m-d');
    $defaultEndDate = $userEndDate ?? $apiDateRangeTo->format('Y-m-d');



        // Filter by document number if provided
        if ($request->filled('uw_doc')) {
            $documentNumber = $request->query('uw_doc');
            $filteredNotes = Helper::getRequestNotesByDocument($documentNumber);
           // return $filteredNotes;
            
        
            
            if (!empty($filteredNotes)) {
                $data = $data->filter(function($item) use ($filteredNotes) {
                    return in_array(strtoupper($item['GRH_REFERENCE_NO'] ?? ''), 
                                 array_map('strtoupper', $filteredNotes));
                })->values();
            } else {
                $data = collect();
            }
        }

        // Filter by reins party if provided
        if ($request->filled('reins_party')) {
            $data = $data->filter(function($item) use ($request) {
                return ($item['RE_COMP_DESC'] ?? '') === $request->reins_party;
            });
        }

        // Filter by category if provided
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
                $data = $data->filter(function($item) use ($deptCode) {
                    return Str::startsWith($item['PDP_DEPT_CODE'] ?? '', (string)$deptCode);
                });
            }
        }
        // Filter by CP_STS if provided
        if ($request->filled('cp_sts')) {
            $cpStsValue = strtolower($request->cp_sts); // yes or no
            $data = $data->filter(function ($item) use ($cpStsValue) {
                $value = strtolower($item['CP_STS'] ?? '');
                return $cpStsValue === 'yes' ? $value === 'yes' : $value !== 'yes';
            });
        }
        


        return view('RequestNote.index', [
            'data' => $data,
            'start_date' => $defaultStartDate,
            'end_date' => $defaultEndDate,
            'reinsParties' => $data->pluck('RE_COMP_DESC')->unique()->values()->toArray(),
            'api_date_range' => [
                'from' => $apiDateRangeFrom->format('d-M-Y'),
                'to' => $apiDateRangeTo->format('d-M-Y')
            ],
            'total_records' => count($result['data']),
            'filtered_records' => $data->count()
        ]);
    }

public function storeReinsTag(Request $request)
{
    //dd($request->all());
    $request->validate([
        'GRH_REFERENCE_NO' => 'required|string',
        'report_name'      => 'nullable|string',
        'tag_action' => 'required|in:revise,cancel,decline,withdraw',
        'remarks'          => 'nullable|string|max:2000',
    ]);

    ReqnoteMark::create([
        'GRH_REFERENCE_NO' => $request->GRH_REFERENCE_NO,
        'tag_action'       => $request->tag_action,
        'remarks'          => $request->remarks,
        'report_name'      => $request->report_name,
        'created_by'       => auth()->user()->name ?? 'system',
        'updated_by'       => auth()->user()->name ?? 'system',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Note saved successfully.',
    ]);
}

// public function getReinsuranceR1Data(Request $request)
//     {
//         $startDate = $request->input('start_date');
//         $endDate = $request->input('end_date');

//        $formStartDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
//        $formEndDate   = $endDate ?? Carbon::now()->format('Y-m-d');

//         $result = Helper::getReinsuranceR1Report($formStartDate, $formEndDate);
//         $data = collect($result['data'] ?? []);
//         $insertedDocs = EmailLog::pluck('reqnote')->toArray();

//         $data = $data->filter(function ($record) use ($insertedDocs) {
//             $docReference = $record->GRH_REFERENCE_NO ?? '';
//             $isExcluded = in_array($docReference, $insertedDocs);
//             // Debug: Log filtering details
//             Log::debug('Filtering record', [
//                 'GRH_REFERENCE_NO' => $docReference,
//                 'isExcluded' => $isExcluded
//             ]);
//             return !$isExcluded;
//         });

        
//         // 5. Format dates for display
//         $apiDateFrom = Carbon::parse($formStartDate)->format('d-M-Y');
//         $apiDateTo = Carbon::parse($formEndDate)->format('d-M-Y');

//         // 6. Category filtering
//         $categoryMapping = [
//             'Fire' => 11,
//             'Marine' => 12,
//             'Motor' => 13,
//             'Miscellaneous' => 14,
//             'Health' => 16,
//         ];

//         if ($request->filled('new_category')) {
//             $selectedCategory = $request->new_category;
//             if ($deptCode = $categoryMapping[$selectedCategory] ?? null) {
//                 $data = $data->filter(function ($item) use ($deptCode) {
//                     return Str::startsWith($item['PDP_DEPT_CODE'] ?? '', (string) $deptCode);
//                 });
//             }
//         }

//         // 7. Branch filtering
//         if ($request->filled('location_category')) {
//             $selectedLocation = $request->location_category;
//             $data = $data->filter(function ($item) use ($selectedLocation) {
//                 return Str::contains($item['PLC_LOCADESC'], $selectedLocation);
//             });
//         }

//         $uniqueCategories = $data->pluck('PLC_LOCADESC')->filter()->unique()->sort()->values();
//         $branchCode = $request->input('location_category');
//         $matchedBranch = BranchesList::where('fbracode', $branchCode)->first();
//         $branches = $matchedBranch ? collect([$matchedBranch]) : BranchesList::all();
//         $notPostedRecords = $data->where('GRH_POSTINGTAG', '!=', 'Y');

//         $now = Carbon::now();
//         $agingBuckets = [
//             '0-3 Days'     => [0, 3],
//             '4-7 Days'     => [4, 7],
//             '8-10 Days'    => [8, 10],
//             '11-15 Days'   => [11, 15],
//             '16-20 Days'   => [16, 20],
//             '20+ Days'     => [21, PHP_INT_MAX],
//         ];

       
//         $groupedByAging = [];
//         foreach ($agingBuckets as $label => [$min, $max]) {
//         $groupedByAging[$label] = $data->filter(function ($item) use ($now, $min, $max) {
//             $date = Carbon::parse($item->GRH_DOCUMENTDATE ?? null);
//             $diff = $date->diffInDays($now);
//             return $diff >= $min && $diff <= $max;
//         });
//     }

//         // 12. Calculate sum insured for all records and not posted records
//         $totalSumInsured = $data->sum(function($item) {
//             return (float)($item->CED_SI ?? 0);
//         });

//         $notPostedSumInsured = $notPostedRecords->sum(function($item) {
//             return (float)($item->CED_SI ?? 0);
//         });

//         // 13. Return view
//         return view('GetRequestReports.r1', [
//             'data' => $data,
//             'start_date' => $formStartDate,
//             'end_date' => $formEndDate,
//             'uniqueCategories' => $uniqueCategories,
//             'api_date_range' => [
//                 'from' => $apiDateFrom,
//                 'to' => $apiDateTo,
//             ],
//             'branches' => $branches,
//             'notPostedCount' => $notPostedRecords->count(),
//             'totalCount' => $data->count(),
//             'groupedByAging' => $groupedByAging,
//             'totalSumInsured' => $totalSumInsured,
//             'notPostedSumInsured' => $notPostedSumInsured,
//         ]);
//     }  
 
 
    // ════════════════════════════════════════════════════════════════
 
    public function getshow(Request $request)
    {
        $this->pingApi("http://172.16.22.204/dashboardApi/reins/rqn/req_reins_schd.php");
 
        $timeFilter       = $request->query('time_filter',  'all');
        $selectedCategory = $request->query('new_category', '');
        $selectedReins    = $request->query('reins_party',  '');
 
        $tagged     = ReqnoteMark::pluck('GRH_REFERENCE_NO')->toArray();
        $allRecords = EmailLog::select('*')
            ->whereIn('id', fn($q) => $q->selectRaw('MAX(id)')->from('emaillogs')->groupBy('reqnote'))
            ->when(!empty($tagged), fn($q) => $q->whereNotIn('reqnote', $tagged))
            ->get();
 
        $dateOf  = fn($r) => $r->datetime ? Carbon::parse($r->datetime) : null;
        $counts  = $this->reinsFilter->buildCounts($allRecords, $dateOf);
        $records = $this->reinsFilter->applyTimeFilter($allRecords, $timeFilter, $dateOf);
 
        if ($selectedCategory) {
            $records = $records->filter(fn($r) =>
                isset($r->dept) && strtolower(trim($r->dept)) === strtolower(trim($selectedCategory))
            );
        }
 
        if ($selectedReins) {
            $records = $records->filter(fn($r) =>
                isset($r->reins_party) && strtolower(trim($r->reins_party)) === strtolower(trim($selectedReins))
            );
        }
 
        $today = Carbon::today();
        foreach ($records as $r) {
            $r->email_count    = EmailLog::where('reqnote', $r->reqnote)->count();
            $first             = EmailLog::where('reqnote', $r->reqnote)->oldest('datetime')->first();
            $latest            = EmailLog::where('reqnote', $r->reqnote)->latest('datetime')->first();
            $r->first_sent_at  = $first  ? $first->datetime  : null;
            $r->latest_sent_at = $latest ? $latest->datetime : null;
            $r->days_old       = $r->first_sent_at
                ? Carbon::parse($r->first_sent_at)->diffInDays($today)
                : null;
        }
        //dd($records->take(15)->map->toArray()->values()->all());
        // $records = EmailLog::first();
        // dd($records);
 
        return view('GetRequestReports.getshow', [
            'records'              => $records,
            'selected_time_filter' => $timeFilter,
            'selected_department'  => $selectedCategory,
            'selected_reins'       => $selectedReins,
            'reins_party_options'  => $allRecords->pluck('reins_party')->filter()->unique()->sort()->values(),
            'counts'               => $counts,
        ]);
    }
 
    // ════════════════════════════════════════════════════════════════
 
    public function show(Request $request)
    {
        $this->pingApi("http://172.16.22.204/dashboardApi/reins/rqn/uw_reins_schd.php");
 
        $timeFilter       = $request->query('time_filter',  'all');
        $selectedCategory = $request->query('new_category', '');
        $selectedRisk     = $request->query('risk_filter',  'Y');
 
        $allRecords = ReinsLog::whereNull('rq_gen')->get();
        $dateOf     = fn($r) => $r->created_at;
 
        $byRisk  = $selectedRisk === 'ALL'
            ? $allRecords
            : $allRecords->filter(fn($r) => $r->riskMarked === $selectedRisk);
 
        $counts  = $this->reinsFilter->buildCounts($byRisk, $dateOf);
        $records = $selectedRisk === 'Y' ? $byRisk->filter(fn($r) => !$r->is_processed) : $byRisk;
        $records = $this->reinsFilter->applyTimeFilter($records, $timeFilter, $dateOf);
 
        if ($selectedCategory && isset($this->reinsFilter->categoryMapping()[$selectedCategory])) {
            $code    = (string) $this->reinsFilter->categoryMapping()[$selectedCategory];
            $records = $records->filter(fn($r) => isset($r->dept) && Str::startsWith($r->dept, $code));
        }
 
        $records = $this->reinsFilter->stampDaysOld($records, $dateOf);
 
        return view('GetRequestReports.show', [
            'records'              => $records,
            'selected_time_filter' => $timeFilter,
            'selected_department'  => $selectedCategory,
            'selected_risk'        => $selectedRisk,
            'counts'               => $counts,
            'currentUserEmail'     => Session::get('user')['email'] ?? null,
        ]);
    }
 
    // ════════════════════════════════════════════════════════════════
 
    public function getReinsuranceR2Data(Request $request)
    {
        $timeFilter       = $request->input('time_filter', 'all');
        $selectedCategory = $request->input('new_category');
        $selectedLocation = $request->input('location');
        $formStartDate    = $request->input('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $formEndDate      = $request->input('end_date',   Carbon::now()->format('Y-m-d'));
 
        $tagged = ReqnoteMark::pluck('GRH_REFERENCE_NO')->toArray();
        $data   = collect(Helper::getReinsuranceR2Report($formStartDate, $formEndDate)['data'] ?? [])
                    ->filter(fn($r) => !in_array($r->GRH_REFERENCE_NO ?? '', $tagged));
 
        $dateOf  = fn($r) => ($r->GRH_ACCEPTEDDATE ?? null) ? Carbon::parse($r->GRH_ACCEPTEDDATE) : null;
        $counts  = $this->reinsFilter->buildCounts($data, $dateOf);
        $data    = $this->reinsFilter->applyTimeFilter($data, $timeFilter, $dateOf);
 
        if ($selectedCategory && isset($this->reinsFilter->categoryMapping()[$selectedCategory])) {
            $data = $data->filter(fn($r) =>
                isset($r->PDP_DEPT_CODE) && $r->PDP_DEPT_CODE == $this->reinsFilter->categoryMapping()[$selectedCategory]
            );
        }
 
        if ($selectedLocation) {
            $data = $data->filter(fn($r) =>
                isset($r->PLC_LOCADESC) && Str::contains($r->PLC_LOCADESC, $selectedLocation)
            );
        }
 
        $data             = $this->reinsFilter->stampDaysOld($data, $dateOf);
        $notPostedRecords = $data->where('GRH_POSTINGTAG', '!=', 'Y');
 
        $branchCode = $request->input('location_category');
        $matched    = BranchesList::where('fbracode', $branchCode)->first();
 
        return view('GetRequestReports.r2', [
            'data'                 => $data,
            'start_date'           => $formStartDate,
            'end_date'             => $formEndDate,
            'uniqueCategories'     => $data->pluck('PLC_LOCADESC')->filter()->unique()->sort()->values(),
            'api_date_range'       => [
                'from' => Carbon::parse($formStartDate)->format('d-M-Y'),
                'to'   => Carbon::parse($formEndDate)->format('d-M-Y'),
            ],
            'branches'             => $matched ? collect([$matched]) : BranchesList::all(),
            'notPostedCount'       => $notPostedRecords->count(),
            'totalCount'           => $data->count(),
            'groupedByAging'       => $this->reinsFilter->groupByAging($data),
            'totalSumInsured'      => $data->sum(fn($r) => (float)($r->CED_SI ?? 0)),
            'notPostedSumInsured'  => $notPostedRecords->sum(fn($r) => (float)($r->CED_SI ?? 0)),
            'selected_time_filter' => $timeFilter,
            'selected_department'  => $selectedCategory,
            'counts'               => $counts,
        ]);
    }
 
    // ════════════════════════════════════════════════════════════════
 
    private function pingApi(string $url): void
    {
        @file_get_contents($url, false, stream_context_create([
            'http' => ['ignore_errors' => true, 'timeout' => 120]
        ]));
    }

public function getReinsuranceR1Data(Request $request)
{
  //    $taggedDocs   = ReqnoteMark::all();
  //     dd($taggedDocs);

    $startDate = $request->input('start_date');
    $endDate   = $request->input('end_date');

    $formStartDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
    $formEndDate   = $endDate   ?? Carbon::now()->format('Y-m-d');

    $result = Helper::getReinsuranceR1Report($formStartDate, $formEndDate);
    $data   = collect($result['data'] ?? []);

    // ── Exclude emailed AND note-tagged documents ──────────────────────
    $insertedDocs = EmailLog::pluck('reqnote')->toArray();
    $taggedDocs   = ReqnoteMark::pluck('GRH_REFERENCE_NO')->toArray();
    $excludedDocs = array_merge($insertedDocs, $taggedDocs);

    $data = $data->filter(function ($record) use ($excludedDocs) {
        return !in_array($record->GRH_REFERENCE_NO ?? '', $excludedDocs);
    });

    // Format dates
    $apiDateFrom = Carbon::parse($formStartDate)->format('d-M-Y');
    $apiDateTo   = Carbon::parse($formEndDate)->format('d-M-Y');

    // Category filtering
    $categoryMapping = [
        'Fire'          => 11,
        'Marine'        => 12,
        'Motor'         => 13,
        'Miscellaneous' => 14,
        'Health'        => 16,
    ];

    if ($request->filled('new_category')) {
        $selectedCategory = $request->new_category;
        if ($deptCode = $categoryMapping[$selectedCategory] ?? null) {
            $data = $data->filter(function ($item) use ($deptCode) {
                return Str::startsWith($item['PDP_DEPT_CODE'] ?? '', (string) $deptCode);
            });
        }
    }

    // Branch filtering
    if ($request->filled('location_category')) {
        $selectedLocation = $request->location_category;
        $data = $data->filter(function ($item) use ($selectedLocation) {
            return Str::contains($item['PLC_LOCADESC'], $selectedLocation);
        });
    }

    $uniqueCategories    = $data->pluck('PLC_LOCADESC')->filter()->unique()->sort()->values();
    $branchCode          = $request->input('location_category');
    $matchedBranch       = BranchesList::where('fbracode', $branchCode)->first();
    $branches            = $matchedBranch ? collect([$matchedBranch]) : BranchesList::all();
    $notPostedRecords    = $data->where('GRH_POSTINGTAG', '!=', 'Y');

    $now          = Carbon::now();
    $agingBuckets = [
        '0-3 Days'   => [0,  3],
        '4-7 Days'   => [4,  7],
        '8-10 Days'  => [8,  10],
        '11-15 Days' => [11, 15],
        '16-20 Days' => [16, 20],
        '20+ Days'   => [21, PHP_INT_MAX],
    ];

    $groupedByAging = [];
    foreach ($agingBuckets as $label => [$min, $max]) {
        $groupedByAging[$label] = $data->filter(function ($item) use ($now, $min, $max) {
            $date = Carbon::parse($item->GRH_DOCUMENTDATE ?? null);
            $diff = $date->diffInDays($now);
            return $diff >= $min && $diff <= $max;
        });
    }

    $totalSumInsured     = $data->sum(fn($item) => (float)($item->CED_SI ?? 0));
    $notPostedSumInsured = $notPostedRecords->sum(fn($item) => (float)($item->CED_SI ?? 0));

    return view('GetRequestReports.r1', [
        'data'                => $data,
        'start_date'          => $formStartDate,
        'end_date'            => $formEndDate,
        'uniqueCategories'    => $uniqueCategories,
        'api_date_range'      => ['from' => $apiDateFrom, 'to' => $apiDateTo],
        'branches'            => $branches,
        'notPostedCount'      => $notPostedRecords->count(),
        'totalCount'          => $data->count(),
        'groupedByAging'      => $groupedByAging,
        'totalSumInsured'     => $totalSumInsured,
        'notPostedSumInsured' => $notPostedSumInsured,
    ]);
} 
// public function getReinsuranceR1Data(Request $request)
// {
//     //    $taggedDocs   = ReqnoteMark::all();
//     //     dd($taggedDocs);

//     $startDate = $request->input('start_date');
//     $endDate   = $request->input('end_date');

//     $formStartDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
//     $formEndDate   = $endDate   ?? Carbon::now()->format('Y-m-d');

//     $result = Helper::getReinsuranceR1Report($formStartDate, $formEndDate);
//     $data   = collect($result['data'] ?? []);

//     // ── Exclude emailed AND note-tagged documents ──────────────────────
//     $insertedDocs = EmailLog::pluck('reqnote')->toArray();
//     $taggedDocs   = ReqnoteMark::pluck('GRH_REFERENCE_NO')->toArray();
//     $excludedDocs = array_merge($insertedDocs, $taggedDocs);

//     $data = $data->filter(function ($record) use ($excludedDocs) {
//         return !in_array($record->GRH_REFERENCE_NO ?? '', $excludedDocs);
//     });

//     // Format dates
//     $apiDateFrom = Carbon::parse($formStartDate)->format('d-M-Y');
//     $apiDateTo   = Carbon::parse($formEndDate)->format('d-M-Y');

//     // Category filtering
//     $categoryMapping = [
//         'Fire'          => 11,
//         'Marine'        => 12,
//         'Motor'         => 13,
//         'Miscellaneous' => 14,
//         'Health'        => 16,
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
//     if ($request->filled('location_category')) {
//         $selectedLocation = $request->location_category;
//         $data = $data->filter(function ($item) use ($selectedLocation) {
//             return Str::contains($item['PLC_LOCADESC'], $selectedLocation);
//         });
//     }

//     $uniqueCategories    = $data->pluck('PLC_LOCADESC')->filter()->unique()->sort()->values();
//     $branchCode          = $request->input('location_category');
//     $matchedBranch       = BranchesList::where('fbracode', $branchCode)->first();
//     $branches            = $matchedBranch ? collect([$matchedBranch]) : BranchesList::all();
//     $notPostedRecords    = $data->where('GRH_POSTINGTAG', '!=', 'Y');

//     $now = Carbon::now();

//    $groupedByAging = $data->groupBy(function ($item) use ($now) {
//     if (!$item->GRH_DOCUMENTDATE) {
//         return 'No Date';
//     }

//     $date = Carbon::parse($item->GRH_DOCUMENTDATE);
//     $days = $date->diffInDays($now);

//     if ($days <= 3) {
//         return '0-3 Days';
//     } elseif ($days <= 7) {
//         return '4-7 Days';
//     } elseif ($days <= 10) {
//         return '8-10 Days';
//     } elseif ($days <= 15) {
//         return '11-15 Days';
//     } elseif ($days <= 20) {
//         return '16-20 Days';
//     } else {
//         return '20+ Days';
//     }
// });

//     $totalSumInsured     = $data->sum(fn($item) => (float)($item->CED_SI ?? 0));
//     $notPostedSumInsured = $notPostedRecords->sum(fn($item) => (float)($item->CED_SI ?? 0));

//     return view('GetRequestReports.r1', [
//         'data'                => $data,
//         'start_date'          => $formStartDate,
//         'end_date'            => $formEndDate,
//         'uniqueCategories'    => $uniqueCategories,
//         'api_date_range'      => ['from' => $apiDateFrom, 'to' => $apiDateTo],
//         'branches'            => $branches,
//         'notPostedCount'      => $notPostedRecords->count(),
//         'totalCount'          => $data->count(),
//         'groupedByAging'      => $groupedByAging,
//         'totalSumInsured'     => $totalSumInsured,
//         'notPostedSumInsured' => $notPostedSumInsured,
//     ]);
// }


// public function getReinsuranceR2Data(Request $request)
// {
// //  $d = ReqnoteMark::latest()->first();
// // dd($d);
//     // 1. Get dates and filters from request
//     $startDate = $request->input('start_date');
//     $endDate = $request->input('end_date');
//     $timeFilter = $request->input('time_filter', 'all'); 
//     $selectedCategory = $request->input('new_category');
//     $selectedLocation = $request->input('location');
//     $formStartDate = $startDate ?? Carbon::now()->startOfYear()->format('Y-m-d');
//     $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');
   
//     $formStartDate = $startDate ?? Carbon::now()->startOfYear()->format('Y-m-d');
//     $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

//     // 2. Define category mapping
//     $categoryMapping = [
//         'Fire' => 11,
//         'Marine' => 12,
//         'Motor' => 13,
//         'Miscellaneous' => 14,
//         'Health' => 16,
//     ];

//     // 3. Initialize time filter counts
//     $counts = [
//         'all' => 0,
//         '2days' => 0,
//         '5days' => 0,
//         '7days' => 0,
//         '10days' => 0,
//         '15days' => 0,
//         '15plus' => 0,
//     ];

//     // 4. Get data from helper
//     $result = Helper::getReinsuranceR2Report($formStartDate, $formEndDate);
//     $data = collect($result['data'] ?? []);

//     // 5. Fetch reqnote values from emaillogs AND reqnote_marks (saved notes)
// $insertedDocs = EmailLog::pluck('reqnote')->toArray();
// $taggedDocs = ReqnoteMark::pluck('GRH_REFERENCE_NO')->toArray();
// $excludedDocs = array_merge($insertedDocs, $taggedDocs);

// // 6. Filter out records that exist in either emaillogs OR reqnote_marks
// $data = $data->filter(function ($record) use ($excludedDocs) {
//     return !in_array($record->GRH_REFERENCE_NO ?? '', $excludedDocs);
// });
//     // 7. Calculate time filter counts based on GRH_ACCEPTEDDATE
//     $today = Carbon::today();
//     $filterDate = fn($record, $condition) => ($date = ($record->GRH_ACCEPTEDDATE ?? null) ? Carbon::parse($record->GRH_ACCEPTEDDATE) : null) && $date->isValid() && $condition($date);

//     $counts['all'] = $data->count();
//     $counts['2days'] = $data->filter(fn($record) => $filterDate($record, fn($date) => $date->gte($today->copy()->subDays(2))))->count();
//     $counts['5days'] = $data->filter(fn($record) => $filterDate($record, fn($date) => $date->between($today->copy()->subDays(5), $today->copy()->subDays(2), false)))->count();
//     $counts['7days'] = $data->filter(fn($record) => $filterDate($record, fn($date) => $date->between($today->copy()->subDays(7), $today->copy()->subDays(5), false)))->count();
//     $counts['10days'] = $data->filter(fn($record) => $filterDate($record, fn($date) => $date->between($today->copy()->subDays(10), $today->copy()->subDays(7), false)))->count();
//     $counts['15days'] = $data->filter(fn($record) => $filterDate($record, fn($date) => $date->between($today->copy()->subDays(15), $today->copy()->subDays(10), false)))->count();
//     $counts['15plus'] = $data->filter(fn($record) => $filterDate($record, fn($date) => $date->lt($today->copy()->subDays(15))))->count();

//     // 8. Apply time filter based on GRH_ACCEPTEDDATE
//     $filteredData = $data;
//     switch ($timeFilter) {
//         case '2days':
//             $filteredData = $data->filter(fn($record) => $filterDate($record, fn($date) => $date->gte($today->copy()->subDays(2))));
//             break;
//         case '5days':
//             $filteredData = $data->filter(fn($record) => $filterDate($record, fn($date) => $date->between($today->copy()->subDays(5), $today->copy()->subDays(2), false)));
//             break;
//         case '7days':
//             $filteredData = $data->filter(fn($record) => $filterDate($record, fn($date) => $date->between($today->copy()->subDays(7), $today->copy()->subDays(5), false)));
//             break;
//         case '10days':
//             $filteredData = $data->filter(fn($record) => $filterDate($record, fn($date) => $date->between($today->copy()->subDays(10), $today->copy()->subDays(7), false)));
//             break;
//         case '15days':
//             $filteredData = $data->filter(fn($record) => $filterDate($record, fn($date) => $date->between($today->copy()->subDays(15), $today->copy()->subDays(10), false)));
//             break;
//         case '15plus':
//             $filteredData = $data->filter(fn($record) => $filterDate($record, fn($date) => $date->lt($today->copy()->subDays(15))));
//             break;
//         case 'all':
//         default:
//             break;
//     }
//     $data = $filteredData;

//     // 9. Category filtering
//     if ($selectedCategory && isset($categoryMapping[$selectedCategory])) {
//         $deptCode = $categoryMapping[$selectedCategory];
//         $data = $data->filter(fn($item) => isset($item->PDP_DEPT_CODE) && $item->PDP_DEPT_CODE == $deptCode);
//     }

//     // 10. Branch filtering by PLC_LOCADESC
//     if ($selectedLocation) {
//         $data = $data->filter(fn($item) => isset($item->PLC_LOCADESC) && Str::contains($item->PLC_LOCADESC, $selectedLocation));
//     }

//     // 11. Extract unique categories from filtered data
//     $uniqueCategories = $data->pluck('PLC_LOCADESC')->filter()->unique()->sort()->values();

//     // 12. Fetch branch info from branches_list table by fbracode
//     $branchCode = $request->input('location_category');
//     $matchedBranch = BranchesList::where('fbracode', $branchCode)->first();
//     $branches = $matchedBranch ? collect([$matchedBranch]) : BranchesList::all();

//     // 13. Filter not posted records
//     $notPostedRecords = $data->where('GRH_POSTINGTAG', '!=', 'Y');

//     // 14. Group aging buckets
//     $now = Carbon::now();
//     $agingBuckets = [
//         '0-3 Days' => [0, 3],
//         '4-7 Days' => [4, 7],
//         '8-10 Days' => [8, 10],
//         '11-15 Days' => [11, 15],
//         '16-20 Days' => [16, 20],
//         '20+ Days' => [21, PHP_INT_MAX],
//     ];

//     $groupedByAging = [];
//     foreach ($agingBuckets as $label => [$min, $max]) {
//         $groupedByAging[$label] = $data->filter(function ($item) use ($now, $min, $max) {
//             $date = ($item->GRH_DOCUMENTDATE ?? null) ? Carbon::createFromFormat('d-M-y', $item->GRH_DOCUMENTDATE) : null;
//             return $date && $date->isValid() && $date->diffInDays($now) >= $min && $date->diffInDays($now) <= $max;
//         });
//     }

//     // 15. Calculate sum insured
//     $totalSumInsured = $data->sum(fn($item) => (float)($item->CED_SI ?? 0));
//     $notPostedSumInsured = $notPostedRecords->sum(fn($item) => (float)($item->CED_SI ?? 0));

//     // 16. Add days_old for highlighting based on GRH_ACCEPTEDDATE
//     $data = $data->map(function ($item) use ($today) {
//         $item->days_old = null;
//         if ($date = ($item->GRH_ACCEPTEDDATE ?? null) ? Carbon::parse($item->GRH_ACCEPTEDDATE) : null) {
//             if ($date->isValid()) {
//                 $item->days_old = $date->diffInDays($today);
//             }
//         }
//         return $item;
//     });

//     // 17. Format dates for display
//     $apiDateFrom = Carbon::parse($formStartDate)->format('d-M-Y');
//     $apiDateTo = Carbon::parse($formEndDate)->format('d-M-Y');

//     // 18. Return view
//     return view('GetRequestReports.r2', [
//         'data' => $data,
//         'start_date' => $formStartDate,
//         'end_date' => $formEndDate,
//         'uniqueCategories' => $uniqueCategories,
//         'api_date_range' => [
//             'from' => $apiDateFrom,
//             'to' => $apiDateTo,
//         ],
//         'branches' => $branches,
//         'notPostedCount' => $notPostedRecords->count(),
//         'totalCount' => $data->count(),
//         'groupedByAging' => $groupedByAging,
//         'totalSumInsured' => $totalSumInsured,
//         'notPostedSumInsured' => $notPostedSumInsured,
//         'selected_time_filter' => $timeFilter,
//         'selected_department' => $selectedCategory,
//         'counts' => $counts,
//     ]);
// } 
//  public function getReinsuranceR2Data(Request $request)
// {
//     // 1. Get dates and filters from request
//     $startDate        = $request->input('start_date');
//     $endDate          = $request->input('end_date');
//     $timeFilter       = $request->input('time_filter', 'all');
//     $selectedCategory = $request->input('new_category');
//     $selectedLocation = $request->input('location');

//     $formStartDate = $startDate ?? Carbon::now()->startOfYear()->format('Y-m-d');
//     $formEndDate   = $endDate   ?? Carbon::now()->format('Y-m-d');

//     // 2. Define category mapping
//     $categoryMapping = [
//         'Fire'          => 11,
//         'Marine'        => 12,
//         'Motor'         => 13,
//         'Miscellaneous' => 14,
//         'Health'        => 16,
//     ];

//     // 3. Initialize time filter counts
//     $counts = [
//         'all'    => 0,
//         '2days'  => 0,
//         '5days'  => 0,
//         '7days'  => 0,
//         '10days' => 0,
//         '15days' => 0,
//         '15plus' => 0,
//     ];

//     // 4. Get data from helper
//     $result = Helper::getReinsuranceR2Report($formStartDate, $formEndDate);
//     $data   = collect($result['data'] ?? []);

//     // 5. Exclude records that have already been tagged via Note Tag (Withdraw etc.)
//     //    EmailLog is NOT used here — r2 shows records that haven't been emailed yet,
//     //    and that filtering belongs to the r1/getshow view, not here.
//     $taggedDocs = ReqnoteMark::pluck('GRH_REFERENCE_NO')->toArray();

//     $data = $data->filter(function ($record) use ($taggedDocs) {
//         return !in_array($record->GRH_REFERENCE_NO ?? '', $taggedDocs);
//     });

//     // 6. Calculate time filter counts based on GRH_ACCEPTEDDATE
//     $today      = Carbon::today();
//     $filterDate = fn($record, $condition) =>
//         ($date = ($record->GRH_ACCEPTEDDATE ?? null)
//             ? Carbon::parse($record->GRH_ACCEPTEDDATE)
//             : null
//         ) && $date->isValid() && $condition($date);

//     $counts['all']    = $data->count();
//     $counts['2days']  = $data->filter(fn($r) => $filterDate($r, fn($d) => $d->gte($today->copy()->subDays(2))))->count();
//     $counts['5days']  = $data->filter(fn($r) => $filterDate($r, fn($d) => $d->between($today->copy()->subDays(5),  $today->copy()->subDays(2),  false)))->count();
//     $counts['7days']  = $data->filter(fn($r) => $filterDate($r, fn($d) => $d->between($today->copy()->subDays(7),  $today->copy()->subDays(5),  false)))->count();
//     $counts['10days'] = $data->filter(fn($r) => $filterDate($r, fn($d) => $d->between($today->copy()->subDays(10), $today->copy()->subDays(7),  false)))->count();
//     $counts['15days'] = $data->filter(fn($r) => $filterDate($r, fn($d) => $d->between($today->copy()->subDays(15), $today->copy()->subDays(10), false)))->count();
//     $counts['15plus'] = $data->filter(fn($r) => $filterDate($r, fn($d) => $d->lt($today->copy()->subDays(15))))->count();

//     // 7. Apply time filter based on GRH_ACCEPTEDDATE
//     $filteredData = $data;
//     switch ($timeFilter) {
//         case '2days':
//             $filteredData = $data->filter(fn($r) => $filterDate($r, fn($d) => $d->gte($today->copy()->subDays(2))));
//             break;
//         case '5days':
//             $filteredData = $data->filter(fn($r) => $filterDate($r, fn($d) => $d->between($today->copy()->subDays(5),  $today->copy()->subDays(2),  false)));
//             break;
//         case '7days':
//             $filteredData = $data->filter(fn($r) => $filterDate($r, fn($d) => $d->between($today->copy()->subDays(7),  $today->copy()->subDays(5),  false)));
//             break;
//         case '10days':
//             $filteredData = $data->filter(fn($r) => $filterDate($r, fn($d) => $d->between($today->copy()->subDays(10), $today->copy()->subDays(7),  false)));
//             break;
//         case '15days':
//             $filteredData = $data->filter(fn($r) => $filterDate($r, fn($d) => $d->between($today->copy()->subDays(15), $today->copy()->subDays(10), false)));
//             break;
//         case '15plus':
//             $filteredData = $data->filter(fn($r) => $filterDate($r, fn($d) => $d->lt($today->copy()->subDays(15))));
//             break;
//         case 'all':
//         default:
//             break;
//     }
//     $data = $filteredData;

//     // 8. Category filtering
//     if ($selectedCategory && isset($categoryMapping[$selectedCategory])) {
//         $deptCode = $categoryMapping[$selectedCategory];
//         $data     = $data->filter(fn($item) => isset($item->PDP_DEPT_CODE) && $item->PDP_DEPT_CODE == $deptCode);
//     }

//     // 9. Branch filtering by PLC_LOCADESC
//     if ($selectedLocation) {
//         $data = $data->filter(fn($item) => isset($item->PLC_LOCADESC) && Str::contains($item->PLC_LOCADESC, $selectedLocation));
//     }

//     // 10. Extract unique location categories from filtered data
//     $uniqueCategories = $data->pluck('PLC_LOCADESC')->filter()->unique()->sort()->values();

//     // 11. Fetch branch info from branches_list table by fbracode
//     $branchCode    = $request->input('location_category');
//     $matchedBranch = BranchesList::where('fbracode', $branchCode)->first();
//     $branches      = $matchedBranch ? collect([$matchedBranch]) : BranchesList::all();

//     // 12. Filter not-posted records (for summary card)
//     $notPostedRecords = $data->where('GRH_POSTINGTAG', '!=', 'Y');

//     // 13. Group aging buckets based on GRH_DOCUMENTDATE
//     $now          = Carbon::now();
//     $agingBuckets = [
//         '0-3 Days'  => [0,  3],
//         '4-7 Days'  => [4,  7],
//         '8-10 Days' => [8,  10],
//         '11-15 Days'=> [11, 15],
//         '16-20 Days'=> [16, 20],
//         '20+ Days'  => [21, PHP_INT_MAX],
//     ];

//     $groupedByAging = [];
//     foreach ($agingBuckets as $label => [$min, $max]) {
//         $groupedByAging[$label] = $data->filter(function ($item) use ($now, $min, $max) {
//             $date = ($item->GRH_DOCUMENTDATE ?? null)
//                 ? Carbon::createFromFormat('d-M-y', $item->GRH_DOCUMENTDATE)
//                 : null;
//             return $date
//                 && $date->isValid()
//                 && $date->diffInDays($now) >= $min
//                 && $date->diffInDays($now) <= $max;
//         });
//     }

//     // 14. Calculate sum insured totals
//     $totalSumInsured    = $data->sum(fn($item) => (float)($item->CED_SI ?? 0));
//     $notPostedSumInsured = $notPostedRecords->sum(fn($item) => (float)($item->CED_SI ?? 0));

//     // 15. Add days_old to each record for row highlighting (based on GRH_ACCEPTEDDATE)
//     $data = $data->map(function ($item) use ($today) {
//         $item->days_old = null;
//         $date = ($item->GRH_ACCEPTEDDATE ?? null)
//             ? Carbon::parse($item->GRH_ACCEPTEDDATE)
//             : null;
//         if ($date && $date->isValid()) {
//             $item->days_old = $date->diffInDays($today);
//         }
//         return $item;
//     });

//     // 16. Format dates for display
//     $apiDateFrom = Carbon::parse($formStartDate)->format('d-M-Y');
//     $apiDateTo   = Carbon::parse($formEndDate)->format('d-M-Y');

//     // 17. Return view
//     return view('GetRequestReports.r2', [
//         'data'                => $data,
//         'start_date'          => $formStartDate,
//         'end_date'            => $formEndDate,
//         'uniqueCategories'    => $uniqueCategories,
//         'api_date_range'      => ['from' => $apiDateFrom, 'to' => $apiDateTo],
//         'branches'            => $branches,
//         'notPostedCount'      => $notPostedRecords->count(),
//         'totalCount'          => $data->count(),
//         'groupedByAging'      => $groupedByAging,
//         'totalSumInsured'     => $totalSumInsured,
//         'notPostedSumInsured' => $notPostedSumInsured,
//         'selected_time_filter'=> $timeFilter,
//         'selected_department' => $selectedCategory,
//         'counts'              => $counts,
//     ]);
// } s

// public function getReinsuranceCase(Request $request)
// {
//     // 1. Fetch business classes
//     $businessClassResponse = Helper::fetchBusinessClasses();
//     if (isset($businessClassResponse['error'])) {
//         return back()->with('error', $businessClassResponse['error']);
//     }

//     // 2. Fetch brokers
//     $brokerResponse = Helper::fetchBroker();
//     if (isset($brokerResponse['error'])) {
//         return back()->with('error', $brokerResponse['error']);
//     }

//     $brokers = collect($brokerResponse)->filter(function ($broker) {
//         return isset($broker['PPS_STATUS']) && $broker['PPS_STATUS'] === 'A';
//     });

//     // 3. Date filters
//     $startDate = $request->filled('start_date')
//         ? Carbon::parse($request->start_date)->format('Y-m-d')
//         : Carbon::now()->startOfMonth()->format('Y-m-d');

//     $endDate = $request->filled('end_date')
//     ? Carbon::parse($request->end_date)->format('Y-m-d')
//     : Carbon::now()->format('Y-m-d');

//     // 4. Sum
//     $sum = $request->filled('sum')
//         ? (float) str_replace(',', '', $request->sum)
//         : 10000000;

//     $businessClass     = $request->input('business_class', 'All');
//     $location_category = $request->input('location_category', 'All');
//     $brokerCode        = $request->input('broker_code', 'All');
//     $clientType        = $request->input('client_type', 'All');


//     $categoryMapping = [
//         'Fire'          => 11,
//         'Marine'        => 12,
//         'Motor'         => 13,
//         'Miscellaneous' => 14,
//         'Health'        => 16,
//     ];

//     // Check if user actually submitted the form
//     $formSubmitted = $request->hasAny([
//         'start_date', 'end_date', 'new_category',
//         'location_category', 'broker_code', 'client_type', 'sum'
//     ]);

//     if ($formSubmitted) {
//         // Use exactly what user checked — empty array means "All"
//         $selectedCategories = $request->input('new_category', []);
//         if (!is_array($selectedCategories)) {
//             $selectedCategories = (array) $selectedCategories;
//         }
//     } else {
        
//        $selectedCategories = ['Fire', 'Motor', 'Miscellaneous'];
//     }

//     if (!empty($selectedCategories)) {
//         $deptArray = [];
//         foreach ($selectedCategories as $category) {
//             if (isset($categoryMapping[$category])) {
//                 $deptArray[] = $categoryMapping[$category];
//             }
//         }
//         $dept = !empty($deptArray) ? implode(',', $deptArray) : 'All';
//     } else {
//         $dept = 'All'; 
//     }

//     if ($location_category !== 'All') {
//         $branchdata   = BranchesList::where('fbracode', $location_category)->first();
//         $singleBranch = $branchdata->fbracode;
//         $takaful      = $branchdata->fbratak;
//     } else {
//         $singleBranch = 'All';
//         $takaful      = 'All';
//     }

//     $result = Helper::getReinsuranceCase(
//         $startDate, $endDate, $sum,
//         $businessClass, $dept,
//         $brokerCode, $clientType,
//         $singleBranch, $takaful
//     );

    

//     $data = collect($result['data'] ?? []);

//     // 8. Exclude already logged documents
//     $insertedDocs = ReinsLog::pluck('uw_doc')->toArray();
//     $data = $data->filter(function ($record) use ($insertedDocs) {
//         return !in_array($record->GDH_DOC_REFERENCE_NO ?? '', $insertedDocs);
//     });

//     // 9. Date display
//     $apiDateFrom = Carbon::parse($startDate)->format('d-M-Y');
//     $apiDateTo   = Carbon::parse($endDate)->format('d-M-Y');
//     $Branches    = BranchesList::all();

//     // 10. Footer totals
//     $totalSumInsured = $data->sum(function ($record) {
//         $val = trim($record->GDH_TOTALSI ?? '');
//         return is_numeric($val) ? (float) $val : 0;
//     });
//     $totalGrossPremium = $data->sum(function ($record) {
//         $val = trim($record->GDH_GROSSPREMIUM ?? '');
//         return is_numeric($val) ? (float) $val : 0;
//     });

//     return view('GetRequestReports.case', [
//         'data'               => $data,
//         'start_date'         => $startDate,
//         'end_date'           => $endDate,
//         'Branches'           => $Branches,
//         'location_category'  => $singleBranch,
//         'selectedCategories' => $selectedCategories, 
//         'api_date_range'     => ['from' => $apiDateFrom, 'to' => $apiDateTo],
//         'brokers'            => $brokers,
//         'businessClasses'    => $businessClassResponse,
//         'totalSumInsured'    => $totalSumInsured,
//         'totalGrossPremium'  => $totalGrossPremium,
//         'applied_filters'    => [
//             'sum'            => $sum,
//             'business_class' => $businessClass,
//             'dept'           => $dept,
//             'broker_code'    => $brokerCode,
//             'client_type'    => $clientType,
//         ],
//     ]);
// }
// public function getReinsuranceCase(Request $request)
// {
//     // 1️⃣ Fetch business classes
//     $businessClassResponse = Helper::fetchBusinessClasses();
//     if (isset($businessClassResponse['error'])) {
//         return back()->with('error', $businessClassResponse['error']);
//     }

//     // 2️⃣ Fetch brokers
//     $brokerResponse = Helper::fetchBroker();
//     if (isset($brokerResponse['error'])) {
//         return back()->with('error', $brokerResponse['error']);
//     }

//     $brokers = collect($brokerResponse)->filter(function ($broker) {
//         return isset($broker['PPS_STATUS']) && $broker['PPS_STATUS'] === 'A';
//     });

//     // 3️⃣ Date filters
//     $startDate = $request->filled('start_date')
//         ? Carbon::parse($request->start_date)->format('Y-m-d')
//         : Carbon::now()->startOfMonth()->format('Y-m-d');

//     $endDate = $request->filled('end_date')
//         ? Carbon::parse($request->end_date)->format('Y-m-d')
//         : Carbon::now()->format('Y-m-d');

//     // 4️⃣ Sum filter
//     $sum = $request->filled('sum')
//         ? (float) str_replace(',', '', $request->sum)
//         : 10000000;

//     // 5️⃣ Other filters
//     $businessClass     = $request->input('business_class', 'All');
//     $location_category = $request->input('location_category', 'All');
//     $brokerCode        = $request->input('broker_code', 'All');
//     $clientType        = $request->input('client_type', 'All');
//     $postingTag        = $request->input('posting_tag', 'All'); 
//     $categoryMapping = [
//         'Fire'          => 11,
//         'Marine'        => 12,
//         'Motor'         => 13,
//         'Miscellaneous' => 14,
//         'Health'        => 16,
//     ];

//     // 6️⃣ Determine selected categories
//     $formSubmitted = $request->hasAny([
//         'start_date', 'end_date', 'new_category',
//         'location_category', 'broker_code', 'client_type', 'sum'
//     ]);

//     if ($formSubmitted) {
//         $selectedCategories = $request->input('new_category', []);
//         if (!is_array($selectedCategories)) {
//             $selectedCategories = (array) $selectedCategories;
//         }
//     } else {
//         $selectedCategories = ['Fire', 'Motor', 'Miscellaneous'];
//     }

//     // Map category names to dept codes
//     $deptArray = [];
//     foreach ($selectedCategories as $category) {
//         if (isset($categoryMapping[$category])) {
//             $deptArray[] = $categoryMapping[$category];
//         }
//     }
//     $dept = !empty($deptArray) ? implode(',', $deptArray) : 'All';

//     // 7️⃣ Branch / location handling
//     if ($location_category !== 'All') {
//         $branchdata   = BranchesList::where('fbracode', $location_category)->first();
//         $singleBranch = $branchdata->fbracode;
//         $takaful      = $branchdata->fbratak;
//     } else {
//         $singleBranch = 'All';
//         $takaful      = 'All';
//     }

//     // 8️⃣ Fetch reinsurance cases
//     $result = Helper::getReinsuranceCase(
//         $startDate, $endDate, $sum,
//         $businessClass, $dept,
//         $brokerCode, $clientType,
//         $singleBranch, $takaful
//     );

//     $data = collect($result['data'] ?? []);

//     // 9️⃣ Exclude already logged documents
//     $insertedDocs = ReinsLog::pluck('uw_doc')->toArray();
//     $data = $data->filter(function ($record) use ($insertedDocs) {
//         return !in_array($record->GDH_DOC_REFERENCE_NO ?? '', $insertedDocs);
//     });

//     // 🔟 Filter by posting tag
//     if ($postingTag !== 'All') {
//     $data = $data->filter(function ($record) use ($postingTag) {
//         $value = strtoupper(trim($record->GDH_POSTING_TAG ?? ''));
//         if ($postingTag === 'Y') {
//             return $value === 'Y';
//         } elseif ($postingTag === 'NOT_POST') {
//             // N + empty + null all treated as "not posted"
//             return $value === 'N' || $value === '';
//         }
//         return true;
//     });
//     }

//     // 11️⃣ Format dates for display
//     $apiDateFrom = Carbon::parse($startDate)->format('d-M-Y');
//     $apiDateTo   = Carbon::parse($endDate)->format('d-M-Y');
//     $Branches    = BranchesList::all();

//     // 12️⃣ Footer totals
//     $totalSumInsured = $data->sum(function ($record) {
//         $val = trim($record->GDH_TOTALSI ?? '');
//         return is_numeric($val) ? (float) $val : 0;
//     });
//     $totalGrossPremium = $data->sum(function ($record) {
//         $val = trim($record->GDH_GROSSPREMIUM ?? '');
//         return is_numeric($val) ? (float) $val : 0;
//     });

//     // 13️⃣ Return view
//     return view('GetRequestReports.case', [
//         'data'               => $data,
//         'start_date'         => $startDate,
//         'end_date'           => $endDate,
//         'Branches'           => $Branches,
//         'location_category'  => $singleBranch,
//         'selectedCategories' => $selectedCategories,
//         'posting_tag'        => $postingTag,
//         'api_date_range'     => ['from' => $apiDateFrom, 'to' => $apiDateTo],
//         'brokers'            => $brokers,
//         'businessClasses'    => $businessClassResponse,
//         'totalSumInsured'    => $totalSumInsured,
//         'totalGrossPremium'  => $totalGrossPremium,
//         'applied_filters'    => [
//             'sum'            => $sum,
//             'business_class' => $businessClass,
//             'dept'           => $dept,
//             'broker_code'    => $brokerCode,
//             'client_type'    => $clientType,
//             'posting_tag'    => $postingTag,
//         ],
//     ]);
// } now and before
public function getReinsuranceCase(Request $request)
{
    // 1️⃣ Fetch business classes
    $businessClassResponse = Helper::fetchBusinessClasses();
    if (isset($businessClassResponse['error'])) {
        return back()->with('error', $businessClassResponse['error']);
    }

    // 2️⃣ Fetch brokers
    $brokerResponse = Helper::fetchBroker();
    if (isset($brokerResponse['error'])) {
        return back()->with('error', $brokerResponse['error']);
    }

    $brokers = collect($brokerResponse)->filter(function ($broker) {
        return isset($broker['PPS_STATUS']) && $broker['PPS_STATUS'] === 'A';
    });

    // 3️⃣ Date filters
    $startDate = $request->filled('start_date')
        ? Carbon::parse($request->start_date)->format('Y-m-d')
        : Carbon::now()->startOfMonth()->format('Y-m-d');

    $endDate = $request->filled('end_date')
        ? Carbon::parse($request->end_date)->format('Y-m-d')
        : Carbon::now()->format('Y-m-d');

    // 4️⃣ Sum filter
    $sum = $request->filled('sum')
        ? (float) str_replace(',', '', $request->sum)
        : 10000000;

    // 5️⃣ Other filters
    $businessClass     = $request->input('business_class', 'All');
    $location_category = $request->input('location_category', 'All');
    $brokerCode        = $request->input('broker_code', 'All');
    $clientType        = $request->input('client_type', 'All');
    $postingTag        = $request->input('posting_tag', 'All'); 
    $categoryMapping = [
        'Fire'          => 11,
        'Marine'        => 12,
        'Motor'         => 13,
        'Miscellaneous' => 14,
        'Health'        => 16,
    ];

    // 6️⃣ Determine selected categories
    $formSubmitted = $request->hasAny([
        'start_date', 'end_date', 'new_category',
        'location_category', 'broker_code', 'client_type', 'sum'
    ]);

    if ($formSubmitted) {
        $selectedCategories = $request->input('new_category', []);
        if (!is_array($selectedCategories)) {
            $selectedCategories = (array) $selectedCategories;
        }
    } else {
        $selectedCategories = ['Fire', 'Motor', 'Miscellaneous'];
    }

    // Map category names to dept codes
    $deptArray = [];
    foreach ($selectedCategories as $category) {
        if (isset($categoryMapping[$category])) {
            $deptArray[] = $categoryMapping[$category];
        }
    }
    $dept = !empty($deptArray) ? implode(',', $deptArray) : 'All';

    // 7️⃣ Branch / location handling
    if ($location_category !== 'All') {
        $branchdata   = BranchesList::where('fbracode', $location_category)->first();
        $singleBranch = $branchdata->fbracode;
        $takaful      = $branchdata->fbratak;
    } else {
        $singleBranch = 'All';
        $takaful      = 'All';
    }

   $insutype = $request->input('insu_type', 'takaful');
   $posted   = $request->input('posted', 'Y');
   $docType  = $request->input('doc_type', 'P');
   $sum      = $request->input('sum', 10000000); 
    // 9️⃣ Fetch reinsurance cases from helper
    $result = Helper::getReinsuranceCase(
        $startDate,
        $endDate,
        $sum,
        $businessClass,
        $dept,
        $brokerCode,
        $clientType,
        $singleBranch,
        $takaful,
        $docType,
        $posted,
        $insutype
    );

    $data = collect($result['data'] ?? []);

    // 🔟 Exclude already logged documents
    $insertedDocs = ReinsLog::pluck('uw_doc')->toArray();
    $data = $data->filter(fn($record) => !in_array($record->GDH_DOC_REFERENCE_NO ?? '', $insertedDocs));

    // 1️⃣1️⃣ Filter by posting tag if needed
    if ($postingTag !== 'All') {
        $data = $data->filter(function ($record) use ($postingTag) {
            $value = strtoupper(trim($record->GDH_POSTING_TAG ?? ''));
            if ($postingTag === 'Y') return $value === 'Y';
            if ($postingTag === 'NOT_POST') return $value === 'N' || $value === '';
            return true;
        });
    }

    // 1️⃣2️⃣ Format dates and fetch branches
    $apiDateFrom = Carbon::parse($startDate)->format('d-M-Y');
    $apiDateTo   = Carbon::parse($endDate)->format('d-M-Y');
    $Branches    = BranchesList::all();

    // 1️⃣3️⃣ Footer totals
    $totalSumInsured = $data->sum(fn($record) => is_numeric(trim($record->GDH_TOTALSI ?? '')) ? (float) trim($record->GDH_TOTALSI) : 0);
    $totalGrossPremium = $data->sum(fn($record) => is_numeric(trim($record->GDH_GROSSPREMIUM ?? '')) ? (float) trim($record->GDH_GROSSPREMIUM) : 0);

    // 1️⃣4️⃣ Return view
    return view('GetRequestReports.case', [
        'data'               => $data,
        'start_date'         => $startDate,
        'end_date'           => $endDate,
        'Branches'           => $Branches,
        'location_category'  => $singleBranch,
        'selectedCategories' => $selectedCategories,
        'posting_tag'        => $postingTag,
        'api_date_range'     => ['from' => $apiDateFrom, 'to' => $apiDateTo],
        'brokers'            => $brokers,
        'businessClasses'    => $businessClassResponse,
        'totalSumInsured'    => $totalSumInsured,
        'totalGrossPremium'  => $totalGrossPremium,
        'applied_filters'    => [
            'sum'            => $sum,
            'business_class' => $businessClass,
            'dept'           => $dept,
            'broker_code'    => $brokerCode,
            'client_type'    => $clientType,
            'posting_tag'    => $postingTag,
            'insu_type'      => $insutype,
            'posted'         => $posted,
            'doc_type'       => $docType,
        ],
    ]);
}


public function fetchReinsuranceData(Request $request)
{
    try {
        $request->validate([
            'uw_doc'          => 'required|string',
            'dept'            => 'required|integer',
            'issue_date'      => 'nullable|string',
            'comm_date'       => 'nullable|string',
            'expiry_date'     => 'nullable|string',
            'insured'         => 'nullable|string',
            'location'        => 'nullable|string',
            'business_class'  => 'nullable|string',
            'sum_insured'     => 'nullable|numeric',
            'gross_premium'   => 'nullable|numeric',
            'net_premium'     => 'nullable|numeric',
            'remarks'         => 'nullable|string',
            'base_doc'        => 'nullable|string',
            'pii_desc'        => 'nullable|string',
            'rqn_sts'         => 'nullable|string',
        ]);

        $userid = Session::get('user')['name'] ?? 'Unknown';

        $convertDate = function ($dateString) {
            if ($dateString === 'N/A' || empty($dateString) || $dateString === null) return null;
            try {
                return \Carbon\Carbon::createFromFormat('d-M-y', $dateString)->format('Y-m-d');
            } catch (\Exception $e) {
                Log::warning("Date conversion failed for: {$dateString}. Error: " . $e->getMessage());
                return null;
            }
        };

        $convertNumeric = function ($value) {
            if ($value === 'N/A' || empty($value) || $value === null) return null;
            $cleanValue = str_replace(',', '', $value);
            return is_numeric($cleanValue) ? (float) $cleanValue : null;
        };

        $record = ReinsLog::create([
    'uw_doc'             => $request->uw_doc,
    'dept'               => $request->dept,
    'issue_date'         => $convertDate($request->issue_date),
    'comm_date'          => $convertDate($request->comm_date),
    'expiry_date'        => $convertDate($request->expiry_date),
    'insured'            => $request->insured !== 'N/A' ? urldecode($request->insured) : null,
    'location'           => $request->location !== 'N/A' ? urldecode($request->location) : null,
    'business_class'     => $request->business_class !== 'N/A' ? urldecode($request->business_class) : null,
    'sum_insured'        => $convertNumeric($request->sum_insured),
    'gross_premium'      => $convertNumeric($request->gross_premium),
    'net_premium'        => $convertNumeric($request->net_premium),
    'riskMarked'         => $request->status, // Use the status from request here
    'noti_att'           => 'N',
    'created_by'         => $userid,
    'updated_by'         => $userid,
    'remarks'            => $request->remarks ?? null,
    'GDH_BASEDOCUMENTNO' => $request->base_doc ?? null,
    'PII_DESC'           => $request->pii_desc ? urldecode($request->pii_desc) : null,
    'RQN_STS'            => $request->rqn_sts ?? null,
    'GDH_POSTING_DATE'   => $request->business_class !== 'N/A' ? urldecode($request->business_class) : null,
]);
//dd($record);

        return response()->json([
            'message' => 'Record added successfully',
            'record'  => $record,
        ], 201);

    } catch (\Exception $e) {
        Log::error('Error adding reinsurance data: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        return response()->json([
            'message' => 'Server error occurred',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function storeUpload(Request $request)
{
    $request->validate([
        'GRH_REFERENCE_NO' => 'nullable|string|max:100',
        'upload_file'      => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip',
    ]);

    $refNo    = trim($request->input('GRH_REFERENCE_NO'));
    $file     = $request->file('upload_file');
    $filename = time() . '_' . $file->getClientOriginalName();

    $path = $file->storeAs(
        'reqnote_uploads/' . $refNo,
        $filename,
        'public'
    );

    \App\Models\ReqnoteMark::updateOrCreate(
        ['GRH_REFERENCE_NO' => $refNo],
        ['upload_file'      => $path]   
    );

    return response()->json([
        'success'  => true,
        'url'      => \Storage::url($path),
        'filename' => $filename,
    ]);
}

// public function show(Request $request)
// {
//     try {

//         // ─────────────────────────────────────────────
//         // 1️⃣ Trigger external API once
//         // ─────────────────────────────────────────────
//         $apiUrl = "http://172.16.22.204/dashboardApi/reins/rqn/uw_reins_schd.php";
//         @file_get_contents($apiUrl, false, stream_context_create([
//             'http' => [
//                 'ignore_errors' => true,
//                 'timeout'       => 120
//             ]
//         ]));

//         $categoryMapping = [
//             'Fire'          => 11,
//             'Marine'        => 12,
//             'Motor'         => 13,
//             'Miscellaneous' => 14,
//             'Health'        => 16,
//         ];

//         $timeFilter       = $request->query('time_filter', 'all');
//         $selectedCategory = $request->query('new_category', '');
//         $selectedRisk     = $request->query('risk_filter', 'Y');
//         $today            = Carbon::today();

//         // ─────────────────────────────────────────────
//         // 2️⃣ Fetch all pending records once
//         // ─────────────────────────────────────────────
//         $allRecords = ReinsLog::whereNull('rq_gen')->get();
//         //dd($allRecord);

       
//         // ─────────────────────────────────────────────
//         // 4️⃣ Prepare Y pending collection for counts
//         // ─────────────────────────────────────────────
//         $pendingY = $allRecords->filter(function ($r) {
//             return $r->riskMarked === 'Y' && !$r->is_processed;
//         });

//         $counts = [
//             'all'    => $pendingY->count(),
//             '2days'  => 0,
//             '5days'  => 0,
//             '7days'  => 0,
//             '10days' => 0,
//             '15days' => 0,
//             '15plus' => 0,
//         ];

//         foreach ($pendingY as $r) {

//             $daysOld = $r->created_at
//                 ? $r->created_at->diffInDays($today)
//                 : 0;

//             if ($daysOld <= 2) {
//                 $counts['2days']++;
//             } elseif ($daysOld > 2 && $daysOld <= 5) {
//                 $counts['5days']++;
//             } elseif ($daysOld > 5 && $daysOld <= 7) {
//                 $counts['7days']++;
//             } elseif ($daysOld > 7 && $daysOld <= 10) {
//                 $counts['10days']++;
//             } elseif ($daysOld > 10 && $daysOld <= 15) {
//                 $counts['15days']++;
//             } elseif ($daysOld > 15) {
//                 $counts['15plus']++;
//             }
//         }

//         // ─────────────────────────────────────────────
//         // 5️⃣ Apply risk filter for listing
//         // ─────────────────────────────────────────────
//         if ($selectedRisk !== 'ALL') {
//             $allRecords = $allRecords->filter(function ($r) use ($selectedRisk) {
//                 return $r->riskMarked === $selectedRisk;
//             });
//         }

//         // ─────────────────────────────────────────────
//         // 6️⃣ Remove processed Y from listing
//         // ─────────────────────────────────────────────
//         $records = $allRecords->filter(function ($record) {

//             if ($record->riskMarked !== 'Y') {
//                 return true;
//             }

//             return !$record->is_processed;
//         });

//         // ─────────────────────────────────────────────
//         // 7️⃣ Apply time filter
//         // ─────────────────────────────────────────────
//         if ($timeFilter !== 'all') {

//             $records = $records->filter(function ($r) use ($timeFilter, $today) {

//                 $daysOld = $r->created_at
//                     ? $r->created_at->diffInDays($today)
//                     : 0;

//                 switch ($timeFilter) {

//                     case '2days':
//                         return $daysOld <= 2;

//                     case '5days':
//                         return $daysOld > 2 && $daysOld <= 5;

//                     case '7days':
//                         return $daysOld > 5 && $daysOld <= 7;

//                     case '10days':
//                         return $daysOld > 7 && $daysOld <= 10;

//                     case '15days':
//                         return $daysOld > 10 && $daysOld <= 15;

//                     case '15plus':
//                         return $daysOld > 15;

//                     default:
//                         return true;
//                 }
//             });
//         }

//         // ─────────────────────────────────────────────
//         // 8️⃣ Department filter
//         // ─────────────────────────────────────────────
//         if ($selectedCategory && isset($categoryMapping[$selectedCategory])) {

//             $deptCode = (string) $categoryMapping[$selectedCategory];

//             $records = $records->filter(function ($item) use ($deptCode) {
//                 return isset($item->dept)
//                     ? Str::startsWith($item->dept, $deptCode)
//                     : false;
//             });
//         }

//         // ─────────────────────────────────────────────
//         // 9️⃣ Add days_old field
//         // ─────────────────────────────────────────────
//         foreach ($records as $record) {
//             $record->days_old = $record->created_at
//                 ? $record->created_at->diffInDays($today)
//                 : null;
//             }
//             $user = User::all();
//             $ri = ReinsLog::latest()->first();
//  //dd($ri->toArray());
// //             $ri = ReinsLog::all();
// //             dd($ri);
//             $currentUserEmail = Session::get('user')['email'];
//         //     dd(Session::get('user'));
//         //  dd($currentUserEmail );


//         // $currentUserEmail = auth()->user()->email ?? null;

//         return view('GetRequestReports.show', [
//             'records'              => $records,
//             'selected_time_filter' => $timeFilter,
//             'selected_department'  => $selectedCategory,
//             'selected_risk'        => $selectedRisk,
//             'counts'               => $counts,
//             'currentUserEmail'     => $currentUserEmail,
           
//         ]);

//     } catch (\Exception $e) {

//         Log::error('Error fetching reinsurance data: ' . $e->getMessage());

//         return back()->with('error', 'An error occurred while fetching data.');
//     }
// } 
// public function show(Request $request)
// {
//   //  $latest = ReinsLog::latest()->first();
//  //dd($latest);
//      $apiUrl = "http://172.16.22.204/dashboardApi/reins/rqn/uw_reins_schd.php";

//     @file_get_contents($apiUrl, false, stream_context_create([
//         'http' => [
//             'ignore_errors' => true,
//             'timeout' => 120
//         ]
//     ]));
//     try {
//         $categoryMapping = [
//             'Fire'          => 11,
//             'Marine'        => 12,
//             'Motor'         => 13,
//             'Miscellaneous' => 14,
//             'Health'        => 16,
//         ];

//         $timeFilter       = $request->query('time_filter', 'all');
//         $selectedCategory = $request->query('new_category', '');
//         $selectedRisk     = $request->query('risk_filter', 'Y');
//         $today            = \Carbon\Carbon::today();

//         $allRecords = \App\Models\ReinsLog::whereNull('rq_gen')->get();

//         // ────────────── Counts based on selected risk ──────────────
//         $recordsForCounts = ($selectedRisk === 'ALL')
//             ? $allRecords
//             : $allRecords->filter(function ($r) use ($selectedRisk) {
//                 return $r->riskMarked === $selectedRisk;
//             });

//         $counts = [
//             'all'    => $recordsForCounts->count(),
//             '2days'  => 0,
//             '5days'  => 0,
//             '7days'  => 0,
//             '10days' => 0,
//             '15days' => 0,
//             '15plus' => 0,
//         ];

//         foreach ($recordsForCounts as $r) {
//             $daysOld = $r->created_at ? $r->created_at->diffInDays($today) : 0;

//             if ($daysOld <= 2) $counts['2days']++;
//             elseif ($daysOld <= 5) $counts['5days']++;
//             elseif ($daysOld <= 7) $counts['7days']++;
//             elseif ($daysOld <= 10) $counts['10days']++;
//             elseif ($daysOld <= 15) $counts['15days']++;
//             else $counts['15plus']++;
//         }

//         // ────────────── Records filtered by risk ──────────────
//         $records = ($selectedRisk === 'ALL')
//             ? $allRecords
//             : $allRecords->filter(function ($r) use ($selectedRisk) {
//                 return $r->riskMarked === $selectedRisk;
//             });

//         // Remove processed Pending if risk = Y
//         if ($selectedRisk === 'Y') {
//             $records = $records->filter(function ($r) {
//                 return !$r->is_processed;
//             });
//         }

//         // Time filter
//         if ($timeFilter !== 'all') {
//             $records = $records->filter(function ($r) use ($timeFilter, $today) {
//                 $daysOld = $r->created_at ? $r->created_at->diffInDays($today) : 0;

//                 switch ($timeFilter) {
//                     case '2days':  return $daysOld <= 2;
//                     case '5days':  return $daysOld > 2 && $daysOld <= 5;
//                     case '7days':  return $daysOld > 5 && $daysOld <= 7;
//                     case '10days': return $daysOld > 7 && $daysOld <= 10;
//                     case '15days': return $daysOld > 10 && $daysOld <= 15;
//                     case '15plus': return $daysOld > 15;
//                     default:       return true;
//                 }
//             });
//         }

//         // Department filter
//         if ($selectedCategory && isset($categoryMapping[$selectedCategory])) {
//             $deptCode = (string) $categoryMapping[$selectedCategory];
//             $records = $records->filter(function ($r) use ($deptCode) {
//                 return isset($r->dept) && \Illuminate\Support\Str::startsWith($r->dept, $deptCode);
//             });
//         }

//         // Add days_old
//         foreach ($records as $r) {
//             $r->days_old = $r->created_at ? $r->created_at->diffInDays($today) : null;
//         }

//         $currentUserEmail = \Illuminate\Support\Facades\Session::get('user')['email'] ?? null;

//         return view('GetRequestReports.show', [
//             'records'              => $records,
//             'selected_time_filter' => $timeFilter,
//             'selected_department'  => $selectedCategory,
//             'selected_risk'        => $selectedRisk,
//             'counts'               => $counts,
//             'currentUserEmail'     => $currentUserEmail,
//         ]);

//     } catch (\Exception $e) {
//         \Illuminate\Support\Facades\Log::error('Error fetching reinsurance data: ' . $e->getMessage());
//         return back()->with('error', 'An error occurred while fetching data.');
//     }
// } s

public function saveRemarks(Request $request)
{
    try {
        $request->validate([
            'uw_doc'  => 'required|string',
            'remarks' => 'required|string',
            'sent_to' => 'nullable|string',
            'sent_cc' => 'nullable|string',


            
        ]);

        Log::info('saveRemarks called', ['uw_doc' => $request->uw_doc, 'remarks' => $request->remarks]);

        $record = ReinsLog::where('uw_doc', $request->uw_doc)->first();

        if (!$record) {
            Log::warning('saveRemarks: record not found', ['uw_doc' => $request->uw_doc]);
            return response()->json(['success' => false, 'message' => 'Record not found for uw_doc: ' . $request->uw_doc], 404);
        }

        if ($record->riskMarked === 'D') {
            return response()->json(['success' => false, 'message' => 'Already declined.'], 422);
        }

        $record->remarks    = $request->remarks;
        $record->riskMarked = 'D';
        $record->updated_by = auth()->check() ? auth()->user()->name : 'System';
        $record->updated_at = now();
      //  dd($record);
        $record->save();

        Log::info('saveRemarks: saved successfully', ['uw_doc' => $request->uw_doc]);

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        Log::error('saveRemarks error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}



 public static function getRequestNotesByDocument($documentNumber)
    {
        if (empty($documentNumber)) {
            return [];
        }

        $apiUrl = "http://172.16.22.204/dashboardApi/reins/rqn/get_notes_uw.php?uw_doc=" . urlencode($documentNumber);
        
        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120
            ]
        ]));

        if ($response === false) {
            Log::error("Failed to fetch request notes for document: " . $documentNumber);
            return [];
        }

        $cleanResponse = preg_replace('/<br \/>\n<b>Notice<\/b>:.*?<br \/>\n/', '', $response);
        $data = json_decode($cleanResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            Log::error("Invalid JSON response for document: " . $documentNumber);
            return [];
        }

        $requestNotes = [];
        foreach ($data as $item) {
            try {
                $decoded = is_string($item) ? json_decode($item, true) : $item;
                if (isset($decoded['GRH_REFERENCE_NO'])) {
                    $requestNotes[] = $decoded['GRH_REFERENCE_NO'];
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return array_unique(array_filter($requestNotes));
    }
 


// public function getshow(Request $request)
// {

//     try {
//         // Get the time filter from the request
//         $timeFilter = $request->query('time_filter', 'all'); // Default to 'all'
//          $selectedCategory = $request->query('new_category', '');
        
//         // Initialize counts for each time filter
//         $counts = [
//             'all' => 0,
//             '2days' => 0,
//             '5days' => 0,
//             '7days' => 0,
//             '10days' => 0,
//             '15days' => 0,
//             '15plus' => 0,
//         ];

//         // Calculate counts for each time filter
//         $today = Carbon::today();
        
//         // Fetch unique records based on reqnote, selecting the latest record
//         $allRecords = EmailLog::select('*')
//             ->whereIn('id', function ($query) {
//                 $query->selectRaw('MAX(id)')
//                       ->from('emaillogs')
//                       ->groupBy('reqnote');
//             })
//             ->get();
        
//         // All records
//         $counts['all'] = $allRecords->count();

//         // 2 Days (0-2 days old)
//         $counts['2days'] = $allRecords->filter(function ($record) use ($today) {
//             return $record->datetime && Carbon::parse($record->datetime) >= $today->copy()->subDays(2);
//         })->count();
//        // dd($allRecords);
//         // 5 Days (2-5 days old)
//         $counts['5days'] = $allRecords->filter(function ($record) use ($today) {
//             return $record->datetime && 
//                    Carbon::parse($record->datetime) >= $today->copy()->subDays(5) &&
//                    Carbon::parse($record->datetime) < $today->copy()->subDays(2);
//         })->count();

//         // 7 Days (5-7 days old)
//         $counts['7days'] = $allRecords->filter(function ($record) use ($today) {
//             return $record->datetime && 
//                    Carbon::parse($record->datetime) >= $today->copy()->subDays(7) &&
//                    Carbon::parse($record->datetime) < $today->copy()->subDays(5);
//         })->count();

//         // 10 Days (7-10 days old)
//         $counts['10days'] = $allRecords->filter(function ($record) use ($today) {
//             return $record->datetime && 
//                    Carbon::parse($record->datetime) >= $today->copy()->subDays(10) &&
//                    Carbon::parse($record->datetime) < $today->copy()->subDays(7);
//         })->count();

//         // 15 Days (10-15 days old)
//         $counts['15days'] = $allRecords->filter(function ($record) use ($today) {
//             return $record->datetime && 
//                    Carbon::parse($record->datetime) >= $today->copy()->subDays(15) &&
//                    Carbon::parse($record->datetime) < $today->copy()->subDays(10);
//         })->count();

//         // 15+ Days (more than 15 days old)
//         $counts['15plus'] = $allRecords->filter(function ($record) use ($today) {
//             return $record->datetime && Carbon::parse($record->datetime) < $today->copy()->subDays(15);
//         })->count();

//         // Apply time filter based on datetime
//         $query = EmailLog::select('*')
//             ->whereIn('id', function ($query) {
//                 $query->selectRaw('MAX(id)')
//                       ->from('emaillogs')
//                       ->groupBy('reqnote');
//             });

//         switch ($timeFilter) {
//             case '2days':
//                 $query->where('datetime', '>=', $today->copy()->subDays(2));
//                 break;
//             case '5days':
//                 $query->where('datetime', '>=', $today->copy()->subDays(5))
//                       ->where('datetime', '<', $today->copy()->subDays(2));
//                 break;
//             case '7days':
//                 $query->where('datetime', '>=', $today->copy()->subDays(7))
//                       ->where('datetime', '<', $today->copy()->subDays(5));
//                 break;
//             case '10days':
//                 $query->where('datetime', '>=', $today->copy()->subDays(10))
//                       ->where('datetime', '<', $today->copy()->subDays(7));
//                 break;
//             case '15days':
//                 $query->where('datetime', '>=', $today->copy()->subDays(15))
//                       ->where('datetime', '<', $today->copy()->subDays(10));
//                 break;
//             case '15plus':
//                 $query->where('datetime', '<', $today->copy()->subDays(15));
//                 break;
//             case 'all':
//             default:
//                 // No time filter for 'all'
//                 break;
//         }
//          $categoryMapping = [
//             'Fire'          => 11,
//             'Marine'        => 12,
//             'Motor'         => 13,
//             'Miscellaneous' => 14,
//             'Health'        => 16,
//         ];

        
//         // Department filter
//         if ($selectedCategory && isset($categoryMapping[$selectedCategory])) {
//             $deptCode = (string) $categoryMapping[$selectedCategory];
//             $records = $records->filter(function ($r) use ($deptCode) {
//                 return isset($r->dept) && \Illuminate\Support\Str::startsWith($r->dept, $deptCode);
//             });
//         }

//         $records = $query->get();
//        // dd( $records);
//       // dd($records[0]->toArray());
        
//         // Add email count for each record
//         foreach ($records as $record) {
//     $record->email_count = EmailLog::where('reqnote', $record->reqnote)->count();

//     // Get the latest datetime for this reqnote
//     $latestEmail = EmailLog::where('reqnote', $record->reqnote)
//                     ->latest('datetime')
//                     ->first();

//     $record->latest_sent_at = $latestEmail ? $latestEmail->datetime : null;

//     $record->days_old = $record->datetime ? Carbon::parse($record->datetime)->diffInDays($today) : null;
// }

//         // Return view with records and counts
//         return view('GetRequestReports.getshow', [
//             'records' => $records,
//             'selected_time_filter' => $timeFilter,
//              'selected_department'  => $selectedCategory,
//             'counts' => $counts,
//         ]);

//     } catch (\Exception $e) {
//         Log::error('Error fetching email log data: ' . $e->getMessage());
//         return back()->with('error', 'An error occurred while fetching data.');
//     }
// }

// public function getshow(Request $request)
// {
//     //dd($request->all());
//     try {
//         $timeFilter       = $request->query('time_filter',   'all');
//         $selectedCategory = $request->query('new_category',  '');   // dept
//         $selectedReins    = $request->query('reins_party',   '');   // reins party
//         $today            = Carbon::today();
 
//         $categoryMapping = [
//             'Fire'          => '11',
//             'Marine'        => '12',
//             'Motor'         => '13',
//             'Miscellaneous' => '14',
//             'Health'        => '16',
//         ];
 
//         // ── Exclude records already tagged via Note (any report_name) ─────
//         $taggedReqnotes = \App\Models\ReqnoteMark::pluck('GRH_REFERENCE_NO')->toArray();
 
//         // ── Base: one row per reqnote (latest id), excluding tagged ────────
//         $baseQuery = EmailLog::select('*')
//             ->whereIn('id', function ($q) {
//                 $q->selectRaw('MAX(id)')
//                   ->from('emaillogs')
//                   ->groupBy('reqnote');
//             });
 
//         if (!empty($taggedReqnotes)) {
//             $baseQuery->whereNotIn('reqnote', $taggedReqnotes);
//         }
//          //dd(optional(EmailLog::orderBy('datetime', 'desc')->first())->toArray());
//         // ── Counts — always over the full unfiltered+untagged set ──────────
//         $allRecords = $baseQuery->get();
 
//         $counts = [
//             'all'    => $allRecords->count(),
//             '2days'  => 0,
//             '5days'  => 0,
//             '7days'  => 0,
//             '10days' => 0,
//             '15days' => 0,
//             '15plus' => 0,
//         ];
 
//         foreach ($allRecords as $r) {
//             if (!$r->datetime) continue;
//             $d = Carbon::parse($r->datetime)->diffInDays($today);
 
//             if     ($d <= 2)  $counts['2days']++;
//             elseif ($d <= 5)  $counts['5days']++;
//             elseif ($d <= 7)  $counts['7days']++;
//             elseif ($d <= 10) $counts['10days']++;
//             elseif ($d <= 15) $counts['15days']++;
//             else              $counts['15plus']++;
//         }
 
//         // ── Filtered query (time + dept + reins_party) ────────────────────
//         $query = EmailLog::select('*')
//             ->whereIn('id', function ($q) {
//                 $q->selectRaw('MAX(id)')
//                   ->from('emaillogs')
//                   ->groupBy('reqnote');
//             });
 
//         if (!empty($taggedReqnotes)) {
//             $query->whereNotIn('reqnote', $taggedReqnotes);
//         }
 
//         // Time filter
//         switch ($timeFilter) {
//             case '2days':
//                 $query->where('datetime', '>=', $today->copy()->subDays(2));
//                 break;
//             case '5days':
//                 $query->where('datetime', '>=', $today->copy()->subDays(5))
//                       ->where('datetime', '<',  $today->copy()->subDays(2));
//                 break;
//             case '7days':
//                 $query->where('datetime', '>=', $today->copy()->subDays(7))
//                       ->where('datetime', '<',  $today->copy()->subDays(5));
//                 break;
//             case '10days':
//                 $query->where('datetime', '>=', $today->copy()->subDays(10))
//                       ->where('datetime', '<',  $today->copy()->subDays(7));
//                 break;
//             case '15days':
//                 $query->where('datetime', '>=', $today->copy()->subDays(15))
//                       ->where('datetime', '<',  $today->copy()->subDays(10));
//                 break;
//             case '15plus':
//                 $query->where('datetime', '<', $today->copy()->subDays(15));
//                 break;
//         }
 
//         // Department filter — matches dept column prefix e.g. '11', '12'
//         if ($selectedCategory && isset($categoryMapping[$selectedCategory])) {
//             $deptCode = $categoryMapping[$selectedCategory];
//             $query->where('dept', 'like', $deptCode . '%');
//         }
 
//         // Reins Party filter — exact match
//         if ($selectedReins) {
//             $query->where('reins_party', $selectedReins);
//         }
 
//         $records = $query->get();
 
//         // ── Attach email_count, latest_sent_at, days_old ──────────────────
//         foreach ($records as $record) {
//             $record->email_count = EmailLog::where('reqnote', $record->reqnote)->count();
 
//             $latestEmail = EmailLog::where('reqnote', $record->reqnote)
//                             ->latest('datetime')
//                             ->first();
 
//             $record->latest_sent_at = $latestEmail ? $latestEmail->datetime : null;
//             $record->days_old       = $record->datetime
//                                         ? Carbon::parse($record->datetime)->diffInDays($today)
//                                         : null;
//         }
 
//         // ── Build unique reins party list for the dropdown ─────────────────
//         // Use the full untagged set so the dropdown always shows all options
//         $reinsPartyOptions = $allRecords
//             ->pluck('reins_party')
//             ->filter()
//             ->unique()
//             ->sort()
//             ->values();
 
//         return view('GetRequestReports.getshow', [
//             'records'              => $records,
//             'selected_time_filter' => $timeFilter,
//             'selected_department'  => $selectedCategory,
//             'selected_reins'       => $selectedReins,
//             'reins_party_options'  => $reinsPartyOptions,
//             'counts'               => $counts,
//         ]);
 
//     } catch (\Exception $e) {
//         Log::error('Error fetching email log data: ' . $e->getMessage());
//         return back()->with('error', 'An error occurred while fetching data.');
//     }
// }
// public function getshow(Request $request)
// {
//     try {
//         $timeFilter       = $request->query('time_filter',  'all');
//         $selectedCategory = $request->query('new_category', '');
//         $selectedReins    = $request->query('reins_party',  '');
//         $today            = Carbon::today();
 
//         $categoryMapping = [
//             'Fire'          => '11',
//             'Marine'        => '12',
//             'Motor'         => '13',
//             'Miscellaneous' => '14',
//             'Health'        => '16',
//         ];
 
//         // ── Exclude note-tagged records ───────────────────────────────────
//         $taggedReqnotes = \App\Models\ReqnoteMark::pluck('GRH_REFERENCE_NO')->toArray();
 
//         // ── Base query: one row per reqnote using MAX(id) ─────────────────
//         // MAX(id) gives the latest row, whose datetime = latest sent date.
//         // We separately fetch the MIN(id) row datetime = first sent date.
//         $baseQuery = EmailLog::select('*')
//             ->whereIn('id', function ($q) {
//                 $q->selectRaw('MAX(id)')
//                   ->from('emaillogs')
//                   ->groupBy('reqnote');
//             });
 
//         if (!empty($taggedReqnotes)) {
//             $baseQuery->whereNotIn('reqnote', $taggedReqnotes);
//         }
 
//         // ── Counts over full unfiltered+untagged set ──────────────────────
//         $allRecords = $baseQuery->get();
 
//         $counts = [
//             'all'    => $allRecords->count(),
//             '2days'  => 0,
//             '5days'  => 0,
//             '7days'  => 0,
//             '10days' => 0,
//             '15days' => 0,
//             '15plus' => 0,
//         ];
 
//         foreach ($allRecords as $r) {
//             if (!$r->datetime) continue;
//             $d = Carbon::parse($r->datetime)->diffInDays($today);
 
//             if     ($d <= 2)  $counts['2days']++;
//             elseif ($d <= 5)  $counts['5days']++;
//             elseif ($d <= 7)  $counts['7days']++;
//             elseif ($d <= 10) $counts['10days']++;
//             elseif ($d <= 15) $counts['15days']++;
//             else              $counts['15plus']++;
//         }
 
//         // ── Filtered query ────────────────────────────────────────────────
//         $query = EmailLog::select('*')
//             ->whereIn('id', function ($q) {
//                 $q->selectRaw('MAX(id)')
//                   ->from('emaillogs')
//                   ->groupBy('reqnote');
//             });
 
//         if (!empty($taggedReqnotes)) {
//             $query->whereNotIn('reqnote', $taggedReqnotes);
//         }
 
//         // Time filter
//         switch ($timeFilter) {
//             case '2days':
//                 $query->where('datetime', '>=', $today->copy()->subDays(2));
//                 break;
//             case '5days':
//                 $query->where('datetime', '>=', $today->copy()->subDays(5))
//                       ->where('datetime', '<',  $today->copy()->subDays(2));
//                 break;
//             case '7days':
//                 $query->where('datetime', '>=', $today->copy()->subDays(7))
//                       ->where('datetime', '<',  $today->copy()->subDays(5));
//                 break;
//             case '10days':
//                 $query->where('datetime', '>=', $today->copy()->subDays(10))
//                       ->where('datetime', '<',  $today->copy()->subDays(7));
//                 break;
//             case '15days':
//                 $query->where('datetime', '>=', $today->copy()->subDays(15))
//                       ->where('datetime', '<',  $today->copy()->subDays(10));
//                 break;
//             case '15plus':
//                 $query->where('datetime', '<', $today->copy()->subDays(15));
//                 break;
//         }
 
//         // Department filter
//         if ($selectedCategory && isset($categoryMapping[$selectedCategory])) {
//             $query->where('dept', 'like', $categoryMapping[$selectedCategory] . '%');
//         }
 
//         // Reins Party filter
//         if ($selectedReins) {
//             $query->where('reins_party', $selectedReins);
//         }
 
//         $records = $query->get();
 
//         // ── Attach per-record extra fields ────────────────────────────────
//         foreach ($records as $record) {
 
//             // Total email count for this reqnote
//             $record->email_count = EmailLog::where('reqnote', $record->reqnote)->count();
 
//             // first_sent_at  = datetime of the FIRST (oldest) email → MIN(id) row
//             $firstEmail = EmailLog::where('reqnote', $record->reqnote)
//                             ->oldest('datetime')   // ORDER BY datetime ASC
//                             ->first();
//             $record->first_sent_at = $firstEmail ? $firstEmail->datetime : null;
 
//             // latest_sent_at = datetime of the LATEST email → MAX(id) row
//             // The current record already came from MAX(id), so its datetime IS the latest.
//             // But we set it explicitly for clarity.
//             $latestEmail = EmailLog::where('reqnote', $record->reqnote)
//                             ->latest('datetime')   // ORDER BY datetime DESC
//                             ->first();
//             $record->latest_sent_at = $latestEmail ? $latestEmail->datetime : null;
 
//             $record->days_old = $record->first_sent_at
//                                     ? Carbon::parse($record->first_sent_at)->diffInDays($today)
//                                     : null;
//         }
 
//         // ── Reins Party options for dropdown ──────────────────────────────
//         $reinsPartyOptions = $allRecords
//             ->pluck('reins_party')
//             ->filter()
//             ->unique()
//             ->sort()
//             ->values();
 
//         return view('GetRequestReports.getshow', [
//             'records'              => $records,
//             'selected_time_filter' => $timeFilter,
//             'selected_department'  => $selectedCategory,
//             'selected_reins'       => $selectedReins,
//             'reins_party_options'  => $reinsPartyOptions,
//             'counts'               => $counts,
//         ]);
 
//     } catch (\Exception $e) {
//         Log::error('Error fetching email log data: ' . $e->getMessage());
//         return back()->with('error', 'An error occurred while fetching data.');
//     }
// }




// public function getshow(Request $request)
// {

//     $apiUrl = "http://172.16.22.204/dashboardApi/reins/rqn/req_reins_schd.php";

//         @file_get_contents($apiUrl, false, stream_context_create([
//             'http' => [
//                 'ignore_errors' => true,
//                 'timeout' => 120
//             ]
//         ]));
//    //  dd(Session::get('user'));
//     try {
//         $timeFilter       = $request->query('time_filter',  'all');
//         $selectedCategory = $request->query('new_category', '');   
//         $selectedReins    = $request->query('reins_party',  '');
//         $today            = Carbon::today();

//         // ── Exclude note-tagged records ───────────────────────────────────
//         $taggedReqnotes = \App\Models\ReqnoteMark::pluck('GRH_REFERENCE_NO')->toArray();

//         // ── Fetch all unique reqnote rows (latest per reqnote via MAX id) ─
//         $baseQuery = EmailLog::select('*')
//             ->whereIn('id', function ($q) {
//                 $q->selectRaw('MAX(id)')
//                   ->from('emaillogs')
//                   ->groupBy('reqnote');
//             });

//         if (!empty($taggedReqnotes)) {
//             $baseQuery->whereNotIn('reqnote', $taggedReqnotes);
//         }

//         $allRecords = $baseQuery->get();

//         // ── Counts (always over full untagged set, no dept/reins filter) ──
//         $counts = [
//             'all'    => $allRecords->count(),
//             '2days'  => 0,
//             '5days'  => 0,
//             '7days'  => 0,
//             '10days' => 0,
//             '15days' => 0,
//             '15plus' => 0,
//         ];

//         foreach ($allRecords as $r) {
//             if (!$r->datetime) continue;
//             $d = Carbon::parse($r->datetime)->diffInDays($today);

//             if     ($d <= 2)  $counts['2days']++;
//             elseif ($d <= 5)  $counts['5days']++;
//             elseif ($d <= 7)  $counts['7days']++;
//             elseif ($d <= 10) $counts['10days']++;
//             elseif ($d <= 15) $counts['15days']++;
//             else              $counts['15plus']++;
//         }

//         // ── Apply filters in-memory on the collection ─────────────────────
//         $records = $allRecords;

//         // Time filter
//         if ($timeFilter !== 'all') {
//             $records = $records->filter(function ($r) use ($timeFilter, $today) {
//                 if (!$r->datetime) return false;
//                 $daysOld = Carbon::parse($r->datetime)->diffInDays($today);

//                 switch ($timeFilter) {
//                     case '2days':  return $daysOld <= 2;
//                     case '5days':  return $daysOld > 2  && $daysOld <= 5;
//                     case '7days':  return $daysOld > 5  && $daysOld <= 7;
//                     case '10days': return $daysOld > 7  && $daysOld <= 10;
//                     case '15days': return $daysOld > 10 && $daysOld <= 15;
//                     case '15plus': return $daysOld > 15;
//                     default:       return true;
//                 }
//             });
//         }

//         // Department filter — direct text match against dept column
//         // emaillogs.dept stores text like "Fire", "Marine" directly
//         if ($selectedCategory) {
//             $records = $records->filter(function ($r) use ($selectedCategory) {
//                 return isset($r->dept)
//                     && strtolower(trim($r->dept)) === strtolower(trim($selectedCategory));
//             });
//         }

//         // Reins Party filter — direct text match
//         if ($selectedReins) {
//             $records = $records->filter(function ($r) use ($selectedReins) {
//                 return isset($r->reins_party)
//                     && strtolower(trim($r->reins_party)) === strtolower(trim($selectedReins));
//             });
//         }

//         // ── Attach per-record extra fields ────────────────────────────────
//         foreach ($records as $record) {
//             $record->email_count = EmailLog::where('reqnote', $record->reqnote)->count();

//             $firstEmail = EmailLog::where('reqnote', $record->reqnote)
//                             ->oldest('datetime')
//                             ->first();
//             $record->first_sent_at = $firstEmail ? $firstEmail->datetime : null;

//             $latestEmail = EmailLog::where('reqnote', $record->reqnote)
//                             ->latest('datetime')
//                             ->first();
//             $record->latest_sent_at = $latestEmail ? $latestEmail->datetime : null;

//             $record->days_old = $record->first_sent_at
//                                     ? Carbon::parse($record->first_sent_at)->diffInDays($today)
//                                     : null;
//         }

//         // ── Reins Party dropdown — from full untagged set ─────────────────
//         $reinsPartyOptions = $allRecords
//             ->pluck('reins_party')
//             ->filter()
//             ->unique()
//             ->sort()
//             ->values();

//         return view('GetRequestReports.getshow', [
//             'records'              => $records,
//             'selected_time_filter' => $timeFilter,
//             'selected_department'  => $selectedCategory,
//             'selected_reins'       => $selectedReins,
//             'reins_party_options'  => $reinsPartyOptions,
//             'counts'               => $counts,
//         ]);

//     } catch (\Exception $e) {
//         Log::error('Error fetching email log data: ' . $e->getMessage());
//         return back()->with('error', 'An error occurred while fetching data.');
//     }
// } s
   
    public function getEmailLogs(Request $request)
    {
        $request->validate([
            'reqnote' => 'required|string',
        ]);

        try {
            // Fetch all email logs for the given reqnote, ordered by datetime
            $logs = EmailLog::where('reqnote', $request->reqnote)
                ->orderBy('datetime', 'desc') // Use datetime for ordering
                ->get();

            return response()->json([
                'success' => true,
                'logs' => $logs->map(function ($log) {
                    return [
                        'datetime' => $log->datetime ?? 'N/A', 
                        'sent_to' => $log->sent_to,
                        'sent_cc' => $log->sent_cc ?? 'N/A',
                        'subject' => $log->subject,
                    ];
                })->toArray()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching email logs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch email logs: ' . $e->getMessage()
            ], 500);
        }
    }



// public function getlast(Request $request)
// {
//     // Get dates from request or set defaults
//     $startDate = $request->input('start_date') ?? Carbon::now()->startOfYear()->format('Y-m-d');
//     $endDate = $request->input('end_date') ?? Carbon::now()->format('Y-m-d');

//     // Get data from helper
//     $result = Helper::getReinsuranceLastReport($startDate, $endDate);
//     $data = collect($result['data'] ?? []);

//     // Filter out verified records
//     $verifiedReferenceNos = VerifyLog::pluck('GCP_DOC_REFERENCENO')->toArray();
//     $data = $data->filter(function ($record) use ($verifiedReferenceNos) {
//         return !in_array($record->GCP_DOC_REFERENCENO ?? '', $verifiedReferenceNos);
//     });

//     // 5. Category filtering
//     $categoryMapping = [
//         'Fire' => 11,
//         'Marine' => 12,
//         'Motor' => 13,
//         'Miscellaneous' => 14,
//         'Health' => 16,
//     ];

//     if ($request->filled('new_category')) {
//         $selectedNewCategory = $request->new_category;
//         $allowedNewCode = $categoryMapping[$selectedNewCategory] ?? null;
    
//         if ($allowedNewCode !== null) {
//             $data = $data->filter(function ($item) use ($allowedNewCode) {
//     // Handle both arrays and objects
//     $deptCode = is_array($item) ? ($item['PDP_DEPT_CODE'] ?? null) : ($item->PDP_DEPT_CODE ?? null);
    
//     return $deptCode !== null && 
//            Str::startsWith((string) $deptCode, (string) $allowedNewCode);
// });
//         }
//     }

//     // Return view
//     return view('GetRequestReports.lastcase', [
//         'data' => $data,
//         'start_date' => $startDate,
//         'end_date' => $endDate,
//         'api_date_range' => [
//             'from' => Carbon::parse($startDate)->format('d-M-Y'),
//             'to' => Carbon::parse($endDate)->format('d-M-Y'),
//         ]
//     ]);
// }
//   public function verifyRecord(Request $request)
//     {
//         // Decode record if it’s a JSON string
//         $recordData = $request->input('record');
//         if (is_string($recordData)) {
//             $recordData = json_decode($recordData, true);
//             if (json_last_error() !== JSON_ERROR_NONE) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Invalid record format: JSON decode failed'
//                 ], 422);
//             }
//         }

//         // Validate the request
//         $request->merge(['record' => $recordData]); 
//         $request->validate([
//             'record' => 'required|array',
//             'record.referenceNo' => 'required|string',
//             'record.departmentCode' => 'nullable|string',
//             'record.serialNo' => 'nullable|string',
//             'record.issueDate' => 'nullable|string',
//             'record.commencementDate' => 'nullable|string',
//             'record.expiryDate' => 'nullable|string',
//             'record.reinsurer' => 'nullable|string',
//             'record.reissueDate' => 'nullable|string',
//             'record.recommendedDate' => 'nullable|string',
//             'record.reexpiryDate' => 'nullable|string',
//             'record.totalSi' => 'nullable|string',
//             'record.totalPremium' => 'nullable|string',
//             'record.reinsuranceSi' => 'nullable|string',
//             'record.reinsurancePremium' => 'nullable|string',
//             'record.commissionAmount' => 'nullable|string',
//             'record.postingTag' => 'nullable|string',
//             'record.cancellationTag' => 'nullable|string',
//             'record.postedBy' => 'nullable|string',
//             'record.theirReferenceNo' => 'nullable|string',
//             'file' => 'nullable|file|mimes:pdf|max:2048' // File is optional
//         ]);


//         //dd($request);
//         try {
//             $filePath = null;

//             // Handle file upload if present
//             if ($request->hasFile('file')) {
//                 $file = $request->file('file');
//                 $referenceNo = $recordData['referenceNo'] ?? 'unknown';
//                 $filename = 'reinsurance_' . $referenceNo . '_' . time() . '.' . $file->getClientOriginalExtension();
//                 $filePath = $file->storeAs('uploads/reinsurance', $filename, 'public');
//             }

//             // Map data to VerifyLog model fields
//             $verifyLog = VerifyLog::create([
//                 'GCP_DOC_REFERENCENO' => $recordData['referenceNo'] ?? null,
//                 'PDP_DEPT_CODE' => $recordData['departmentCode'] ?? null,
//                 'GCP_SERIALNO' => $recordData['serialNo'] ?? null,
//                 'GCP_ISSUEDATE' => $recordData['issueDate'] ?? null,
//                 'GCP_COMMDATE' => $recordData['commencementDate'] ?? null,
//                 'GCP_EXPIRYDATE' => $recordData['expiryDate'] ?? null,
//                 'GCP_REINSURER' => $recordData['reinsurer'] ?? null,
//                 'GCP_REISSUEDATE' => $recordData['reissueDate'] ?? null,
//                 'GCP_RECOMMDATE' => $recordData['recommendedDate'] ?? null,
//                 'GCP_REEXPIRYDATE' => $recordData['reexpiryDate'] ?? null,
//                 'GCP_COTOTALSI' => $recordData['totalSi'] ?? null,
//                 'GCP_COTOTALPREM' => $recordData['totalPremium'] ?? null,
//                 'GCP_REINSI' => $recordData['reinsuranceSi'] ?? null,
//                 'GCP_REINPREM' => $recordData['reinsurancePremium'] ?? null,
//                 'GCP_COMMAMOUNT' => $recordData['commissionAmount'] ?? null,
//                 'GCP_POSTINGTAG' => $recordData['postingTag'] ?? null,
//                 'GCP_CANCELLATIONTAG' => $recordData['cancellationTag'] ?? null,
//                 'GCP_POST_USER' => $recordData['postedBy'] ?? null,
//                 'GCT_THEIR_REF_NO' => $recordData['theirReferenceNo'] ?? null,
//                 'avatar' => $filePath, // Store file path if uploaded, else null
//                 'datetime' => Carbon::now()->format('Y-m-d H:i:s'),
//                 'sent_to' => null,
//                 'sent_cc' => null,
//                 'subject' => null,
//                 'body' => null,
//                 'rep_name' => null,
//                 'created_by' => auth()->user()->name ?? 'System',
//                 'updated_by' => auth()->user()->name ?? 'System'
//             ]);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Record verified and saved successfully'
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Failed to save record: ' . $e->getMessage()
//             ], 500);
//         }
// } 
public function getlast(Request $request)

{

    // Get dates from request or set defaults

    $startDate = $request->input('start_date') ?? Carbon::now()->startOfYear()->format('Y-m-d');

    $endDate = $request->input('end_date') ?? Carbon::now()->format('Y-m-d');

 

    // Get data from helper

    $result = Helper::getReinsuranceLastReport($startDate, $endDate);

    $data = collect($result['data'] ?? []);

 

    // Filter out verified records

    $verifiedReferenceNos = VerifyLog::pluck('GCP_DOC_REFERENCENO')->toArray();

    $data = $data->filter(function ($record) use ($verifiedReferenceNos) {

        return !in_array($record->GCP_DOC_REFERENCENO ?? '', $verifiedReferenceNos);

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

    // Handle both arrays and objects

    $deptCode = is_array($item) ? ($item['PDP_DEPT_CODE'] ?? null) : ($item->PDP_DEPT_CODE ?? null);

   

    return $deptCode !== null &&

           Str::startsWith((string) $deptCode, (string) $allowedNewCode);

});

        }

    }

 

    // Return view

    return view('GetRequestReports.lastcase', [

        'data' => $data,

        'start_date' => $startDate,

        'end_date' => $endDate,

        'api_date_range' => [

            'from' => Carbon::parse($startDate)->format('d-M-Y'),

            'to' => Carbon::parse($endDate)->format('d-M-Y'),

        ]

    ]);

}

public function verifyRecord(Request $request)
{
   // dd($request->all());
    // Decode record JSON string
    $recordData = $request->input('record');
    if (is_string($recordData)) {
        $recordData = json_decode($recordData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid record format: JSON decode failed',
            ], 422);
        }
    }
 
    $request->merge(['record' => $recordData]);
 
    $request->validate([
        'record'                     => 'required|array',
        'record.referenceNo'         => 'required|string',
        'record.departmentCode'      => 'nullable|string',
        'record.serialNo'            => 'nullable|string',
        'record.issueDate'           => 'nullable|string',
        'record.commencementDate'    => 'nullable|string',
        'record.expiryDate'          => 'nullable|string',
        'record.reinsurer'           => 'nullable|string',
        'record.reissueDate'         => 'nullable|string',
        'record.recommendedDate'     => 'nullable|string',
        'record.reexpiryDate'        => 'nullable|string',
        'record.totalSi'             => 'nullable|string',
        'record.totalPremium'        => 'nullable|string',
        'record.reinsuranceSi'       => 'nullable|string',
        'record.reinsurancePremium'  => 'nullable|string',
        'record.commissionAmount'    => 'nullable|string',
        'record.postingTag'          => 'nullable|string',
        'record.cancellationTag'     => 'nullable|string',
        'record.postedBy'            => 'nullable|string',
        'record.theirReferenceNo'    => 'nullable|string',
        // Multiple files — each max 10 MB, any common type
        'files'                      => 'nullable|array',
        'files.*'                    => 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip',
    ]);
 
    try {
        $referenceNo = $recordData['referenceNo'] ?? 'unknown';
        $filePaths   = [];
 
        // Handle multiple file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if (!$file->isValid()) continue;
 
                $filename  = 'reinsurance_' . $referenceNo . '_' . time() . '_' . $file->getClientOriginalName();
                $filePaths[] = $file->storeAs('uploads/reinsurance/' . $referenceNo, $filename, 'public');
            }
        }
 
        // Store comma-separated paths (TEXT column required — same as reqnote_mark)
        $filePathString = !empty($filePaths) ? implode(',', $filePaths) : null;
 
        VerifyLog::create([
            'GCP_DOC_REFERENCENO' => $recordData['referenceNo']        ?? null,
            'PDP_DEPT_CODE'       => $recordData['departmentCode']      ?? null,
            'GCP_SERIALNO'        => $recordData['serialNo']            ?? null,
            'GCP_ISSUEDATE'       => $recordData['issueDate']           ?? null,
            'GCP_COMMDATE'        => $recordData['commencementDate']    ?? null,
            'GCP_EXPIRYDATE'      => $recordData['expiryDate']          ?? null,
            'GCP_REINSURER'       => $recordData['reinsurer']           ?? null,
            'GCP_REISSUEDATE'     => $recordData['reissueDate']         ?? null,
            'GCP_RECOMMDATE'      => $recordData['recommendedDate']     ?? null,
            'GCP_REEXPIRYDATE'    => $recordData['reexpiryDate']        ?? null,
            'GCP_COTOTALSI'       => $recordData['totalSi']             ?? null,
            'GCP_COTOTALPREM'     => $recordData['totalPremium']        ?? null,
            'GCP_REINSI'          => $recordData['reinsuranceSi']       ?? null,
            'GCP_REINPREM'        => $recordData['reinsurancePremium']  ?? null,
            'GCP_COMMAMOUNT'      => $recordData['commissionAmount']    ?? null,
            'GCP_POSTINGTAG'      => $recordData['postingTag']          ?? null,
            'GCP_CANCELLATIONTAG' => $recordData['cancellationTag']     ?? null,
            'GCP_POST_USER'       => $recordData['postedBy']            ?? null,
            'GCT_THEIR_REF_NO'    => $recordData['theirReferenceNo']    ?? null,
            'avatar'              => $filePathString, 
            'datetime'            => Carbon::now()->format('Y-m-d H:i:s'),
            'sent_to'             => null,
            'sent_cc'             => null,
            'subject'             => null,
            'body'                => null,
            'rep_name'            => null,
            'verify_tag'          => 'N',
            'created_by'          => auth()->user()->name ?? 'System',
            'updated_by'          => auth()->user()->name ?? 'System',
        ]);
 
        return response()->json([
            'success' => true,
            'message' => 'Record verified and saved successfully.',
        ]);
 
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save record: ' . $e->getMessage(),
        ], 500);
    }
}


  

}

 



     
