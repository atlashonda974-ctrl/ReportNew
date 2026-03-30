<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ClaimDBHelper
{
    public static function getClaimS1(
        $startDate = null,
        $endDate = null,
        $dept = 'All',
        $branch = 'All',
        $takaful = 'All'
    ) {
        // Set default dates
        if (!$startDate) {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
        }
        if (!$endDate) {
            $endDate = Carbon::now()->format('Y-m-d');
        }

        // Format dates
        $dateFrom = Carbon::parse($startDate)->format('d-M-y');
        $dateTo = Carbon::parse($endDate)->format('d-M-y');

        // Build query parameters
        $params = [
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
            'dept' => $dept,
            'branch' => $branch,
            'takaful' => $takaful,
        ];

        // API URL
        $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/clm/getClmStats.php?" . http_build_query($params);
        
        // Call API with cache-busting headers
        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 600,
                'header' => "Cache-Control: no-cache\r\n" .
                           "Pragma: no-cache\r\n"
            ],
        ]);

        $response = @file_get_contents($apiUrl, false, $context);

        if ($response === false) {
            Log::error('API Request Failed');
            return [
                'status' => 'error',
                'message' => 'API Request Failed',
                'data' => null
            ];
        }

        $decodedResponse = json_decode($response, true);
        
        return [
            'status' => 'success',
            'data' => $decodedResponse,
        ];
    }

    public static function getClaimS2(
        $startDate = null,
        $endDate = null,
        $dept = 'All',
        $branch = 'All',
        $takaful = 'All' // Removed trailing comma
    ) {
        // Set default dates
        if (!$startDate) {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
        }
        if (!$endDate) {
            $endDate = Carbon::now()->format('Y-m-d');
        }

        // Format dates
        $dateFrom = Carbon::parse($startDate)->format('d-M-y');
        $dateTo = Carbon::parse($endDate)->format('d-M-y');

        // Build query parameters
        $params = [
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
            'dept' => $dept,
            'branch' => $branch,
            'takaful' => $takaful
        ];

        // API URL
        $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/clm/getClmSltdStats.php?" . http_build_query($params);
        //dd($apiUrl );

        // Call API with cache-busting headers
        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 600,
                'header' => "Cache-Control: no-cache\r\n" .
                           "Pragma: no-cache\r\n"
            ],
        ]);

        $response = @file_get_contents($apiUrl, false, $context);

        if ($response === false) {
            Log::error('API Request Failed');
            return [
                'status' => 'error',
                'message' => 'API Request Failed',
                'data' => null
            ];
        }

        $decodedResponse = json_decode($response, true);
        //    echo "<pre>";
        //         print_r($decodedResponse);
        //         echo "</pre>";
        //         exit;
        
        return [
            'status' => 'success',
            'data' => $decodedResponse,
        ];
    }

public static function getClaimS3(
    $startDate = null,
    $endDate = null,
    $dept = 'All',
    $branch = 'All',
    $takaful = 'All'
) {
    // Set default dates
    if (!$startDate) {
        $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
    }
    if (!$endDate) {
        $endDate = Carbon::now()->format('Y-m-d');
    }

    // Format dates
    $dateFrom = Carbon::parse($startDate)->format('d-M-y');
    $dateTo = Carbon::parse($endDate)->format('d-M-y');

    // Build query parameters
    $params = [
        'datefrom' => $dateFrom,
        'dateto' => $dateTo,
        'dept' => $dept,
        'branch' => $branch,
        'takaful' => $takaful
    ];

    // API URL
    $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/clm/getClmStatus.php?" . http_build_query($params);

    // Call API with cache-busting headers
    $context = stream_context_create([
        'http' => [
            'ignore_errors' => true,
            'timeout' => 600,
            'header' => "Cache-Control: no-cache\r\n" .
                       "Pragma: no-cache\r\n"
        ],
    ]);

    $response = @file_get_contents($apiUrl, false, $context);

    if ($response === false) {
        Log::error('API Request Failed');
        return [
            'status' => 'error',
            'message' => 'API Request Failed',
            'data' => null
        ];
    }

    $decodedResponse = json_decode($response, true);
            //  echo "<pre>";
            //     print_r($decodedResponse);
            //     echo "</pre>";
            //     exit;

    // Decode the JSON string in the first element of the response, if it exists
    $finalData = isset($decodedResponse[0]) ? json_decode($decodedResponse[0], true) : [];

    return [
        'status' => 'success',
        'data' => $finalData, // Return the decoded array
    ];
}
public static function getClaimS4(
    $startDate = null,
    $endDate = null,
    $dept = 'All',
    $branch = 'All',
    $takaful = 'All',
    $insu = 'All'
) {
    // Set default dates
    if (!$startDate) {
        $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
    }
    if (!$endDate) {
        $endDate = Carbon::now()->format('Y-m-d');
    }

    // Validate and format dates
    $dateFrom = Carbon::parse($startDate)->format('d-M-y');
    $dateTo = Carbon::parse($endDate)->format('d-M-y');

    // Build query parameters
    $params = [
        'datefrom' => $dateFrom,
        'dateto' => $dateTo,
        'dept' => $dept,
        'branch' => $branch,
        'takaful' => $takaful,
        'insu' => $insu
    ];

    // API URL
    $apiUrl = "http://172.16.22.204/dashboardApi/clm/temp/getOsSurvClm.php?" . http_build_query($params);

    // Call API with cache-busting headers
    $context = stream_context_create([
        'http' => [
            'ignore_errors' => true,
            'timeout' => 600,
            'header' => "Cache-Control: no-cache\r\n" .
                       "Pragma: no-cache\r\n"
        ],
    ]);

    $response = @file_get_contents($apiUrl, false, $context);

    if ($response === false) {
        return [
            'status' => 'error',
            'message' => 'API Request Failed',
            'data' => null
        ];
    }

    // Decode the JSON response
    $decodedResponse = json_decode($response, true);

    // Check for JSON validity
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'status' => 'error',
            'message' => 'Invalid API response',
            'data' => null
        ];
    }

    // Access the relevant data directly
    $finalData = isset($decodedResponse) ? $decodedResponse : [];

    return [
        'status' => 'success',
        'data' => $finalData,
    ];
}
public static function getClaimS5(
        $startDate = null,
        $endDate = null,
        $dept = 'All',
        $branch = 'All',
        $takaful = 'All'
    ) {
        // Set default dates
        if (!$startDate) {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
        }
        if (!$endDate) {
            $endDate = Carbon::now()->format('Y-m-d');
        }

        // Format dates
        $dateFrom = Carbon::parse($startDate)->format('d-M-y');
        $dateTo = Carbon::parse($endDate)->format('d-M-y');

        // Build query parameters
        $params = [
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
            'dept' => $dept,
            'branch' => $branch,
            'takaful' => $takaful,
        ];

        // API URL
        $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/clm/getClmStats2.php?" . http_build_query($params);
        
        // Call API with cache-busting headers
        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 600,
                'header' => "Cache-Control: no-cache\r\n" .
                           "Pragma: no-cache\r\n"
            ],
        ]);

        $response = @file_get_contents($apiUrl, false, $context);

        if ($response === false) {
            Log::error('API Request Failed');
            return [
                'status' => 'error',
                'message' => 'API Request Failed',
                'data' => null
            ];
        }

        $decodedResponse = json_decode($response, true);
        //   echo "<pre>";
        //         print_r($decodedResponse);
        //         echo "</pre>";
        //         exit;
        
        return [
            'status' => 'success',
            'data' => $decodedResponse,
        ];
    }
}