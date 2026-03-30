<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Str;



class BrokerCodeController extends Controller

{

public function index(Request $request)
{
    $brokerData = Helper::fetchBroker(); 
    $brokers = collect($brokerData);
    $brokers = $brokers->filter(function ($broker) {
        return $broker['PPS_STATUS'] === 'A';
    });

    if ($request->filled('supplier')) {
        $brokers = $brokers->where('PPS_PARTY_CODE', $request->supplier);
    }
    $filteredBrokers = $brokers->map(function ($broker) {
        return [
            'PPS_PARTY_CODE' => $broker['PPS_PARTY_CODE'],
            'PPS_DESC' => $broker['PPS_DESC'],
        ];
    });

    return view('code.index', [
        'data' => $filteredBrokers->values()->toArray(),
        'suppliers' => $brokers->pluck('PPS_PARTY_CODE')->unique(), 
    ]);
}
     
}
