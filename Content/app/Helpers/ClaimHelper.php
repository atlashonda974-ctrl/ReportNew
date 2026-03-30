<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ClaimHelper
{
    public static function getClaim(
        $startDate = null,
        $endDate = null,
        $dept = 'All',
        $branch = 'All',
        $takaful = 'All'
    ) {
        // Set default start and end dates if not provided
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
        }

        $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
        $dateTo = Carbon::parse($endDate)->format('d-M-Y');

        // Log parameters
        Log::info('ClaimHelper Parameters:', [
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
            'dept' => $dept,
            'branch' => $branch,
            'takaful' => $takaful,
        ]);

        // Build query parameters dynamically
        $params = [
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
            'dept' => $dept,
            'branch' => $branch,
            'takaful' => $takaful,
        ];

        // Construct the API URL
        $apiUrl = "http://172.16.22.204/dashboardApi/clm/getIntiClm.php?" . http_build_query($params);
        Log::info('API URL: ' . $apiUrl);

        // Call the API
        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120,
            ],
        ]));

        if ($response === false) {
            Log::error('API Request Failed for URL: ' . $apiUrl);
            return [
                'status' => 'error',
                'message' => 'API Request Failed',
            ];
        }

        Log::info('Raw API Response: ' . substr($response, 0, 1000));

        // Decode JSON
        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON Decode Error: ' . json_last_error_msg() . ' for response: ' . substr($response, 0, 500));
            return [
                'status' => 'error',
                'message' => 'Invalid JSON response from API',
                'raw_response' => substr($response, 0, 500),
            ];
        }

        return [
            'status' => 'success',
            'data' => $decodedResponse,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
        ];
    }

    public static function getClaimR2(
        $startDate = null,
        $endDate = null,
        $dept = 'All',
        $branch = 'All',
        $takaful = 'All',
        $insu = ['All']
    ) {
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
        }

        $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
        $dateTo = Carbon::parse($endDate)->format('d-M-Y');

        if (is_array($insu)) {
            $insu = empty($insu) ? 'All' : implode(',', $insu);
        }

        $apiUrl = "http://172.16.22.204/dashboardApi/clm/getOsSurvClm.php?" .
            "datefrom={$dateFrom}&" .
            "dateto={$dateTo}&" .
            "dept={$dept}&" .
            "branch={$branch}&" .
            "takaful={$takaful}&" .
            "insu={$insu}";
           // dd($apiUrl);

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120,
            ],
        ]));

        if ($response === false) {
            return [
                'status' => 'error',
                'message' => 'API Request Failed',
            ];
        }

        $decodedResponse = json_decode($response, true);

                //    echo "<pre>";
                // print_r($decodedResponse);
                // echo "</pre>";
                // exit;


        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON response from API',
                'raw_response' => substr($response, 0, 500),
            ];
        }

        return [
            'status' => 'success',
            'data' => $decodedResponse,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
        ];
    }

    public static function getClaimR3(
        $startDate = null,
        $endDate = null,
        $dept = 'All',
        $branch = 'All',
        $takaful = 'All',
        $insu = ['All']
    ) {
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
        }

        $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
        $dateTo = Carbon::parse($endDate)->format('d-M-Y');

        if (is_array($insu)) {
            $insu = empty($insu) ? 'All' : implode(',', $insu);
        }

        $apiUrl = "http://172.16.22.204/dashboardApi/clm/getOsRepClm.php?" .
            "datefrom={$dateFrom}&" .
            "dateto={$dateTo}&" .
            "dept={$dept}&" .
            "branch={$branch}&" .
            "takaful={$takaful}&" .
            "insu={$insu}";

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120,
            ],
        ]));

        if ($response === false) {
            return [
                'status' => 'error',
                'message' => 'API Request Failed',
            ];
        }

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON response from API',
                'raw_response' => substr($response, 0, 500),
            ];
        }

        return [
            'status' => 'success',
            'data' => $decodedResponse,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
        ];
    }

    public static function getClaimR4(
        $startDate = null,
        $endDate = null,
        $dept = 'All',
        $branch = 'All',
        $takaful = 'All',
        $insu = ['All']
    ) {
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
        }

        $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
        $dateTo = Carbon::parse($endDate)->format('d-M-Y');

        if (is_array($insu)) {
            $insu = empty($insu) ? 'All' : implode(',', $insu);
        }

        $apiUrl = "http://172.16.22.204/dashboardApi/clm/getOsStlClmALL.php?" .
            "datefrom={$dateFrom}&" .
            "dateto={$dateTo}&" .
            "dept={$dept}&" .
            "branch={$branch}&" .
            "takaful={$takaful}&" .
            "insu={$insu}";

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120,
            ],
        ]));

        if ($response === false) {
            return [
                'status' => 'error',
                'message' => 'API Request Failed',
            ];
        }

        $decodedResponse = json_decode($response, true);
                //    echo "<pre>";
                //  print_r($decodedResponse);
                //  echo "</pre>";
                //  exit;

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON response from API',
                'raw_response' => substr($response, 0, 500),
            ];
        }

        return [
            'status' => 'success',
            'data' => $decodedResponse,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
        ];
    }

     public static function getClaimR5(
        $startDate = null,
        $endDate = null,
        $dept = 'All',
        $branch = 'All',
        $takaful = 'All',
        $insu = ['All']
    ) {
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
        }

        $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
        $dateTo = Carbon::parse($endDate)->format('d-M-Y');

        if (is_array($insu)) {
            $insu = empty($insu) ? 'All' : implode(',', $insu);
        }

        $apiUrl = "http://172.16.22.204/dashboardApi/clm/getStlClm.php?" .
            "datefrom={$dateFrom}&" .
            "dateto={$dateTo}&" .
            "dept={$dept}&" .
            "branch={$branch}&" .
            "takaful={$takaful}&" .
            "insu={$insu}";
        //dd($apiUrl);
        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120,
            ],
        ]));

        if ($response === false) {
            return [
                'status' => 'error',
                'message' => 'API Request Failed',
            ];
        }

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON response from API',
                'raw_response' => substr($response, 0, 500),
            ];
        }

        return [
            'status' => 'success',
            'data' => $decodedResponse,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
        ];
    }
    //      public static function getClaimR6(
    //     $startDate = null,
    //     $endDate = null,
    //     $dept = 'All',
    //     $branch = 'All',
    //     $takaful = 'All'
       
    // ) {
    //     if (!$startDate || !$endDate) {
    //         $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
    //         $endDate = Carbon::now()->format('Y-m-d');
    //     }

    //     $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
    //     $dateTo = Carbon::parse($endDate)->format('d-M-Y');

       

    //     $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/clm/getIntiClm.php?" .
    //         "datefrom={$dateFrom}&" .
    //         "dateto={$dateTo}&" .
    //         "dept={$dept}&" .
    //         "branch={$branch}&" .
    //         "takaful={$takaful}&" .
    //     //dd($apiUrl);
    //     $response = @file_get_contents($apiUrl, false, stream_context_create([
    //         'http' => [
    //             'ignore_errors' => true,
    //             'timeout' => 120,
    //         ],
    //     ]));

    //     if ($response === false) {
    //         return [
    //             'status' => 'error',
    //             'message' => 'API Request Failed',
    //         ];
    //     }

    //     $decodedResponse = json_decode($response, true);

    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         return [
    //             'status' => 'error',
    //             'message' => 'Invalid JSON response from API',
    //             'raw_response' => substr($response, 0, 500),
    //         ];
    //     }

    //     return [
    //         'status' => 'success',
    //         'data' => $decodedResponse,
    //         'api_url' => $apiUrl,
    //         'datefrom' => $dateFrom,
    //         'dateto' => $dateTo,
    //     ];
    // }
    // before addding all . takaful

    // public static function getClaimR6(
    //             $startDate = null,
    //             $endDate = null,
    //             $dept = 'All',
    //             $branch = 'All',
    //             $takaful = 'All'
    //         ) {
    //             // Set default start and end dates if not provided
    //             if (!$startDate || !$endDate) {
    //                 $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
    //                 $endDate = Carbon::now()->format('Y-m-d');
    //             }

    //             $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
    //             $dateTo = Carbon::parse($endDate)->format('d-M-Y');

                

    //             // Build query parameters dynamically
    //             $params = [
    //                 'datefrom' => $dateFrom,
    //                 'dateto' => $dateTo,
    //                 'dept' => $dept,
    //                 'branch' => $branch,
    //                 'takaful' => $takaful,
    //             ];

    //             // Construct the API URL
    //             $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/clm/getIntiClm.php?" . http_build_query($params);
                
    //             // Call the API
    //             $response = @file_get_contents($apiUrl, false, stream_context_create([
    //                 'http' => [
    //                     'ignore_errors' => true,
    //                     'timeout' => 120,
    //                 ],
    //             ]));

            
    //             // Decode JSON
    //             $decodedResponse = json_decode($response, true);
    //             //          echo "<pre>";
    //             // print_r($decodedResponse);
    //             // echo "</pre>";
    //             // exit;

            
    //             return [
    //                 'status' => 'success',
    //                 'data' => $decodedResponse,
    //                 'api_url' => $apiUrl,
    //                 'datefrom' => $dateFrom,
    //                 'dateto' => $dateTo,
    //             ];
    //         }

        public static function getClaimR6(
        $startDate = null,
        $endDate = null,
        $dept = 'All',
        $branch = 'All',
        $takaful = 'All',
        $insu = ['All']
    ) {
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
        }

        $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
        $dateTo = Carbon::parse($endDate)->format('d-M-Y');

        if (is_array($insu)) {
            $insu = empty($insu) ? 'All' : implode(',', $insu);
        }

        $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/clm/getIntiClm.php?" .
            "datefrom={$dateFrom}&" .
            "dateto={$dateTo}&" .
            "dept={$dept}&" .
            "branch={$branch}&" .
            "takaful={$takaful}&" .
            "insu={$insu}";
         //dd($apiUrl);

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120,
            ],
        ]));

        if ($response === false) {
            return [
                'status' => 'error',
                'message' => 'API Request Failed',
            ];
        }

        $decodedResponse = json_decode($response, true);
         
             
                //          echo "<pre>";
                // print_r($decodedResponse);
                // echo "</pre>";
                // exit;

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON response from API',
                'raw_response' => substr($response, 0, 500),
            ];
        }

        return [
            'status' => 'success',
            'data' => $decodedResponse,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
        ];
    }

    //
    public static function getClaimR8(
        $startDate = null,
        $endDate = null,
        $dept = 'All',
        $branch = 'All',
        $takaful = 'All',
        $insu = ['All']
    ) {
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
        }

        $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
        $dateTo = Carbon::parse($endDate)->format('d-M-Y');

        if (is_array($insu)) {
            $insu = empty($insu) ? 'All' : implode(',', $insu);
        }

        $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/clm/getWorkshopReport.php?" .
            "datefrom={$dateFrom}&" .
            "dateto={$dateTo}&" .
            "dept={$dept}&" .
            "branch={$branch}&" .
            "takaful={$takaful}&" .
            "insu={$insu}";

            //dd( $apiUrl);

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120,
            ],
        ]));

        if ($response === false) {
            return [
                'status' => 'error',
                'message' => 'API Request Failed',
            ];
        }

        $decodedResponse = json_decode($response, true);
                //  echo "<pre>";
                // print_r($decodedResponse);
                // echo "</pre>";
                // exit;

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON response from API',
                'raw_response' => substr($response, 0, 500),
            ];
        }

        return [
            'status' => 'success',
            'data' => $decodedResponse,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
        ];
    }

    public static function getClaimR9(
        $startDate = null,
        $endDate = null,
        $dept = 'All',
        $branch = 'All',
        $takaful = 'All',
        $insu = ['All']
    ) {
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
        }

        $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
        $dateTo = Carbon::parse($endDate)->format('d-M-Y');

        if (is_array($insu)) {
            $insu = empty($insu) ? 'All' : implode(',', $insu);
        }

        $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/clm/getSurvReport.php?".
            "datefrom={$dateFrom}&" .
            "dateto={$dateTo}&" .
            "dept={$dept}&" .
            "branch={$branch}&" .
            "takaful={$takaful}&" .
            "insu={$insu}";

            //dd( $apiUrl);

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120,
            ],
        ]));

        if ($response === false) {
            return [
                'status' => 'error',
                'message' => 'API Request Failed',
            ];
        }

        $decodedResponse = json_decode($response, true);
                //  echo "<pre>";
                // print_r($decodedResponse);
                // echo "</pre>";
                // exit;

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON response from API',
                'raw_response' => substr($response, 0, 500),
            ];
        }

        return [
            'status' => 'success',
            'data' => $decodedResponse,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
        ];
    }

     public static function getClaimR10(
        $startDate = null,
        $endDate = null,
        $dept = 'All',
        $branch = 'All',
        $takaful = 'All',
        $insu = ['All']
    ) {
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
        }

        $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
        $dateTo = Carbon::parse($endDate)->format('d-M-Y');

        if (is_array($insu)) {
            $insu = empty($insu) ? 'All' : implode(',', $insu);
        }

        $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/clm/getBrClm.php?".
            "datefrom={$dateFrom}&" .
            "dateto={$dateTo}&" .
            "dept={$dept}&" .
            "branch={$branch}&" .
            "takaful={$takaful}&" .
            "insu={$insu}";

            //dd( $apiUrl);

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120,
            ],
        ]));

        if ($response === false) {
            return [
                'status' => 'error',
                'message' => 'API Request Failed',
            ];
        }

        $decodedResponse = json_decode($response, true);
                //  echo "<pre>";
                // print_r($decodedResponse);
                // echo "</pre>";
                // exit;

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON response from API',
                'raw_response' => substr($response, 0, 500),
            ];
        }

        return [
            'status' => 'success',
            'data' => $decodedResponse,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
        ];
    }
         public static function getClaimR11(
        $startDate = null,
        $endDate = null,
        $dept = 'All',
        $branch = 'All',
        $takaful = 'All',
        $insu = ['All'],
        $userZone = 'All'
    ) {
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
        }

        $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
        $dateTo = Carbon::parse($endDate)->format('d-M-Y');

        if (is_array($insu)) {
            $insu = empty($insu) ? 'All' : implode(',', $insu);
        }

      $apiUrl = "http://172.16.22.204/dashboardApi/clm/getOsStlClm_GL2.php?".
            "datefrom={$dateFrom}&" .
            "dateto={$dateTo}&" .
            "dept={$dept}&" .
            "branch={$branch}&" .
            "takaful={$takaful}&" .
            "insu={$insu}&" .
            "zone={$userZone}";

           // dd( $apiUrl);

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120,
            ],
        ]));

        if ($response === false) {
            return [
                'status' => 'error',
                'message' => 'API Request Failed',
            ];
        }

        $decodedResponse = json_decode($response, true);
                //  echo "<pre>";
                // print_r($decodedResponse);
                // echo "</pre>";
                // exit;

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON response from API',
                'raw_response' => substr($response, 0, 500),
            ];
        }

        return [
            'status' => 'success',
            'data' => $decodedResponse,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
        ];
    }
   
    



// public static function getClaimR12(
//     $startDate = null,
//     $endDate = null,
//     $dept = 'All',
//     $branch = 'All',
//     $takaful = 'All'
// ) {
//     // Default to today if dates are missing
//     if (!$startDate || !$endDate) {
//         $startDate = Carbon::today()->format('Y-m-d');
//         $endDate = Carbon::today()->format('Y-m-d');
//     }

//     $start = Carbon::parse($startDate);
//     $end = Carbon::parse($endDate);

//     // If it's a single day, fetch directly
//     if ($start->equalTo($end)) {
//         return self::fetchSingleDay($start, $dept, $branch, $takaful);
//     }

//     // For ranges, fetch day-by-day and merge, skipping empty responses
//     $combinedData = [
//         'status' => 'success',
//         'data' => ['uw' => ['C' => []]],  // Initialize structure
//         'api_urls' => [],
//         'skipped_dates' => [],  // Track dates with no data
//         'datefrom' => $start->format('d-M-Y'),
//         'dateto' => $end->format('d-M-Y')
//     ];
//     $current = $start->copy();

//     while ($current->lessThanOrEqualTo($end)) {
//         $dayResult = self::fetchSingleDay($current, $dept, $branch, $takaful);
        
//         if ($dayResult['status'] === 'success') {
//             // Merge data (assuming structure like {"status":"success","uw":{"C":[...data...]}})
//             if (isset($dayResult['data']['uw']['C']) && is_array($dayResult['data']['uw']['C'])) {
//                 $combinedData['data']['uw']['C'] = array_merge($combinedData['data']['uw']['C'], $dayResult['data']['uw']['C']);
//             }
//             $combinedData['api_urls'][] = $dayResult['api_url'];
//         } elseif (strpos($dayResult['message'], 'empty response') !== false) {
//             // Skip and note empty responses
//             $combinedData['skipped_dates'][] = $current->format('d-M-Y');
//         } else {
//             // For other errors (e.g., network), return immediately
//             return $dayResult;
//         }
        
//         $current->addDay();
//     }

//     return $combinedData;
// }

// // Helper method to fetch data for a single day
// private static function fetchSingleDay(Carbon $date, $dept, $branch, $takaful) {
//     $dateFormatted = $date->format('d-M-Y');
    
//     $params = [
//         'datefrom' => $dateFormatted,
//         'dateto' => $dateFormatted,
//         'dept' => $dept,
//         'branch' => $branch,
//         'takaful' => $takaful,
//     ];

//     $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/dateWiseData.php?" . http_build_query($params);

//     // Use curl for better error handling and control
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $apiUrl);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_TIMEOUT, 120);
//     $response = curl_exec($ch);
//     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//     $curlError = curl_error($ch);
//     curl_close($ch);

//     if ($response === false || $httpCode !== 200) {
//         return [
//             'status' => 'error',
//             'message' => 'Failed to fetch data: HTTP ' . $httpCode . ' - ' . $curlError,
//             'api_url' => $apiUrl
//         ];
//     }

//     // Check for empty response
//     if (empty(trim($response))) {
//         return [
//             'status' => 'error',
//             'message' => 'API returned an empty response for date: ' . $dateFormatted,
//             'api_url' => $apiUrl
//         ];
//     }

//     // Decode JSON response
//     $decodedResponse = json_decode($response, true);

//     if (json_last_error() !== JSON_ERROR_NONE) {
//         return [
//             'status' => 'error',
//             'message' => 'Invalid JSON response: ' . json_last_error_msg(),
//             'api_url' => $apiUrl
//         ];
//     }

//     // Return structured array on success
//     return [
//         'status' => 'success',
//         'data' => $decodedResponse,
//         'api_url' => $apiUrl,
//         'date' => $dateFormatted,
//     ];
// } 
       public static function getClaimR12(
    $startDate = null,
    $endDate = null,
    $dept = 'All',
    $branch = 'All',
    $takaful = 'All'
) {
    // Set default start and end dates if not provided
    if (!$startDate || !$endDate) {
        $startDate = Carbon::today()->format('Y-m-d'); // Default to today
        $endDate = Carbon::today()->format('Y-m-d');
    }

    // Convert dates to required format (e.g., 07-Nov-2025)
    $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
    $dateTo = Carbon::parse($endDate)->format('d-M-Y');

    // API endpoint
    $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/dateWiseData.php?" .
        "datefrom={$dateFrom}&" .
        "dateto={$dateTo}&" .
        "dept={$dept}&" .
        "branch={$branch}&" .
        "takaful={$takaful}";
    //dd($apiUrl );
    // Fetch API response
    $response = @file_get_contents($apiUrl, false, stream_context_create([
        'http' => [
            'ignore_errors' => true,
            'timeout' => 120,
        ],
    ]));

    // Handle connection failure
    if ($response === false) {
        return [
            'status' => 'error',
            'message' => 'API Request Failed',
        ];
    }

    // Decode the JSON response
    $decodedResponse = json_decode($response, true);

    // Handle invalid JSON response
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'status' => 'error',
            'message' => 'Invalid JSON response from API',
            'raw_response' => substr($response, 0, 500),
        ];
    }

    // Return success response
    return [
        'status' => 'success',
        'data' => $decodedResponse,
        'api_url' => $apiUrl,
        'datefrom' => $dateFrom,
        'dateto' => $dateTo,
    ];
}
// public static function getClaimR12(
//     $startDate = null,
//     $endDate = null,
//     $dept = 'All',
//     $branch = 'All',
//     $takaful = 'All'
// ) {
//     // Default to today's date if not provided
//     if (!$startDate || !$endDate) {
//         $startDate = Carbon::today()->format('Y-m-d');
//         $endDate = Carbon::today()->format('Y-m-d');
//     }

//     // Convert to required API format: 07-Nov-2025
//     $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
//     $dateTo = Carbon::parse($endDate)->format('d-M-Y');

//     // Build API URL
//     $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/dateWiseData.php?" .
//         "datefrom={$dateFrom}&" .
//         "dateto={$dateTo}&" .
//         "dept={$dept}&" .
//         "branch={$branch}&" .
//         "takaful={$takaful}";

//     // Create stream context
//     $context = stream_context_create([
//         'http' => [
//             'ignore_errors' => true,
//             'timeout' => 120,
//         ],
//     ]);

//     // Get the response
//     $response = @file_get_contents($apiUrl, false, $context);

//     // Handle connection failure
//     if ($response === false || trim($response) === '') {
//         return [
//             'status' => 'error',
//             'message' => 'API Request Failed or Empty Response',
//             'api_url' => $apiUrl,
//         ];
//     }

//     // Remove BOM or extra spaces (some APIs send hidden characters)
//     $response = trim(preg_replace('/^\xEF\xBB\xBF/', '', $response));

//     // Try to decode JSON
//     $decodedResponse = json_decode($response, true);

//     // If invalid JSON, include raw response (preview)
//     if (json_last_error() !== JSON_ERROR_NONE) {
//         return [
//             'status' => 'error',
//             'message' => 'Invalid JSON response from API',
//             'api_url' => $apiUrl,
//             'raw_response' => substr($response, 0, 1000), // first 1000 chars for inspection
//         ];
//     }

//     // Successful response
//     return [
//         'status' => 'success',
//         'data' => $decodedResponse,
//         'api_url' => $apiUrl,
//         'datefrom' => $dateFrom,
//         'dateto' => $dateTo,
//     ];
// }



}