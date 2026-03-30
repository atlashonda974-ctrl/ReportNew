<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Str;



class UserActiveController extends Controller

{
    // public function index(Request $request)
    // {
    //     // Retrieve user data
    //     $data = Helper::fetchUser();
        
    //     // Check for errors in data fetching
    //     if (isset($data['error'])) {
    //         return response()->json(['error' => $data['error']], 500);
    //     }
    
    //     // Convert data to a collection
    //     $data = collect($data);
    
    //     // Filter users based on selected user status
    //     if ($request->has('user_status')) {
    //         $userStatus = $request->input('user_status');
    
    //         if ($userStatus === 'A') {
    //             // Filter for active users only
    //             $data = $data->filter(function ($user) {
    //                 return isset($user['SUS_ACTIVE']) && $user['SUS_ACTIVE'] === 'A'; 
    //             });
    //         } elseif ($userStatus === 'I') {
    //             // Filter for inactive users only
    //             $data = $data->filter(function ($user) {
    //                 return isset($user['SUS_ACTIVE']) && $user['SUS_ACTIVE'] === 'I'; 
    //             });
    //         }
    //         // If no specific status is selected, return all users (both A and I)
    //     }
    
    //     // Count the total number of users after filtering
    //     $totalCount = $data->count();
    
    //     return view('usersactive.index', compact('data', 'totalCount'));
    // }
    // public function index(Request $request)
    // {
    //     // Retrieve user data
    //     $data = Helper::fetchUser();
        
    //     // Check for errors in data fetching
    //     if (isset($data['error'])) {
    //         return response()->json(['error' => $data['error']], 500);
    //     }
    
    //     // Convert data to a collection
    //     $data = collect($data);
    
    //     // Filter users based on selected user status
    //     if ($request->has('user_status')) {
    //         $userStatus = $request->input('user_status');
    
    //         if ($userStatus === 'A') {
    //             // Filter for active users only
    //             $data = $data->filter(function ($user) {
    //                 return isset($user['SUS_ACTIVE']) && $user['SUS_ACTIVE'] === 'A'; 
    //             });
    //         } elseif ($userStatus === 'I') {
    //             // Filter for inactive users only
    //             $data = $data->filter(function ($user) {
    //                 return isset($user['SUS_ACTIVE']) && $user['SUS_ACTIVE'] === 'I'; 
    //             });
    //         }
    //         // If no specific status is selected, return all users (both A and I)
    //     }
    
    //     // Ensure SUS_NAME is unique
    //     $data = $data->unique('SUS_NAME');
    
    //     // Count the total number of unique users after filtering
    //     $totalCount = $data->count();
    
    //     return view('usersactive.index', compact('data', 'totalCount'));
    // }
//     public function index(Request $request)
//     {
//         // Retrieve user data
//         $data = Helper::fetchUser();
        
//         // Check for errors in data fetching
//         if (isset($data['error'])) {
//             return response()->json(['error' => $data['error']], 500);
//         }
        
//         // Convert data to a collection
//         $data = collect($data);
        
//         // Filter users based on selected user status
//         if ($request->has('user_status')) {
//             $userStatus = $request->input('user_status');

//             if ($userStatus === 'A') {
//                 $data = $data->filter(function ($user) {
//                     return isset($user['SUS_ACTIVE']) && $user['SUS_ACTIVE'] === 'A'; 
//                 });
//             } elseif ($userStatus === 'I') {
//                 $data = $data->filter(function ($user) {
//                     return isset($user['SUS_ACTIVE']) && $user['SUS_ACTIVE'] === 'I'; 
//                 });
//             }
//         }

//         // Branch Filtering
//         if ($request->filled('location')) {
//             $location = $request->input('location');
//             $data = $data->filter(function ($user) use ($location) {
//                 return isset($user['PLC_DESC']) && $user['PLC_DESC'] === $location;
//             });
//         }

//         // Ensure SUS_NAME is unique
//         $data = $data->unique('SUS_NAME');

//         // Count the total number of unique users after filtering
//         $totalCount = $data->count();

//         // Extract unique branch names from the data
//         $locations = $data->pluck('PLC_DESC')->unique()->values();

//         // Group users by branch and count how many users per branch
// $branchUserCounts = $data->groupBy('PLC_DESC')->map(function ($group) {
//     return $group->count();
// });


//         return view('usersactive.index', compact('data', 'totalCount', 'locations','branchUserCounts'));
//     }
// public function index(Request $request)
// {
//     // Retrieve user data
//     $data = Helper::fetchUser();

//     // Check for errors in data fetching
//     if (isset($data['error'])) {
//         return response()->json(['error' => $data['error']], 500);
//     }

//     // Convert data to a collection
//     $data = collect($data);

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
    

//     if ($request->filled('login_timeframe')) {
//         $timeframe = $request->input('login_timeframe');
//         $now = now(); // Current date and time
    
//         $data = $data->filter(function ($user) use ($timeframe, $now) {
//             $lastLogin = isset($user['SUS_LASTLOGIN']) ? \Carbon\Carbon::parse($user['SUS_LASTLOGIN']) : null;
    
//             if (!$lastLogin) {
//                 return false; // Skip users without a login date
//             }
    
//             switch ($timeframe) {
//                 case 'today':
//                     return $lastLogin->isToday();
    
//                 case '2w':
//                     return $lastLogin->greaterThanOrEqualTo($now->copy()->subWeeks(2));
    
//                 case '3w':
//                     return $lastLogin->greaterThanOrEqualTo($now->copy()->subWeeks(3));
    
//                 case '1m':
//                     return $lastLogin->greaterThanOrEqualTo($now->copy()->subMonth());
    
//                 case 'more1m':
//                     return $lastLogin->lessThan($now->copy()->subMonth());
    
//                 default:
//                     return true; // No filtering
//             }
//         });
//     }
    
    

//     // Ensure SUS_NAME is unique
//     $data = $data->unique('SUS_NAME');

//     // Count the total number of unique users after filtering
//     $totalCount = $data->count();

//     // Extract unique branch names from the data
//     $locations = $data->pluck('PLC_DESC')->unique()->values();

//     // Pass the results to the view
//     return view('usersactive.index', compact('data', 'totalCount', 'locations'));
// }
// public function index(Request $request)
// {
//     // Retrieve user data
//     $data = Helper::fetchUser();

//     // Check for errors in data fetching
//     if (isset($data['error'])) {
//         return response()->json(['error' => $data['error']], 500);
//     }

//     // Convert data to a collection
//     $data = collect($data);

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

//     // Login Timeframe Filtering
//     if ($request->filled('login_timeframe')) {
//         $timeframe = $request->input('login_timeframe');
//         $now = now();
    
//         $data = $data->filter(function ($user) use ($timeframe, $now) {
//             $lastLogin = isset($user['SUS_LASTLOGIN']) ? \Carbon\Carbon::parse($user['SUS_LASTLOGIN']) : null;
    
//             if (!$lastLogin) {
//                 return false;
//             }
    
//             switch ($timeframe) {
//                 case 'today':
//                     return $lastLogin->isToday();
//                 case '2w':
//                     return $lastLogin->greaterThanOrEqualTo($now->copy()->subWeeks(2));
//                 case '3w':
//                     return $lastLogin->greaterThanOrEqualTo($now->copy()->subWeeks(3));
//                 case '1m':
//                     return $lastLogin->greaterThanOrEqualTo($now->copy()->subMonth());
//                 case 'more1m':
//                     return $lastLogin->lessThan($now->copy()->subMonth());
//                 default:
//                     return true;
//             }
//         });
//     }

//     // Count active and inactive users
//     $activeCount = $data->where('SUS_ACTIVE', 'A')->unique('SUS_NAME')->count();
//     $inactiveCount = $data->where('SUS_ACTIVE', 'I')->unique('SUS_NAME')->count();

//     // Ensure SUS_NAME is unique
//     $data = $data->unique('SUS_NAME');

//     // Count the total number of unique users after filtering
//     $totalCount = $data->count();

//     // Extract unique branch names from the data
//     $locations = $data->pluck('PLC_DESC')->unique()->values();

//     // Pass the results to the view
//     return view('usersactive.index', compact('data', 'totalCount', 'activeCount', 'inactiveCount', 'locations'));
// } previous
// public function index(Request $request)
// {
//     // Retrieve user data
//     $data = Helper::fetchUser();

//     // Check for errors in data fetching
//     if (isset($data['error'])) {
//         return response()->json(['error' => $data['error']], 500);
//     }

//     // Convert data to a collection
//     $data = collect($data);

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

//     // Login Timeframe Filtering
//     if ($request->filled('login_timeframe')) {
//         $timeframe = $request->input('login_timeframe');
//         $now = now();
    
//         $data = $data->filter(function ($user) use ($timeframe, $now) {
//             $lastLogin = isset($user['SUS_LASTLOGIN']) ? \Carbon\Carbon::parse($user['SUS_LASTLOGIN']) : null;
    
//             if (!$lastLogin) {
//                 return false;
//             }
    
//             switch ($timeframe) {
//                 case 'today':
//                     return $lastLogin->isToday();
//                 case '2w':
//                     return $lastLogin->greaterThanOrEqualTo($now->copy()->subWeeks(2));
//                 case '3w':
//                     return $lastLogin->greaterThanOrEqualTo($now->copy()->subWeeks(3));
//                 case '1m':
//                     return $lastLogin->greaterThanOrEqualTo($now->copy()->subMonth());
//                 case 'more1m':
//                     return $lastLogin->lessThan($now->copy()->subMonth());
//                 default:
//                     return true;
//             }
//         });
//     }

//     // Count active and inactive users
//     $activeCount = $data->where('SUS_ACTIVE', 'A')->unique('SUS_NAME')->count();
//     $inactiveCount = $data->where('SUS_ACTIVE', 'I')->unique('SUS_NAME')->count();

//     // Ensure SUS_NAME is unique
//     $data = $data->unique('SUS_NAME');

//     // Count the total number of unique users after filtering
//     $totalCount = $data->count();

//     // Extract unique branch names from the data
//     $locations = $data->pluck('PLC_DESC')->unique()->values();

//     // Calculate branch-wise statistics
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
//         'branchStats'
//     ));
// } 
// public function index(Request $request)
// {
//     // Retrieve user data
//     $data = Helper::fetchUser();

//     // Check for errors in data fetching
//     if (isset($data['error'])) {
//         return response()->json(['error' => $data['error']], 500);
//     }

//     // Convert data to a collection
//     $data = collect($data);

//     // Default to showing users active today
//     $defaultActiveTimeframe = 'today';

//     // Check for user status filter
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

//     // Login Timeframe Filtering
//     $timeframe = $request->filled('login_timeframe') ? $request->input('login_timeframe') : $defaultActiveTimeframe;
//     $now = now();

//     $data = $data->filter(function ($user) use ($timeframe, $now) {
//         $lastLogin = isset($user['SUS_LASTLOGIN']) ? \Carbon\Carbon::parse($user['SUS_LASTLOGIN']) : null;

//         if (!$lastLogin) {
//             return false;
//         }

//         switch ($timeframe) {
//             case 'today':
//                 return $lastLogin->isToday();
//             case '2w':
//                 return $lastLogin->greaterThanOrEqualTo($now->copy()->subWeeks(2));
//             case '3w':
//                 return $lastLogin->greaterThanOrEqualTo($now->copy()->subWeeks(3));
//             case '1m':
//                 return $lastLogin->greaterThanOrEqualTo($now->copy()->subMonth());
//             case 'more1m':
//                 return $lastLogin->lessThan($now->copy()->subMonth());
//             default:
//                 return true;
//         }
//     });

//     // Count active and inactive users
//     $activeCount = $data->where('SUS_ACTIVE', 'A')->unique('SUS_NAME')->count();
//     $inactiveCount = $data->where('SUS_ACTIVE', 'I')->unique('SUS_NAME')->count();

//     // Ensure SUS_NAME is unique
//     $data = $data->unique('SUS_NAME');

//     // Count the total number of unique users after filtering
//     $totalCount = $data->count();

//     // Extract unique branch names from the data
//     $locations = $data->pluck('PLC_DESC')->unique()->values();

//     // Calculate branch-wise statistics
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
//         'branchStats'
//     ));
// }
// public function index(Request $request)
// {
//     // Retrieve user data
//     $data = Helper::fetchUser();

//     // Check for errors in data fetching
//     if (isset($data['error'])) {
//         return response()->json(['error' => $data['error']], 500);
//     }

//     // Convert data to a collection
//     $data = collect($data);

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
    
//     $data = $data->filter(function ($user) use ($timeframe, $now) {
//         $lastLogin = isset($user['SUS_LASTLOGIN']) ? \Carbon\Carbon::parse($user['SUS_LASTLOGIN']) : null;

//         if (!$lastLogin) {
//             return false;
//         }

//         switch ($timeframe) {
//             case 'today':
//                 return $lastLogin->isToday();
//             case '2w':
//                 return $lastLogin->greaterThanOrEqualTo($now->copy()->subWeeks(2));
//             case '3w':
//                 return $lastLogin->greaterThanOrEqualTo($now->copy()->subWeeks(3));
//             case '1m':
//                 return $lastLogin->greaterThanOrEqualTo($now->copy()->subMonth());
//             case 'more1m':
//                 return $lastLogin->lessThan($now->copy()->subMonth());
//             default:
//                 return true;
//         }
//     });

//     // Count active and inactive users
//     $activeCount = $data->where('SUS_ACTIVE', 'A')->unique('SUS_NAME')->count();
//     $inactiveCount = $data->where('SUS_ACTIVE', 'I')->unique('SUS_NAME')->count();

//     // Ensure SUS_NAME is unique
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
//     //dd($branchStats);

//     // Pass the results to the view
//     return view('usersactive.index', compact(
//         'data', 
//         'totalCount', 
//         'activeCount', 
//         'inactiveCount', 
//         'locations', // All locations, even if no user logged in today
//         'branchStats'
//     ));
// }
// public function index(Request $request)
// {
//     // Retrieve user data
//     $data = Helper::fetchUser();

//     // Check for errors in data fetching
//     if (isset($data['error'])) {
//         return response()->json(['error' => $data['error']], 500);
//     }

//     // Convert data to a collection
//     $data = collect($data);

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

//     // Ensure SUS_NAME is unique
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
//         'branchStats'
//     ));
// } thk thk 
// public function index(Request $request)
// {
//     // Retrieve user data
//     $data = Helper::fetchUser();

//     // Check for errors in data fetching
//     if (isset($data['error'])) {
//         return response()->json(['error' => $data['error']], 500);
//     }

//     // Convert data to a collection
//     $data = collect($data);

//     // Extract all branch names (locations)
//     $locations = $data->pluck('PLC_DESC')->values();

//     // Filter users based on selected user status
//     if ($request->has('user_status')) {
//         $userStatus = $request->input('user_status');
//         $data = $data->filter(function($user) use ($userStatus) {
//             return isset($user['SUS_ACTIVE']) && $user['SUS_ACTIVE'] === $userStatus;
//         });
//     }

//     // Branch Filtering
//     if ($request->filled('location')) {
//         $location = $request->input('location');
//         $data = $data->filter(function($user) use ($location) {
//             return isset($user['PLC_DESC']) && $user['PLC_DESC'] === $location;
//         });
//     }

//     // Login Date Filtering
//     $timeframe = $request->input('login_timeframe', 'today');
//     $now = now();

//     $data = $data->filter(function ($user) use ($timeframe, $now) {
//         $lastLogin = isset($user['SUS_LASTLOGIN']) ? \Carbon\Carbon::parse($user['SUS_LASTLOGIN']) : null;

//         if (!$lastLogin) {
//             return false;
//         }

//         if ($timeframe === 'today') {
//             return $lastLogin->isToday();
//         } elseif ($timeframe === 'yesterday') {
//             return $lastLogin->isYesterday();
//         } elseif ($timeframe === '2days') {
//             return $lastLogin->isSameDay($now->subDays(2));
//         } elseif ($timeframe === '3days') {
//             return $lastLogin->isSameDay($now->subDays(3));
//         } elseif ($timeframe === '2w') {
//             return $lastLogin >= $now->subWeeks(2);
//         } elseif ($timeframe === '3w') {
//             return $lastLogin >= $now->subWeeks(3);
//         } elseif ($timeframe === '1m') {
//             return $lastLogin >= $now->subMonth();
//         } elseif ($timeframe === 'more1m') {
//             return $lastLogin < $now->subMonth();
//         }

//         return true; // default case
//     });

//     // Count active and inactive users
//     $activeCount = $data->where('SUS_ACTIVE', 'A')->count();
//     $inactiveCount = $data->where('SUS_ACTIVE', 'I')->count();
    
//     // Calculate total user count
//     $totalCount = $data->count();

//     // Calculate branch-wise statistics
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
//         'activeCount', 
//         'inactiveCount', 
//         'totalCount', // Add totalCount here
//         'locations',
//         'branchStats'
//     ));
// } without unique means all 

  
// public function index(Request $request)
// {
//     // Retrieve user data
//     $data = Helper::fetchUser();

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
//     });

//     $logoutsToday = $data->filter(function ($user) use ($today) {
//         return isset($user['SUL_LOGOUTDATE']) && Carbon::createFromFormat('d-M-y h.i.s.u A', $user['SUL_LOGOUTDATE'])->toDateString() === $today;
//     });


//     // Counts
//     $loginsTodayCount = $loginsToday->count();
//     $logoutsTodayCount = $logoutsToday->count();

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

//     // Ensure SUS_NAME is unique
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
// }
public function index(Request $request)
{
    // Retrieve user data
    $data = Helper::fetchUser();

    // Check for errors in data fetching
    if (isset($data['error'])) {
        return response()->json(['error' => $data['error']], 500);
    }

    // Convert data to a collection
    $data = collect($data);
    // Today's date
    $today = now()->toDateString();

    // Filter logins and logouts for today
    $loginsToday = $data->filter(function ($user) use ($today) {
        return isset($user['SUL_LOGINDATE']) && Carbon::createFromFormat('d-M-y h.i.s.u A', $user['SUL_LOGINDATE'])->toDateString() === $today;
    })->unique('SUS_NAME'); // Ensure unique logins

    $logoutsToday = $data->filter(function ($user) use ($today) {
        return isset($user['SUL_LOGOUTDATE']) && Carbon::createFromFormat('d-M-y h.i.s.u A', $user['SUL_LOGOUTDATE'])->toDateString() === $today;
    })->unique('SUS_NAME'); // Ensure unique logouts

    //dd($loginsToday );
    // Counts
    $loginsTodayCount = $loginsToday->count();
    $logoutsTodayCount = $logoutsToday->count();

    // Extract all unique branch names (locations) before filtering
    $locations = $data->pluck('PLC_DESC')->unique()->values();

    // Filter users based on selected user status
    if ($request->has('user_status')) {
        $userStatus = $request->input('user_status');

        if ($userStatus === 'A') {
            $data = $data->filter(fn($user) => isset($user['SUS_ACTIVE']) && $user['SUS_ACTIVE'] === 'A');
        } elseif ($userStatus === 'I') {
            $data = $data->filter(fn($user) => isset($user['SUS_ACTIVE']) && $user['SUS_ACTIVE'] === 'I');
        }
    }

    // Branch Filtering
    if ($request->filled('location')) {
        $location = $request->input('location');
        $data = $data->filter(fn($user) => isset($user['PLC_DESC']) && $user['PLC_DESC'] === $location);
    }
    
    // Default to today's login timeframe if not specified
    $timeframe = $request->input('login_timeframe', 'today');
    $now = now();

    // Login Date Filtering
    $data = $data->filter(function ($user) use ($timeframe, $now) {
        $lastLogin = isset($user['SUS_LASTLOGIN']) ? \Carbon\Carbon::parse($user['SUS_LASTLOGIN']) : null;

        if (!$lastLogin) {
            return false;
        }

        switch ($timeframe) {
            case 'today':
                return $lastLogin->toDateString() === $now->toDateString();
            case 'yesterday':
                return $lastLogin->toDateString() === $now->copy()->subDay()->toDateString();
            case '2days':
                return $lastLogin->toDateString() === $now->copy()->subDays(2)->toDateString();
            case '3days':
                return $lastLogin->toDateString() === $now->copy()->subDays(3)->toDateString();
            case '2w':
                return $lastLogin->greaterThanOrEqualTo($now->copy()->subWeeks(2)->startOfDay());
            case '3w':
                return $lastLogin->greaterThanOrEqualTo($now->copy()->subWeeks(3)->startOfDay());
            case '1m':
                return $lastLogin->greaterThanOrEqualTo($now->copy()->subMonth()->startOfDay());
            case 'more1m':
                return $lastLogin->lessThan($now->copy()->subMonth()->startOfDay());
            default:
                return true;
        }
    });

    // Count active and inactive users
    $activeCount = $data->where('SUS_ACTIVE', 'A')->unique('SUS_NAME')->count();
    $inactiveCount = $data->where('SUS_ACTIVE', 'I')->unique('SUS_NAME')->count();

    // Ensure SUS_NAME is unique for the full user list
    $data = $data->unique('SUS_NAME');

    // Count the total number of unique users after filtering
    $totalCount = $data->count();

    // Calculate branch-wise statistics (only for displayed users)
    $branchStats = $data->groupBy('PLC_DESC')->map(function ($branchUsers) {
        return [
            'total' => $branchUsers->count(),
            'active' => $branchUsers->where('SUS_ACTIVE', 'A')->count(),
            'inactive' => $branchUsers->where('SUS_ACTIVE', 'I')->count(),
            'users' => $branchUsers->sortBy('SUS_NAME')->values()
        ];
    })->sortByDesc('total');

    // Pass the results to the view
    return view('usersactive.index', compact(
        'data', 
        'totalCount', 
        'activeCount', 
        'inactiveCount', 
        'locations',
        'branchStats',
        'loginsTodayCount', 
        'logoutsTodayCount', 
        'loginsToday', 
        'logoutsToday'
    ));
}


// public function getLoginsByDate(Request $request)
// {
//     $date = $request->input('date');
//     $search = $request->input('search');

//     // Fetch all login data from API
//     $loginData =  Helper::fetchUser(); // Adjust this to your actual API call
  
//     // Convert to collection for easier manipulation
//     $logins = collect($loginData);

//     // Apply date filter if provided
//     if ($date) {
//         $filterDate = Carbon::createFromFormat('Y-m-d', $date);
//         $logins = $logins->filter(function ($login) use ($filterDate) {
//             try {
//                 $cleanedDate = preg_replace('/\.\d+ /', ' ', $login['SUL_LOGINDATE']);
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
//                    str_contains(strtolower($login['SUS_USERCODE'] ?? ''), $search) ||
//                    str_contains(strtolower($login['PLC_DESC'] ?? ''), $search);
//         });
//     }

//     return response()->json(['data' => $logins->values()->all()]);
// }
public function getLoginsByDate(Request $request)
{
    $date = $request->input('date');
    $search = $request->input('search');

    // Fetch all login data from API
    $loginData = Helper::fetchUser();
    
    // Convert to collection for easier manipulation
    $logins = collect($loginData);

    // Apply date filter if provided
    if ($date) {
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
    }

    // Apply search filter if provided
    if ($search) {
        $search = strtolower($search);
        $logins = $logins->filter(function ($login) use ($search) {
            return str_contains(strtolower($login['SUS_NAME'] ?? ''), $search) ||
                   str_contains(strtolower($login['SUS_USERCODE'] ?? ''), $search) ||
                   str_contains(strtolower($login['PLC_DESC'] ?? ''), $search);
        });
    }

    return response()->json(['data' => $logins->values()->all()]);
}


// public function getLogoutsByDate(Request $request)
// {
//     $date = $request->input('date');

//     $logouts = Helper::fetchUser();
//     $logouts = collect($logouts)
//         ->filter(function ($user) use ($date) {
//             return isset($user['SUL_LOGOUTDATE']) && \Carbon\Carbon::createFromFormat('d-M-y h.i.s.u A', $user['SUL_LOGOUTDATE'])->toDateString() === $date;
//         })
//         ->unique('SUS_NAME')
//         ->values(); // Return as a simple array

//     return response()->json($logouts);
// }
public function getLogoutsByDate(Request $request)
{
    $date = $request->input('date');
    $search = $request->input('search');

    // Fetch all logout data (adjust this to your actual data source)
    $logoutData = Helper::fetchUser(); // Or your method to get logout data
    
    // Convert to collection for easier manipulation
    $logouts = collect($logoutData);

    // Apply date filter if provided
    if ($date) {
        $filterDate = Carbon::createFromFormat('Y-m-d', $date);
        $logouts = $logouts->filter(function ($logout) use ($filterDate) {
            try {
                $dateField = $logout['SUS_LASTLOGIN'] ?? $logout['SUS_LASTLOGIN'] ?? null;
                if (!$dateField) return false;
                
                $cleanedDate = preg_replace('/\.\d+ /', ' ', $dateField);
                $logoutDate = Carbon::createFromFormat('d-M-y h.i.s A', $cleanedDate);
                return $logoutDate->isSameDay($filterDate);
            } catch (\Exception $e) {
                return false;
            }
        });
    }

    // Apply search filter if provided
    if ($search) {
        $search = strtolower($search);
        $logouts = $logouts->filter(function ($logout) use ($search) {
            return str_contains(strtolower($logout['SUS_NAME'] ?? ''), $search) ||
                   str_contains(strtolower($logout['SUS_USERCODE'] ?? ''), $search) ||
                   str_contains(strtolower($logout['PLC_DESC'] ?? ''), $search);
        });
    }

    return response()->json(['data' => $logouts->values()->all()]);
}


}
