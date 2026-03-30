<?php

namespace App\Http\Controllers;

use App\Models\ClaimDoc;
use App\Models\GlDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\BranchesList;
use Illuminate\Support\Facades\DB;
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

                    $groupedByDoc = collect($apiResponse)->groupBy('LVD_VCDTNARRATION1');

                    foreach ($groupedByDoc as $docNum => $payments) {
                        $docNum = trim($docNum);

                        $claimDoc = ClaimDoc::firstOrCreate(
                            ['doc_num' => $docNum],
                            ['repname' => $payments[0]['PARTY_DESCRIPTION'] ?? '', 'paid' => 'N']
                        );

                        if ($claimDoc->paid === 'Y') continue;

                        foreach ($payments as $payment) {
                            $voucherSerial = trim($payment['LVD_VCDTVOUCHSR'] ?? '');
                            $amount        = (float)($payment['LSB_SDTLCRAMTFC'] ?? 0);
                            $code          = trim($payment['PSA_SACTACCOUNT'] ?? '');
                            $partyType     = trim($payment['PARTY_TYPE'] ?? '');

                            $glDoc = GlDoc::firstOrCreate(
                                [
                                    'doc_num'        => $docNum,
                                    'voucher_serial' => $voucherSerial,
                                    'amount'         => $amount,
                                    'code'           => $code,
                                    'ledger_type'    => $partyType,
                                    'claim_doc_id'   => $claimDoc->id,
                                ],
                                [
                                    'voucher_no'        => $payment['LVH_VCHDNO'] ?? null,
                                    'location_code'     => $payment['PLC_LOCACODE'] ?? null,
                                    'party_description' => $payment['PARTY_DESCRIPTION'] ?? null,
                                    'created_at'        => $payment['CREATED_AT'] ?? null,
                                     'dept'              => $payment['DEPT'] ?? null,
                                    'is_uploaded'       => false,
                                ]
                            );

                            if (!$glDoc->is_uploaded) {
                                $records[] = [
                                    'LVD_VCDTNARRATION1' => $docNum,
                                    'PARTY_DESCRIPTION'  => $payment['PARTY_DESCRIPTION'] ?? 'N/A',
                                    'PARTY_TYPE'         => $partyType,
                                    'CREATED_AT'         => $payment['CREATED_AT'] ?? 'N/A',
                                    'PSA_SACTACCOUNT'    => $code,
                                    'LSB_SDTLCRAMTFC'    => $amount,
                                    'LVD_VCDTVOUCHSR'    => $voucherSerial,
                                    'PLC_LOCACODE'       => $payment['PLC_LOCACODE'] ?? '',
                                    'LVH_VCHDNO'         => $payment['LVH_VCHDNO'] ?? '',
                                     'DEPT'               => $payment['DEPT'] ?? '',  
                                    'gl_doc_id'          => $glDoc->id,
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

                    $branches = BranchesList::all();
                    $branchMap = [];
                    foreach ($branches as $branch) {
                        if (!empty($branch->fbracode)) {
                            $branchMap[$branch->fbracode] = $branch->fbradsc;
                        }
                        if (!empty($branch->fbratak)) {
                            $branchMap[$branch->fbratak] = $branch->fbradsc;
                        }
                    }
        return view('gl_upload.index', [
            'records'  => $records,
            'apiError' => $apiError,
            'branchMap' => $branchMap,
        ]);
    }

    /* ─────────────────────────────────────────────────────────────────────
     | SINGLE UPLOAD
     | 
     ──────────────────────────────────────────────────────────────────── */
    // public function upload(Request $request, string $id)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'gl_file'     => 'required|file|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx|max:5120',
    //         'doc_num'     => 'required|string',
    //         'gl_doc_id'   => 'required|integer',
    //         'file_index'  => 'required|integer|min:0',
    //         'total_files' => 'required|integer|min:1',
    //     ]);

    //     if ($validator->fails()) {
    //         Log::error('GL single upload validation failed', [
    //             'errors'     => $validator->errors()->toArray(),
    //             'file_index' => $request->input('file_index'),
    //             'doc_num'    => $request->input('doc_num'),
    //             'gl_doc_id'  => $request->input('gl_doc_id'),
    //             'has_file'   => $request->hasFile('gl_file'),
    //         ]);
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation failed.',
    //             'errors'  => $validator->errors()->toArray(),
    //         ], 422);
    //     }

    //     try {
    //         $docNum      = trim($request->input('doc_num'));
    //         $glDocId     = (int) $request->input('gl_doc_id');
    //         $fileIndex   = (int) $request->input('file_index');
    //         $totalFiles  = (int) $request->input('total_files');
    //         $isFirstFile = ($fileIndex === 0);
    //         $isLastFile  = ($fileIndex === $totalFiles - 1);

    //         $glDoc    = GlDoc::findOrFail($glDocId);
    //         $origName = $this->storeFile($request->file('gl_file'), $docNum);

    //         $user     = session('user', []);
    //         $userName = $user['name'] ?? 'System Administrator';

    //         if ($isFirstFile) {
    //             // First file: mark row as uploaded + start gl_file_names array
    //             $glDoc->markAsUploaded($origName, $userName);
    //         } else {
    //             // Additional files: append to gl_file_names only
    //             $glDoc->appendFileName($origName);
    //         }

    //         // On last file: check if the entire ClaimDoc is now fully paid
    //         $fullyPaid = false;
    //         if ($isLastFile) {
    //             $claimDoc         = $glDoc->fresh()->claimDoc;
    //             $totalPayments    = $claimDoc->glDocs()->count();
    //             $uploadedPayments = $claimDoc->glDocs()->where('is_uploaded', true)->count();

    //             if ($totalPayments === $uploadedPayments) {
    //                 $claimDoc->update(['paid' => 'Y']);
    //                 $fullyPaid = true;
    //             }
    //         }

    //         return response()->json([
    //             'success'    => true,
    //             'message'    => 'File uploaded successfully.',
    //             'file_name'  => $origName,
    //             'doc_num'    => $docNum,
    //             'gl_doc_id'  => $glDocId,
    //             'file_index' => $fileIndex,
    //             'fully_paid' => $fullyPaid,
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error('GL single upload error: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Upload failed: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function upload(Request $request, string $id)
{
    $validator = Validator::make($request->all(), [
        'gl_file'     => 'required|file|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx|max:5120',
        'doc_num'     => 'required|string',
        'gl_doc_id'   => 'required|integer',
        'file_index'  => 'required|integer|min:0',
        'total_files' => 'required|integer|min:1',
    ]);

    if ($validator->fails()) {
        Log::error('GL single upload validation failed', [
            'errors'     => $validator->errors()->toArray(),
            'file_index' => $request->input('file_index'),
            'doc_num'    => $request->input('doc_num'),
            'gl_doc_id'  => $request->input('gl_doc_id'),
            'has_file'   => $request->hasFile('gl_file'),
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors'  => $validator->errors()->toArray(),
        ], 422);
    }

    try {
        $docNum      = trim($request->input('doc_num'));
        $glDocId     = (int) $request->input('gl_doc_id');
        $fileIndex   = (int) $request->input('file_index');
        $totalFiles  = (int) $request->input('total_files');
        $isFirstFile = ($fileIndex === 0);
        $isLastFile  = ($fileIndex === $totalFiles - 1);

        $glDoc = GlDoc::findOrFail($glDocId);
        
      
        $correctDept = null;
        try {
            $response = Http::timeout(10)
                ->get('http://172.16.22.204/dashboardApi/clm/glOS.php');
            
            if ($response->successful()) {
                $apiResponse = $response->json();
                foreach ($apiResponse as $item) {
                    if (($item['LVD_VCDTNARRATION1'] ?? '') == $docNum && 
                        ($item['LVD_VCDTVOUCHSR'] ?? '') == $glDoc->voucher_serial) {
                        $correctDept = $item['DEPT'] ?? null;
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch API for department update', [
                'doc_num' => $docNum,
                'error' => $e->getMessage()
            ]);
        }
        
        $origName = $this->storeFile($request->file('gl_file'), $docNum);
        $userName = session('user', [])['name'] ?? 'System Administrator';

        if ($isFirstFile) {
           
            $glDoc->markAsUploaded($origName, $userName);
        } else {
          
            $glDoc->appendFileName($origName);
        }
        
      
        if ($correctDept && $glDoc->dept != $correctDept) {
            Log::info('Updating department during upload from API', [
                'doc_num' => $docNum,
                'voucher_serial' => $glDoc->voucher_serial,
                'old_dept' => $glDoc->dept,
                'new_dept' => $correctDept
            ]);
          
            DB::table('gl_docs')
                ->where('id', $glDoc->id)
                ->update(['dept' => $correctDept]);
       
            $glDoc->refresh();
        }

       
        $fullyPaid = false;
        if ($isLastFile) {
            $claimDoc         = $glDoc->fresh()->claimDoc;
            $totalPayments    = $claimDoc->glDocs()->count();
            $uploadedPayments = $claimDoc->glDocs()->where('is_uploaded', true)->count();

            if ($totalPayments === $uploadedPayments) {
                $claimDoc->update(['paid' => 'Y']);
                $fullyPaid = true;
            }
        }

        return response()->json([
            'success'    => true,
            'message'    => 'File uploaded successfully.',
            'file_name'  => $origName,
            'doc_num'    => $docNum,
            'gl_doc_id'  => $glDocId,
            'file_index' => $fileIndex,
            'fully_paid' => $fullyPaid,
            'dept_updated' => isset($correctDept) ? true : false,
        ]);

    } catch (\Exception $e) {
        Log::error('GL single upload error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Upload failed: ' . $e->getMessage(),
        ], 500);
    }
}

    /* ─────────────────────────────────────────────────────────────────────
     | BULK UPLOAD
     | 
     ──────────────────────────────────────────────────────────────────── */
    // public function bulkUpload(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'gl_file'     => 'required|file|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx|max:5120',
    //         'doc_num'     => 'required|string',
    //         'file_index'  => 'required|integer|min:0',
    //         'total_files' => 'required|integer|min:1',
    //     ]);

    //     if ($validator->fails()) {
    //         Log::error('GL bulk upload validation failed', [
    //             'errors'     => $validator->errors()->toArray(),
    //             'file_index' => $request->input('file_index'),
    //             'doc_num'    => $request->input('doc_num'),
    //         ]);
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation failed.',
    //             'errors'  => $validator->errors()->toArray(),
    //         ], 422);
    //     }

    //     try {
    //         $docNum     = trim($request->input('doc_num'));
    //         $fileIndex  = (int) $request->input('file_index');
    //         $totalFiles = (int) $request->input('total_files');
    //         $isLastFile = ($fileIndex === $totalFiles - 1);

    //         $origName = $this->storeFile($request->file('gl_file'), $docNum);

    //         $user     = session('user', []);
    //         $userName = $user['name'] ?? 'System Administrator';

    //         // Accumulate filenames in session across multiple requests for this doc
    //         $sessionKey    = 'bulk_files_' . md5($docNum);
    //         $accumulated   = session($sessionKey, []);
    //         $accumulated[] = $origName;
    //         session([$sessionKey => $accumulated]);

    //         if ($isLastFile) {
    //             $allFileNames = $accumulated;
    //             $firstFile    = $allFileNames[0] ?? $origName;
    //             $now          = now();

    //             // Update each pending gl_doc row individually so the model
    //             // casts handle the gl_file_names array correctly
    //             GlDoc::where('doc_num', $docNum)
    //                 ->where('is_uploaded', false)
    //                 ->each(function ($glDoc) use ($firstFile, $allFileNames, $now, $userName) {
    //                     $glDoc->update([
    //                         'is_uploaded'   => true,
    //                         'gl_file_name'  => $firstFile,       // primary / first file
    //                         'gl_file_names' => $allFileNames,    // full array (model casts handle JSON)
    //                         'uploaded_at'   => $now,
    //                         'uploaded_by'   => $userName,
    //                     ]);
    //                 });

    //             // Mark parent ClaimDoc as fully paid
    //             ClaimDoc::where('doc_num', $docNum)->update(['paid' => 'Y']);

    //             // Clear session accumulator
    //             session()->forget($sessionKey);
    //         }

    //         return response()->json([
    //             'success'      => true,
    //             'message'      => $isLastFile
    //                 ? "Document {$docNum} marked as fully uploaded."
    //                 : 'File ' . ($fileIndex + 1) . " of {$totalFiles} stored.",
    //             'file_name'    => $origName,
    //             'doc_num'      => $docNum,
    //             'is_last_file' => $isLastFile,
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error('GL bulk upload error: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Upload failed: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }
public function bulkUpload(Request $request)
{
    $validator = Validator::make($request->all(), [
        'gl_file'     => 'required|file|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx|max:5120',
        'doc_num'     => 'required|string',
        'file_index'  => 'required|integer|min:0',
        'total_files' => 'required|integer|min:1',
    ]);

    if ($validator->fails()) {
        Log::error('GL bulk upload validation failed', [
            'errors'     => $validator->errors()->toArray(),
            'file_index' => $request->input('file_index'),
            'doc_num'    => $request->input('doc_num'),
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors'  => $validator->errors()->toArray(),
        ], 422);
    }

    try {
        $docNum     = trim($request->input('doc_num'));
        $fileIndex  = (int) $request->input('file_index');
        $totalFiles = (int) $request->input('total_files');
        $isLastFile = ($fileIndex === $totalFiles - 1);

        $origName = $this->storeFile($request->file('gl_file'), $docNum);

        $user     = session('user', []);
        $userName = $user['name'] ?? 'System Administrator';

        
        $sessionKey    = 'bulk_files_' . md5($docNum);
        $accumulated   = session($sessionKey, []);
        $accumulated[] = $origName;
        session([$sessionKey => $accumulated]);

        if ($isLastFile) {
            $allFileNames = $accumulated;
            $firstFile    = $allFileNames[0] ?? $origName;
            $now          = now();
            
           
            $apiDepts = [];
            try {
                $response = Http::timeout(10)
                    ->get('http://172.16.22.204/dashboardApi/clm/glOS.php');
                
                if ($response->successful()) {
                    $apiResponse = $response->json();
                    foreach ($apiResponse as $item) {
                        if (($item['LVD_VCDTNARRATION1'] ?? '') == $docNum) {
                            $voucherSerial = trim($item['LVD_VCDTVOUCHSR'] ?? '');
                            $apiDepts[$voucherSerial] = $item['DEPT'] ?? null;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch API for bulk department update', [
                    'doc_num' => $docNum,
                    'error' => $e->getMessage()
                ]);
            }

            
            GlDoc::where('doc_num', $docNum)
                ->where('is_uploaded', false)
                ->each(function ($glDoc) use ($firstFile, $allFileNames, $now, $userName, $apiDepts) {
                    
                    $updateData = [
                        'is_uploaded'   => true,
                        'gl_file_name'  => $firstFile,
                        'gl_file_names' => $allFileNames,
                        'uploaded_at'   => $now,
                        'uploaded_by'   => $userName,
                    ];
                    
                    
                    if (!empty($apiDepts) && isset($apiDepts[$glDoc->voucher_serial])) {
                        $correctDept = $apiDepts[$glDoc->voucher_serial];
                        if ($glDoc->dept != $correctDept) {
                            Log::info('Bulk upload: Updating department from API', [
                                'doc_num' => $glDoc->doc_num,
                                'voucher_serial' => $glDoc->voucher_serial,
                                'old_dept' => $glDoc->dept,
                                'new_dept' => $correctDept
                            ]);
                            $updateData['dept'] = $correctDept;
                        }
                    }
                    
                    $glDoc->update($updateData);
                });

            // Mark parent ClaimDoc as fully paid
            ClaimDoc::where('doc_num', $docNum)->update(['paid' => 'Y']);

            // Clear session accumulator
            session()->forget($sessionKey);
        }

        return response()->json([
            'success'      => true,
            'message'      => $isLastFile
                ? "Document {$docNum} marked as fully uploaded."
                : 'File ' . ($fileIndex + 1) . " of {$totalFiles} stored.",
            'file_name'    => $origName,
            'doc_num'      => $docNum,
            'is_last_file' => $isLastFile,
        ]);

    } catch (\Exception $e) {
        Log::error('GL bulk upload error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Upload failed: ' . $e->getMessage(),
        ], 500);
    }
}
    /* ─────────────────────────────────────────────────────────────────────
     | Shared helper — store file, auto-rename on collision
     ──────────────────────────────────────────────────────────────────── */
    private function storeFile($file, string $docNum): string
    {
        $origName    = $file->getClientOriginalName();
        $directory   = 'public/claims/' . $docNum;
        $storagePath = $directory . '/' . $origName;

        if (Storage::exists($storagePath)) {
            $ext      = pathinfo($origName, PATHINFO_EXTENSION);
            $baseName = pathinfo($origName, PATHINFO_FILENAME);
            $counter  = 1;
            do {
                $newName     = $baseName . '_' . $counter . '.' . $ext;
                $storagePath = $directory . '/' . $newName;
                $counter++;
            } while (Storage::exists($storagePath));
            $origName = $newName;
        }

        $file->storeAs($directory, $origName);
        return $origName;
    }

    /* ─────────────────────────────────────────────────────────────────────
     | Doc info 
     ──────────────────────────────────────────────────────────────────── */
    public function docInfo(Request $request)
{
    $request->validate([
        'doc_num' => 'required|string',
    ]);

    $docNum = trim($request->input('doc_num'));

    // Find the document in claim_docs 
    $claimDoc = ClaimDoc::where('doc_num', $docNum)->first();

    if (!$claimDoc) {
        return response()->json([
            'success' => false,
            'message' => 'Document not found.',
            'file_name' => null,
            'file_url' => null,
        ]);
    }

    // Check if the directory exists and get any files in it
    $directory = 'public/claims/' . $docNum;
    
    if (!Storage::exists($directory)) {
        return response()->json([
            'success' => false,
            'message' => 'No files found for this document.',
            'file_name' => null,
            'file_url' => null,
        ]);
    }

    // Get all files in the directory
    $files = Storage::files($directory);
    
    if (count($files) === 0) {
        return response()->json([
            'success' => false,
            'message' => 'No files found in the document directory.',
            'file_name' => null,
            'file_url' => null,
        ]);
    }

    // Use the first file found (you might want to sort by date or name)
    $firstFile = $files[0];
    $fileName = basename($firstFile);
    
    $fileUrl = route('gl.upload.serve.file', [
        'docNum' => $docNum,
        'fileName' => $fileName,
    ]);

    return response()->json([
        'success' => true,
        'file_name' => $fileName,
        'file_url' => $fileUrl,
        'doc_num' => $docNum,
        'message' => 'Found file: ' . $fileName,
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

    public function paymentStatus(string $docNum)
    {
        try {
            $claimDoc = ClaimDoc::where('doc_num', $docNum)->firstOrFail();
            $total    = $claimDoc->glDocs()->count();
            $uploaded = $claimDoc->glDocs()->where('is_uploaded', true)->count();

            return response()->json([
                'success'           => true,
                'doc_num'           => $docNum,
                'total_payments'    => $total,
                'uploaded_payments' => $uploaded,
                'pending_payments'  => $total - $uploaded,
                'is_fully_paid'     => $uploaded === $total,
                'paid_status'       => $claimDoc->paid,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

   public function paidReports()
{
    $records = [];
 
    try {
        $uploadedPayments = GlDoc::where('is_uploaded', true)
            ->orderBy('doc_num')
            ->orderBy('voucher_serial')
            ->get();
 
        foreach ($uploadedPayments as $glDoc) {
            $fileNames = $glDoc->gl_file_names;
 
            if (is_string($fileNames)) {
                $decoded   = json_decode($fileNames, true);
                $fileNames = is_array($decoded) ? $decoded : [$fileNames];
            } elseif (!is_array($fileNames) || empty($fileNames)) {
                $fileNames = $glDoc->gl_file_name ? [$glDoc->gl_file_name] : [];
            }
 
            $records[] = [
                'id'                   => $glDoc->id,
                'doc_num'              => $glDoc->doc_num,
                'party_description'    => $glDoc->party_description,
                'party_type'           => $glDoc->ledger_type,
                'code'                 => $glDoc->code,
                'amount'               => $glDoc->amount,
                'voucher_serial'       => $glDoc->voucher_serial,
                'voucher_no'           => $glDoc->voucher_no,
                'gl_file_name'         => $glDoc->gl_file_name,
                'gl_file_names'        => $fileNames,
                'uploaded_at'          => $glDoc->uploaded_at,
                'uploaded_by'          => $glDoc->uploaded_by,
                'created_at'           => $glDoc->created_at,
                'location_code'        => $glDoc->location_code ?? '',
                'dept'                 => $glDoc->dept ?? '',
                'document_paid_status' => $glDoc->claimDoc->paid ?? 'N',
            ];
        }
    } catch (\Exception $e) {
        Log::error('Paid reports error: ' . $e->getMessage());


        return view('gl_upload.paid_reports', [
            'records'   => [],
            'error'     => 'Failed to load paid records.',
            'branchMap' => $branchMap,
        ]);
    }

        $branches = BranchesList::all();
        $branchMap = [];
        foreach ($branches as $branch) {
            if (!empty($branch->fbracode)) {
                $branchMap[$branch->fbracode] = $branch->fbradsc;
            }
            if (!empty($branch->fbratak)) {
                $branchMap[$branch->fbratak] = $branch->fbradsc;
            }
        }
        
            return view('gl_upload.paid_reports', [
                'records'   => $records,
                'error'     => null,
                'branchMap' => $branchMap,
            ]);
        }



    public function servePaidFile(Request $request, string $docNum, string $fileName)
    {
        
        $docNum   = basename($docNum);
        $fileName = basename($fileName);
 
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
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }


    
}