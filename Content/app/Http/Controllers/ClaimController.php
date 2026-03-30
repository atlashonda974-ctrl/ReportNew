<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\BranchesList;
use App\Models\Feedback;
use App\Models\GlApproval;
use App\Models\EmailLog;
use App\Models\ClaimDoc;

use Illuminate\Support\Facades\DB;


use Illuminate\Support\Facades\Session;


use App\Helpers\ClaimHelper;
use Illuminate\Support\Facades\Log;

class ClaimController extends Controller
{
    public function index(Request $request)
    {
        // Debug: Log all incoming request data
        Log::info('Request Inputs:', $request->all());

        // 1. Get dates from request or set defaults (last 30 days)
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $formStartDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

        // 2. Define additional parameters from request or set defaults
        $branchCode = $request->input('location_category', 'All');

        // 3. Get branch code and takaful code based on selected branch
        $branch = $branchCode ?: 'All'; // Ensure branch is 'All' if null or empty
        $takaful = 'All';
        if ($branch !== 'All' && !empty($branchCode)) {
            $selectedBranch = BranchesList::where('fbracode', $branchCode)->first();
            if ($selectedBranch) {
                $takaful = $selectedBranch->fbratak;
            }
        }

        // 4. Map new_category to department code
        $dept = 'All';
        $categoryMapping = [
            'Fire' => 11,
            'Marine' => 12,
            'Motor' => 13,
            'Miscellaneous' => 14,
            'Health' => 16,
        ];
        if ($request->filled('new_category')) {
            $selectedCategory = $request->input('new_category');
            Log::info('Selected Category: ' . $selectedCategory);
            if (isset($categoryMapping[$selectedCategory])) {
                $dept = $categoryMapping[$selectedCategory];
                Log::info('Mapped Department Code: ' . $dept);
            } else {
                Log::warning('Invalid Category Selected: ' . $selectedCategory);
            }
        } else {
            Log::info('No Category Selected, Defaulting to dept=All');
        }

        // Debug: Log all parameters before API call
        Log::info('API Call Parameters:', [
            'start_date' => $formStartDate,
            'end_date' => $formEndDate,
            'dept' => $dept,
            'branch' => $branch,
            'takaful' => $takaful,
        ]);

        // 5. Get data from API using helper
        $result = ClaimHelper::getClaim($formStartDate, $formEndDate, $dept, $branch, $takaful);

        // 6. Check API response status
        if ($result['status'] === 'error') {
            Log::error('Claim API Error: ' . $result['message']);
            return view('claimregister.claimcase', [
                'data' => [],
                'start_date' => $formStartDate,
                'end_date' => $formEndDate,
                'selected_category' => $request->input('new_category', ''),
                'branches' => BranchesList::all(),
                'error_message' => $result['message'],
            ]);
        }

        // 7. Handle API response data
        $data = collect($result['data'] ?? [])->map(function ($item) {
            if (is_string($item)) {
                $decoded = json_decode($item);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('JSON decode error: ' . json_last_error_msg() . ' for item: ' . $item);
                    return (object) [];
                }
                return $decoded;
            }
            return (object) $item;
        });

        // Debug: Log the number of records returned
        Log::info('API Returned ' . $data->count() . ' Records');

        // 8. Get all branches for dropdown
        $branches = BranchesList::all();

        // 9. Return view with data
        return view('claimregister.claimcase', [
            'data' => $data,
            'start_date' => $formStartDate,
            'end_date' => $formEndDate,
            'selected_category' => $request->input('new_category', ''),
            'branches' => $branches,
            'error_message' => null,
        ]);
    }

    public function claim2(Request $request)
{
    $emailLogs = EmailLog::all();
    //dd($emailLogs->last());
    //dd($emailLogs);
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $formStartDate = $startDate ?: Carbon::now()->subDays(60)->format('Y-m-d');
    $formEndDate = $endDate ?: Carbon::now()->format('Y-m-d');

    $branchCode = $request->input('location_category', 'All');
    $selectedCategory = $request->input('new_category', '');
    $timeFilter = $request->input('time_filter', 'all');

    $insuInput = $request->input('insu', ['D', 'O']);
    $insu = (is_array($insuInput) && !empty($insuInput)) ? implode(',', $insuInput) : 'All';

    $branch = $branchCode ?: 'All';
    $takaful = 'All';

    if ($branch !== 'All') {
        $selectedBranch = BranchesList::where('fbracode', $branchCode)->first();
        if ($selectedBranch) {
            $takaful = $selectedBranch->fbratak;
        }
    }

    $categoryMapping = [
        'Fire' => 11,
        'Marine' => 12,
        'Motor' => 13,
        'Miscellaneous' => 14,
        'Health' => 16,
    ];
    $dept = $categoryMapping[$selectedCategory] ?? 'All';

    $result = ClaimHelper::getClaimR2($formStartDate, $formEndDate, $dept, $branch, $takaful, $insu);

    $claimNos = collect($result['data'] ?? [])
        ->pluck('GIH_DOC_REF_NO')
        ->filter()
        ->map(fn($val) => trim($val))
        ->unique()
        ->values()
        ->toArray();

    
    $emailHistory = EmailLog::select('doc_num', 'datetime', 'created_by', 'sent_to', 'sent_cc', 'subject', 'body')
        ->whereNotNull('doc_num')
        ->where('doc_num', '!=', '')
        ->orderBy('datetime', 'desc')
        ->get()
        ->groupBy('doc_num')
        ->toArray();

    $branches = BranchesList::all();
    $branchMap = $branches->pluck('fbradsc', 'fbracode')->toArray();

    $data = collect($result['data'] ?? [])
        ->map(function ($item) use ($branchMap, $emailHistory) {
            $obj = is_string($item) ? json_decode($item) : (object) $item;
            
            $code = $obj->PLC_LOC_CODE ?? null;
            $obj->branch_description = $branchMap[$code] ?? ($obj->PLC_DESC ?? 'N/A');
            
            $claimNo = trim($obj->GIH_DOC_REF_NO ?? '');
            
            // Attach full email history for this claim
            $obj->email_history = $emailHistory[$claimNo] ?? [];
            $obj->email_send_count = count($obj->email_history);
            
            return $obj;
        });

    $filteredData = $data;

    if ($timeFilter !== 'all') {
        $filteredData = $data->filter(function ($item) use ($timeFilter) {
            if (empty($item->GIH_INTIMATIONDATE)) {
                return false;
            }

            $days = Carbon::parse($item->GIH_INTIMATIONDATE)->diffInDays(Carbon::now());

            if ($timeFilter == '2days') return $days <= 2;
            elseif ($timeFilter == '5days') return $days <= 5;
            elseif ($timeFilter == '7days') return $days <= 7;
            elseif ($timeFilter == '10days') return $days <= 10;
            elseif ($timeFilter == '15days') return $days <= 15;
            elseif ($timeFilter == '15plus') return $days > 15;

            return true;
        });
    }

    $counts = [
        'all' => $data->count(),
        '2days' => $data->filter(fn($i) => !empty($i->GIH_INTIMATIONDATE) && Carbon::parse($i->GIH_INTIMATIONDATE)->diffInDays(now()) <= 2)->count(),
        '5days' => $data->filter(fn($i) => !empty($i->GIH_INTIMATIONDATE) && Carbon::parse($i->GIH_INTIMATIONDATE)->diffInDays(now()) <= 5)->count(),
        '7days' => $data->filter(fn($i) => !empty($i->GIH_INTIMATIONDATE) && Carbon::parse($i->GIH_INTIMATIONDATE)->diffInDays(now()) <= 7)->count(),
        '10days' => $data->filter(fn($i) => !empty($i->GIH_INTIMATIONDATE) && Carbon::parse($i->GIH_INTIMATIONDATE)->diffInDays(now()) <= 10)->count(),
        '15days' => $data->filter(fn($i) => !empty($i->GIH_INTIMATIONDATE) && Carbon::parse($i->GIH_INTIMATIONDATE)->diffInDays(now()) <= 15)->count(),
        '15plus' => $data->filter(fn($i) => !empty($i->GIH_INTIMATIONDATE) && Carbon::parse($i->GIH_INTIMATIONDATE)->diffInDays(now()) > 15)->count(),
    ];

    return view('claimregister.claimr2', [
        'data' => $filteredData,
        'start_date' => $formStartDate,
        'end_date' => $formEndDate,
        'selected_category' => $selectedCategory,
        'branches' => $branches,
        'selected_time_filter' => $timeFilter,
        'counts' => $counts,
    ]);
}
   

   public function claim3(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $formStartDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');
        $branchCode = $request->input('location_category', 'All');
        $selectedCategory = $request->input('new_category', '');
        $timeFilter = $request->input('time_filter', 'all');

        $insuInput = $request->input('insu', []);
        $insu = is_array($insuInput) && !empty($insuInput) ? $insuInput : ['All'];

        $branch = $branchCode ?: 'All';
        $takaful = 'All';
        
        if ($branch !== 'All' && !empty($branchCode)) {
            $selectedBranch = BranchesList::where('fbracode', $branchCode)->first();
            if ($selectedBranch) {
                $takaful = $selectedBranch->fbratak;
            }
        }

        $dept = 'All';
        $categoryMapping = [
            'Fire' => 11,
            'Marine' => 12,
            'Motor' => 13,
            'Miscellaneous' => 14,
            'Health' => 16,
        ];

        if (isset($categoryMapping[$selectedCategory])) {
            $dept = $categoryMapping[$selectedCategory];
        }

        $result = ClaimHelper::getClaimR3($formStartDate, $formEndDate, $dept, $branch, $takaful, $insu);

        $data = collect($result['data'] ?? [])->map(function ($item) {
            return is_string($item) ? json_decode($item) : (object) $item;
        });

        $filteredData = $data;
        if ($timeFilter !== 'all') {
            $filteredData = $data->filter(function ($item) use ($timeFilter) {
                $days = Carbon::parse($item->GUD_APPOINTMENTDATE)->diffInDays(Carbon::now());
                
                switch ($timeFilter) {
                    case '2days': return $days <= 2;
                    case '5days': return $days <= 5;
                    case '7days': return $days <= 7;
                    case '10days': return $days <= 10;
                    case '15days': return $days <= 15;
                    case '15plus': return $days > 15;
                    default: return true;
                }
            });
        }

        $counts = [
            'all' => $data->count(),
            '2days' => $data->filter(function ($item) {
                return Carbon::parse($item->GUD_APPOINTMENTDATE)->diffInDays(Carbon::now()) <= 2;
            })->count(),
            '5days' => $data->filter(function ($item) {
                return Carbon::parse($item->GUD_APPOINTMENTDATE)->diffInDays(Carbon::now()) <= 5;
            })->count(),
            '7days' => $data->filter(function ($item) {
                return Carbon::parse($item->GUD_APPOINTMENTDATE)->diffInDays(Carbon::now()) <= 7;
            })->count(),
            '10days' => $data->filter(function ($item) {
                return Carbon::parse($item->GUD_APPOINTMENTDATE)->diffInDays(Carbon::now()) <= 10;
            })->count(),
            '15days' => $data->filter(function ($item) {
                return Carbon::parse($item->GUD_APPOINTMENTDATE)->diffInDays(Carbon::now()) <= 15;
            })->count(),
            '15plus' => $data->filter(function ($item) {
                return Carbon::parse($item->GUD_APPOINTMENTDATE)->diffInDays(Carbon::now()) > 15;
            })->count(),
        ];

        $branches = BranchesList::all();

        return view('claimregister.claimr3', [
            'data' => $filteredData,
            'start_date' => $formStartDate,
            'end_date' => $formEndDate,
            'selected_category' => $selectedCategory,
            'branches' => $branches,
            'error_message' => null,
            'selected_time_filter' => $timeFilter,
            'counts' => $counts,
        ]);
    }
    
public function claim4(Request $request)
{
   //$result = ClaimDoc::all();
     //$result = ClaimHelper::getClaimR4();
    //dd($result );
    $startDate = $request->input('start_date');
    $endDate   = $request->input('end_date');

    $formStartDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
    $formEndDate   = $endDate ?? Carbon::now()->format('Y-m-d');

    $branchCode       = $request->input('location_category', 'All');
    $selectedCategory = $request->input('new_category', '');
    $timeFilter       = $request->input('time_filter', 'all');

    $insuInput = $request->input('insu', []);
    $insu = is_array($insuInput) && !empty($insuInput)
        ? implode(',', $insuInput)
        : 'All';

    $branch  = $branchCode ?: 'All';
    $takaful = 'All';

    if ($branch !== 'All') {
        $selectedBranch = BranchesList::where('fbracode', $branch)->first();
        if ($selectedBranch) {
            $takaful = $selectedBranch->fbratak;
        }
    }

    $categoryMapping = [
        'Fire'           => 11,
        'Marine'         => 12,
        'Motor'          => 13,
        'Miscellaneous'  => 14,
        'Health'         => 16,
    ];

    $dept = isset($categoryMapping[$selectedCategory])
        ? $categoryMapping[$selectedCategory]
        : 'All';

    $result = ClaimHelper::getClaimR4(
        $formStartDate,
        $formEndDate,
        $dept,
        $branch,
        $takaful,
        $insu
    );
   
    $emailHistory = EmailLog::select('doc_num', 'repname', 'datetime', 'created_by', 'sent_to', 'sent_cc', 'subject', 'body')
        ->whereNotNull('doc_num')
        ->where('doc_num', '!=', '')
        ->whereNotNull('repname')
        ->where('repname', '!=', '')
        ->orderBy('datetime', 'desc')
        ->get()
        ->groupBy(function($item) {
            return $item->doc_num . '|||' . $item->repname;
        })
        ->toArray();

    $data = collect($result['data'] ?? [])->map(function ($item) use ($emailHistory) {
        $obj = is_string($item) ? json_decode($item) : (object) $item;
        
        $claimNo = trim($obj->GIH_DOC_REF_NO ?? '');
        
        $reportName = 'surveyor report';
        $emailKey = $claimNo . '|||' . $reportName;
        
        $obj->email_history = $emailHistory[$emailKey] ?? [];
        $obj->email_send_count = count($obj->email_history);
        $obj->report_name = $reportName; 
        
        return $obj;
    });

    $getDays = function ($item) {
        if (empty($item->GUD_REPORT_DATE)) {
            return null;
        }

        return Carbon::createFromFormat('d-M-y', $item->GUD_REPORT_DATE)
            ->diffInDays(Carbon::now());
    };
  
    $allClaimNos = $data->pluck('GIH_DOC_REF_NO')
        ->map(function ($v) {
            return trim($v ?: '');
        })
        ->filter()
        ->unique()
        ->values();

    $uploadedClaimNos = ClaimDoc::whereIn('doc_num', $allClaimNos)
        ->where('repname', 'surveyor report')
        ->distinct()
        ->pluck('doc_num')
        ->map(function ($v) {
            return trim($v);
        })
        ->toArray();

    $filteredData = $data->filter(function ($item) use ($uploadedClaimNos) {
        return !in_array(trim($item->GIH_DOC_REF_NO ?? ''), $uploadedClaimNos);
    });

   
    $getDays = function ($item) {
        if (empty($item->GUD_REPORT_DATE)) {
            return null;
        }
        return Carbon::createFromFormat('d-M-y', $item->GUD_REPORT_DATE)
            ->diffInDays(Carbon::now());
    };

    $counts = [
        'all' => $filteredData->count(),
        '2days' => $filteredData->filter(function ($i) use ($getDays) {
            $d = $getDays($i);
            return $d !== null && $d <= 2;
        })->count(),
        '5days' => $filteredData->filter(function ($i) use ($getDays) {
            $d = $getDays($i);
            return $d !== null && $d >= 3 && $d <= 5;
        })->count(),
        '7days' => $filteredData->filter(function ($i) use ($getDays) {
            $d = $getDays($i);
            return $d !== null && $d >= 6 && $d <= 7;
        })->count(),
        '10days' => $filteredData->filter(function ($i) use ($getDays) {
            $d = $getDays($i);
            return $d !== null && $d >= 8 && $d <= 10;
        })->count(),
        '15days' => $filteredData->filter(function ($i) use ($getDays) {
            $d = $getDays($i);
            return $d !== null && $d >= 11 && $d <= 15;
        })->count(),
        '15plus' => $filteredData->filter(function ($i) use ($getDays) {
            $d = $getDays($i);
            return $d !== null && $d > 15;
        })->count(),
    ];

    if ($timeFilter !== 'all') {
        $filteredData = $filteredData->filter(function ($item) use ($timeFilter, $getDays) {

            $days = $getDays($item);
            if ($days === null) {
                return false;
            }

            if ($timeFilter === '2days') {
                return $days <= 2;
            } elseif ($timeFilter === '5days') {
                return $days >= 3 && $days <= 5;
            } elseif ($timeFilter === '7days') {
                return $days >= 6 && $days <= 7;
            } elseif ($timeFilter === '10days') {
                return $days >= 8 && $days <= 10;
            } elseif ($timeFilter === '15days') {
                return $days >= 11 && $days <= 15;
            } elseif ($timeFilter === '15plus') {
                return $days > 15;
            }

            return true;
        });
    }

    return view('claimregister.claim4', [
        'data' => $filteredData,
        'start_date' => $formStartDate,
        'end_date' => $formEndDate,
        'selected_category' => $selectedCategory,
        'branches' => BranchesList::all(),
        'selected_time_filter' => $timeFilter,
        'counts' => $counts,
        'error_message' => null,
    ]);
}

public function uploadClaimDocument(Request $request)
{
    try {
        $validated = $request->validate([
            'claim_no'     => 'required|string|max:100',
            'report_name'  => 'required|string|max:100',
            'bank1'        => 'required|string|max:255',
            'bank2'        => 'nullable|string|max:255',
            'remarks'      => 'nullable|string|max:355',
            'file_name'    => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $claimNo    = trim($validated['claim_no']);
        $reportName = trim($validated['report_name']);

        $existingDoc = ClaimDoc::where('doc_num', $claimNo)
            ->where('repname', $reportName)
            ->first();

        if ($existingDoc) {
            return response()->json([
                'success' => false,
                'message' => 'A document with this claim number and report name already exists.'
            ], 422);
        }

        $startDate = Carbon::now()->subYear()->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');
        
        $result = ClaimHelper::getClaimR4($startDate, $endDate);

        if (!isset($result['status']) || $result['status'] !== 'success') {
            return response()->json([
                'success' => false,
                'message' => 'API Error: ' . ($result['message'] ?? 'Unknown error')
            ], 422);
        }

        if (!isset($result['data']) || empty($result['data'])) {
            return response()->json([
                'success' => false,
                'message' => 'No claims found in the date range'
            ], 422);
        }

        $foundClaim = null;
        foreach ($result['data'] as $item) {
            $claimData = is_string($item) ? json_decode($item, true) : $item;
            
            $apiClaimNo = $claimData['GIH_DOC_REF_NO'] ?? 
                         $claimData['doc_num'] ?? 
                         $claimData['claim_no'] ?? 
                         $claimData['DOC_REF'] ?? 
                         null;
            
            if ($apiClaimNo && trim($apiClaimNo) === $claimNo) {
                $foundClaim = $claimData;
                break;
            }
        }

        if (!$foundClaim) {
            return response()->json([
                'success' => false,
                'message' => 'Claim information not found for number: ' . $claimNo
            ], 422);
        }

        if (!isset($foundClaim['GSH_SETTLEMENTDATE']) || trim($foundClaim['GSH_SETTLEMENTDATE']) === '') {
            return response()->json([
                'success' => false,
                'message' => 'Document upload is NOT allowed because settlement date is not set.'
            ], 422);
        }

        if (!$request->hasFile('file_name')) {
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 422);
        }

        $file = $request->file('file_name');
        
        if (!$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'File upload failed: ' . $file->getErrorMessage()
            ], 422);
        }

        $originalName = $file->getClientOriginalName();

        try {
            $path = $file->storeAs(
                "claims/{$claimNo}",
                $originalName,
                'public'
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store file: ' . $e->getMessage()
            ], 500);
        }

    
        $amount = $foundClaim['GIH_AMOUNT'] ?? $foundClaim['AMOUNT'] ?? $foundClaim['GUD_AMOUNT'] ?? null;

    
        $dbData = [
            'repname'     => $reportName,
            'doc_num'     => $claimNo,
            'file_name'   => $originalName,
            'amount'      => $amount,
            'remarks'     => $validated['remarks'] ?? null, // This will save the user's remarks
            'bank1'       => $validated['bank1'],
            'bank2'       => $validated['bank2'] ?? null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];

       
        $dbData = array_filter($dbData, function($value) {
            return $value !== null && $value !== '';
        });

        try {
            $savedDoc = ClaimDoc::create($dbData);
            
        } catch (\Illuminate\Database\QueryException $e) {
            try {
                $savedDoc = ClaimDoc::updateOrCreate(
                    [
                        'doc_num'  => $claimNo,
                        'repname'  => $reportName,
                    ],
                    $dbData
                );
            } catch (\Exception $e2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'File and bank details saved successfully' . (!empty($validated['remarks']) ? ' with remarks.' : '.'),
            'data' => [
                'id' => $savedDoc->id,
                'claim_no' => $claimNo,
                'file_name' => $originalName,
                'remarks' => $validated['remarks'] ?? null
            ]
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

// public function uploadClaimDocument(Request $request)
// {
//     //dd($request->all());
//     $validated = $request->validate([
//         'claim_no'     => 'required|string|max:100',
//         'report_name'  => 'required|string|max:100',
//         'bank1'        => 'required|string|max:255',
//         'bank2'        => 'nullable|string|max:255',
//         'file_name'    => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
//     ]);

//     $claimNo    = trim($validated['claim_no']);
//     $reportName = trim($validated['report_name']);

//     $result = ClaimHelper::getClaimR4($claimNo);

//     if (!isset($result['data']) || empty($result['data'])) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Claim information not found.'
//         ], 422);
//     }

//     $claimData = json_decode($result['data'][0], true);

//     if (
//         !isset($claimData['GSH_SETTLEMENTDATE']) ||
//         trim($claimData['GSH_SETTLEMENTDATE']) === ''
//     ) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Document upload is NOT allowed because settlement date is not set.'
//         ], 422);
//     }

//     $file = $request->file('file_name');
//     $originalName = $file->getClientOriginalName();

//     $path = $file->storeAs(
//         "claims/{$claimNo}",
//         $originalName,
//         'public'
//     );

//     \App\Models\ClaimDoc::updateOrCreate(
//         [
//             'doc_num'  => $claimNo,
//             'repname'  => $reportName,
//         ],
//         [
//             'bank1'       => $validated['bank1'],
//             'bank2'       => $validated['bank2'] ?? null,
//             'file_name'   => $originalName,
//             'created_by'  => auth()->id() ?? null,
//             'updated_by'  => auth()->id() ?? null,
//             'created_at'  => now(),
//             'updated_at'  => now(),
//         ]
//     );

//     return response()->json([
//         'success' => true,
//         'message' => 'File and bank details saved successfully'
//     ]);
// }


  public function claim5(Request $request)
    {
        // $data = Feedback::all();
        // dd($data);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $formStartDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

        $branchCode = $request->input('location_category', 'All');
        $selectedCategory = $request->input('new_category', '');
        $timeFilter = $request->input('time_filter', 'all');
        
        $insuInput = $request->input('insu', []);
        $insu = is_array($insuInput) && !empty($insuInput) ? implode(',', $insuInput) : 'All';

        $branch = $branchCode ?: 'All';
        $takaful = 'All';
        
        if ($branch !== 'All' && !empty($branchCode)) {
            $selectedBranch = BranchesList::where('fbracode', $branchCode)->first();
            if ($selectedBranch) {
                $takaful = $selectedBranch->fbratak;
            }
        }

        $dept = 'All';
        $categoryMapping = [
            'Fire' => 11,
            'Marine' => 12,
            'Motor' => 13,
            'Miscellaneous' => 14,
            'Health' => 16,
        ];
        
        if (isset($categoryMapping[$selectedCategory])) {
            $dept = $categoryMapping[$selectedCategory];
        }

        $result = ClaimHelper::getClaimR5($formStartDate, $formEndDate, $dept, $branch, $takaful, $insu);

        $data = collect($result['data'] ?? [])->map(function ($item) {
            return is_string($item) ? json_decode($item) : (object) $item;
        });

        $completedDocs = Feedback::pluck('uw_doc')->toArray();
        $data = $data->filter(function ($record) use ($completedDocs) {
            $docReference = $record->GIH_DOC_REF_NO ?? '';
            $isExcluded = in_array($docReference, $completedDocs);
            Log::debug('Filtering record', [
                'GIH_DOC_REF_NO' => $docReference,
                'isExcluded' => $isExcluded
            ]);
            return !$isExcluded;
        });

        $filteredData = $data;
        if ($timeFilter !== 'all') {
            $filteredData = $data->filter(function ($item) use ($timeFilter) {
                $days = Carbon::parse($item->GSH_SETTLEMENTDATE)->diffInDays(Carbon::now());
                
                switch ($timeFilter) {
                    case '2days': return $days <= 2;
                    case '5days': return $days <= 5;
                    case '7days': return $days <= 7;
                    case '10days': return $days <= 10;
                    case '15days': return $days <= 15;
                    case '15plus': return $days > 15;
                    default: return true;
                }
            });
        }

        $counts = [
            'all' => $data->count(),
            '2days' => $data->filter(function ($item) {
                return Carbon::parse($item->GSH_SETTLEMENTDATE)->diffInDays(Carbon::now()) <= 2;
            })->count(),
            '5days' => $data->filter(function ($item) {
                return Carbon::parse($item->GSH_SETTLEMENTDATE)->diffInDays(Carbon::now()) <= 5;
            })->count(),
            '7days' => $data->filter(function ($item) {
                return Carbon::parse($item->GSH_SETTLEMENTDATE)->diffInDays(Carbon::now()) <= 7;
            })->count(),
            '10days' => $data->filter(function ($item) {
                return Carbon::parse($item->GSH_SETTLEMENTDATE)->diffInDays(Carbon::now()) <= 10;
            })->count(),
            '15days' => $data->filter(function ($item) {
                return Carbon::parse($item->GSH_SETTLEMENTDATE)->diffInDays(Carbon::now()) <= 15;
            })->count(),
            '15plus' => $data->filter(function ($item) {
                return Carbon::parse($item->GSH_SETTLEMENTDATE)->diffInDays(Carbon::now()) > 15;
            })->count(),
        ];

        $branches = BranchesList::all();

        return view('claimregister.claim5', [
            'data' => $filteredData,
            'start_date' => $formStartDate,
            'end_date' => $formEndDate,
            'selected_category' => $selectedCategory,
            'branches' => $branches,
            'error_message' => null,
            'selected_time_filter' => $timeFilter,
            'counts' => $counts,
        ]);
    }

    public function storeFeedback(Request $request)
    {
        $request->validate([
            'uw_doc' => 'required|string',
            'surv_prof' => 'nullable|in:1,2,3,4,5',
            'surv_resp' => 'nullable|in:1,2,3,4,5',
            'surv_acc' => 'nullable|in:1,2,3,4,5',
            'surv_overall' => 'nullable|in:1,2,3,4,5',
            'clt_req' => 'nullable|in:1,2,3,4,5',
            'clt_info' => 'nullable|in:1,2,3,4,5',
            'clt_coop' => 'nullable|in:1,2,3,4,5',
            'clt_overall' => 'nullable|in:1,2,3,4,5',
        ]);

        try {
            Feedback::create([
                'uw_doc' => $request->uw_doc,
                'surv_prof' => $request->surv_prof,
                'surv_resp' => $request->surv_resp,
                'surv_acc' => $request->surv_acc,
                'surv_overall' => $request->surv_overall,
                'clt_req' => $request->clt_req,
                'clt_info' => $request->clt_info,
                'clt_coop' => $request->clt_coop,
                'clt_overall' => $request->clt_overall,
                'created_by' => auth()->user()->name ?? 'System',
                'updated_by' => auth()->user()->name ?? 'System',
            ]);

            return response()->json(['success' => true, 'message' => 'Feedback saved successfully.']);
        } catch (\Exception $e) {
            Log::error('Feedback submission failed: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json(['success' => false, 'message' => 'Failed to save feedback: ' . $e->getMessage()], 500);
        }
    }

    public function completeAction(Request $request, $id)
    {
        try {
            $hasFeedback = Feedback::where('uw_doc', $id)->exists();
            if (!$hasFeedback) {
                return response()->json(['success' => false, 'message' => 'Please submit remarks first.'], 403);
            }

            return response()->json(['success' => true, 'message' => 'Action completed successfully.']);
        } catch (\Exception $e) {
            Log::error('Action completion failed: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['success' => false, 'message' => 'Failed to complete action: ' . $e->getMessage()], 500);
        }
    }

    public function claim6(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $formStartDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

        $branchCode = $request->input('location_category', 'All');
        $selectedCategory = $request->input('new_category', '');
        $timeFilter = $request->input('time_filter', 'all');
        $businessType = $request->input('business_type', 'all'); 
        
        $insuInput = $request->input('insu', []);
        $insu = is_array($insuInput) && !empty($insuInput) ? implode(',', $insuInput) : 'All';

        // Initialize branch and takaful variables
        $branch = 'All';
        $takaful = 'All';
        
        // Handle branch and takaful logic based on business type
        if ($branchCode !== 'All' && !empty($branchCode)) {
            $selectedBranch = BranchesList::where('fbracode', $branchCode)->first();
            
            if ($selectedBranch) {
                switch ($businessType) {
                    case 'takaful':
                        // For Takaful: use takaful code as branch, set branch to 0
                        $branch = 0;
                        $takaful = $selectedBranch->fbratak;
                        break;
                        
                    case 'conventional':
                        // For Conventional: use branch code, set takaful to 0
                        $branch = $selectedBranch->fbracode;
                        $takaful = 0;
                        break;
                        
                    case 'all':
                    default:
                        // For All: use both branch and takaful codes
                        $branch = $selectedBranch->fbracode;
                        $takaful = $selectedBranch->fbratak;
                        break;
                }
            }
        } else {
            // If no specific branch is selected, handle business type for 'All'
            if ($businessType === 'takaful') {
                $branch = 0;
                $takaful = 'All';
            } elseif ($businessType === 'conventional') {
                $branch = 'All';
                $takaful = 0;
            }
            // For 'all', both remain 'All'
        }

        $dept = 'All';
        $categoryMapping = [
            'Fire' => 11,
            'Marine' => 12,
            'Motor' => 13,
            'Miscellaneous' => 14,
            'Health' => 16,
        ];
        
        if (isset($categoryMapping[$selectedCategory])) {
            $dept = $categoryMapping[$selectedCategory];
        }

        $result = ClaimHelper::getClaimR6($formStartDate, $formEndDate, $dept, $branch, $takaful, $insu);

        $data = collect($result['data'] ?? [])->map(function ($item) {
            return is_string($item) ? json_decode($item) : (object) $item;
        });

        $filteredData = $data;
        if ($timeFilter !== 'all') {
            $filteredData = $data->filter(function ($item) use ($timeFilter) {
                $days = Carbon::parse($item->GIH_INTIMATIONDATE)->diffInDays(Carbon::now());
                
                switch ($timeFilter) {
                    case '2days': return $days <= 2;
                    case '5days': return $days <= 5;
                    case '7days': return $days <= 7;
                    case '10days': return $days <= 10;
                    case '15days': return $days <= 15;
                    case '15plus': return $days > 15;
                    default: return true;
                }
            });
        }

        $counts = [
            'all' => $data->count(),
            '2days' => $data->filter(function ($item) {
                return Carbon::parse($item->GIH_INTIMATIONDATE)->diffInDays(Carbon::now()) <= 2;
            })->count(),
            '5days' => $data->filter(function ($item) {
                return Carbon::parse($item->GIH_INTIMATIONDATE)->diffInDays(Carbon::now()) <= 5;
            })->count(),
            '7days' => $data->filter(function ($item) {
                return Carbon::parse($item->GIH_INTIMATIONDATE)->diffInDays(Carbon::now()) <= 7;
            })->count(),
            '10days' => $data->filter(function ($item) {
                return Carbon::parse($item->GIH_INTIMATIONDATE)->diffInDays(Carbon::now()) <= 10;
            })->count(),
            '15days' => $data->filter(function ($item) {
                return Carbon::parse($item->GIH_INTIMATIONDATE)->diffInDays(Carbon::now()) <= 15;
            })->count(),
            '15plus' => $data->filter(function ($item) {
                return Carbon::parse($item->GIH_INTIMATIONDATE)->diffInDays(Carbon::now()) > 15;
            })->count(),
        ];

        $branches = BranchesList::all();
        $userRole = Session::get('user')['role'] ?? null;

        return view('claimregister.claim6', [
            'data' => $filteredData,
            'start_date' => $formStartDate,
            'end_date' => $formEndDate,
            'selected_category' => $selectedCategory,
            'branches' => $branches,
            'error_message' => null,
            'selected_time_filter' => $timeFilter,
            'selected_business_type' => $businessType,
            'counts' => $counts,
            'userRole' => $userRole,
        ]);
    }

public function claim8(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $formStartDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
    $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

    $branchCode = $request->input('location_category', 'All');
    $selectedCategory = $request->input('new_category', '');
    $timeFilter = $request->input('time_filter', 'all');
    $claimFilter = $request->input('claim_filter', 'all'); // New filter for NO_CLM

    $insuInput = $request->input('insu', []);
    $insu = is_array($insuInput) && !empty($insuInput) ? implode(',', $insuInput) : 'All';

    $branch = $branchCode ?: 'All';
    $takaful = 'All';

    if ($branch !== 'All' && !empty($branchCode)) {
        $selectedBranch = BranchesList::where('fbracode', $branchCode)->first();
        if ($selectedBranch) {
            $takaful = $selectedBranch->fbratak;
        }
    }

    $dept = 'All';
    $categoryMapping = [
        'Fire' => 11,
        'Marine' => 12,
        'Motor' => 13,
        'Miscellaneous' => 14,
        'Health' => 16,
    ];

    if (isset($categoryMapping[$selectedCategory])) {
        $dept = $categoryMapping[$selectedCategory];
    }

    // Fetch claim data
    $result = ClaimHelper::getClaimR8($formStartDate, $formEndDate, $dept, $branch, $takaful, $insu);

    // Log the raw data
    Log::info('Raw Claim Data: ', $result['data'] ?? []);

    $data = collect($result['data'] ?? [])->map(function ($item) {
        return is_string($item) ? json_decode($item) : (object) $item;
    });

    // Ensure GPD_PAYEE_AMOUNT and NO_CLM exist and handle potential nulls
    $data = $data->filter(function ($item) {
        return isset($item->GPD_PAYEE_AMOUNT) && isset($item->NO_CLM);
    });

    // Apply amount-based filter (time_filter)
    $filteredData = $data;
    if ($timeFilter !== 'all') {
        $filteredData = $filteredData->filter(function ($item) use ($timeFilter) {
            $amount = (float) ($item->GPD_PAYEE_AMOUNT ?? 0);
            switch ($timeFilter) {
                case '0_100k': return $amount >= 0 && $amount <= 100000;
                case '100k_200k': return $amount > 100000 && $amount <= 200000;
                case '200k_500k': return $amount > 200000 && $amount <= 500000;
                case '500k_1m': return $amount > 500000 && $amount <= 1000000;
                case '1m_2m': return $amount > 1000000 && $amount <= 2000000;
                case '2m_5m': return $amount > 2000000 && $amount <= 5000000;
                case '5m_plus': return $amount > 5000000;
                default: return true;
            }
        });
    }

    // Apply claim-based filter (claim_filter)
    if ($claimFilter !== 'all') {
        $filteredData = $filteredData->filter(function ($item) use ($claimFilter) {
            $claims = (int) ($item->NO_CLM ?? 0);
            switch ($claimFilter) {
                case '0_5': return $claims >= 0 && $claims <= 5;
                case '6_10': return $claims > 5 && $claims <= 10;
                case '11_20': return $claims > 10 && $claims <= 20;
                case '21_25': return $claims > 20 && $claims <= 25;
                case '25_plus': return $claims > 25;
                default: return true;
            }
        });
    }

    // Count for amount ranges
    $amountCounts = [
        'all' => $data->count(),
        '0_100k' => $data->filter(fn($item) => (float) ($item->GPD_PAYEE_AMOUNT ?? 0) >= 0 && (float) ($item->GPD_PAYEE_AMOUNT ?? 0) <= 100000)->count(),
        '100k_200k' => $data->filter(fn($item) => (float) ($item->GPD_PAYEE_AMOUNT ?? 0) > 100000 && (float) ($item->GPD_PAYEE_AMOUNT ?? 0) <= 200000)->count(),
        '200k_500k' => $data->filter(fn($item) => (float) ($item->GPD_PAYEE_AMOUNT ?? 0) > 200000 && (float) ($item->GPD_PAYEE_AMOUNT ?? 0) <= 500000)->count(),
        '500k_1m' => $data->filter(fn($item) => (float) ($item->GPD_PAYEE_AMOUNT ?? 0) > 500000 && (float) ($item->GPD_PAYEE_AMOUNT ?? 0) <= 1000000)->count(),
        '1m_2m' => $data->filter(fn($item) => (float) ($item->GPD_PAYEE_AMOUNT ?? 0) > 1000000 && (float) ($item->GPD_PAYEE_AMOUNT ?? 0) <= 2000000)->count(),
        '2m_5m' => $data->filter(fn($item) => (float) ($item->GPD_PAYEE_AMOUNT ?? 0) > 2000000 && (float) ($item->GPD_PAYEE_AMOUNT ?? 0) <= 5000000)->count(),
        '5m_plus' => $data->filter(fn($item) => (float) ($item->GPD_PAYEE_AMOUNT ?? 0) > 5000000)->count(),
    ];

    // Count for claim ranges
    $claimCounts = [
        'all' => $data->count(),
        '0_5' => $data->filter(fn($item) => (int) ($item->NO_CLM ?? 0) >= 0 && (int) ($item->NO_CLM ?? 0) <= 5)->count(),
        '6_10' => $data->filter(fn($item) => (int) ($item->NO_CLM ?? 0) > 5 && (int) ($item->NO_CLM ?? 0) <= 10)->count(),
        '11_20' => $data->filter(fn($item) => (int) ($item->NO_CLM ?? 0) > 10 && (int) ($item->NO_CLM ?? 0) <= 20)->count(),
        '21_25' => $data->filter(fn($item) => (int) ($item->NO_CLM ?? 0) > 20 && (int) ($item->NO_CLM ?? 0) <= 25)->count(),
        '25_plus' => $data->filter(fn($item) => (int) ($item->NO_CLM ?? 0) > 25)->count(),
    ];

    $branches = BranchesList::all();

    return view('claimregister.claim8', [
        'data' => $filteredData,
        'start_date' => $formStartDate,
        'end_date' => $formEndDate,
        'selected_category' => $selectedCategory,
        'branches' => $branches,
        'error_message' => null,
        'selected_time_filter' => $timeFilter,
        'amount_counts' => $amountCounts, // Updated to avoid naming conflict
        'claim_counts' => $claimCounts, // New counts for claim filter
        'selected_claim_filter' => $claimFilter, // New filter selection
    ]);
}

public function claim9(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $formStartDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
    $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

    $branchCode = $request->input('location_category', 'All');
    $selectedCategory = $request->input('new_category', '');
    $timeFilter = $request->input('time_filter', 'all');
    
    $insuInput = $request->input('insu', []);
    $insu = is_array($insuInput) && !empty($insuInput) ? implode(',', $insuInput) : 'All';

    $branch = $branchCode ?: 'All';
    $takaful = 'All';
    
    if ($branch !== 'All' && !empty($branchCode)) {
        $selectedBranch = BranchesList::where('fbracode', $branchCode)->first();
        if ($selectedBranch) {
            $takaful = $selectedBranch->fbratak;
        }
    }

    $dept = 'All';
    $categoryMapping = [
        'Fire' => 11,
        'Marine' => 12,
        'Motor' => 13,
        'Miscellaneous' => 14,
        'Health' => 16,
    ];
    
    if (isset($categoryMapping[$selectedCategory])) {
        $dept = $categoryMapping[$selectedCategory];
    }

    $result = ClaimHelper::getClaimR9($formStartDate, $formEndDate, $dept, $branch, $takaful, $insu);
    
    $data = collect($result['data'] ?? [])->map(function ($item) {
        return is_string($item) ? json_decode($item) : (object) $item;
    });

    Log::info('Claim Data Amounts: ', $data->pluck('GPD_PAYEE_AMOUNT')->toArray());

    if ($timeFilter !== 'all') {
        $data = $data->filter(function ($item) use ($timeFilter) {
            $amount = (float) ($item->GPD_PAYEE_AMOUNT ?? 0);
            switch ($timeFilter) {
                case '0_100k': return $amount >= 0 && $amount <= 100000;
                case '100k_200k': return $amount > 100000 && $amount <= 200000;
                case '200k_500k': return $amount > 200000 && $amount <= 500000;
                case '500k_1m': return $amount > 500000 && $amount <= 1000000;
                case '1m_2m': return $amount > 1000000 && $amount <= 2000000;
                case '2m_5m': return $amount > 2000000 && $amount <= 5000000;
                case '5m_plus': return $amount > 5000000;
                default: return true;
            }
        });
    }

    $counts = [
        'all' => $data->count(),
        '0_100k' => $data->filter(fn($item) => (float) ($item->GPD_PAYEE_AMOUNT ?? 0) >= 0 && (float) ($item->GPD_PAYEE_AMOUNT ?? 0) <= 100000)->count(),
        '100k_200k' => $data->filter(fn($item) => (float) ($item->GPD_PAYEE_AMOUNT ?? 0) > 100000 && (float) ($item->GPD_PAYEE_AMOUNT ?? 0) <= 200000)->count(),
        '200k_500k' => $data->filter(fn($item) => (float) ($item->GPD_PAYEE_AMOUNT ?? 0) > 200000 && (float) ($item->GPD_PAYEE_AMOUNT ?? 0) <= 500000)->count(),
        '500k_1m' => $data->filter(fn($item) => (float) ($item->GPD_PAYEE_AMOUNT ?? 0) > 500000 && (float) ($item->GPD_PAYEE_AMOUNT ?? 0) <= 1000000)->count(),
        '1m_2m' => $data->filter(fn($item) => (float) ($item->GPD_PAYEE_AMOUNT ?? 0) > 1000000 && (float) ($item->GPD_PAYEE_AMOUNT ?? 0) <= 2000000)->count(),
        '2m_5m' => $data->filter(fn($item) => (float) ($item->GPD_PAYEE_AMOUNT ?? 0) > 2000000 && (float) ($item->GPD_PAYEE_AMOUNT ?? 0) <= 5000000)->count(),
        '5m_plus' => $data->filter(fn($item) => (float) ($item->GPD_PAYEE_AMOUNT ?? 0) > 5000000)->count(),
    ];

    $branches = BranchesList::all();

    return view('claimregister.claim9', [
        'data' => $data,
        'start_date' => $formStartDate,
        'end_date' => $formEndDate,
        'selected_category' => $selectedCategory,
        'branches' => $branches,
        'error_message' => null,
        'selected_time_filter' => $timeFilter, 
        'counts' => $counts, 
    ]);
}
    public function claim10(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $formStartDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
    $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

    $branchCode = $request->input('location_category', 'All');
    $selectedCategory = $request->input('new_category', '');
    $timeFilter = $request->input('time_filter', 'all');
    
    $insuInput = $request->input('insu', []);
    $insu = is_array($insuInput) && !empty($insuInput) ? implode(',', $insuInput) : 'All';

    $branch = $branchCode ?: 'All';
    $takaful = 'All';
    
    if ($branch !== 'All' && !empty($branchCode)) {
        $selectedBranch = BranchesList::where('fbracode', $branchCode)->first();
        if ($selectedBranch) {
            $takaful = $selectedBranch->fbratak;
        }
    }

    $dept = 'All';
    $categoryMapping = [
        'Fire' => 11,
        'Marine' => 12,
        'Motor' => 13,
        'Miscellaneous' => 14,
        'Health' => 16,
    ];
    
    if (isset($categoryMapping[$selectedCategory])) {
        $dept = $categoryMapping[$selectedCategory];
    }

    $result = ClaimHelper::getClaimR10($formStartDate, $formEndDate, $dept, $branch, $takaful, $insu);
    
    $data = collect($result['data'] ?? [])->map(function ($item) {
        return is_string($item) ? json_decode($item) : (object) $item;
    });
   // dd( $data );

    $branches = BranchesList::all();

    return view('claimregister.claim10', [
        'data' => $data,
        'start_date' => $formStartDate,
        'end_date' => $formEndDate,
        'selected_category' => $selectedCategory,
        'branches' => $branches,
        'error_message' => null,
    ]);
}

public function claim11(Request $request)
{
     $currentUser = Session::get('user')['name'];
   $dept = Session::get('user')['dept'];
   $zone = Session::get('user')['zone'];
    $user = GlApproval::all();

    $allowedDocs = GlApproval::where('approve', 'OK')
        ->where('created_by', '!=', $currentUser)
        ->pluck('doc')
        ->toArray();
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $formStartDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
    $formEndDate = $endDate ?? Carbon::now()->format('Y-m-d');

    // Branch & category filters
    $branchCode = $request->input('location_category', 'All');
    $selectedCategory = $request->input('new_category', '');
    $timeFilter = $request->input('time_filter', 'all');
    
    $insuInput = $request->input('insu', []);
    $insu = is_array($insuInput) && !empty($insuInput) ? implode(',', $insuInput) : 'All';

    $branch = $branchCode ?: 'All';
    $takaful = 'All';
    if ($branch !== 'All' && !empty($branchCode)) {
        $selectedBranch = BranchesList::where('fbracode', $branchCode)->first();
        if ($selectedBranch) {
            $takaful = $selectedBranch->fbratak;
        }
    }


    //  $dept = 'All';
    $categoryMapping = [
        'Fire' => 11,
        'Marine' => 12,
        'Motor' => 13,
        'Miscellaneous' => 14,
        'Health' => 16,
    ];
    if (isset($categoryMapping[$selectedCategory])) {
        $dept = $categoryMapping[$selectedCategory];
    }


    $result = ClaimHelper::getClaimR11($formStartDate, $formEndDate, $dept, $branch, $takaful, $insu, $zone);
    $data = collect($result['data'] ?? [])->map(function ($item) {
        return is_string($item) ? json_decode($item) : (object) $item;
    });
    
    

    // UPDATED: Exclude globally only fully 'approved' docs (permanent for everyone)
    $fullyApprovedDocs = GlApproval::where('approve', 'approved')->pluck('doc')->toArray();
    $data = $data->filter(function ($item) use ($fullyApprovedDocs) {
        $doc = $item->GSH_DOC_REF_NO ?? null;
        return $doc && !in_array($doc, $fullyApprovedDocs);
    });

    // ADDITIONAL: Exclude docs that the CURRENT USER has already approved (any status, including 'OK') - user-specific removal
    $userApprovedDocs = GlApproval::where('created_by', $currentUser)->pluck('doc')->toArray();
    $data = $data->filter(function ($item) use ($userApprovedDocs) {
        $doc = $item->GSH_DOC_REF_NO ?? null;
        return $doc && !in_array($doc, $userApprovedDocs);
    });

    

    if ($timeFilter !== 'all') {
        $data = $data->filter(function ($item) use ($timeFilter) {
            $days = Carbon::parse($item->GSH_SETTLEMENTDATE)->diffInDays(Carbon::now());
            switch ($timeFilter) {
                case '2days': return $days <= 2;
                case '5days': return $days <= 5;
                case '7days': return $days <= 7;
                case '10days': return $days <= 10;
                case '15days': return $days <= 15;
                case '15plus': return $days > 15;
                default: return true;
            }
        });
    }

    $branches = BranchesList::all();
    $u_name = $currentUser;
    $userLim = Session::get('user')['gl'];

    if ($userLim == '-1') {
        $filteredData = $data;
        
        $filteredData = $filteredData->map(function ($item) {
            $item->button_type = 'ok';
            $item->can_approve = true;
            return $item;
        });
    } else {
        $filteredData = $data->filter(function ($item) use ($allowedDocs) {
            $doc = $item->GSH_DOC_REF_NO ?? null;
            return $doc && in_array($doc, $allowedDocs);
        });

        $filteredData = $filteredData->map(function ($item) use ($currentUser, $userLim) {
            $approval = GlApproval::where('doc', $item->GSH_DOC_REF_NO)->first();

            if (!$approval) {
                $item->button_type = 'ok';
                $item->can_approve = true;
            } else {
                if ($approval->approve == 'OK' && $approval->created_by != $currentUser) {
                    $item->button_type = 'approve';
                    $item->can_approve = ($item->GIH_LOSSCLAIMED ?? 0) <= $userLim;
                } else {
                    $item->button_type = null;
                    $item->can_approve = false;
                }
            }
            return $item;
        });
    }
    
    $counts = [
        'all' => $filteredData->count(),
        '2days' => $filteredData->filter(fn($item) => Carbon::parse($item->GSH_SETTLEMENTDATE)->diffInDays(Carbon::now()) <= 2)->count(),
        '5days' => $filteredData->filter(fn($item) => Carbon::parse($item->GSH_SETTLEMENTDATE)->diffInDays(Carbon::now()) <= 5)->count(),
        '7days' => $filteredData->filter(fn($item) => Carbon::parse($item->GSH_SETTLEMENTDATE)->diffInDays(Carbon::now()) <= 7)->count(),
        '10days' => $filteredData->filter(fn($item) => Carbon::parse($item->GSH_SETTLEMENTDATE)->diffInDays(Carbon::now()) <= 10)->count(),
        '15days' => $filteredData->filter(fn($item) => Carbon::parse($item->GSH_SETTLEMENTDATE)->diffInDays(Carbon::now()) <= 15)->count(),
        '15plus' => $filteredData->filter(fn($item) => Carbon::parse($item->GSH_SETTLEMENTDATE)->diffInDays(Carbon::now()) > 15)->count(),
    ];
    
    return view('claimregister.claim11', [
        'data' => $filteredData,
        'start_date' => $formStartDate,
        'end_date' => $formEndDate,
        'selected_category' => $selectedCategory,
        'branches' => $branches,
        'error_message' => null,
        'selected_time_filter' => $timeFilter,
        'counts' => $counts,
        'created_by' => $u_name
    ]);
}



public function claim12(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate   = $request->input('end_date');
    $formStartDate    = $startDate   ?? Carbon::today()->format('Y-m-d');
    $formEndDate   = $endDate   ?? Carbon::today()->format('Y-m-d');

    $branchCode = $request->input('location_category', 'All');
    $selectedCategory = $request->input('new_category', '');

    $branch  = Session::get('user')['loc_code'] ?? 'All';
    $takaful = Session::get('user')['loc_code_tak'] ?? null;

    if ($branch !== 'All' && !empty($branchCode)) {
        $selectedBranch = BranchesList::where('fbracode', $branchCode)->first();
        if ($selectedBranch) {
            $takaful = $selectedBranch->fbratak;
        }
    }

    $dept = 'All';
    $categoryMapping = [
        'Fire' => 11,
        'Marine' => 12,
        'Motor' => 13,
        'Miscellaneous' => 14,
        'Health' => 16,
    ];

    if (isset($categoryMapping[$selectedCategory])) {
        $dept = $categoryMapping[$selectedCategory];
    }

    // Fetch data from API
    $result = ClaimHelper::getClaimR12($formStartDate, $formEndDate, $dept, $branch, $takaful);

    if (($result['status'] ?? '') !== 'success') {
        return view('claimregister.claim12', [
            'error_message' => $result['message'] ?? 'Failed to fetch data',
            'uw_collection' => collect([ (object) ['message' => 'No UW policy found'] ]),
            'claim' => collect(),
            'gl_exp' => collect(),
            'uw_count' => 0,
            'claim_count' => 0,
            'gl_exp_count' => 0,
            'start_date' => $formStartDate,
            'end_date'   => $formEndDate,
        ]);
    }

    $apiData = $result['data'] ?? [];

    // Prepare UW collection
    $uwData = $apiData['uw'] ?? [];
    if (empty($uwData) || (isset($uwData['Status']) && !empty($uwData['Status']))) {
        $uw_collection = collect([ (object) ['message' => $uwData['Status'] ?? 'No UW policy found'] ]);
    } else {
        $uw_collection = collect();
        foreach ($uwData as $type => $records) {
            $uw_collection = $uw_collection->merge(
                collect($records)->map(function ($item) use ($type) {
                    $itemArray = is_array($item) ? $item : (array) $item;
                    $itemArray['insurance_type_key'] = $type;
                    return (object) $itemArray;
                })
            );
        }
    }

    // Convert Claim and GL_EXP to object collections
    $claim  = collect($apiData['claim'] ?? [])->map(fn($item) => (object) $item);
    $gl_exp = collect($apiData['gl_exp'] ?? [])->map(fn($item) => (object) $item);

    return view('claimregister.claim12', [
        'uw_collection' => $uw_collection,
        'claim'         => $claim,
        'gl_exp'        => $gl_exp,
        'uw_count'      => $uw_collection->count(),
        'claim_count'   => $claim->count(),
        'gl_exp_count'  => $gl_exp->count(),
        'start_date'    => $formStartDate,
        'end_date'      => $formEndDate,
    ]);
}

public function insertApproval(Request $request)
{
    $doc       = $request->doc;
    $inRange   = $request->in_range;
    $remakrs   = $request->remakrs;
    $createdBy = Session::get('user')['name']; 
    $userLimit = Session::get('user')['gl'];

    // Check if user already has a record for this document
    $existingApproval = GlApproval::where('doc', $doc)
                                  ->where('created_by', $createdBy)
                                  ->first();

    // CASE 1: Only Remarks (in_range = 0 means it's just a remark, not an approval)
    if ($inRange == 0 && !empty($remakrs)) {
        if ($existingApproval) {
            // Update existing record's remarks
            $existingApproval->remakrs = $remakrs;
            $existingApproval->save();
        } else {
            // Create new record with only remarks (no approval status)
            GlApproval::create([
                'doc'        => $doc,
                'approve'    => null,  
                'in_range'   => 0,
                'remakrs'    => $remakrs,
                'created_by' => $createdBy,
                'updated_by' => null,
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Remarks saved successfully.'
        ]);
    }

    // CASE 2: Actual Approval (OK or Approve button)
    if ($existingApproval && $existingApproval->approve !== null) {
        // User already approved - block duplicate approval
        return response()->json([
            'status'  => false,
            'message' => 'You have already approved this document.'
        ], 403);
    }

    // Check if any approval exists (not just remarks)
    $anyApprovalExists = GlApproval::where('doc', $doc)
                                   ->whereNotNull('approve')
                                   ->exists();

    if (!$anyApprovalExists) {
        $status = 'OK'; 
    } else {
        // Check Limit
        if ($inRange > $userLimit) {
            return response()->json([
                'status'  => false,
                'message' => 'Amount exceeds your approval limit.'
            ], 403);
        }
        $status = 'approved';
    }

    if ($existingApproval) {
        // Update existing remarks-only record with approval
        $existingApproval->approve = $status;
        $existingApproval->in_range = $inRange;
        $existingApproval->save();
    } else {
        // Create new approval record
        GlApproval::create([
            'doc'        => $doc,
            'approve'    => $status,
            'in_range'   => $inRange,
            'remakrs'    => $remakrs ?? '',
            'created_by' => $createdBy,
            'updated_by' => null,
        ]);
    }

    return response()->json([
        'status'  => true,
        'approve' => $status,
        'message' => 'Approval recorded successfully.'
    ]);
}











}