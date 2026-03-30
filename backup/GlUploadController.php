<?php

namespace App\Http\Controllers;

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

                    $paidDocNums = DB::table('claim_docs') /// If this document number is already paid in the database  
                    // than we will remove it from API records , So payments that still need payment upload and only unpaid documents will be shown
                        ->where('paid', 'Y')
                        ->pluck('doc_num')
                        ->map(fn($d) => trim($d))
                        ->toArray();
// filter reord 
                    $records = array_values(
                        array_filter($apiResponse, function ($item) use ($paidDocNums) {
                            $docNum = trim($item['LVD_VCDTNARRATION1'] ?? '');
                            return !in_array($docNum, $paidDocNums, true);
                        })
                    );

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
        ]);

        try {
            $docNum   = trim($request->input('doc_num'));
            $file     = $request->file('gl_file');
            $origName = $file->getClientOriginalName();

            $storedPath = $file->storeAs('public/claims/' . $docNum, $origName);
            $fileName   = basename($storedPath);

            DB::table('claim_docs')->updateOrInsert(
                ['doc_num' => $docNum],
                [
                    'paid'         => 'Y',
                    'gl_file_name' => $fileName,
                    'updated_at'   => now(),
                ]
            );

            return response()->json([
                'success'   => true,
                'message'   => 'Claim Payment file uploaded successfully.',
                'file_name' => $fileName,
                'doc_num'   => $docNum,
                'id'        => $id,
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
        $request->validate(['doc_num' => 'required|string']);

        $docNum = trim($request->input('doc_num'));

        $row = DB::table('claim_docs')
            ->where('doc_num', $docNum)
            ->select('file_name', 'gl_file_name')
            ->first();

        if (!$row) {
            return response()->json([
                'success'   => false,
                'message'   => 'No record found for this document.',
                'file_name' => null,
                'file_url'  => null,
            ]);
        }

        $fileToShow = $row->gl_file_name ?: $row->file_name;

        if (!$fileToShow) {
            return response()->json([
                'success'   => false,
                'message'   => 'No file attached to this document.',
                'file_name' => null,
                'file_url'  => null,
            ]);
        }

        $storagePath = 'public/claims/' . $docNum . '/' . $fileToShow;

        if (!Storage::exists($storagePath)) {
            return response()->json([
                'success'   => false,
                'message'   => 'File not found on server: ' . $fileToShow,
                'file_name' => $fileToShow,
                'file_url'  => null,
            ]);
        }

        // Uses the serve route 
        $fileUrl = route('gl.upload.serve.file', [
            'docNum'   => $docNum,
            'fileName' => $fileToShow,
        ]);

        return response()->json([
            'success'   => true,
            'file_name' => $fileToShow,
            'file_url'  => $fileUrl,
            'doc_num'   => $docNum,
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

        // Force download if ?download=1, otherwise inline preview
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