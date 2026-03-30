<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ClaimDBHelper;

class ClaimDBController extends Controller
{
public function index(Request $request)
{
    $allParams = $request->all();

    // Fetch data from the ClaimDBHelper
    $result = ClaimDBHelper::getClaimS1($allParams);
    $result1 = ClaimDBHelper::getClaimS2($allParams);
    $result2 = ClaimDBHelper::getClaimS3($allParams);
    $result3 = ClaimDBHelper::getClaimS4($allParams);
    $result4 = ClaimDBHelper::getClaimS5($allParams);


 //dd($result4);
    $combinedData = [
        'Surveyor' => [],
        'Report' => [],
        'Stl' => [],
    ];

    
    foreach ($result3['data'] as $key => $data) {
        foreach ($data as $item) {
            $ageSegment = $item['AGE_SEGMENT'];
            $count = (int)$item['SEGMENT_COUNT']; 
            $combinedData[ucfirst(str_replace('os_', '', $key))][$ageSegment] = $count; 
        }
    }

    return response()->view('claimregister.claim7', [
        'apiData' => $result['data'],
        'apiData2' => $result2['data'],
        'combinedData' => $combinedData,
        'apiStatus' => $result1,
        'apiBIStatus' => $result4,
        'apiUrlUsed' => $result['api_url_used'] ?? null,
    ]);
}
}
