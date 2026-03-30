<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;


class UserActiveController extends Controller

{
    
    // public function index(Request $request)
    // {
    //     // Retrieve user data
    //     $data = Helper::fetchUser();
    //     //dd($data );

    //     // Check for errors in data fetching
    //     if (isset($data['error'])) {
    //         return response()->json(['error' => $data['error']], 500);
    //     }

    //     // Convert data to a collection
    //     $data = collect($data);
    //     // Today's date
    //     $today = now()->toDateString();

    //     // Filter logins and logouts for today
    //     $loginsToday = $data->filter(function ($user) use ($today) {
    //         return isset($user['SUL_LOGINDATE']) && Carbon::createFromFormat('d-M-y h.i.s.u A', $user['SUL_LOGINDATE'])->toDateString() === $today;
    //     })->unique('SUS_NAME'); // Ensure unique logins

    //     $logoutsToday = $data->filter(function ($user) use ($today) {
    //         return isset($user['SUS_LASTLOGIN']) 
    //             && Carbon::createFromFormat('d-M-y', $user['SUS_LASTLOGIN'])->toDateString() === $today;
    //     })->unique('SUS_NAME');


    //     // Ensure unique logouts

    //      //dd($logoutsToday );
    //     // Counts
    //     $loginsTodayCount = $loginsToday->count();
    //     $logoutsTodayCount = $logoutsToday->count();
    //     //dd($logoutsTodayCount);
    //     //dd($logoutsTodayCount);

    //     // Extract all unique branch names (locations) before filtering
    //     $locations = $data->pluck('PLC_DESC')->unique()->values();

    //     // Filter users based on selected user status
    //     if ($request->has('user_status')) {
    //         $userStatus = $request->input('user_status');

    //         if ($userStatus === 'A') {
    //             $data = $data->filter(fn($user) => isset($user['SUS_ACTIVE']) && $user['SUS_ACTIVE'] === 'A');
    //         } elseif ($userStatus === 'I') {
    //             $data = $data->filter(fn($user) => isset($user['SUS_ACTIVE']) && $user['SUS_ACTIVE'] === 'I');
    //         }
    //     }

    //     // Branch Filtering
    //     if ($request->filled('location')) {
    //         $location = $request->input('location');
    //         $data = $data->filter(fn($user) => isset($user['PLC_DESC']) && $user['PLC_DESC'] === $location);
    //     }
        
    //     // Default to today's login timeframe if not specified
    //     $timeframe = $request->input('login_timeframe', 'today');
    //     $now = now();

    //     // Login Date Filtering
    //     $data = $data->filter(function ($user) use ($timeframe, $now) {
    //         $lastLogin = isset($user['SUS_LASTLOGIN']) ? \Carbon\Carbon::parse($user['SUS_LASTLOGIN']) : null;

    //         if (!$lastLogin) {
    //             return false;
    //         }

    //         switch ($timeframe) {
    //             case 'today':
    //                 return $lastLogin->toDateString() === $now->toDateString();
    //             case 'yesterday':
    //                 return $lastLogin->toDateString() === $now->copy()->subDay()->toDateString();
    //             case '2days':
    //                 return $lastLogin->toDateString() === $now->copy()->subDays(2)->toDateString();
    //             case '3days':
    //                 return $lastLogin->toDateString() === $now->copy()->subDays(3)->toDateString();
    //             case '2w':
    //                 return $lastLogin->greaterThanOrEqualTo($now->copy()->subWeeks(2)->startOfDay());
    //             case '3w':
    //                 return $lastLogin->greaterThanOrEqualTo($now->copy()->subWeeks(3)->startOfDay());
    //             case '1m':
    //                 return $lastLogin->greaterThanOrEqualTo($now->copy()->subMonth()->startOfDay());
    //             case 'more1m':
    //                 return $lastLogin->lessThan($now->copy()->subMonth()->startOfDay());
    //             default:
    //                 return true;
    //         }
    //     });

    //     // Count active and inactive users
    //     $activeCount = $data->where('SUS_ACTIVE', 'A')->unique('SUS_NAME')->count();
    //     $inactiveCount = $data->where('SUS_ACTIVE', 'I')->unique('SUS_NAME')->count();

    //     // Ensure SUS_NAME is unique for the full user list
    //     $data = $data->unique('SUS_NAME');

    //     // Count the total number of unique users after filtering
    //     $totalCount = $data->count();

    //     // Calculate branch-wise statistics (only for displayed users)
    //     $branchStats = $data->groupBy('PLC_DESC')->map(function ($branchUsers) {
    //         return [
    //             'total' => $branchUsers->count(),
    //             'active' => $branchUsers->where('SUS_ACTIVE', 'A')->count(),
    //             'inactive' => $branchUsers->where('SUS_ACTIVE', 'I')->count(),
    //             'users' => $branchUsers->sortBy('SUS_NAME')->values()
    //         ];
    //     })->sortByDesc('total');

    //     // Pass the results to the view
    //     return view('usersactive.index', compact(
    //         'data', 
    //         'totalCount', 
    //         'activeCount', 
    //         'inactiveCount', 
    //         'locations',
    //         'branchStats',
    //         'loginsTodayCount', 
    //         'logoutsTodayCount', 
    //         'loginsToday', 
    //         'logoutsToday'
    //     ));
    // } now changing the report - new report 
    
//    public function index(Request $request)
// {
//     // Retrieve user data
//     $data = Helper::fetchUser();

//     // Check for errors
//     if (isset($data['error'])) {
//         return response()->json(['error' => $data['error']], 500);
//     }

//     $data = collect($data);

//     // Get all unique locations (for filtering)
//     $locations = $data->pluck('PLC_DESC')->unique()->values();

//     // Filter users based on selected status (if provided)
//     if ($request->has('user_status')) {
//         $userStatus = $request->input('user_status');
//         if ($userStatus === 'A') {
//             $data = $data->where('SUS_ACTIVE', 'A');
//         } elseif ($userStatus === 'I') {
//             $data = $data->where('SUS_ACTIVE', 'I');
//         }
//     }

//     // Filter by location (if provided)
//     if ($request->filled('location')) {
//         $location = $request->input('location');
//         $data = $data->where('PLC_DESC', $location);
//     }

//     // Count ACTIVE & INACTIVE users (without unique())
//     $activeCount = $data->where('SUS_ACTIVE', 'A')->count();
//     $inactiveCount = $data->where('SUS_ACTIVE', 'I')->count();
//     $totalCount = $data->count(); // Should be 15,848

//     // Branch-wise statistics
//     $branchStats = $data->groupBy('PLC_DESC')->map(function ($branchUsers) {
//         return [
//             'total' => $branchUsers->count(),
//             'active' => $branchUsers->where('SUS_ACTIVE', 'A')->count(),
//             'inactive' => $branchUsers->where('SUS_ACTIVE', 'I')->count(),
//             'users' => $branchUsers->sortBy('SUS_NAME')->values()
//         ];
//     })->sortByDesc('total');

//     return view('usersactive.index', compact(
//         'data', 
//         'totalCount', 
//         'activeCount', 
//         'inactiveCount', 
//         'locations',
//         'branchStats'
//     ));
// } with full only shows active / inactive and total

    // public function getLoginsByDate(Request $request)
    // {
    //     $date = $request->input('date');
    //     $search = $request->input('search');

    //     // Fetch all login data from API
    //     $loginData = Helper::fetchUser();
        
    //     // Convert to collection for easier manipulation
    //     $logins = collect($loginData);

    //     // Apply date filter if provided
    //     if ($date) {
    //         $filterDate = Carbon::createFromFormat('Y-m-d', $date);
    //         $logins = $logins->filter(function ($login) use ($filterDate) {
    //             try {
    //                 // Check if SUL_LOGINDATE exists, otherwise use SUS_LASTLOGIN
    //                 $dateField = $login['SUL_LOGINDATE'] ?? $login['SUS_LASTLOGIN'] ?? null;
    //                 if (!$dateField) return false;
                    
    //                 $cleanedDate = preg_replace('/\.\d+ /', ' ', $dateField);
    //                 $loginDate = Carbon::createFromFormat('d-M-y h.i.s A', $cleanedDate);
    //                 return $loginDate->isSameDay($filterDate);
    //             } catch (\Exception $e) {
    //                 return false;
    //             }
    //         });
    //     }

    //     // Apply search filter if provided
    //     if ($search) {
    //         $search = strtolower($search);
    //         $logins = $logins->filter(function ($login) use ($search) {
    //             return str_contains(strtolower($login['SUS_NAME'] ?? ''), $search) ||
    //                 str_contains(strtolower($login['SUS_USERCODE'] ?? ''), $search) ||
    //                 str_contains(strtolower($login['PLC_DESC'] ?? ''), $search);
    //         });
    //     }

    //     return response()->json(['data' => $logins->values()->all()]);
    // }
    public function getLoginsByDate(Request $request)
    {
        $date = $request->input('date') ?? now()->format('Y-m-d');
        $search = $request->input('search');

        // Fetch all login data from API
        $loginData = Helper::fetchUser();
        
        // Convert to collection for easier manipulation
        $logins = collect($loginData);

        // Apply date filter for today's logins
        $filterDate = Carbon::createFromFormat('Y-m-d', $date);
        $logins = $logins->filter(function ($login) use ($filterDate) {
            try {
                // Check if SUL_LOGINDATE exists, otherwise use SUS_LASTLOGIN
                $dateField = $login['SUL_LOGINDATE'] ?? $login['SUS_LASTLOGIN'] ?? null;
                if (!$dateField) return false;
                
                $cleanedDate = preg_replace('/\.\d+ /', ' ', $dateField);
                $loginDate = Carbon::createFromFormat('d-M-y h.i.s A', $cleanedDate);
                return $loginDate->isSameDay($filterDate);
            } catch (\Exception $e) {
                return false;
            }
        });

        // Apply search filter if provided
        if ($search) {
            $search = strtolower($search);
            $logins = $logins->filter(function ($login) use ($search) {
                return str_contains(strtolower($login['SUS_NAME'] ?? ''), $search) ||
                    str_contains(strtolower($login['SUS_USERCODE'] ?? ''), $search) ||
                    str_contains(strtolower($login['PLC_DESC'] ?? ''), $search);
            });
        }

        // Ensure unique users based on SUS_NAME
        $uniqueLogins = $logins->unique('SUS_NAME');

        return response()->json(['data' => $uniqueLogins->values()->all()]);
    }


public function getLogoutsByDate(Request $request)
{
    $date = $request->input('date') ?? now()->format('Y-m-d');
    $search = $request->input('search');

    // Fetch all user data (adjust this to your actual data source)
    $logoutData = Helper::fetchUser();
    $logouts = collect($logoutData);

    // Ensure unique users by SUS_NAME
    $logouts = $logouts->unique('SUS_NAME');

    // Apply date filter if provided
    $filterDate = Carbon::createFromFormat('Y-m-d', $date);
    $logouts = $logouts->filter(function ($logout) use ($filterDate) {
        try {
            $dateField = $logout['SUS_LASTLOGIN'] ?? null;
            if (!$dateField) return false;

            $logoutDate = Carbon::createFromFormat('d-M-y', $dateField);
            return $logoutDate->isSameDay($filterDate);
        } catch (\Exception $e) {
            return false;
        }
    });

    // Apply search filter if provided
    if ($search) {
        $search = strtolower($search);
        $logouts = $logouts->filter(function ($logout) use ($search) {
            return str_contains(strtolower($logout['SUS_NAME'] ?? ''), $search) ||
                str_contains(strtolower($logout['SUS_USERCODE'] ?? ''), $search) ||
                str_contains(strtolower($logout['PLC_DESC'] ?? ''), $search);
        });
    }

    // Sort by the most recent logout date
    $logouts = $logouts->sortByDesc(function ($logout) {
        return Carbon::createFromFormat('d-M-y', $logout['SUS_LASTLOGIN'])->timestamp;
    });

    return response()->json(['data' => $logouts->values()->all()]);
}


// public function uio(Request $request)
// {
//     // Fetch API data
//     $data = Helper::fetcloginandout();

//     if (isset($data['error'])) {
//         return response()->json(['error' => $data['error']], 500);
//     }

//     // Flatten nested data if your API returns nested 'data' arrays
//     if (isset($data['data'][0]['data'])) {
//         $data = collect($data['data'][0]['data']);
//     } else {
//         $data = collect($data['data']);
//     }

//     $today = now()->toDateString();

//     // Filter logins today
//     $loginsToday = $data->filter(function ($user) use ($today) {
//         return isset($user['SAH_LOGINDATE']) &&
//                Carbon::createFromFormat('d-M-y', $user['SAH_LOGINDATE'])->toDateString() === $today;
//     })->unique('SAH_USERCODE');

//     // Filter logouts today
//     $logoutsToday = $data->filter(function ($user) use ($today) {
//         return isset($user['SAH_LOGOUTDATE']) &&
//                $user['SAH_LOGOUTDATE'] !== null &&
//                Carbon::createFromFormat('d-M-y', $user['SAH_LOGOUTDATE'])->toDateString() === $today;
//     })->unique('SAH_USERCODE');

//     $loginsTodayCount = $loginsToday->count();
//     $logoutsTodayCount = $logoutsToday->count();

//     // Total unique users
//     $totalCount = $data->unique('SAH_USERCODE')->count();

//     // Active users: SAH_MESSAGETYPE = 'Success'
//     $activeCount = $data->where('SAH_MESSAGETYPE', 'Success')->unique('SAH_USERCODE')->count();

//     // Logout/Inactive users: all others
//     $inactiveCount = $data->where('SAH_MESSAGETYPE', '!=', 'Success')->unique('SAH_USERCODE')->count();

//     // Branch stats (using IP address as example branch)
//     $branchStats = $data->groupBy('SAH_IPADDRESS')->map(function ($group) {
//         $active = $group->where('SAH_MESSAGETYPE', 'Success')->count();
//         $inactive = $group->where('SAH_MESSAGETYPE', '!=', 'Success')->count();
//         return [
//             'total' => $group->count(),
//             'active' => $active,
//             'inactive' => $inactive,
//             'users' => $group->sortBy('SAH_USERCODE')->values()
//         ];
//     })->sortByDesc('total');

//     return view('usersactive.uio', compact(
//         'data',
//         'totalCount',
//         'activeCount',
//         'inactiveCount',
//         'loginsTodayCount',
//         'logoutsTodayCount',
//         'loginsToday',
//         'logoutsToday',
//         'branchStats'
//     ));
// }
public function uio(Request $request)
{
    $dateFrom   = $request->input('start_date', now()->format('Y-m-d'));
    $dateTo     = $request->input('end_date', now()->format('Y-m-d'));
    $userStatus = $request->input('user_status');
    $branch     = $request->input('branch');

    // Fetch raw data from your API/helper
    $rawData = Helper::fetcloginandout($dateFrom, $dateTo);

    if (isset($rawData['error'])) {
        return response()->json(['error' => $rawData['error']], 500);
    }

    $data = isset($rawData['data'][0]['data'])
        ? collect($rawData['data'][0]['data'])
        : collect($rawData['data']);

    $data = $data->unique()->values();

    // -------------------------------------------------------
    // 1. FILTER BY USER STATUS (A = Active, I = Inactive)
    // -------------------------------------------------------
    if ($userStatus === 'A') {
        // Active = successful login AND still no logout
        $data = $data->filter(function ($item) {
            return $item['SAH_MESSAGETYPE'] === 'Success' && is_null($item['SAH_LOGOUTDATE']);
        });
    } elseif ($userStatus === 'I') {
        // Inactive = has logged out (has SAH_LOGOUTDATE)
        $data = $data->whereNotNull('SAH_LOGOUTDATE');
    }

    // -------------------------------------------------------
    // 2. FILTER BY BRANCH
    // -------------------------------------------------------
    if (!empty($branch)) {
        $data = $data->where('PLC_DESC', $branch);
    }

    // -------------------------------------------------------
    // 3. GROUP BY USER + DATE → BUILD CORRECT ROW FOR MAIN TABLE
    // -------------------------------------------------------
    $grouped = $data->filter(function ($item) {
    return !empty($item['SAH_LOGINDATE']);
    })
    ->groupBy(function ($item) {
        return $item['SAH_USERCODE'] . '_' . substr($item['SAH_LOGINDATE'], 0, 10);
    })
    ->map(function ($items) {
        $userCode = $items->first()['SAH_USERCODE'];
        $branch = $items->first()['PLC_DESC'] ?? 'N/A';

        // Get all login attempts (both success and failed)
        $allLogins = $items->sortByDesc('SAH_LOGINDATE');

        // Get only successful logins
        $successfulLogins = $items->where('SAH_MESSAGETYPE', 'Success');

        // CRITICAL: Only show user if they have AT LEAST ONE successful login today
        if ($successfulLogins->isEmpty()) {
            return null; // This skips the user entirely
        }

        // Now we know there is at least one success → show the user
        $latestSuccessfulLogin = $successfulLogins->sortByDesc('SAH_LOGINDATE')->first();

        // Get logout from the record that has logout (could be any, but usually the successful one)
        $logoutRecord = $items->whereNotNull('SAH_LOGOUTDATE')
                            ->sortByDesc('SAH_LOGOUTDATE')
                            ->first();

        $logoutDate = $logoutRecord['SAH_LOGOUTDATE'] ?? null;

        return [
            'SAH_USERCODE' => $userCode,
            'PLC_DESC' => $branch,
            'SAH_MESSAGETYPE' => 'Success', 
            'SAH_LOGINS' => $allLogins->values()->toArray(), 
            'SAH_LOGOUTDATE' => $logoutDate,
            'SAH_IPADDRESS' => $latestSuccessfulLogin['SAH_IPADDRESS'] ?? null,
            'SAH_MESSAGE' => $latestSuccessfulLogin['SAH_MESSAGE'] ?? null,
            'SAH_LOGINDATE' => $latestSuccessfulLogin['SAH_LOGINDATE'] ?? null,
        ];
    })
    ->filter() 
    ->values();

    // -------------------------------------------------------
    // 4. BRANCH LIST FOR FILTER DROPDOWN
    // -------------------------------------------------------
    $branches = $data->pluck('PLC_DESC')->filter()->unique()->sort()->values();

    // -------------------------------------------------------
    // 5. BRANCH STATISTICS (Modal uses raw $data → already correct)
    // -------------------------------------------------------
    $branchStats = $data->groupBy('PLC_DESC')->map(function ($group) {
        $uniqueUsers = $group->pluck('SAH_USERCODE')->unique();

        $active = $group->filter(function ($item) {
            return $item['SAH_MESSAGETYPE'] === 'Success' && is_null($item['SAH_LOGOUTDATE']);
        })->pluck('SAH_USERCODE')->unique()->count();

        $inactive = $group->whereNotNull('SAH_LOGOUTDATE')
                          ->pluck('SAH_USERCODE')->unique()->count();

        return [
            'total'    => $uniqueUsers->count(),
            'active'   => $active,
            'inactive' => $inactive,
            'users'    => $group->sortBy('SAH_USERCODE')->values()->toArray(),
        ];
    });

    // -------------------------------------------------------
    // 6. DASHBOARD SUMMARY COUNTS
    // -------------------------------------------------------
    $totalCount    = $grouped->unique('SAH_USERCODE')->count();
    $activeCount   = $grouped->where('SAH_MESSAGETYPE', 'Success')
                             ->whereNull('SAH_LOGOUTDATE')
                             ->count();
    $inactiveCount = $grouped->whereNotNull('SAH_LOGOUTDATE')->count();

    // -------------------------------------------------------
    // RETURN TO VIEW
    // -------------------------------------------------------
    return view('usersactive.uio', [
        'data'          => $grouped,
        'branches'      => $branches,
        'totalCount'    => $totalCount,
        'activeCount'   => $activeCount,
        'inactiveCount' => $inactiveCount,
        'branchStats'   => $branchStats,
    ]);
}




}
