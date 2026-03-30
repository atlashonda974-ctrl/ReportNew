<?php

namespace App\Helpers;
use Carbon\Carbon;  

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log; 

use Illuminate\Support\Collection;

class POHelper
{
    public static function getPO($startDate = null, $endDate = null)
    {
        
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
        }

        // Format dates for the API
        $dateFrom = Carbon::parse($startDate)->format('d-M-Y'); 
        $dateTo = Carbon::parse($endDate)->format('d-M-Y'); 

        // Log the dates
        error_log("Date From: $dateFrom, Date To: $dateTo");

    

        $apiUrl = "http://172.16.22.204/dashboardApi/all/uw/client_doc.php?datefrom={$dateFrom}&dateto={$dateTo}";


        // Call the API
        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120
            ]
        ]));

        if ($response === false) {
            error_log('API Request Failed');
            return [
                'status' => 'error',
                'message' => 'API Request Failed'
            ];
        }

        // Log the raw response
        error_log("Raw API Response: $response");

        // Decode the JSON response
        $decodedResponse = json_decode($response, true);

        // Check for JSON errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON',
                'raw_response' => substr($response, 0, 500)
            ];
        }

        // If the response is an array of JSON strings, decode each item
        $data = array_map('json_decode', $decodedResponse);

            //        echo "<pre>";
            // print_r($data);
            // echo "</pre>";
            // exit;

        return [
            'status' => 'success',
            'data' => $data,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo
        ];
    }

}






