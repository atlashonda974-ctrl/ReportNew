<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // ✅ correct
use Carbon\Carbon;
use App\Helpers\POHelper;
use Illuminate\Support\Str;
use App\Models\BranchesList;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ReinsLog;
use App\Models\EmailLog;
use App\Models\VerifyLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReinsuranceRequestEmail;

class POController extends Controller

{


public function index(Request $request)
    {
        $startDate = $request->input('start_date') ?? Carbon::now()->startOfYear()->format('Y-m-d');
        $endDate = $request->input('end_date') ?? Carbon::now()->format('Y-m-d');
        $statusFilter = $request->input('status_filter', 'all');

        // Get data from helper
        $result = POHelper::getPO($startDate, $endDate);
        $data = collect($result['data'] ?? []);

        // Apply outstanding filter if selected
        if ($statusFilter === 'outstanding') {
            $data = $data->filter(function ($record) {
                $outstanding = ($record->GDH_GROSSPREMIUM ?? 0) - ($record->TOT_COL ?? 0);
                return $outstanding > 0;
            });
        }

        // Return view
        return view('PO.index', [
            'data' => $data,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status_filter' => $statusFilter,
            'api_date_range' => [
                'from' => Carbon::parse($startDate)->format('d-M-Y'),
                'to' => Carbon::parse($endDate)->format('d-M-Y'),
            ]
        ]);
    }
 

}

     
