<?php

namespace App\Http\Controllers;

use App\Models\ClaimDoc;
use App\Models\GLDoc; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class GlUploadController extends Controller
{
   public function index()
{
    $records  = [];
    $apiError = null;

    try {
        $response = Http::timeout(30)
            ->get('http://172.16.22.204/dashboardApi/clm/glOS.php');

        if ($response->successful()) {
            $apiResponse = $response->json();

            if (!empty($apiResponse) && is_array($apiResponse)) {

                // Group API response by document number
                $groupedByDoc = collect($apiResponse)->groupBy('LVD_VCDTNARRATION1');
                
                $records = [];
                
                foreach ($groupedByDoc as $docNum => $payments) {
                    $docNum = trim($docNum);
                    
                    $claimDoc = ClaimDoc::firstOrCreate(
                        ['doc_num' => $docNum],
                        [
                            'repname' => $payments[0]['PARTY_DESCRIPTION'] ?? '',
                            'paid' => 'N'
                        ]
                    );
                    
                    // if document is fully paid ignore it 
                    if ($claimDoc->paid === 'Y') {
                        continue;
                    }
                    
                    // Process each payment for current document
                    foreach ($payments as $payment) {
                        $voucherSerial = trim($payment['LVD_VCDTVOUCHSR'] ?? '');
                        $amount = (float)($payment['LSB_SDTLCRAMTFC'] ?? 0);
                        $code = trim($payment['PSA_SACTACCOUNT'] ?? '');
                        $partyType = trim($payment['PARTY_TYPE'] ?? '');
                        
                        // Include claim_doc_id in the unique check
                        $glDoc = GlDoc::firstOrCreate(
                            [
                                'doc_num' => $docNum,
                                'voucher_serial' => $voucherSerial,
                                'amount' => $amount,
                                'code' => $code,
                                'ledger_type' => $partyType,
                                'claim_doc_id' => $claimDoc->id, // prevent duplicates: API calls will only create records if they don't already exist for that specific claim_doc_id!
                            ],
                            [
                                'voucher_no' => $payment['LVH_VCHDNO'] ?? null,
                                'location_code' => $payment['PLC_LOCACODE'] ?? null,
                                'party_description' => $payment['PARTY_DESCRIPTION'] ?? null,
                                'created_at' => $payment['CREATED_AT'] ?? null,
                                'is_uploaded' => false
                            ]
                        );
                        
                        // Only add to records if not uploaded
                        if (!$glDoc->is_uploaded) {
                            $records[] = [
                                'LVD_VCDTNARRATION1' => $docNum,
                                'PARTY_DESCRIPTION' => $payment['PARTY_DESCRIPTION'] ?? 'N/A',
                                'PARTY_TYPE' => $partyType,
                                'CREATED_AT' => $payment['CREATED_AT'] ?? 'N/A',
                                'PSA_SACTACCOUNT' => $code,
                                'LSB_SDTLCRAMTFC' => $amount,
                                'LVD_VCDTVOUCHSR' => $voucherSerial,
                                'PLC_LOCACODE' => $payment['PLC_LOCACODE'] ?? '',
                                'LVH_VCHDNO' => $payment['LVH_VCHDNO'] ?? '',
                                'gl_doc_id' => $glDoc->id
                            ];
                        }
                    }
                }

            } else {
                $apiError = 'No records found.';
            }
        } else {
            Log::error('GL OS API error', ['status' => $response->status()]);
            $apiError = 'The server returned an unexpected error. Please try again later.';
        }
    } catch (\Illuminate\Http\Client\ConnectionException $e) {
        Log::error('GL OS connection failed', ['message' => $e->getMessage()]);
        $apiError = 'Connection timed out. Please try again later.';
    } catch (\Exception $e) {
        Log::error('GL OS unexpected error', ['message' => $e->getMessage()]);
        $apiError = 'An unexpected error occurred. Please contact support.';
    }

    return view('gl_upload.index', [
        'records'  => $records,
        'apiError' => $apiError,
    ]);
}

    public function upload(Request $request, string $id)
    {
        $request->validate([
            'gl_file' => 'required|file|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx|max:5120',
            'doc_num' => 'required|string',
            'gl_doc_id' => 'required|integer', 
            'voucher_serial' => 'required|string',
            'amount' => 'required|numeric',
            'code' => 'required|string',
            'party_type' => 'required|string', // Changed from ledger_type to party_type
        ]);

        try {
            $docNum = trim($request->input('doc_num'));
            $glDocId = $request->input('gl_doc_id');
            $voucherSerial = $request->input('voucher_serial');
            $amount = $request->input('amount');
            $code = $request->input('code');
            $partyType = $request->input('party_type'); // Now receiving party_type
            
            $file = $request->file('gl_file');
            $origName = $file->getClientOriginalName();

            // Store file
            $storedPath = $file->storeAs('public/claims/' . $docNum, $origName);
            $fileName = basename($storedPath);

            // Find the GL Doc record
            $glDoc = GlDoc::findOrFail($glDocId);
            
            // Verify it matches the request data
            if ($glDoc->doc_num !== $docNum || 
                $glDoc->voucher_serial !== $voucherSerial || 
                $glDoc->amount != $amount ||
                $glDoc->code !== $code ||
                $glDoc->ledger_type !== $partyType) { // Compare with party_type
                return response()->json([
                    'success' => false,
                    'message' => 'Payment data mismatch.',
                ], 400);
            }
            
            // Get username from session 
            $user = session('user', []);
            $userName = $user['name'] ?? 'System Administrator';
            
            $glDoc->markAsUploaded($fileName, $userName);
            
            // Get the parent document
            $claimDoc = $glDoc->claimDoc;
            
            // Calculate progress
            $totalPayments = $claimDoc->glDocs()->count();
            $uploadedPayments = $claimDoc->glDocs()->where('is_uploaded', true)->count();
            $remaining = $totalPayments - $uploadedPayments;
            
            
            // Prepare message
            if ($claimDoc->paid === 'Y') {
                $message = " All {$totalPayments} payments for document {$docNum} have been uploaded! Document is now fully paid.";
            } else {
                $message = "Payment uploaded. {$uploadedPayments} of {$totalPayments} payments completed. {$remaining} remaining.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'file_name' => $fileName,
                'doc_num' => $docNum,
                'id' => $id,
                'fully_paid' => $claimDoc->paid === 'Y',
                'remaining' => $remaining,
                'uploaded_count' => $uploadedPayments,
                'total_count' => $totalPayments
            ]);

        } catch (\Exception $e) {
            Log::error('GL upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function docInfo(Request $request)
    {
        $request->validate([
            'doc_num' => 'required|string',
        ]);

        $docNum = trim($request->input('doc_num'));

        // Find the document in claim_docs 
        $claimDoc = ClaimDoc::where('doc_num', $docNum)->first();

        if (!$claimDoc || !$claimDoc->file_name) {
            return response()->json([
                'success' => false,
                'message' => 'No document file found.',
                'file_name' => null,
                'file_url' => null,
            ]);
        }

        $fileToShow = $claimDoc->file_name;
        $storagePath = 'public/claims/' . $docNum . '/' . $fileToShow;

        if (!Storage::exists($storagePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Document file not found on server: ' . $fileToShow,
                'file_name' => $fileToShow,
                'file_url' => null,
            ]);
        }

        $fileUrl = route('gl.upload.serve.file', [
            'docNum' => $docNum,
            'fileName' => $fileToShow,
        ]);

        return response()->json([
            'success' => true,
            'file_name' => $fileToShow,
            'file_url' => $fileUrl,
            'doc_num' => $docNum,
        ]);
    }

    public function serveFile(Request $request, string $docNum, string $fileName)
    {
        $storagePath = 'public/claims/' . $docNum . '/' . $fileName;

        if (!Storage::exists($storagePath)) {
            abort(404, 'File not found.');
        }

        $fullPath = Storage::path($storagePath);
        $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

        if ($request->query('download') == '1') {
            return response()->download($fullPath, $fileName, [
                'Content-Type' => $mimeType,
            ]);
        }

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
    
    /**
     * Get payment status for a document
     */
    public function paymentStatus(string $docNum)
    {
        try {
            $claimDoc = ClaimDoc::where('doc_num', $docNum)->first();
            
            if (!$claimDoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }
            
            $total = $claimDoc->total_payments_count;
            $uploaded = $claimDoc->uploaded_count;
            
            return response()->json([
                'success' => true,
                'doc_num' => $docNum,
                'total_payments' => $total,
                'uploaded_payments' => $uploaded,
                'pending_payments' => $total - $uploaded,
                'is_fully_paid' => $claimDoc->is_fully_paid,
                'paid_status' => $claimDoc->paid
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * PAID REPORTS
     */
    public function paidReports()
    {
        $records = [];
        
        try {
            // Get ALL uploaded payments from gl_docs )
            $uploadedPayments = GLDoc::where('is_uploaded', 1)
                ->orderBy('doc_num')
                ->orderBy('voucher_serial')
                ->get();
            
            foreach ($uploadedPayments as $glDoc) {
                $records[] = [
                    'id' => $glDoc->id,
                    'doc_num' => $glDoc->doc_num,
                    'party_description' => $glDoc->party_description,
                    'party_type' => $glDoc->ledger_type, // This now contains Party Type
                    'code' => $glDoc->code,
                    'amount' => $glDoc->amount,
                    'voucher_serial' => $glDoc->voucher_serial,
                     'voucher_no' => $glDoc->voucher_no,
                    'gl_file_name' => $glDoc->gl_file_name,
                    'uploaded_at' => $glDoc->uploaded_at,
                    'uploaded_by' => $glDoc->uploaded_by,
                    'created_at' => $glDoc->created_at, // Include created date
                    'document_paid_status' => $glDoc->claimDoc->paid ?? 'N'
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Paid reports error: ' . $e->getMessage());
            return view('gl_upload.paid_reports', [
                'records' => [],
                'error' => 'Failed to load paid records.'
            ]);
        }
        
        return view('gl_upload.paid_reports', [
            'records' => $records,
            'error' => null
        ]);
    }
}