<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SurveyorAppointmentMail; 
use App\Models\EmailLog;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SurveyorEmailController extends Controller
{
    public function sendSurveyorEmail(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'to'      => 'required|email',
            'cc'      => 'nullable|string', 
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
            
            // Claim reference data for logging
            'claim_data' => 'nullable|array',
            'claim_data.claimNo' => 'nullable|string',
            'claim_data.insured' => 'nullable|string',
            'claim_data.policy'  => 'nullable|string',
            'claim_data.intimation' => 'nullable|string',
        ]);

        try {
            // Get current user name from session
            $userName = Session::get('user')['name'] ?? 'System';

            // Extract claim number (this is the key field for counting)
            $claimNo = $validated['claim_data']['claimNo'] ?? null;

            // Prepare email data
            $mailData = [
                'subject' => $validated['subject'],
                'body'    => $validated['body'],
                'claim_info' => $validated['claim_data'] ?? null,
            ];

            // Send email
            $mail = Mail::to($validated['to']);

            // Handle CC if provided
            if (!empty($validated['cc'])) {
                $ccEmails = array_filter(array_map('trim', explode(',', $validated['cc'])));
                if (!empty($ccEmails)) {
                    $mail->cc($ccEmails);
                }
            }

            $mail->send(new SurveyorAppointmentMail($mailData));

            // Log the email sending with ONLY the claim number in doc_num
            EmailLog::create([
                'sent_to'         => $validated['to'],
                'sent_cc'         => $validated['cc'] ?? null,
                'subject'         => $validated['subject'],
                'body'            => $validated['body'],
                'datetime'        => now(),
                'claim_no'        => $claimNo,
                'insured'         => $validated['claim_data']['insured'] ?? null,
                'policy_no'       => $validated['claim_data']['policy'] ?? null,
                'intimation_date' => $validated['claim_data']['intimation'] ?? null,
                'created_by'      => $userName,
                'module'          => 'Surveyor Appointment',
                
                // CRITICAL: Only store the claim number here
                'doc_num'         => $claimNo,
                'repname'         => 'surveyor report',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending surveyor email: ' . $e->getMessage(), [
                'request' => $request->except(['body']), 
                'exception' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }
}