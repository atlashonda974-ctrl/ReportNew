<?php

namespace App\Helpers;
use Carbon\Carbon;  

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log; // ✅ correct

use Illuminate\Support\Collection;

class Helper
{
    public static function fetchData()
    {
        $apiUrl = "http://172.16.22.204/ReportsNew/api/query_test.php";

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => ['ignore_errors' => true, 'timeout' => 120]
        ]));

        if (!$response) {
            return ['error' => 'Failed to fetch data'];
        }

        $data = json_decode($response, true) ?? [];

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Invalid JSON format'];
        }

        return $data;
    }
    public static function fetchCategories()
    {
        $apiUrl = "http://172.16.22.204/ReportsNew/api/getCatg.php";

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => ['ignore_errors' => true, 'timeout' => 120]
        ]));

        if (!$response) {
            return ['error' => 'Failed to fetch categories'];
        }

        $data = json_decode($response, true) ?? [];
        //dd($data);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Invalid JSON format'];
        }

        return $data;
    }
    public static function fetchDepreciation()
    {
        $apiUrl = "http://172.16.22.204/ReportsNew/api/get_dep.php";

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => ['ignore_errors' => true, 'timeout' => 120]
        ]));
        //dd($response);
        if (!$response) {
            return ['error' => 'Failed to fetch depreciation data'];
        }

        $data = json_decode($response, true) ?? [];

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Invalid JSON format'];
        }

        return $data;
    }
    public static function fetchTransfer()
    {
        $apiUrl = "http://172.16.22.204/ReportsNew/api/get_transfer.php";

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => ['ignore_errors' => true, 'timeout' => 120]
        ]));
        //dd($response);

        if (!$response) {
            return ['error' => 'Failed to fetch transfer data'];
        }

        $data = json_decode($response, true) ?? [];

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Invalid JSON format'];
        }

        return $data;
    }
    public static function fetchRegister()
    {
        $apiUrl = "http://172.16.22.204/ReportsNew/api/get_register.php";

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => ['ignore_errors' => true, 'timeout' => 120]
        ]));

        if (!$response) {
            return ['error' => 'Failed to fetch register data'];
        }

        $data = json_decode($response, true) ?? [];

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Invalid JSON format'];
        }

        return $data;
    }
    public static function fetchReinsurance(array $queryParams = []): array
    {
        $apiUrl = "http://172.16.22.204/ReportsNew/api/query_test_fac.php";

        if (!empty($queryParams)) {
            $apiUrl .= '?' . http_build_query($queryParams);
        }

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => ['ignore_errors' => true, 'timeout' => 120],
        ]));

        if (!$response) {
            return [
                'success' => false,
                'data' => [],
                'message' => 'Failed to fetch reinsurance data.',
                'raw_response' => null
            ];
        }
        $rawResponse = $response;

        // Parse the JSON records only
        $rawItems = preg_split('/<\/?br\s*\/?>/i', $response);
        $records = [];

        foreach ($rawItems as $item) {
            $item = trim($item);
            if (empty($item) || str_starts_with($item, 'Date Range') || str_starts_with($item, 'Input dates')) continue;

            $decoded = json_decode($item, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $records[] = $decoded;
            }
        }

        return [
            'success' => true,
            'data' => $records,
            'message' => null,
            'raw_response' => $rawResponse, 
        ];
    }
    public static function fetchBrokerData()
    {
        $apiUrl = "http://172.16.22.204/dashboardApi/broker/module/broker_wise_summary.php?expiryfrom=01-Jan-2025&expiryto=30-Apr-2025&broker=4100100035";

        // Parse the URL to get the broker code
        $parsedUrl = parse_url($apiUrl);
        parse_str($parsedUrl['query'], $queryParams);
        $currentBrokerCode = $queryParams['broker'] ?? null;

        // Fetch data from the API
        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => ['ignore_errors' => true, 'timeout' => 120]
        ]));

        if ($response === false) {
            return [
                'error' => 'Failed to fetch Broker Data',
                'current_broker' => $currentBrokerCode
            ];
        }

        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => 'Invalid JSON format',
                'current_broker' => $currentBrokerCode
            ];
        }

        return [
            'data' => $data,
            'current_broker' => $currentBrokerCode
        ];
    }
    public static function fetchPremiumData($startDate, $endDate)
    {
        // Format dates as required by the API (dd-MMM-yyyy)
        $formattedStart = Carbon::parse($startDate)->format('d-M-Y');
        $formattedEnd = Carbon::parse($endDate)->format('d-M-Y');
        
        $apiUrl = "http://172.16.22.204/dashboardApi/broker/module/broker_wise_summary.php?expiryfrom={$formattedStart}&expiryto={$formattedEnd}&broker=4100100035";

        $parsedUrl = parse_url($apiUrl);
        parse_str($parsedUrl['query'], $queryParams);
        $currentBrokerCode = $queryParams['broker'] ?? null;

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => ['ignore_errors' => true, 'timeout' => 120]
        ]));

        if ($response === false) {
            return [
                'error' => 'Failed to fetch Premium Data',
                'current_broker' => $currentBrokerCode
            ];
        }

        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => 'Invalid JSON format',
                'current_broker' => $currentBrokerCode
            ];
        }

        return [
            'data' => $data,
            'current_broker' => $currentBrokerCode
        ];
    }
    public static function fetchUser()
    {
        $apiUrl = "http://172.16.22.204/ReportsNew/api/all_fac.php";

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => ['ignore_errors' => true, 'timeout' => 120]
        ]));

        if (!$response) {
            return ['error' => 'Failed to fetch User data'];
        }

        // Log the raw response for debugging
        file_put_contents('response.log', $response);

        // Split the response by </br> and trim each part
        $jsonParts = array_map('trim', explode('</br>', $response));

        // Filter out any empty strings and invalid JSON formats
        $jsonObjects = array_filter($jsonParts, function($part) {
            return !empty($part) && self::is_json($part); // Call the method within the same class
        });

        // If no valid JSON objects were found
        if (empty($jsonObjects)) {
            return ['error' => 'No valid JSON objects found'];
        }

        // Join the valid JSON parts into a single JSON array
        $jsonString = '[' . implode(',', $jsonObjects) . ']';

        // Decode the JSON
        $data = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Invalid JSON format: ' . json_last_error_msg()];
        }

        return $data;
    }
    private static function is_json($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    public static function fetchUserActive()
    {
        $apiUrl = "http://172.16.22.204/ReportsNew/api/all_fac.php";

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => ['ignore_errors' => true, 'timeout' => 120]
        ]));

        if (!$response) {
            return ['error' => 'Failed to fetch User data'];
        }

        // Log the raw response for debugging
        file_put_contents('response.log', $response);

        // Split the response by </br> and trim each part
        $jsonParts = array_map('trim', explode('</br>', $response));

        // Filter out any empty strings and invalid JSON formats
        $jsonObjects = array_filter($jsonParts, function($part) {
            return !empty($part) && self::is_json($part); // Call the method within the same class
        });

        // If no valid JSON objects were found
        if (empty($jsonObjects)) {
            return ['error' => 'No valid JSON objects found'];
        }

        // Join the valid JSON parts into a single JSON array
        $jsonString = '[' . implode(',', $jsonObjects) . ']';

        // Decode the JSON
        $data = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Invalid JSON format: ' . json_last_error_msg()];
        }

        return $data;
    }
    public static function fetchBroker() {
        $apiUrl = "http://172.16.22.204/ReportsNew/api/broker_code.php";

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => ['ignore_errors' => true, 'timeout' => 120]
        ]));
        //dd( $response);

        if ($response === false) {
            return ['error' => 'Failed to fetch Broker Code'];
        }

        // Log the raw response for debugging
        file_put_contents('response_log.txt', $response);

        // Split the response into an array if multiple objects are returned
        $responseArray = explode('</br>', $response);
        
        // Initialize an array to hold the decoded data
        $data = [];
        
        foreach ($responseArray as $item) {
            $jsonItem = json_decode($item, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data[] = $jsonItem;
            }
        }

        if (empty($data)) {
            return ['error' => 'No valid JSON objects found'];
        }

        return $data;
    }

    public static function  fetchBusinessClasses() {
        $apiUrl = "http://172.16.22.204/ReportsNew/api/UW_BSCLASS.php";

        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => ['ignore_errors' => true, 'timeout' => 120]
        ]));
               
        if ($response === false) {
            return ['error' => 'Failed to fetch Broker Code'];
        }

        // Log the raw response for debugging
        file_put_contents('response_log.txt', $response);

        // Split the response into an array if multiple objects are returned
        $responseArray = explode('</br>', $response);
        
        // Initialize an array to hold the decoded data
        $data = [];
        
        foreach ($responseArray as $item) {
            $jsonItem = json_decode($item, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data[] = $jsonItem;
            }
        }

        if (empty($data)) {
            return ['error' => 'No valid JSON objects found'];
        }

        return $data;
    }
    public static function fetchRenewal()
    {
        $apiUrl = "http://172.16.22.204/dashboardApi/broker/module/broker_renewal.php?expiryfrom=01-Jan-2025&expiryto=30-Apr-2025&broker=4100100035";
        
        // Extract the current broker code from the URL
        $parsedUrl = parse_url($apiUrl);
        parse_str($parsedUrl['query'], $queryParams);
        $currentBrokerCode = $queryParams['broker'] ?? null;
        
        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => ['ignore_errors' => true, 'timeout' => 120]
        ]));

        if ($response === false) {
            return [
                'error' => 'Failed to fetch Renewal Data',
                'current_broker' => $currentBrokerCode 
            ];
        }

        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => 'Invalid JSON format',
                'current_broker' => $currentBrokerCode
            ];
        }

        return [
            'data' => $data,
            'current_broker' => $currentBrokerCode
        ];
    }
    public static function fetchReinsuranceNotesData($startDate = null, $endDate = null) 
    {
        // Set default dates (last 3 months) if none provided
        if (!$startDate || !$endDate) {
        $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');
     }


        // Format dates as required by API
        $formattedStart = Carbon::parse($startDate)->format('d-M-Y');
        $formattedEnd = Carbon::parse($endDate)->format('d-M-Y');
        //dd($formattedEnd);
      // dd($formattedStart);

        // Build API URL
        $apiUrl = "http://172.16.22.204/dashboardApi/reins/rqn/get_notes.php?expiryfrom={$formattedStart}&expiryto={$formattedEnd}";

        //dd($apiUrl);

        // Make the API request
        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120
            ]
        ]));

        // Handle API errors
        if ($response === false) {
            return [
                'error' => 'API request failed',
                'status' => 'error',
                'details' => error_get_last()['message'] ?? 'Unknown error'
            ];
        }

        // Clean the response by removing PHP notices
        $cleanResponse = preg_replace('/<br \/>\n<b>Notice<\/b>:.*?<br \/>\n/', '', $response);

        // Decode the cleaned response
        $initialData = json_decode($cleanResponse, true);
       // dd($initialData );
        // Handle JSON decode errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => 'Invalid JSON format in response',
                'status' => 'error',
                'raw_response' => substr($cleanResponse, 0, 500)
            ];
        }

        // Process the data
        $processedData = [];
        if (is_array($initialData)) {
            foreach ($initialData as $item) {
                if (is_string($item)) {
                    $decodedItem = json_decode($item, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $processedData[] = $decodedItem;
                    }
                } elseif (is_array($item)) {
                    $processedData[] = $item;
                }
            }
        }
//        return response()->json($processedData);
//             echo "<pre>";
// print_r($processedData);
// echo "</pre>";
// exit;

     //   dd($processedData);

        return [
            'data' => $processedData,
            'status' => 'success',
            'api_url' => $apiUrl,
            'api_expiry_from' => $formattedStart,
            'api_expiry_to' => $formattedEnd
        ];
    }
    public static function getRequestNotesByDocument($documentNumber)
        {
            if (empty($documentNumber)) {
                return [];
            }

            // Build API URL for document-based request notes
            $apiUrl = "http://172.16.22.204/dashboardApi/reins/rqn/get_notes_uw.php?uw_doc=" . urlencode($documentNumber);
            
            // Make the API request
            $response = @file_get_contents($apiUrl, false, stream_context_create([
                'http' => [
                    'ignore_errors' => true,
                    'timeout' => 120
                ]
            ]));
            // dd($response);
            // Handle API errors
            if ($response === false) {
                Log::error("Failed to fetch request notes for document: " . $documentNumber);
                return [];
            }

            // Clean the response by removing PHP notices
            $cleanResponse = preg_replace('/<br \/>\n<b>Notice<\/b>:.*?<br \/>\n/', '', $response);

            // Decode the cleaned response
            $data = json_decode($cleanResponse, true);
            //dd($data);

            // Handle JSON decode errors
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
    public static function fetchRenewalData($startDate = null, $endDate = null) 
    {
            // Set default dates (last 3 months) if none provided
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->format('Y-m-d');
            $endDate = Carbon::now()->addDays(30)->format('Y-m-d');
        }


        // Format dates as required by API
        $formattedStart = Carbon::parse($startDate)->format('d-M-Y');
        $formattedEnd = Carbon::parse($endDate)->format('d-M-Y');

        // Build API URL
        $apiUrl = "http://172.16.22.204/dashboardApi/uw/renewal.php?expiryfrom={$formattedStart}&expiryto={$formattedEnd}&dept=All&branch=All&takaful=All";

        // Make the API request
        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120
            ]
        ]));
       // dd($response);

        // Handle API errors
        if ($response === false) {
            return [
                'error' => 'API request failed',
                'status' => 'error',
                'details' => error_get_last()['message'] ?? 'Unknown error'
            ];
        }

        // Clean the response by removing PHP notices
        $cleanResponse = preg_replace('/<br \/>\n<b>Notice<\/b>:.*?<br \/>\n/', '', $response);

        // Decode the cleaned response
        $initialData = json_decode($cleanResponse, true);

                //   echo "<pre>";
                // print_r($initialData);
                // echo "</pre>";
                // exit;

        // Process the data
        $processedData = [];
        if (is_array($initialData)) {
            foreach ($initialData as $item) {
                if (is_string($item)) {
                    $decodedItem = json_decode($item, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $processedData[] = $decodedItem;
                    }
                } elseif (is_array($item)) {
                    $processedData[] = $item;
                }
            }
        }

        return [
            'data' => $processedData,
            'status' => 'success',
            'api_url' => $apiUrl,
            'api_expiry_from' => $formattedStart,
            'api_expiry_to' => $formattedEnd
        ];
    }
    // public static function getOutstandingData($startDate = null, $endDate = null)
    // {
    //     // Default: today to 30 days later if not provided
    
    //     if (!$startDate || !$endDate) {
    //     $startDate = Carbon::now()->startOfYear()->format('Y-m-d'); // 01-Jan-2025
    //     $endDate = Carbon::now()->format('Y-m-d'); // today's date
    //     }


    //     // Format to API format: 01-Jan-2025
    //     $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
    //     $dateTo = Carbon::parse($endDate)->format('d-M-Y');
    //     //dd( $dateTo);

    //     // Build your API URL using correct param names
    //     $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/br_col.php?datefrom={$dateFrom}&dateto={$dateTo}&dept=All&branch=All&takaful=All";
    //     //dd($apiUrl);

    //     // Call the API
    //     $response = @file_get_contents($apiUrl, false, stream_context_create([
    //         'http' => [
    //             'ignore_errors' => true,
    //             'timeout' => 120
    //         ]
    //     ]));
    //     //dd($response);

    //     // If the request fails
    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //     error_log('JSON Error: ' . json_last_error_msg());
    //     return [
    //         'status' => 'error',
    //         'message' => 'Invalid JSON',
    //         'raw_response' => substr($cleanResponse, 0, 500)
    //     ];
    //     }

    //     // Clean potential PHP warnings from HTML
    //     $cleanResponse = preg_replace('/<br \/>\n<b>.*?<\/b>:.*?<br \/>\n/', '', $response);

    //     // Decode JSON
    //     $data = json_decode($cleanResponse, true);

    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         return [
    //             'status' => 'error',
    //             'message' => 'Invalid JSON',
    //             'raw_response' => substr($cleanResponse, 0, 500)
    //         ];
    //     }

    //     return [
    //         'status' => 'success',
    //         'data' => $data,
    //         'api_url' => $apiUrl,
    //         'datefrom' => $dateFrom,
    //         'dateto' => $dateTo
    //     ];
    // } dynamic api before dept
        public static function getOutstandingData($startDate = null, $endDate = null, $dept = 'All')
    {
        // Default: today to 30 days later if not provided
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d'); // 01-Jan-2025
            $endDate = Carbon::now()->format('Y-m-d'); // today's date
        }

        // Format to API format: 01-Jan-2025
        $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
        $dateTo = Carbon::parse($endDate)->format('d-M-Y');

        // Build your API URL using correct param names
        $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/br_col.php?datefrom={$dateFrom}&dateto={$dateTo}&dept={$dept}&branch=All&takaful=All";
        //dd($apiUrl);
        // Call the API
        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120
            ]
        ]));

        // If the request fails
        if ($response === false) {
            error_log('API Request Failed: ' . $apiUrl);
            return [
                'status' => 'error',
                'message' => 'API request failed',
                'raw_response' => ''
            ];
        }

        // Clean potential PHP warnings from HTML
        $cleanResponse = preg_replace('/<br \/>\n<b>.*?<\/b>:.*?<br \/>\n/', '', $response);

        // Decode JSON
        $data = json_decode($cleanResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON',
                'raw_response' => substr($cleanResponse, 0, 500)
            ];
        }

        return [
            'status' => 'success',
            'data' => $data,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo
        ];
    }
    public static function getOutstandingGroupData($startDate = null, $endDate = null)
    {
        // Default: today to 30 days later if not provided
    
        if (!$startDate || !$endDate) {
        $startDate = Carbon::now()->startOfYear()->format('Y-m-d'); // 01-Jan-2025
        $endDate = Carbon::now()->format('Y-m-d'); // today's date
        }


        // Format to API format: 01-Jan-2025
        $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
        $dateTo = Carbon::parse($endDate)->format('d-M-Y');
        //dd( $dateTo);

        // Build your API URL using correct param names
        $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/br_col.php?datefrom={$dateFrom}&dateto={$dateTo}&dept=All&branch=All&takaful=All";
        //dd($apiUrl);

        // Call the API
        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120
            ]
        ]));
        //dd($response);

        // If the request fails
        if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Error: ' . json_last_error_msg());
        return [
            'status' => 'error',
            'message' => 'Invalid JSON',
            'raw_response' => substr($cleanResponse, 0, 500)
        ];
                }

        // Clean potential PHP warnings from HTML
        $cleanResponse = preg_replace('/<br \/>\n<b>.*?<\/b>:.*?<br \/>\n/', '', $response);

        // Decode JSON
        $data = json_decode($cleanResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON',
                'raw_response' => substr($cleanResponse, 0, 500)
            ];
        }

        return [
            'status' => 'success',
            'data' => $data,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo
        ];
    }
    public static function getOutstandingGroupBranchData($startDate = null, $endDate = null)
    {
        // Default: today to 30 days later if not provided
    
        if (!$startDate || !$endDate) {
        $startDate = Carbon::now()->startOfYear()->format('Y-m-d'); // 01-Jan-2025
        $endDate = Carbon::now()->format('Y-m-d'); // today's date
        }


        // Format to API format: 01-Jan-2025
        $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
        $dateTo = Carbon::parse($endDate)->format('d-M-Y');
        //dd( $dateTo);

        // Build your API URL using correct param names
        $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/br_col.php?datefrom={$dateFrom}&dateto={$dateTo}&dept=All&branch=All&takaful=All";
        //dd($apiUrl);

        // Call the API
        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120
            ]
        ]));
        //dd($response);

        // If the request fails
        if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Error: ' . json_last_error_msg());
        return [
            'status' => 'error',
            'message' => 'Invalid JSON',
            'raw_response' => substr($cleanResponse, 0, 500)
        ];
     }

        $cleanResponse = preg_replace('/<br \/>\n<b>.*?<\/b>:.*?<br \/>\n/', '', $response);

        $data = json_decode($cleanResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON',
                'raw_response' => substr($cleanResponse, 0, 500)
            ];
        }

        return [
            'status' => 'success',
            'data' => $data,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo
        ];
    }
    public static function getOutstandingTimeline($startDate = null, $endDate = null)
    {
        // Default: today to 30 days later if not provided
    
        if (!$startDate || !$endDate) {
        $startDate = Carbon::now()->startOfYear()->format('Y-m-d'); // 01-Jan-2025
        $endDate = Carbon::now()->format('Y-m-d'); // today's date
        }


        // Format to API format: 01-Jan-2025
        $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
        $dateTo = Carbon::parse($endDate)->format('d-M-Y');
        //dd( $dateTo);

        // Build your API URL using correct param names
        $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/br_col.php?datefrom={$dateFrom}&dateto={$dateTo}&dept=All&branch=All&takaful=All";
        //dd($apiUrl);

        // Call the API
        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120
            ]
        ]));
        //dd($response);

        // If the request fails
        if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Error: ' . json_last_error_msg());
        return [
            'status' => 'error',
            'message' => 'Invalid JSON',
            'raw_response' => substr($cleanResponse, 0, 500)
        ];
     }

        // Clean potential PHP warnings from HTML
        $cleanResponse = preg_replace('/<br \/>\n<b>.*?<\/b>:.*?<br \/>\n/', '', $response);

        // Decode JSON
        $data = json_decode($cleanResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON',
                'raw_response' => substr($cleanResponse, 0, 500)
            ];
        }

        return [
            'status' => 'success',
            'data' => $data,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo
        ];
    }
    public static function getDoReport($startDate = null, $endDate = null)
    {
        // Default: today to 30 days later if not provided
    
        if (!$startDate || !$endDate) {
        $startDate = Carbon::now()->startOfYear()->format('Y-m-d'); // 01-Jan-2025
        $endDate = Carbon::now()->format('Y-m-d'); // today's date
        }


        // Format to API format: 01-Jan-2025
        $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
        $dateTo = Carbon::parse($endDate)->format('d-M-Y');
        //dd( $dateTo);

        // Build your API URL using correct param names
        $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/do_rep.php?datefrom={$dateFrom}&dateto={$dateTo}&dept=All&branch=All&takaful=All";
        //dd($apiUrl);
     // http://192.168.170.24/dashboardApi/branch_portal/reports/do_rep.php?datefrom=01-Jan-2025&dateto=10-Mar-2025&branch=All&takaful=All

        // Call the API
        $response = @file_get_contents($apiUrl, false, stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 120
            ]
        ]));
        //dd($response);

        // If the request fails
        if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Error: ' . json_last_error_msg());
        return [
            'status' => 'error',
            'message' => 'Invalid JSON',
            'raw_response' => substr($cleanResponse, 0, 500)
        ];
      }

        // Clean potential PHP warnings from HTML
        $cleanResponse = preg_replace('/<br \/>\n<b>.*?<\/b>:.*?<br \/>\n/', '', $response);

        // Decode JSON
        $data = json_decode($cleanResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid JSON',
                'raw_response' => substr($cleanResponse, 0, 500)
            ];
        }

        return [
            'status' => 'success',
            'data' => $data,
            'api_url' => $apiUrl,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo
        ];
    }

// public static function getBranchReport($startDate = null, $endDate = null, $branch, $takaful)
// {
//     // Default date handling
//     if (!$startDate || !$endDate) {
//         $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
//         $endDate = Carbon::now()->format('Y-m-d');
//     }

//     // Format dates in API-expected format
//     $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
//     $dateTo = Carbon::parse($endDate)->format('d-M-Y');
//    // dd($takaful);

//     // Build API URL with proper parameter encoding
//     $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/level2/clm_reg.php?" . http_build_query([
//         'datefrom' => $dateFrom,
//         'dateto' => $dateTo,
//         'branch' => $branch,
//         'takaful' => $takaful
//     ]);
//    // dd( $apiUrl);
    
//     try {
//         // Use Guzzle for more reliable HTTP requests
//         $client = new \GuzzleHttp\Client();
//         $response = $client->get($apiUrl, [
//             'timeout' => 120,
//             'headers' => [
//                 'Accept' => 'application/json',
//             ]
//         ]);

//         // Get the raw response content
//         $content = $response->getBody()->getContents();

//         // Clean the response if needed (remove PHP errors/warnings)
//         $cleanContent = preg_replace('/<br \/>\n<b>.*?<\/b>:.*?<br \/>\n/', '', $content);

//         // Decode JSON response
//         $data = json_decode($cleanContent, true);

//         // Verify if data exists
//         if (empty($data)) {
//             return new \Illuminate\Support\Collection();
//         }

//         // Return as Collection
//         return new \Illuminate\Support\Collection($data);

//     } catch (\Exception $e) {
//         // Log the error for debugging
//         \Log::error('API Request Failed: ' . $e->getMessage(), [
//             'url' => $apiUrl,
//             'params' => [
//                 'dateFrom' => $dateFrom,
//                 'dateTo' => $dateTo,
//                 'branch' => $branch,
//                 'takaful' => $takaful
//             ]
//         ]);

//         return new \Illuminate\Support\Collection();
//     }
// }

 public static function getBranchReport($startDate = null, $endDate = null, $branch = null, $takaful = null)
{
    if (!$startDate || !$endDate) {
        $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');
    }

    $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
    $dateTo = Carbon::parse($endDate)->format('d-M-Y');

    // Build query parameters dynamically
    $queryParams = [
        'datefrom' => $dateFrom,
        'dateto' => $dateTo,
    ];
    
    // Only add branch and takaful if they have values
    if ($branch !== null && $branch !== '') {
        $queryParams['branch'] = $branch;
    }
    
    if ($takaful !== null && $takaful !== '') {
        $queryParams['takaful'] = $takaful;
    }
    //dd($takaful);

    // Build API URL with dynamic parameters
    $apiUrl = "http://172.16.22.204/dashboardApi/branch_portal/reports/level2/clm_reg.php?" . http_build_query($queryParams);
//dd($apiUrl);
    // Call the API
    $response = @file_get_contents($apiUrl, false, stream_context_create([
        'http' => [
            'ignore_errors' => true,
            'timeout' => 120
        ]
    ]));

    if ($response === false) {
        return [
            'status' => 'error',
            'message' => 'API request failed',
            'api_url' => $apiUrl
        ];
    }

    $cleanResponse = preg_replace('/<br \/>\n<b>.*?<\/b>:.*?<br \/>\n/', '', $response);
    $data = json_decode($cleanResponse, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'status' => 'error',
            'message' => 'Invalid JSON',
            'raw_response' => substr($cleanResponse, 0, 500),
            'api_url' => $apiUrl
        ];
    }

    // Parse the takaful value from the API URL if not in response
    $urlComponents = parse_url($apiUrl);
    parse_str($urlComponents['query'], $queryParams);
    
    // Priority: 1. API response value, 2. URL parameter, 3. Original parameter
    $finalBranch = $data['branch'] ?? $queryParams['branch'] ?? $branch;
    $finalTakaful = $data['takaful'] ?? $queryParams['takaful'] ?? $takaful;

    return [
        'status' => 'success',
        'data' => $data,
        'api_url' => $apiUrl,
        'params' => [
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
            'branch' => $finalBranch,
            'takaful' => $finalTakaful
        ]
    ];
}

public static function getReinsuranceR1Report($startDate = null, $endDate = null)
{
    if (!$startDate || !$endDate) {
        $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');
    }

    // Format dates for the API
    $dateFrom = Carbon::parse($startDate)->format('d-M-Y'); // Format: 01-Jan-2025
    $dateTo = Carbon::parse($endDate)->format('d-M-Y'); // Format: 30-Jul-2025

    // Log the dates
    error_log("Date From: $dateFrom, Date To: $dateTo");

    $apiUrl = "http://172.16.22.204/dashboardApi/reins/rqn/os/acpt_no.php?expiryfrom={$dateFrom}&expiryto={$dateTo}";

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

 
    return [
        'status' => 'success',
        'data' => $data,
        'api_url' => $apiUrl,
        'datefrom' => $dateFrom,
        'dateto' => $dateTo
    ];
}
public static function getReinsuranceR2Report($startDate = null, $endDate = null)
{
    if (!$startDate || !$endDate) {
        $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');
    }

    // Format dates for the API
    $dateFrom = Carbon::parse($startDate)->format('d-M-Y'); // Format: 01-Jan-2025
    $dateTo = Carbon::parse($endDate)->format('d-M-Y'); // Format: 30-Jul-2025

    // Log the dates
    error_log("Date From: $dateFrom, Date To: $dateTo");

    $apiUrl = "http://172.16.22.204/dashboardApi/reins/rqn/os/req_note.php?expiryfrom={$dateFrom}&expiryto={$dateTo}";

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

    return [
        'status' => 'success',
        'data' => $data,
        'api_url' => $apiUrl,
        'datefrom' => $dateFrom,
        'dateto' => $dateTo
    ];
}
//
//
// public static function getReinsuranceCase(
//     $startDate = null,
//     $endDate = null,
//     $sum = 10000000,
//     $bus = 'All',
//     $dept = 'All',
//     $broker = 'All',
//     $clientType = 'All'
// ) {
//     // Set default start and end dates if not provided
//     if (!$startDate || !$endDate) {
//         $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
//         $endDate   = Carbon::now()->format('Y-m-d');
//     }

//     // Format dates for the API (e.g., 01-Jan-2025)
//     $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
//     $dateTo   = Carbon::parse($endDate)->format('d-M-Y');

//     // Log dates for debugging
//     error_log("Date From: $dateFrom, Date To: $dateTo");

//     // Build query parameters dynamically
//     $params = [
//         'datefrom'    => $dateFrom,
//         'dateto'      => $dateTo,
//         'sum'         => $sum,
//         'bus'         => $bus,
//         'dept'        => $dept,
//         'broker'      => $broker,
//         'client_type' => $clientType
//     ];

//     // Construct the API URL
//     $apiUrl = "http://172.16.22.204/dashboardApi/reins/rqn/get_uwDocs.php?" . http_build_query($params);

//     // Call the API
//     $response = @file_get_contents($apiUrl, false, stream_context_create([
//         'http' => [
//             'ignore_errors' => true,
//             'timeout'       => 120
//         ]
//     ]));

//     if ($response === false) {
//         error_log('API Request Failed');
//         return [
//             'status'  => 'error',
//             'message' => 'API Request Failed'
//         ];
//     }

//     // Log raw API response for debugging
//     error_log("Raw API Response: $response");
         

//     // Decode JSON
//     $decodedResponse = json_decode($response, true);

//     if (json_last_error() !== JSON_ERROR_NONE) {
//         return [
//             'status'        => 'error',
//             'message'       => 'Invalid JSON',
//             'raw_response'  => substr($response, 0, 500)
//         ];
//     }

//     // If API returns an array of JSON strings, decode each item
//     $data = array_map('json_decode', $decodedResponse);
// //              echo "<pre>";
// // print_r($data);
// // echo "</pre>";
// // exit;

//     return [
//         'status'    => 'success',
//         'data'      => $data,
//         'api_url'   => $apiUrl,
//         'datefrom'  => $dateFrom,
//         'dateto'    => $dateTo
//     ];
// } make it dymic 
// public static function getReinsuranceCase(
//     $startDate = null,
//     $endDate = null,
//     $sum = 1000000000,
//     $bus = 'All',
//     $dept = 'All',
//     $broker = 'All',
//     $clientType = 'All',
//     $singleBranch = 'All',
//     $takaful = 'All'
// ) {
//     // Default dates if not provided
//     if (!$startDate || !$endDate) {
//         $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
//         $endDate   = Carbon::now()->format('Y-m-d');
//     }

//     // Format dates for the API
//     $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
//     $dateTo   = Carbon::parse($endDate)->format('d-M-Y');

   
//     $deptParam = $dept; 

//     // Build query parameters
//     $params = [
//         'datefrom'    => $dateFrom,
//         'dateto'      => $dateTo,
//         'sum'         => $sum,
//         'bus'         => $bus,
//         'dept'        => $deptParam,   
//         'broker'      => $broker,
//         'client_type' => $clientType,
//         'singleBranch'=> $singleBranch,
//         'takaful'     => $takaful,
//     ];

//     $apiUrl = "http://172.16.22.204/dashboardApi/reins/rqn/get_uwDocs.php?" . http_build_query($params);

//     // REMOVE dd() — it was stopping execution!
//     // dd($apiUrl);

//     error_log("Reinsurance API URL: $apiUrl");

//     $response = @file_get_contents($apiUrl, false, stream_context_create([
//         'http' => [
//             'ignore_errors' => true,
//             'timeout'       => 120,
//         ]
//     ]));

//     if ($response === false) {
//         error_log('Reinsurance API Request Failed: ' . $apiUrl);
//         return ['status' => 'error', 'message' => 'API Request Failed'];
//     }

//     error_log("Raw API Response (first 500): " . substr($response, 0, 500));

//    $decodedResponse = json_decode($response, true);
// //    echo '<pre>';
// // print_r($decodedResponse);
// // echo '</pre>';

// // dd($decodedResponse);

//     if (json_last_error() !== JSON_ERROR_NONE) {
//         return [
//             'status'       => 'error',
//             'message'      => 'Invalid JSON from API',
//             'raw_response' => substr($response, 0, 500),
//         ];
//     }

//     // API returns array of JSON strings — decode each item
//     $data = array_map('json_decode', $decodedResponse);

//     return [
//         'status'   => 'success',
//         'data'     => $data,
//         'api_url'  => $apiUrl,
//         'datefrom' => $dateFrom,
//         'dateto'   => $dateTo,
//     ];
// }
public static function getReinsuranceCase(
    $startDate = null,
    $endDate = null,
    $sum = 10000000,
    $bus = 'All',
    $dept = 'All',
    $broker = 'All',
    $clientType = 'All',
    $singleBranch = 'All',
    $takaful = 'All',
    $docType = 'P',
    $posted = 'Y',
    $insutype = 'takaful'
) {
    // Default dates
    $startDate = $startDate ?: Carbon::now()->startOfMonth()->format('Y-m-d');
    $endDate   = $endDate   ?: Carbon::now()->format('Y-m-d');

    $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
    $dateTo   = Carbon::parse($endDate)->format('d-M-Y');

    // API parameters
    $params = [
        'datefrom'     => $dateFrom,
        'dateto'       => $dateTo,
        'sum'          => $sum,
        'bus'          => $bus,
        'dept'         => $dept,
        'broker'       => $broker,
        'client_type'  => $clientType,
        'singleBranch' => $singleBranch,
        'takaful'      => $takaful,
        'doc_type'     => $docType,
        'posted'       => $posted,
        'insu_type'    => $insutype
    ];

    $apiUrl = "http://172.16.22.204/dashboardApi/reins/rqn/get_uwDocs.php?" . http_build_query($params);
    //dd($apiUrl);
    error_log("Reinsurance API URL: $apiUrl");

    $response = @file_get_contents($apiUrl, false, stream_context_create([
        'http' => [
            'ignore_errors' => true,
            'timeout'       => 120,
        ]
    ]));

    if ($response === false) {
        error_log('Reinsurance API Request Failed: ' . $apiUrl);
        return ['status' => 'error', 'message' => 'API Request Failed'];
    }

    $decodedResponse = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'status'       => 'error',
            'message'      => 'Invalid JSON from API',
            'raw_response' => substr($response, 0, 500),
        ];
    }

    // API returns array of JSON strings — decode each item
    $data = array_map('json_decode', $decodedResponse);

    return [
        'status'   => 'success',
        'data'     => $data,
        'api_url'  => $apiUrl,
        'datefrom' => $dateFrom,
        'dateto'   => $dateTo,
    ];
}
public static function getReinsuranceLastReport($startDate = null, $endDate = null)
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

   

 $apiUrl = "http://172.16.22.204/dashboardApi/reins/rqn/get_cp.php?datefrom={$dateFrom}&dateto={$dateTo}";
//$apiUrl = "http://172.16.22.204/dashboardApi/reins/rqn/get_cp.php?datefrom=01-Jan-2025&dateto=22-Jun-2025";

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
// public static function fetcloginandout()
// {
//     $apiUrl = "http://172.16.22.204/ReportsNew/api/login_logout.php";

//     $response = @file_get_contents($apiUrl, false, stream_context_create([
//         'http' => [
//             'ignore_errors' => true,
//             'timeout' => 120,
//         ]
//     ]));

//     if ($response === false) {
//         return [
//             'status' => 'error',
//             'message' => 'API Request Failed',
//             'data' => null
//         ];
//     }

//     $decoded = json_decode($response, true);

//     if ($decoded === null) {
//         return [
//             'status' => 'error',
//             'message' => 'Invalid JSON',
//             'data' => null
//         ];
//     }

//     return [
//         'status' => 'success',
//         'data' => $decoded
//     ];
// }
// public static function fetcloginandout()
// {
//     ini_set('memory_limit', '512M'); // increase memory limit

//     $apiUrl = "http://172.16.22.204/ReportsNew/api/login_logout.php";

//     $response = @file_get_contents($apiUrl, false, stream_context_create([
//         'http' => [
//             'ignore_errors' => true,
//             'timeout' => 300,
//         ]
//     ]));

//     if ($response === false) {
//         return [
//             'status' => 'error',
//             'message' => 'API Request Failed',
//             'data' => null
//         ];
//     }

//     $decoded = json_decode($response, true);

//     if ($decoded === null) {
//         return [
//             'status' => 'error',
//             'message' => 'Invalid JSON',
//             'data' => null
//         ];
//     }

//     return [
//         'status' => 'success',
//         'data' => $decoded
//     ];
// }
public static function fetcloginandout($startDate = null, $endDate = null)
{
    ini_set('memory_limit', '512M');

    // Default dates to today if not provided
    if (!$startDate || !$endDate) {
        $startDate = Carbon::now()->format('Y-m-d');
        $endDate   = Carbon::now()->format('Y-m-d');
    }

    // Format dates for API
    $dateFrom = Carbon::parse($startDate)->format('d-M-Y');
    $dateTo   = Carbon::parse($endDate)->format('d-M-Y');

    // Build API URL
    $apiUrl = "http://172.16.22.204/ReportsNew/api/login_logout.php?datefrom={$dateFrom}&dateto={$dateTo}";

    // Call API
    $response = @file_get_contents($apiUrl, false, stream_context_create([
        'http' => [
            'ignore_errors' => true,
            'timeout' => 300,
        ]
    ]));

    if (!$response) {
        return [
            'status' => 'error',
            'message' => 'Failed to fetch User data',
            'data' => null
        ];
    }

    // Log raw response for debugging
    file_put_contents('response.log', $response);

    // Split response by <br> variants
    $rows = preg_split('/<br\s*\/?>|<\/br>/', $response);

    $final = [];

    foreach ($rows as $row) {
        $row = trim($row);
        if (empty($row)) continue;

        // Decode only valid JSON
        $json = json_decode($row, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $final[] = $json;
        }
    }

    if (empty($final)) {
        return [
            'status' => 'error',
            'message' => 'No valid JSON found',
            'data' => null
        ];
    }
        

    return [
        'status' => 'success',
        'data' => $final
    ];
}



}






