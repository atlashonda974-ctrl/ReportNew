<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReinsuranceRequestEmail;
use App\Models\EmailLog;
use App\Models\VerifyLog;
use App\Models\ReqnoteMark;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmailController extends Controller
{
    /**
     * Parse a comma-separated email string into a clean array of trimmed emails.
     */
    private function parseEmails(?string $raw): array
    {
        if (empty($raw)) return [];

        return array_values(
            array_filter(
                array_map('trim', explode(',', $raw)),
                fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL)
            )
        );
    }

    /**
     * Send bulk email with optional single or multiple file attachments.
     */
    public function sendEmail(Request $request)
    {
        $request->validate([
            'to'              => 'required|string',
            'cc'              => 'nullable|string',
            'subject'         => 'required|string|max:255',
            'body'            => 'required|string',
            'records'         => 'nullable|json',
            'upload_files'    => 'nullable|array',
            'upload_files.*'  => 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip',
        ]);

        $toAddresses = $this->parseEmails($request->to);
        $ccAddresses = $this->parseEmails($request->cc);

        if (empty($toAddresses)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid recipient email address(es) provided in "To" field.',
            ], 422);
        }

        $attachments = [];
        $records     = [];
        $refNo       = null;

        if ($request->filled('records')) {
            $records = json_decode($request->records, true) ?? [];
        }

        try {
            // ── Session user — used for from address AND audit logging ────────
            $sessionUser = Session::get('user') ?? [];
            $userid      = $sessionUser['name']  ?? 'Unknown';

            $fromEmail = $sessionUser['email'] ?? config('mail.from.address');
            $fromName  = $sessionUser['name']  ?? config('mail.from.name');

            $refNo = !empty($records) ? ($records[0]['reqNote'] ?? 'bulk_email') : 'bulk_email';

            // ================================================================
            // Handle file uploads (single or multiple)
            // ================================================================
            if ($request->hasFile('upload_files')) {

                $uploadedFiles = $request->file('upload_files');
                $attachmentNames = [];
                $attachmentUrls  = [];

                foreach ($uploadedFiles as $file) {

                    if (!$file->isValid()) continue;

                    $origName = $file->getClientOriginalName();
                    $filename = time() . '_' . $origName;

                    $path     = $file->storeAs('reqnote_uploads/' . $refNo, $filename, 'public');
                    $fullPath = storage_path('app/public/' . $path);

                    $fileUrl = route('reins.serve.file', [
                        'refNo'    => $refNo,
                        'filename' => $filename,
                    ]);

                    $attachments[] = [
                        'path' => $fullPath,
                        'name' => $origName,
                        'mime' => $file->getMimeType() ?? 'application/octet-stream',
                    ];

                    $attachmentNames[] = $origName;
                    $attachmentUrls[]  = $fileUrl;
                }

                // Attach file info to each record
                foreach ($records as &$rec) {
                    $rec['attachmentUrls']  = $attachmentUrls;
                    $rec['attachmentNames'] = $attachmentNames;
                }
                unset($rec);

                // Store last uploaded file path against each reqnote
                // (stores comma-separated paths if multiple)
                $uploadPaths = implode(',', array_column($attachments, 'path'));

                foreach ($records as $rec) {
                    if (!empty($rec['reqNote'])) {
                        ReqnoteMark::updateOrCreate(
                            ['GRH_REFERENCE_NO' => $rec['reqNote']],
                            ['upload_file'      => $uploadPaths]
                        );
                    }
                }
            }

            // ================================================================
            // Build and send email
            // ================================================================
            $mailData = [
                'subject'   => $request->subject,
                'body'      => $request->body,
                'records'   => $records,
                'from'      => $fromEmail,
                'from_name' => $fromName,
            ];

            $mail = Mail::to($toAddresses);
            if (!empty($ccAddresses)) {
                $mail->cc($ccAddresses);
            }
            $mail->send(new ReinsuranceRequestEmail($mailData, $attachments));

            // ================================================================
            // Log the email for each record
            // ================================================================
            foreach ($records as $rec) {
                EmailLog::create([
                    'sent_to'            => $request->to,
                    'sent_cc'            => $request->cc,
                    'subject'            => $request->subject,
                    'body'               => $request->body,
                    'repname'            => 'RQN',
                    'datetime'           => now(),
                    'reqnote'            => $rec['reqNote']           ?? null,
                    'doc_date'           => $rec['docDate']           ?? null,
                    'dept'               => $rec['dept']              ?? null,
                    'business_desc'      => $rec['businessDesc']      ?? null,
                    'insured'            => $rec['insured']           ?? null,
                    'reins_party'        => $rec['reinsParty']        ?? null,
                    'total_sum_ins'      => $rec['totalSumIns']       ?? null,
                    'ri_sum_ins'         => $rec['riSumIns']          ?? null,
                    'share'              => $rec['share']             ?? null,
                    'total_premium'      => $rec['totalPremium']      ?? null,
                    'ri_premium'         => $rec['riPremium']         ?? null,
                    'comm_date'          => $rec['commDate']          ?? null,
                    'expiry_date'        => $rec['expiryDate']        ?? null,
                    'cp'                 => $rec['cp']                ?? null,
                    'conv_takaful'       => $rec['convTakaful']       ?? null,
                    'posted'             => $rec['posted']            ?? null,
                    'user_name'          => $rec['userName']          ?? null,
                    'acceptance_date'    => $rec['acceptanceDate']    ?? null,
                    'warranty_period'    => $rec['warrantyPeriod']    ?? null,
                    'commission_percent' => $rec['commissionPercent'] ?? null,
                    'commission_amount'  => $rec['commissionAmount']  ?? null,
                    'acceptance_no'      => (isset($rec['acceptanceNo']) && $rec['acceptanceNo'] !== '' && $rec['acceptanceNo'] !== 'N/A')
                                                ? $rec['acceptanceNo']
                                                : null,
                    'created_by'         => $userid,
                ]);

                VerifyLog::create([
                    'GCP_DOC_REFERENCENO' => $rec['referenceNo']      ?? null,
                    'PDP_DEPT_CODE'       => $rec['dept']             ?? null,
                    'GCP_SERIALNO'        => $rec['serialNo']         ?? null,
                    'GCP_ISSUEDATE'       => $rec['docDate']          ?? null,
                    'GCP_COMMDATE'        => $rec['commDate']         ?? null,
                    'GCP_EXPIRYDATE'      => $rec['expiryDate']       ?? null,
                    'GCP_REINSURER'       => $rec['insured']          ?? null,
                    'GCP_REISSUEDATE'     => $rec['reinsParty']       ?? null,
                    'GCP_RECOMMDATE'      => $rec['businessDesc']     ?? null,
                    'GCP_REEXPIRYDATE'    => null,
                    'GCP_COTOTALSI'       => $rec['totalSumIns']      ?? null,
                    'GCP_COTOTALPREM'     => $rec['totalPremium']     ?? null,
                    'GCP_REINSI'          => $rec['riSumIns']         ?? null,
                    'GCP_REINPREM'        => $rec['riPremium']        ?? null,
                    'GCP_COMMAMOUNT'      => $rec['commissionAmount'] ?? null,
                    'GCP_POSTINGTAG'      => null,
                    'GCP_CANCELLATIONTAG' => null,
                    'GCP_POST_USER'       => $userid,
                    'GCT_THEIR_REF_NO'    => null,
                    'datetime'            => now(),
                    'sent_to'             => $request->to,
                    'sent_cc'             => $request->cc,
                    'subject'             => $request->subject,
                    'body'                => $request->body,
                    'created_by'          => $userid,
                    'updated_by'          => null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Email sent and logged successfully.',
            ]);

        } catch (\Exception $e) {
            Log::error('EmailController@sendEmail error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Serve an uploaded file directly from storage.
     */
    public function serveUpload(string $refNo, string $filename)
    {
        $path = storage_path('app/public/reqnote_uploads/' . $refNo . '/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        $mime = mime_content_type($path);

        return response()->file($path, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'inline; filename="' . rawurlencode($filename) . '"',
        ]);
    }

    /**
     * Serve a cached PDF by token (legacy).
     */
    public function servePdf(string $token)
    {
        $filename = cache()->get('email_pdf_' . $token);

        if (!$filename) {
            abort(404, 'PDF link has expired or does not exist.');
        }

        $path = storage_path('app/public/email_pdfs/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'PDF file not found.');
        }

        return response()->file($path, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filename) . '"',
        ]);
    }
}