<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReinsuranceController;
use App\Http\Controllers\BrokerController;
use App\Http\Controllers\PremiumOutstandingController;
use App\Http\Controllers\UserReportController;
use App\Http\Controllers\UWController;
use App\Http\Controllers\UserActiveController; 
use App\Http\Controllers\RenewalController; 
use App\Http\Controllers\BrokerCodeController;
use App\Http\Controllers\RequestNoteController;
use App\Http\Controllers\OutstandingReportController;
use App\Http\Controllers\DashReport2Controller;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SurveyorEmailController;


use App\Http\Controllers\POController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\ClaimDBController;
use App\Http\Controllers\UserHealthController;
use App\Http\Controllers\GlUploadController;




















/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware'=>"web"], function(){

    Route::get('/', function () {
        return view('main');
    });


        //logout
        Route::get('logout', function () {
            Session::forget('user');
            return redirect('/login');
        });



    Route::match(['get', 'post'], '/login', [UserController::class, 'login']);
    Route::match(['get', 'post'], '/changePassword', [UserController::class, 'changePassword']);
    Route::match(['get', 'post'], '/makeHash', [UserController::class, 'makeHash']);

    Route::match(['get', 'post'], 'bank_salary', [SalaryController::class, 'bankSalary']);
    Route::match(['get', 'post'], 'bank_salary_export', [SalaryController::class, 'exportBankSalaryPrint']);
    Route::match(['get', 'post'], 'emp_salary', [SalaryController::class, 'empSalary']);

    Route::match(['get', 'post'], 'gisulog', [LogController::class, 'gisUserLog']);


    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/dep', [ReportController::class, 'depreciation']);           
    Route::get('/transfer', [ReportController::class, 'transfer']);
    Route::get('/register', [ReportController::class, 'register']);
    Route::post('/dep/history', [ReportController::class, 'addHistory'])->name('addHistory');

    Route::get('/reinsurace', [ReinsuranceController::class, 'index']);
    Route::get('/broker', [BrokerController::class, 'index']);
    Route::get('/premium', [PremiumOutstandingController::class, 'index']);
    Route::get('/user', [UserReportController::class, 'index']);
   // Route::get('/u_active', [UserActiveController::class, 'index']);
    Route::get('/renewal', [RenewalController::class, 'index']);
    Route::get('/code', [BrokerCodeController::class, 'index']);
Route::get('/logins-by-date', [UserActiveController::class, 'getLoginsByDate'])->name('logins.by.date');
Route::get('/logouts-by-date', [UserActiveController::class, 'getLogoutsByDate'])->name('logouts.by.date');
//
Route::get('/getnote', [RequestNoteController::class, 'index']);
Route::get('/getnote/document-numbers', [RequestNoteController::class, 'getDocumentNumbers']);




Route::get('/uw', [UWController::class, 'getRenewalData']);
Route::get('/os', [OutstandingReportController::class, 'getOutstandingData']);
Route::get('/osg', [OutstandingReportController::class, 'getOutstandingGroupData']);
Route::get('/osgd', [OutstandingReportController::class, 'getOutstandingGroupBranchData']);
Route::get('/ost', [OutstandingReportController::class, 'getOutstandingTimelineData']);

//

Route::get('/do', [DashReport2Controller::class, 'getOutDoData']);

//

Route::get('/do-os', [DashReport2Controller::class, 'getOutstandingData']);
Route::get('/do-osg', [DashReport2Controller::class, 'getOutstandingGroupData']);
Route::get('/do-osgd', [DashReport2Controller::class, 'getOutstandingGroupBranchData']);
Route::get('/do-ost', [DashReport2Controller::class, 'getOutstandingTimelineData']);
Route::get('/do-uw', [DashReport2Controller::class, 'getRenewalData']);

//
Route::get('/do-gd', [DashReport2Controller::class, 'getBranchReport']);
Route::post('/generate-request-note-pdf', [RequestNoteController::class, 'generatePDF'])->name('generate.request.note.pdf');


//
Route::get('/reports/pdf/{requestNote}', [RequestNoteController::class, 'generatePdf'])->name('reports.pdf');
Route::get('/reports/preview/{requestNote}', [RequestNoteController::class, 'previewPdf'])->name('reports.preview');
Route::get('/generate-pdf/{recordId}', [RequestNoteController::class, 'generatePdf'])->name('generate.pdf');


Route::post('/r1/export-pdf', [RequestNoteController::class, 'exportR1RowPdf'])->name('r1.export.pdf');
Route::get('/generate-request-note-pdf/{reqNote}', [RequestNoteController::class, 'generatePdf'])->name('generate.request.note.pdf');
Route::get('/email', [RequestNoteController::class, ' EmailLog']);

// Reinsurance Cases project
Route::get('/r1', [RequestNoteController::class, 'getReinsuranceR1Data']);
Route::post('/reins/upload', [RequestNoteController::class, 'storeUpload'])
     ->name('reins.upload.store');
Route::get('/r2', [RequestNoteController::class, 'getReinsuranceR2Data']);
Route::get('/c', [RequestNoteController::class, 'getReinsuranceCase']);
Route::get('/show', [RequestNoteController::class, 'show']);
Route::get('/getshow', [RequestNoteController::class, 'getshow']);
Route::get('/get-email-logs', [RequestNoteController::class, 'getEmailLogs'])->name('get.email.logs');
Route::get('/getlast', [RequestNoteController::class, 'getlast'])->name('reinsurance.getlast');
Route::post('/verify-record', [RequestNoteController::class, 'verifyRecord'])->name('verify.record');
Route::post('/send-email', [EmailController::class, 'sendEmail'])->name('send.email');
Route::post('/fetch-reinsurance-data', [RequestNoteController::class, 'fetchReinsuranceData'])->name('fetch.reinsurance.data');
//
Route::post('/save-remarks', [RequestNoteController::class, 'saveRemarks'])->name('save.remarks');
Route::post('/reinsurance/single-action', [RequestNoteController::class, 'singleAction'])
     ->name('reinsurance.single.action');
// Route::get('/reins/file/{refNo}/{filename}', [EmailController::class, 'serveUpload'])
//      ->name('reins.serve.file')
//      ->where('filename', '.*');
     Route::post('/reins-tag/store', [RequestNoteController::class, 'storeReinsTag'])->name( 'reins.tag.store');
     Route::get('/reins/file/{refNo}/{filename}', [EmailController::class, 'serveUpload'])
    ->name('reins.serve.file');


//Route::get('/r1/emailed', [ReportController::class, 'getReinsuranceR1EmailedData'])->name('r1.emailed');
//Route::get('/getlast', [RequestNoteController::class, 'getlast']);

//

Route::post('/upload-file', [RequestNoteController::class, 'uploadFile'])->name('upload.file');

Route::post('/send-and-verify-email', [RequestNoteController::class, 'sendAndVerifyEmail'])->name('send-and-verify-email');
//Route::post('/verify-record', [YourController::class, 'verifyRecord'])->name('verify.record');

Route::get('/po', [POController::class, 'index']);
//
// Cliam 
Route::get('/claim', [ClaimController::class, 'index']);
Route::get('/cr2', [ClaimController::class, 'claim2']);
Route::get('/cr3', [ClaimController::class, 'Claim3']);
Route::get('/cr4', [ClaimController::class, 'Claim4']);
Route::get('/cr5', [ClaimController::class, 'claim5'])->name('claim5');
Route::post('/cr5/feedback', [ClaimController::class, 'storeFeedback'])->name('claim5.feedback');
Route::post('/cr5/complete/{id}', [ClaimController::class, 'completeAction'])->name('claim5.complete');

//
Route::get('/cr6', [ClaimController::class, 'Claim6']);
// dashboard
Route::get('/cr7', [ClaimDBController::class, 'index']);

//
Route::get('/cr8', [ClaimController::class, 'Claim8']);
Route::get('/cr9', [ClaimController::class, 'Claim9']);
Route::get('/cr10', [ClaimController::class, 'Claim10']);
Route::get('/cr11', [ClaimController::class, 'Claim11']);
Route::get('/cr12', [ClaimController::class, 'Claim12']);
Route::post('/insertApproval', [ClaimController::class, 'insertApproval'])->name('insertApproval');





Route::get('/uio', [UserActiveController::class, 'uio']);




Route::post('/insert-approval', [ClaimController::class, 'insertApproval'])->name('insertApproval');
Route::post('/save-remark', [ClaimController::class, 'saveRemark'])->name('saveRemark');
Route::post('/get-remarks', [ClaimController::class, 'getRemarks'])->name('getRemarks');


// Route::post('/saveRemarks', [ClaimController::class, 'saveRemarks'])->name('saveRemarks');
// Existing route (keep it)
Route::post('/insert-approval', [ClaimController::class, 'insertApproval'])
     ->name('insertApproval');

// NEW route for saving remarks only
Route::post('/save-claim-remarks', [ClaimController::class, 'saveClaimRemarks'])
     ->name('saveClaimRemarks');

     
     // Addition
Route::get('/employee-addition', [UserHealthController::class, 'create']);
Route::post('/employee-addition', [UserHealthController::class, 'store'])->name('employee.add.store');
Route::get('employee/view', [UserHealthController::class, 'show'])->name('employee.view');
Route::get('employee/edit/{id}', [UserHealthController::class, 'edit'])->name('employee.edit');
Route::post('employee/update/{id}', [UserHealthController::class, 'update'])->name('employee.update');

    // Change
Route::get('/change', [UserHealthController::class, 'Createchange']);
Route::post('/change', [UserHealthController::class, 'storeChange'])->name('employee.store.change');
Route::get('employee/view/change', [UserHealthController::class, 'showChange'])->name('employee.view.change');
Route::get('employee/change/edit/{id}', [UserHealthController::class, 'editChange'])
    ->name('employee.edit.change');
Route::post('employee/change/update/{id}', [UserHealthController::class, 'updateChange'])
    ->name('employee.update.change');
    
    // Deletion
Route::get('/del', [UserHealthController::class, 'CreateDel']);
Route::post('/del', [UserHealthController::class, 'storeDeletion'])->name('employee.store.del');
Route::get('employee/view/del', [UserHealthController::class, 'showDel'])->name('employee.view.del');
Route::get('employee/del/edit/{id}', [UserHealthController::class, 'EditDel'])
    ->name('employee.edit.del');
Route::post('employee/del/update/{id}', [UserHealthController::class, 'updateDel'])
    ->name('employee.update.del');

   // Exist
Route::get('/exist', [UserReportController::class, 'CreateExist']);
Route::post('/exist', [UserReportController::class, 'storeExist'])->name('employee.store.exist');
Route::get('employee/view/exist', [UserReportController::class, 'ShowExist'])->name('employee.view.exist');
Route::get('employee/exist/edit/{id}', [UserReportController::class, 'editExist'])
    ->name('employee.edit.exist');
Route::post('employee/exist/update/{id}', [UserReportController::class, 'updateExist'])
    ->name('employee.update.exist');

// now health form 
Route::get('/auth', [UserReportController::class, 'CreatAuth']);
Route::post('/auth', [UserReportController::class, 'storeAuth'])->name('employee.store.auth');

Route::get('/auth1', [UserReportController::class, 'CreatAuth1']);
Route::post('/auth1', [UserReportController::class, 'storeAuth1'])->name('employee.store.authreqp');

Route::get('/auth2', [UserReportController::class, 'CreatAuth2']);
Route::post('/auth2', [UserReportController::class, 'storeAuth2'])->name('employee.store.authpf');

Route::get('/auth3', [UserReportController::class, 'CreatAuth3']);
Route::post('/auth3', [UserReportController::class, 'storeAuth3'])->name('employee.store.authlast');

Route::get('/showauth1', [UserReportController::class, 'showAuth1'])->name('showauth1');
Route::get('/showauth1/edit/{id}', [UserReportController::class, 'editauth1'])->name('showauth1.edit');
Route::put('/showauth1/update/{id}', [UserReportController::class, 'updateauth1'])->name('showauth1.update');

Route::get('/showauth', [UserReportController::class, 'showAuth'])->name('showauth');
Route::get('/showauth/edit/{id}', [UserReportController::class, 'editauth'])->name('showauth.edit');
Route::put('/showauth/update/{id}', [UserReportController::class, 'updateauth'])->name('showauth.update');

Route::get('/showauth2', [UserReportController::class, 'showAuth2'])->name('showauth2');
Route::get('/showauth2/edit/{id}', [UserReportController::class, 'editauth2'])->name('showauth2.edit');
Route::put('/showauth2/update/{id}', [UserReportController::class, 'updateauth2'])->name('showauth2.update');

Route::get('/showauth3', [UserReportController::class, 'showAuth3'])->name('showauth3');
Route::get('/showauth3/edit/{id}', [UserReportController::class, 'editauth3'])->name('showauth3.edit');
Route::put('/showauth3/update/{id}', [UserReportController::class, 'updateauth3'])->name('showauth3.update');

//
//Route::get('/logins-by-date', [UserActiveController::class, 'getLoginsByDate'])->name('logins.by.date');

     Route::post('/send-surveyor-email', [SurveyorEmailController::class, 'sendSurveyorEmail'])
    ->name('send.surveyor.email');

Route::post('/claim/upload-document', [ClaimController::class, 'uploadClaimDocument'])
    ->name('claim.upload.document');
});

/* CP - UPLOAD REPORT */
Route::prefix('claim-payment')->group(function () {

    // GET routes
    Route::get('/',                          [GlUploadController::class, 'index'])     ->name('gl.upload.index');
    Route::get('/doc-info',                  [GlUploadController::class, 'docInfo'])   ->name('gl.upload.doc.info');
    Route::get('/serve/{docNum}/{fileName}', [GlUploadController::class, 'serveFile']) ->name('gl.upload.serve.file');

    // POST routes 
    Route::post('/bulk',  [GlUploadController::class, 'bulkUpload']) ->name('gl.upload.bulk');
    Route::post('/{id}',  [GlUploadController::class, 'upload'])     ->name('gl.upload.upload');

});

Route::get('/paid-reports', [GlUploadController::class, 'paidReports'])->name('gl.paid.reports');
Route::get('/paid-claim-payments/file/{docNum}/{fileName}', [GlUploadController::class, 'servePaidFile'])
    ->name('paid.serve.file')
    ->where('fileName', '.+');









































/////////////////////////////////////////////////TEST/////////////////////
Route::get('/claim', function () {

    $columns = Schema::getColumnListing('claim_docs');

    $records = DB::table('claim_docs')->get();

  
    return response()->json([
        'columns' => $columns,
        'records' => $records,
    ]);
});

Route::get('/users', function () {

    $columns = Schema::getColumnListing('users');

    $records = DB::table('users')->get();

  
    return response()->json([
        'columns' => $columns,
        'records' => $records,
    ]);
});
Route::get('/test-gl-connection', [GlUploadController::class, 'testConnection']);


Route::get('/T_gl_uplaod', function () {

    $columns = Schema::getColumnListing('gl_docs');

    $records = DB::table('gl_docs')->get();

  
    return response()->json([
        'columns' => $columns,
        'records' => $records,
    ]);
});
Route::get('/check-session', function () {
    
    // Get all session data
    $allSession = session()->all();
    
    // Get specific user-related session values
    $userSession = [
        'user.name' => session('user.name'),
        'user_id' => session('user_id'),
        'user_name' => session('user_name'),
        'username' => session('username'),
        'name' => session('name'),
        'email' => session('email'),
        'auth' => session('auth'),
        'logged_in' => session('logged_in'),
        'user' => session('user'),
    ];
    
    // If using Laravel Auth
    $authUser = null;
    if (auth()->check()) {
        $authUser = [
            'id' => auth()->id(),
            'name' => auth()->user()->name ?? null,
            'email' => auth()->user()->email ?? null,
            'username' => auth()->user()->username ?? null,
        ];
    }
    
    return response()->json([
        'all_session_data' => $allSession,
        'user_session_values' => $userSession,
        'auth_user' => $authUser,
        'session_id' => session()->getId(),
    ]);
});

Route::get('/claim-debug/{docNum?}', function ($docNum = null) {
    // If no document number provided, show all with counts
    if (!$docNum) {
        $claimDocs = DB::table('claim_docs')
            ->select('claim_docs.*', 
                DB::raw('(SELECT COUNT(*) FROM gl_docs WHERE gl_docs.claim_doc_id = claim_docs.id) as total_payments'),
                DB::raw('(SELECT COUNT(*) FROM gl_docs WHERE gl_docs.claim_doc_id = claim_docs.id AND gl_docs.is_uploaded = 1) as uploaded_payments')
            )
            ->get();
            
        return response()->json([
            'total_claim_docs' => count($claimDocs),
            'records' => $claimDocs
        ]);
    }
    
    // For specific document number - shows both claim_doc and all gl_docs
    $claimDoc = DB::table('claim_docs')->where('doc_num', $docNum)->first();
    
    if (!$claimDoc) {
        return response()->json(['error' => 'Document not found'], 404);
    }
    
    $glDocs = DB::table('gl_docs')
        ->where('claim_doc_id', $claimDoc->id)
        ->get();
    
    return response()->json([
        'claim_doc' => $claimDoc,
        'gl_docs_count' => count($glDocs),
        'uploaded_count' => DB::table('gl_docs')
            ->where('claim_doc_id', $claimDoc->id)
            ->where('is_uploaded', 1)
            ->count(),
        'gl_docs' => $glDocs
    ]);
});


Route::get('/branches_lists', function () {

    $columns = Schema::getColumnListing('branches_lists');

    $records = DB::table('branches_lists')->get();

  
    return response()->json([
        'columns' => $columns,
        'records' => $records,
    ]);
});
Route::get('/debug-claim-paths', function() {
    $testDocNum = '2026HACTAPCDI00023'; // Use one of your actual document numbers
    $directory = 'public/claims/' . $testDocNum;
    $fullPath = storage_path('app/' . $directory);
    
    return response()->json([
        'doc_num' => $testDocNum,
        'directory' => $directory,
        'full_path' => $fullPath,
        'directory_exists' => is_dir($fullPath),
        'files' => is_dir($fullPath) ? scandir($fullPath) : [],
        'storage_url_example' => $testDocNum ? Storage::url('claims/' . $testDocNum . '/sample.pdf') : null
    ]);
});

Route::get('/debug-file-url/{docNum}', function($docNum) {
    $directory = storage_path('app/public/claims/' . $docNum);
    $files = is_dir($directory) ? scandir($directory) : [];
    $urls = [];
    
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $urls[] = [
                'file' => $file,
                'asset_url' => asset('storage/claims/' . $docNum . '/' . $file),
                'custom_url' => url('/temp-claim-file/' . $docNum . '/' . $file),
                'file_exists' => file_exists($directory . '/' . $file)
            ];
        }
    }
    
    return response()->json([
        'doc_num' => $docNum,
        'directory' => $directory,
        'files' => $urls
    ]);
});
// Add this to your routes/web.php
Route::get('/debug-upload-flow/{docNum}', function($docNum) {
    $results = [
        'document_number' => $docNum,
        'debug_time' => now()->toDateTimeString(),
        'steps' => []
    ];
    
    // STEP 1: Check what's in the database RIGHT NOW
    $glDocs = DB::table('gl_docs')
        ->where('doc_num', $docNum)
        ->orderBy('id')
        ->get();
    
    $results['steps']['database_current'] = [
        'total_records' => $glDocs->count(),
        'records' => $glDocs->map(function($doc) {
            return [
                'id' => $doc->id,
                'voucher_serial' => $doc->voucher_serial,
                'dept_code' => $doc->dept,
                'dept_name' => $doc->dept == '13' ? 'Motor' : ($doc->dept == '11' ? 'Fire' : 'Unknown'),
                'is_uploaded' => $doc->is_uploaded,
                'gl_file_name' => $doc->gl_file_name,
                'uploaded_at' => $doc->uploaded_at,
                'created_at' => $doc->created_at,
                'updated_at' => $doc->updated_at,
            ];
        })
    ];
    
    // STEP 2: Check what the API says NOW
    try {
        $response = Http::timeout(30)
            ->get('http://172.16.22.204/dashboardApi/clm/glOS.php');
        
        if ($response->successful()) {
            $apiResponse = $response->json();
            $apiRecords = [];
            
            foreach ($apiResponse as $item) {
                if (($item['LVD_VCDTNARRATION1'] ?? '') == $docNum) {
                    $apiRecords[] = [
                        'voucher_serial' => $item['LVD_VCDTVOUCHSR'] ?? '',
                        'dept_from_api' => $item['DEPT'] ?? 'MISSING',
                        'dept_name' => ($item['DEPT'] ?? '') == '13' ? 'Motor' : (($item['DEPT'] ?? '') == '11' ? 'Fire' : 'Unknown'),
                        'party' => $item['PARTY_DESCRIPTION'] ?? '',
                        'amount' => $item['LSB_SDTLCRAMTFC'] ?? 0,
                    ];
                }
            }
            
            $results['steps']['api_current'] = [
                'records_found' => count($apiRecords),
                'records' => $apiRecords
            ];
        } else {
            $results['steps']['api_current'] = ['error' => 'API failed'];
        }
    } catch (\Exception $e) {
        $results['steps']['api_current'] = ['error' => $e->getMessage()];
    }
    
    // STEP 3: Check storage/files
    $storagePath = storage_path('app/public/claims/' . $docNum);
    $results['steps']['storage'] = [
        'path' => $storagePath,
        'exists' => is_dir($storagePath),
    ];
    
    if (is_dir($storagePath)) {
        $files = array_diff(scandir($storagePath), ['.', '..']);
        $fileDetails = [];
        foreach ($files as $file) {
            $fullPath = $storagePath . '/' . $file;
            $fileDetails[] = [
                'name' => $file,
                'size' => filesize($fullPath),
                'modified' => date('Y-m-d H:i:s', filemtime($fullPath)),
                'url' => url('storage/claims/' . $docNum . '/' . $file),
            ];
        }
        $results['steps']['storage']['files'] = $fileDetails;
        $results['steps']['storage']['file_count'] = count($fileDetails);
    }
    
    // STEP 4: Compare DB vs API
    $comparison = [];
    foreach ($glDocs as $glDoc) {
        $matchingApi = null;
        foreach ($apiRecords as $apiRec) {
            if ($apiRec['voucher_serial'] == $glDoc->voucher_serial) {
                $matchingApi = $apiRec;
                break;
            }
        }
        
        $comparison[] = [
            'voucher_serial' => $glDoc->voucher_serial,
            'db_dept' => $glDoc->dept,
            'db_dept_name' => $glDoc->dept == '13' ? 'Motor' : ($glDoc->dept == '11' ? 'Fire' : 'Unknown'),
            'api_dept' => $matchingApi['dept_from_api'] ?? 'NOT_IN_API',
            'api_dept_name' => isset($matchingApi) ? ($matchingApi['dept_from_api'] == '13' ? 'Motor' : 'Fire') : 'N/A',
            'match' => isset($matchingApi) ? ($glDoc->dept == $matchingApi['dept_from_api']) : false,
            'is_uploaded' => $glDoc->is_uploaded,
        ];
    }
    
    $results['steps']['comparison'] = $comparison;
    
    // STEP 5: Check if there's a mismatch
    $mismatches = [];
    foreach ($comparison as $item) {
        if ($item['api_dept'] != 'NOT_IN_API' && !$item['match']) {
            $mismatches[] = $item;
        }
    }
    
    $results['summary'] = [
        'total_db_records' => $glDocs->count(),
        'total_api_records' => count($apiRecords ?? []),
        'mismatches_found' => count($mismatches),
        'mismatch_details' => $mismatches,
        'conclusion' => count($mismatches) > 0 
            ? "Database has WRONG department. Run: UPDATE gl_docs SET dept = '13' WHERE doc_num = '$docNum';"
            : "Database matches API. No issue found.",
    ];
    
    return response()->json($results, 200, [], JSON_PRETTY_PRINT);
});