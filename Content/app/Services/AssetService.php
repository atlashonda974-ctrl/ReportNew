<?php

namespace App\Services;

use Carbon\Carbon;
use Log;
class AssetService
{
    public function calculateAssetValues($item)
    {
        $openingAmount = floatval(str_replace(',', '', trim($item['Addition Cost'] ?? 0)));
        $depRate = floatval($item['Dep Rate'] ?? 0);
        $months = 13 - (Carbon::parse($item['Purchase Date'] ?? now())->month);
        
        // Calculate Opening Depreciation
        $openingDepreciation = ($openingAmount * ($depRate / 100)) / 12 * $months;
        $openingDepreciation = round($openingDepreciation, 2); 

        // Calculate Total Amount
        $totalAmount = $openingAmount; 

        // Calculate For the Year Depreciation
        $v = $totalAmount - $openingDepreciation;
        $forTheYearDepreciation = round($v * ($depRate / 100), 2) / 12 * 1;

        // Calculate Total Depreciation
        $totalDepreciation = $openingDepreciation + $forTheYearDepreciation;

        // Calculate W.D.V (Written Down Value)
        $wdv = round($totalAmount - $totalDepreciation, 2);

        // Add calculated values to item
        $item['Opening Depreciation'] = $openingDepreciation;
        $item['Total Amount'] = $totalAmount;
        $item['For the Year Depreciation'] = $forTheYearDepreciation;
        $item['Total Depreciation'] = $totalDepreciation;
        $item['W.D.V'] = $wdv;

        return $item;
    }

//     public function calculateAssetValues($item)
// {
//     $openingAmount = floatval(str_replace(',', '', trim($item['Addition Cost'] ?? 0)));
//     $depRate = floatval($item['Dep Rate'] ?? 0);

//     // Get the purchase month
//     $purchaseMonth = Carbon::parse($item['Purchase Date'] ?? now())->month;

//     // Calculate remaining months for Opening Depreciation (Including purchase month)
//     $monthsForOpeningDep = 13 - $purchaseMonth; // Includes purchase month

//     // Calculate Opening Depreciation
//     $openingDepreciation = ($openingAmount * ($depRate / 100)) / 12 * $monthsForOpeningDep;
//     $openingDepreciation = round($openingDepreciation, 2); 

//     // Calculate Total Amount
//     $totalAmount = $openingAmount; 

//     // Hard-coded value for For the Year Depreciation
//     $forTheYearDepreciation = 869.56; // Replace this with your desired hard-coded value

//     // Calculate Total Depreciation
//     $totalDepreciation = $openingDepreciation + $forTheYearDepreciation;

//     // Calculate W.D.V (Written Down Value)
//     $wdv = round($totalAmount - $totalDepreciation, 2);

//     // Add calculated values to item
//     $item['Opening Depreciation'] = $openingDepreciation;
//     $item['Total Amount'] = $totalAmount;
//     $item['For the Year Depreciation'] = $forTheYearDepreciation; // Use hard-coded value here
//     $item['Total Depreciation'] = $totalDepreciation;
//     $item['W.D.V'] = $wdv;

//     return $item;
// }


    


    

    // public function calculateAssetValues($item)
    // {
    //     $openingAmount = floatval(str_replace(',', '', trim($item['Addition Cost'] ?? 0)));
    //     $depRate = floatval($item['Dep Rate'] ?? 0);

    //     // Get the purchase month
    //     $purchaseMonth = Carbon::parse($item['Purchase Date'] ?? now())->month;
        
    //     // Calculate remaining months for Opening Depreciation (Including purchase month)
    //     $monthsForOpeningDep = 13 - $purchaseMonth;

    //     // Get the current month (Use current month for the year depreciation)
    //     $currentMonth = Carbon::now()->month;

    //     // Calculate Opening Depreciation
    //     $openingDepreciation = ($openingAmount * ($depRate / 100)) / 12 * $monthsForOpeningDep;
    //     $openingDepreciation = round($openingDepreciation, 2); 

    //     // Calculate For the Year Depreciation (Using one-line formula)
    //     $forTheYearDepreciation = (($openingAmount - $openingDepreciation) * ($depRate / 100)) / 12 * $currentMonth;
    //     $forTheYearDepreciation = round($forTheYearDepreciation, 2);

    //     // Calculate Total Depreciation
    //     $totalDepreciation = $openingDepreciation + $forTheYearDepreciation;

    //     // Calculate W.D.V (Written Down Value)
    //     $wdv = round($openingAmount - $totalDepreciation, 2);

    //     // Add calculated values to item
    //     $item['Opening Depreciation'] = $openingDepreciation;
    //     $item['Total Amount'] = $openingAmount;
    //     $item['For the Year Depreciation'] = $forTheYearDepreciation;
    //     $item['Total Depreciation'] = $totalDepreciation;
    //     $item['W.D.V'] = $wdv;

    //     return $item;
    // }.. now
//     public function calculateAssetValues($item)
// {
//     // Log the input item for debugging
//     //Log::debug('Calculating asset values for item:', $item);

//     // Safely retrieve values with defaults
//     $openingAmount = floatval(str_replace(',', '', trim($item['Addition Cost'] ?? '0')));
//     $depRate = floatval($item['Dep Rate'] ?? '0');
//     $purchaseDate = $item['Purchase Date'] ?? null;

//     // Check for missing Purchase Date
//     if (!$purchaseDate) {
//         Log::warning('Purchase Date is missing for item:', $item);
//         return $item; // You can handle this differently if needed
//     }

//     // Get the purchase month
//     $purchaseMonth = Carbon::parse($purchaseDate)->month;

//     // Calculate remaining months for Opening Depreciation (Including purchase month)
//     $monthsForOpeningDep = 13 - $purchaseMonth;

//     // Get the current month
//     $currentMonth = Carbon::now()->month;

//     // Calculate Opening Depreciation
//     $openingDepreciation = ($openingAmount * ($depRate / 100)) / 12 * $monthsForOpeningDep;
//     $openingDepreciation = round($openingDepreciation, 2);

//     // Calculate For the Year Depreciation
//     $forTheYearDepreciation = (($openingAmount - $openingDepreciation) * ($depRate / 100)) / 12 * $currentMonth;
//     $forTheYearDepreciation = round($forTheYearDepreciation, 2);

//     // Total Depreciation
//     $totalDepreciation = $openingDepreciation + $forTheYearDepreciation;

//     // Calculate W.D.V (Written Down Value)
//     $wdv = round($openingAmount - $totalDepreciation, 2);

//     // Add calculated values to item
//     $item['Opening Depreciation'] = $openingDepreciation;
//     $item['Total Amount'] = $openingAmount;
//     $item['For the Year Depreciation'] = $forTheYearDepreciation;
//     $item['Total Depreciation'] = $totalDepreciation;
//     $item['W.D.V'] = $wdv;

//     return $item;
// }
// public function calculateAssetValues($item)
// {
//     Log::debug('Calculating asset values for item:', $item);

//     // Safely retrieve values with defaults
//     $openingAmount = floatval(str_replace(',', '', trim($item['Addition Cost'] ?? '0')));
//     $depRate = floatval($item['Dep Rate'] ?? '0');
//     $purchaseDate = $item['Purchase Date'] ?? null;

//     // Check for missing Purchase Date
//     if (!$purchaseDate) {
//         Log::warning('Purchase Date is missing for item:', $item);
//         return $item; // You can handle this differently if needed
//     }

//     // Attempt to parse the purchase date with different formats
//     try {
//         $date = Carbon::createFromFormat('d-M-y', $purchaseDate);
//     } catch (\Exception $e) {
//         Log::error('Failed to parse Purchase Date:', ['date' => $purchaseDate, 'error' => $e->getMessage()]);
//         return $item; // Handle the error as needed
//     }

//     $purchaseMonth = $date->month;

//     // Calculate remaining months for Opening Depreciation (Including purchase month)
//     $monthsForOpeningDep = 13 - $purchaseMonth;

//     // Get the current month
//     $currentMonth = Carbon::now()->month;

//     // Calculate Opening Depreciation
//     $openingDepreciation = ($openingAmount * ($depRate / 100)) / 12 * $monthsForOpeningDep;
//     $openingDepreciation = round($openingDepreciation, 2);

//     // Calculate For the Year Depreciation
//     $forTheYearDepreciation = (($openingAmount - $openingDepreciation) * ($depRate / 100)) / 12 * $currentMonth;
//     $forTheYearDepreciation = round($forTheYearDepreciation, 2);

//     // Total Depreciation
//     $totalDepreciation = $openingDepreciation + $forTheYearDepreciation;

//     // Calculate W.D.V (Written Down Value)
//     $wdv = round($openingAmount - $totalDepreciation, 2);

//     // Add calculated values to item
//     $item['Opening Depreciation'] = $openingDepreciation;
//     $item['Total Amount'] = $openingAmount;
//     $item['For the Year Depreciation'] = $forTheYearDepreciation;
//     $item['Total Depreciation'] = $totalDepreciation;
//     $item['W.D.V'] = $wdv;

//     return $item;
// }



}