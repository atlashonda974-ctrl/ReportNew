<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Services\AssetService;
use App\Services\DepreciationService;
use Illuminate\Support\Facades\Log;



class ReportController extends Controller
{
    protected $assetService;
    protected $depreciationService;

    public function __construct(AssetService $assetService, DepreciationService $depreciationService)
    {
        $this->assetService = $assetService;
        $this->depreciationService = $depreciationService;
    }

    public function index(Request $request)
    {
        $data = Helper::fetchData();

        if (isset($data['error'])) {
            return response()->json(['error' => $data['error']], 500);
        }

        $collection = collect($data);
        $locations = $collection->pluck('LOC')->unique()->values();
        $suppliers = $collection->pluck('SUPPLIER')->unique()->values();

        $categoriesData = Helper::fetchCategories();
        if (isset($categoriesData['error'])) {
            return response()->json(['error' => $categoriesData['error']], 500);
        }

        $categoryMapping = [
            'Computers' => '105',
            'Furniture and Fixtures' => '106',
            'Office Equipment' => '104',
            'Building' => '10201',
            'Vehicles' => '103'
        ];

        // Date Filtering
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $collection = $collection->filter(function ($item) use ($startDate, $endDate) {
                $date = Carbon::createFromFormat('d-M-y', $item['Date']);
                return $date->between($startDate, $endDate);
            });
        }

        // Supplier Filtering
        if ($request->filled('supplier')) {
            $collection = $collection->where('SUPPLIER', $request->supplier);
        }

        // Branch Filtering
        if ($request->filled('location')) {
            $collection = $collection->where('LOC', $request->location);
        }

        // Category Filtering
        if ($request->filled('category')) {
            $selectedCategory = $request->category;
            $allowedCode = $categoryMapping[$selectedCategory] ?? null;

            if ($allowedCode) {
                $collection = $collection->filter(function ($item) use ($allowedCode) {
                    return isset($item['ASSET']) && Str::startsWith($item['ASSET'], $allowedCode);
                });
            }
        }

        // Amount Filtering
        if ($request->filled('amount')) {
            $amountFilter = $request->amount;
            $collection = $collection->filter(function ($item) use ($amountFilter) {
                $amount = isset($item['AMOUNT']) ? floatval(str_replace(',', '', $item['AMOUNT'])) : 0;

                return ($amountFilter === 'above_25000' && $amount >= 25000) ||
                       ($amountFilter === 'below_25000' && $amount < 25000) ||
                       $amountFilter === null; 
            });
        }

        // Check if collection is not empty
        if ($collection->isEmpty()) {
            return response()->json(['message' => 'No records found.'], 404);
        }

        // Sort by Date in Descending Order
        $collection = $collection->sortByDesc(fn ($item) => Carbon::parse($item['Date']));
        //dd( $collection);

        return view('reports.index', [
            'data' => $collection,
            'locations' => $locations,
            'suppliers' => $suppliers,
            'categories' => collect($categoriesData)->pluck('FFM_ASSET_DESC')->values(),
        ]);
    }
 
    
    public function depreciation(Request $request)
    {
        $data = Helper::fetchDepreciation();

        if (isset($data['error'])) {
            \Log::error('Depreciation fetch error: ' . $data['error']);
            return response()->json(['message' => $data['error']], 500);
        }

        // Filter data by date if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $data = collect($data)->filter(function ($item) use ($startDate, $endDate) {
                $date = Carbon::createFromFormat('d-M-y', $item['Date of Sale']);
                return $date->between($startDate, $endDate);
            })->values()->all(); 
        }

        // Calculate totals using the service
        $totals = $this->depreciationService->calculateTotals($data);

        // Return Blade view with data and totals
        return view('reports.dep', array_merge($totals, ['data' => $data]));
    }

    
    public function transfer(Request $request)
    {
        $data = Helper::fetchTransfer();
        
        $collection = collect($data);
        //dd($collection);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            $collection = $collection->filter(function ($item) use ($startDate, $endDate) {
                $date = Carbon::createFromFormat('d-M-y', $item['Date of Purchase']);
                return $date->between($startDate, $endDate);
            });
        }

        return view('reports.transfer', ['data' => $collection]);
    }


    public function register(Request $request)
    {
        $data = Helper::fetchRegister();
        $collection = collect($data);

        // Calculate W.D.V for each item
        $collection = $collection->map(function ($item) {
            $calculatedItem = $this->assetService->calculateAssetValues($item);
            
            // Log the calculated item
            Log::info('Calculated Asset Values:', $calculatedItem);

            return $calculatedItem; // Ensure that we return the calculated item
        });

        // Date filtering
        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();

                $collection = $collection->filter(function ($item) use ($startDate, $endDate) {
                    try {
                        // Convert '20-MAR-25' to Carbon date object
                        $date = Carbon::createFromFormat('d-M-y', $item['Purchase Date']);
                        return $date->between($startDate, $endDate);
                    } catch (\Exception $e) {
                        return false; // Skip invalid date formats
                    }
                });
            } catch (\Exception $e) {
                Log::error('Date filtering error: '.$e->getMessage());
            }
        }

        // W.D.V filtering
        if ($request->filled('wdv')) {
            $wdvFilter = $request->wdv;

            $collection = $collection->filter(function ($item) use ($wdvFilter) {
                $wdv = $item['W.D.V'] ?? 0;
                switch ($wdvFilter) {
                    case 'less_5000':
                        return $wdv < 5000;
                    case 'between_5000_14999':
                        return $wdv >= 5000 && $wdv <= 14999;
                    case 'between_15000_24999':
                        return $wdv >= 15000 && $wdv <= 24999;
                    case 'above_25000':
                        return $wdv > 25000;
                    case 'greater_equal_5000':
                    default:
                        return $wdv >= 5000; // Default to show 5000 and above
                }
            });
        } else {
            // If no filter is applied, default to showing records of 5000 and above
            $collection = $collection->filter(function ($item) {
                return ($item['W.D.V'] ?? 0) >= 5000;
            });
        }

        return view('reports.register', [
            'data' => $collection,
        ]);
    }

}
