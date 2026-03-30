<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use App\Models\EmployeeRecord;   
use App\Models\MedicalClaim;
use App\Models\MedicalPreAuthRequest;
use App\Models\OpdClaim;
use App\Models\EmployeeMedicalClaim; 






class UserHealthController extends Controller

{
    public function index(Request $request)
    {
        $data = Helper::fetchUser();
      
        if (isset($data['error'])) {
            return response()->json(['error' => $data['error']], 500);
        }

        $data = collect($data);
        return view('users.index', compact('data'));
    }
    

    public function create()
    {
        //dd()
        $employees = EmployeeRecord::all();
        return view('employee-addition', compact('employees'));
    } 
    public function store(Request $request)
    {
        // Generate new transaction_id based on "addition" only
        $last = EmployeeRecord::where('record_type', 'addition')
                            ->max('transaction_id');

        $transactionId = $last ? $last + 1 : 1;

        foreach ($request->employees as $emp) {

            EmployeeRecord::create([
                'record_type'      => 'addition',
                'transaction_id'   => $transactionId,
                'client_name'      => $request->client_name,
                'policy_no'        => $request->policy_no,

                'employee_name'    => $emp['employee_name'] ?? null,
                'gender'           => $emp['gender'] ?? null,
                'employee_id'      => $emp['employee_id'] ?? null,
                'plan'             => $emp['plan'] ?? null,
                'dob'              => $emp['dob'] ?? null,
                'cnic_no'          => $emp['cnic'] ?? null,
                'relation'         => $emp['relation'] ?? null,
                'effective_date'   => $emp['effective_date'] ?? null,
            ]);
        }

        return back()->with('success', 'Employee(s) saved successfully!');
    }
    public function show()
    {
        $data = EmployeeRecord::where('record_type', 'addition')
                            ->orderBy('transaction_id', 'desc')
                            ->get();

        return view('employee-view', compact('data'));
    }
    public function edit($id)
    {
        $record = EmployeeRecord::findOrFail($id);
        return view('employee-edit', compact('record'));
    }
    public function update(Request $request, $id)
    {
        $record = EmployeeRecord::findOrFail($id);

        $record->update($request->all());

        return redirect()->route('employee.view')->with('success', 'Record updated successfully');
    }

  
    public function CreateDel()
    {
        //dd()
        $employees = EmployeeRecord::all();
        //dd($employees);
        return view('deleting-form', compact('employees'));
    }
    public function storeDeletion(Request $request)
    {
        
        $last = EmployeeRecord::where('record_type', 'deletion')
                            ->max('transaction_id');

        $transactionId = $last ? $last + 1 : 1;

        foreach ($request->employees as $emp) {
            EmployeeRecord::create([
                'record_type'                => 'deletion',
                'transaction_id'             => $transactionId,
                'client_name'                => $request->client_name,
                'policy_no'                  => $request->policy_no,
                'employee_name'              => $emp['employee_name'] ?? null,
                'gender'                     => $emp['gender'] ?? null,
                'employee_code'              => $emp['employee_code'] ?? null,
                'folio_no'                   => $emp['folio_no'] ?? null,
                'remarks'                    => $emp['remarks'] ?? null,
                'effective_date_of_deletion'=> $emp['effective_date_of_deletion'] ?? null,
            ]);
        }

        return back()->with('success', "Deletion submitted successfully!");
    }
    public function ShowDel()
    {
        $data = EmployeeRecord::where('record_type', 'deletion')
            ->orderBy('transaction_id', 'desc')
            ->get();

        return view('employee-view-del', compact('data'));
        
    }
    public function EditDel($id)
    {
        $record = EmployeeRecord::findOrFail($id);
        return view('employee-edit-del', compact('record'));
    }
    public function updateDel(Request $request, $id)
    {
        $record = EmployeeRecord::findOrFail($id);

        $record->update($request->all());

        return redirect()->route('employee.view.del')->with('success', 'Record updated successfully');
    }
    

    


    public function CreateExist()
    {
        //dd()
        $employees = EmployeeRecord::all();
        return view('existing-form', compact('employees'));
    } 
    public function storeExist(Request $request)
    {
    
        $last = EmployeeRecord::where('record_type', 'Exist')
                            ->max('transaction_id');

        $transactionId = $last ? $last + 1 : 1;

        foreach ($request->employees as $emp) {

            EmployeeRecord::create([
                'record_type'      => 'Exist',
                'transaction_id'   => $transactionId,

                'client_name'      => $request->client_name,
                'policy_no'        => $request->policy_no,

                'employee_name'    => $emp['employee_name'] ?? null,
                'dependent_name'   => $emp['dependent_name'] ?? null,
                'gender'           => $emp['gender'] ?? null,
                'employee_code'    => $emp['employee_code'] ?? null,
                'folio_no'         => $emp['folio_no'] ?? null,
                'effective_date'   => $emp['effective_date'] ?? null,
                'remarks'          => $emp['remarks'] ?? null,
            ]);
        }

        return back()->with('success', 'Employee(s) saved successfully!');
    }
    public function ShowExist()
    {
        $data = EmployeeRecord::where('record_type', 'Exist')
            ->orderBy('transaction_id', 'desc')
            ->get();

        return view('employee-view-exist', compact('data'));
        
    }
    public function editExist($id)
    {
        $record = EmployeeRecord::findOrFail($id);
        return view('employee-edit-exist', compact('record'));
    }
    public function updateExist(Request $request, $id)
    {
        $record = EmployeeRecord::findOrFail($id);

        $record->update($request->all());

        return redirect()->route('employee.view.exist')->with('success', 'Record updated successfully');
    }




    public function Createchange()
    {
        //dd()
        $employees = EmployeeRecord::all();
        return view('changing-form', compact('employees'));
    }
    public function storeChange(Request $request)
    {
        $last = EmployeeRecord::max('transaction_id');
        $transactionId = $last ? $last + 1 : 1;

        foreach ($request->employees as $emp) {

            EmployeeRecord::create([
                'record_type'      => 'change',  
                'transaction_id'   => $transactionId,
                'client_name'      => $request->client_name,
                'policy_no'        => $request->policy_no,
                'existing_details' => $emp['existing_details'] ?? null,
                'employee_name'    => $emp['employee_name'] ?? null,
                'folio_no'         => $emp['folio_no'] ?? null,
                'remarks'          => $emp['remarks'] ?? null,
            ]);
        }
        return back()->with('success', 'Employee(s) saved successfully!');
    }
    public function showChange()
    {
        $data = EmployeeRecord::where('record_type', 'change')
            ->orderBy('transaction_id', 'desc')
            ->get();

        return view('employee-view-change', compact('data'));
    
    }
    public function editChange($id)
    {
        $record = EmployeeRecord::findOrFail($id);
        return view('employee-edit-change', compact('record'));
    }
    public function updateChange(Request $request, $id)
    {
        $record = EmployeeRecord::findOrFail($id);

        $record->update($request->all());

        return redirect()->route('employee.view.change')->with('success', 'Record updated successfully');
    }

  
   //  health forms 

   // auth - Patient Medical form
  
   public function CreatAuth()
    {
       $med = MedicalClaim::all();
       //dd($med );
        //dd($request->all());
        //$employees = EmployeeRecord::all();
        return view('auth');
    }
   
    public function storeAuth(Request $request)
    {
        $data = $request->all();

        // Convert arrays to strings
        $data['patient_relationship'] = isset($data['patient_relationship'])
            ? implode(', ', $data['patient_relationship'])
            : null;

        $data['consultations'] = isset($data['consultations'])
            ? json_encode($data['consultations'])
            : null;

        $data['expenses'] = isset($data['expenses'])
            ? json_encode($data['expenses'])
            : null;

        $data['previous_treatments'] = isset($data['previous_treatments'])
            ? json_encode($data['previous_treatments'])
            : null;

        unset($data['_token']);

        $claim = MedicalClaim::create($data);

        return back()->with('success', 'Medical claim submitted successfully! ');
    }
     public function ShowAuth(Request $request)
    {
      
       
      $employees = MedicalClaim::all();       
      //dd($employees);
        //$employees = EmployeeRecord::all();
        return view('showauth' ,  compact('employees'));
    }
     
    // Show edit form
    public function editauth($id)
    {
        $claim = MedicalClaim::findOrFail($id);
        return view('edit_auth', compact('claim'));
    }

    // Update record
    public function updateauth(Request $request, $id)
    {
        $claim = MedicalClaim::findOrFail($id);

        // Handle patient_relationship checkbox array
        $patient_relationship = $request->has('patient_relationship')
            ? implode(', ', $request->patient_relationship)
            : null;

        $claim->update([
            // Section A
            'insured_name'                  => $request->insured_name,
            'claimant_name'                 => $request->claimant_name,
            'employee_name'                 => $request->employee_name,
            'employee_telephone'            => $request->employee_telephone,
            'policy_no'                     => $request->policy_no,
            'health_card_no'                => $request->health_card_no,
            'patient_relationship'          => $patient_relationship,
            'relationship_other'            => $request->relationship_other,
            'patient_name'                  => $request->patient_name,
            'patient_dob'                   => $request->patient_dob,
            'patient_gender'                => $request->patient_gender,
            'patient_cnic_passport'         => $request->patient_cnic_passport,
            'country_residence'             => $request->country_residence,
            'nationality'                   => $request->nationality,
            'employer_address_line1'        => $request->employer_address_line1,
            'employer_address_line2'        => $request->employer_address_line2,
            'employer_telephone'            => $request->employer_telephone,
            'illness_description'           => $request->illness_description,
            'symptoms_first_date'           => $request->symptoms_first_date,
            'last_work_date'                => $request->last_work_date,
            'is_accident'                   => $request->is_accident,
            'accident_date'                 => $request->accident_date,
            'accident_details'              => $request->accident_details,
            'usual_doctor_name_address'     => $request->usual_doctor_name_address,
            'usual_doctor_address_line2'    => $request->usual_doctor_address_line2,
            'usual_doctor_telephone'        => $request->usual_doctor_telephone,
            'consulted_other_doctor'        => $request->consulted_other_doctor,
            'patient_location'              => $request->patient_location,
            'contact_for_exam'              => $request->contact_for_exam,
            'other_insurance_details'       => $request->other_insurance_details,
            'is_continuation'               => $request->is_continuation,
            'continuation_details'          => $request->continuation_details,

            // Section B - Treating Physician
            'physician_duration'            => $request->physician_duration,
            'records_back'                  => $request->records_back,
            'referring_physician'           => $request->referring_physician,
            'referring_physician_line2'     => $request->referring_physician_line2,
            'first_consulted_date'          => $request->first_consulted_date,
            'diagnosis'                     => $request->diagnosis,
            'diagnosis_line2'               => $request->diagnosis_line2,
            'accident_happen_details'       => $request->accident_happen_details,
            'accident_happen_details_line2' => $request->accident_happen_details_line2,
            'treatment_given'               => $request->treatment_given,
            'treatment_given_line2'         => $request->treatment_given_line2,
            'condition_history'             => $request->condition_history,
            'expected_delivery_date'        => $request->expected_delivery_date,
            'maternity_first_consulted'     => $request->maternity_first_consulted,
            'physician_name'                => $request->physician_name,
            'physician_address_line1'       => $request->physician_address_line1,
            'physician_address_line2'       => $request->physician_address_line2,
            'physician_telephone'           => $request->physician_telephone,

            // Optional: track who updated
            'updated_by'                    => auth()->user()->name ?? null,
        ]);

    return redirect()->route('showauth')->with('success', 'Record updated successfully.');
    }



      // auth - Pre Authorization form

        public function CreatAuth1(Request $request)
        {
         $employees = MedicalPreAuthRequest::all();
            return view('auth1');
        }
        public function storeAuth1(Request $request)
        {
          // dd( $request->all());
            // Optional: validate request (you can add more rules as needed)
            $request->validate([
                'health_card_no' => 'nullable|string|max:255',
                'policy_holder_name' => 'nullable|string|max:255',
                'policy_no' => 'nullable|string|max:255',
                'employee_name' => 'nullable|string|max:255',
                'patient_name_age_relation' => 'nullable|string|max:255',
                'hospital_name' => 'nullable|string|max:255',
                'mr_patient_no' => 'nullable|string|max:255',
                'expected_admission_date' => 'nullable|date',
                'room_bed_no' => 'nullable|string|max:255',
                'presenting_complaints' => 'nullable|string',
                'history_illness' => 'nullable|string',
                'diagnosis_procedure' => 'nullable|string',
                'expected_stay' => 'nullable|string|max:255',
                'expected_cost' => 'nullable|string|max:255',
                'doctor_name_contact' => 'nullable|string|max:255',
                'doctor_signature' => 'nullable|string|max:255',
            ]);

            // Store data using mass assignment
            MedicalPreAuthRequest::create($request->all());

            return back()->with('success', 'Medical claim submitted successfully!');
        }
        public function ShowAuth1(Request $request)
        {
        
          $employees = MedicalPreAuthRequest::all();        
            //$employees = EmployeeRecord::all();
            return view('showauth1' ,  compact('employees'));
        }
        
        public function editauth1($id)
        {
            $employee = MedicalPreAuthRequest::findOrFail($id);
            return view('edit_auth1', compact('employee'));
        }

        public function updateauth1(Request $request, $id)
        {
            $employee = MedicalPreAuthRequest::findOrFail($id);

            $employee->update([
                'health_card_no' => $request->health_card_no,
                'policy_holder_name' => $request->policy_holder_name,
                'policy_no' => $request->policy_no,
                'employee_name' => $request->employee_name,
                'patient_name_age_relation' => $request->patient_name_age_relation,
                'hospital_name' => $request->hospital_name,
                'mr_patient_no' => $request->mr_patient_no,
                'expected_admission_date' => $request->expected_admission_date,
                'room_bed_no' => $request->room_bed_no,
                'presenting_complaints' => $request->presenting_complaints,
                'history_illness' => $request->history_illness,
                'diagnosis_procedure' => $request->diagnosis_procedure,
                'expected_stay' => $request->expected_stay,
                'expected_cost' => $request->expected_cost,
                'doctor_name_contact' => $request->doctor_name_contact,
                'doctor_signature' => $request->doctor_signature,
                // optionally you can track who updated it
                'updated_by' => auth()->user()->name ?? null,
            ]);

         return redirect()->route('showauth1')->with('success', 'Record updated successfully.');
        }
          
        // auth2 - OPD form
        public function CreatAuth2(Request $request)
        {
            $employees =  OpdClaim::all();
            //dd($employees);
            return view('auth2');
        }
       public function storeAuth2(Request $request)
{
    // Validate according to actual request payload
    $validated = $request->validate([
        'employer_name'               => 'nullable|string|max:255',
        'employee_name'               => 'nullable|string|max:255',
        'claimant_name'               => 'nullable|string|max:255',
        'patient_name'                => 'nullable|string|max:255',

        // IMPORTANT: relationship is an array
        'patient_relationship'        => 'nullable|array',
        'patient_relationship.*'      => 'string|max:255',

        'patient_relationship_other'  => 'nullable|string|max:255',
        'health_card_no'              => 'nullable|string|max:255',
        'clinic_hospital_doctor'       => 'nullable|string|max:255',
        'consultation_fee'            => 'nullable|string|max:255',
        'cost_of_medicines'            => 'nullable|string|max:255',
        'cost_of_investigation'        => 'nullable|string|max:255',
        'total_cost'                  => 'nullable|string|max:255',
        'specialized_hospital_name'    => 'nullable|string|max:255',
        'referring_specialist_name'    => 'nullable|string|max:255',
        'investigation_procedure_name' => 'nullable|string|max:255',

        // checkboxes
        'cat_scan'        => 'nullable|string',
        'mri'             => 'nullable|string',
        'nuclear_scan'    => 'nullable|string',
        'angiography'     => 'nullable|string',
        'ercp'            => 'nullable|string',
    ]);

    // Convert relationship array to JSON (BEST PRACTICE)
    $validated['patient_relationship'] = isset($validated['patient_relationship'])
        ? json_encode($validated['patient_relationship'])
        : null;

    // Normalize checkbox values (unchecked = No)
    $validated['cat_scan']      = $request->has('cat_scan') ? 'Yes' : 'No';
    $validated['mri']           = $request->has('mri') ? 'Yes' : 'No';
    $validated['nuclear_scan']  = $request->has('nuclear_scan') ? 'Yes' : 'No';
    $validated['angiography']   = $request->has('angiography') ? 'Yes' : 'No';
    $validated['ercp']          = $request->has('ercp') ? 'Yes' : 'No';

    // Save OPD claim
    OpdClaim::create($validated);

    return back()->with('success', 'OPD claim submitted successfully!');
}

        public function ShowAuth2(Request $request)
        {
        
        
        $enrollments = OpdClaim::all();       
            return view('showauth2' ,  compact('enrollments'));
        }
        public function editauth2($id)
        {
            $enrollment = OpdClaim::findOrFail($id);
            return view('edit_auth2', compact('enrollment'));
        }
        public function updateauth2(Request $request, $id)
{
    $claim = OpdClaim::findOrFail($id);

    $validated = $request->validate([
        'employer_name'               => 'nullable|string|max:255',
        'employee_name'               => 'nullable|string|max:255',
        'claimant_name'               => 'nullable|string|max:255',
        'patient_name'                => 'nullable|string|max:255',
        'patient_relationship'        => 'nullable|array',
        'patient_relationship.*'      => 'string|max:255',
        'patient_relationship_other'  => 'nullable|string|max:255',
        'health_card_no'              => 'nullable|string|max:255',
        'clinic_hospital_doctor'       => 'nullable|string|max:255',
        'consultation_fee'            => 'nullable|string|max:255',
        'cost_of_medicines'            => 'nullable|string|max:255',
        'cost_of_investigation'        => 'nullable|string|max:255',
        'total_cost'                  => 'nullable|string|max:255',
        'specialized_hospital_name'    => 'nullable|string|max:255',
        'referring_specialist_name'    => 'nullable|string|max:255',
        'investigation_procedure_name' => 'nullable|string|max:255',
        'cat_scan'                    => 'nullable|string|max:10',
        'mri'                         => 'nullable|string|max:10',
        'nuclear_scan'                => 'nullable|string|max:10',
        'angiography'                 => 'nullable|string|max:10',
        'ercp'                        => 'nullable|string|max:10',
    ]);

    // Convert relationship array to string
    $validated['patient_relationship'] = isset($validated['patient_relationship'])
        ? implode(',', $validated['patient_relationship'])
        : null;

    // Handle unchecked checkboxes
    foreach (['cat_scan','mri','nuclear_scan','angiography','ercp'] as $field) {
        $validated[$field] = $request->has($field) ? 'Yes' : null;
    }

    $claim->update($validated);

    return redirect()
        ->route('showauth2')
        ->with('success', 'OPD claim updated successfully!');
}



     // auth3 - Declaration form

    public function storeAuth3(Request $request)
    {

       
        $validated = $request->validate([
            'employee_name' => 'nullable|string|max:255',
            'sex' => 'nullable|string|max:50',
            'parent_name' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|string|max:50',
            'id_number' => 'nullable|string|max:255',
            'policy_holder_name' => 'nullable|string|max:255',
            'home_address' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'employer_name' => 'nullable|string|max:255',
            'business_address' => 'nullable|string|max:255',
            'business_telephone' => 'nullable|string|max:255',
            'dependents' => 'nullable|array',
            'q1a' => 'nullable|string|max:50',
            'q1b' => 'nullable|string|max:50',
            'q2a' => 'nullable|string|max:50',
            'q2b' => 'nullable|string|max:50',
            'q3' => 'nullable|string|max:50',
            'q4a' => 'nullable|string|max:50',
            'q4b' => 'nullable|string|max:50',
            'medical_details' => 'nullable|string',
        ]);

        // Convert dependents array to JSON if provided
        if (isset($validated['dependents'])) {
            $validated['dependents'] = json_encode($validated['dependents']);
        }

        // Save Employee Medical Claim
        EmployeeMedicalClaim::create($request->all());

    }
     public function CreatAuth3(Request $request)
    {
      
       
       //$employees = MedicalPreAuthRequest::all();
       $employees = EmployeeMedicalClaim::all();
      // dd($employees);
        
        //$employees = EmployeeRecord::all();
        return view('auth3');
    }
     public function showAuth3(Request $request)
    {
      
       
       
       $declarations = EmployeeMedicalClaim::all();
      //dd($declarations);
        
        //$employees = EmployeeRecord::all();
         return view('showauth3', compact('declarations'));
    }
    public function editauth3($id)
    {
        $declaration = EmployeeMedicalClaim::findOrFail($id);
        return view('edit_auth3', compact('declaration'));
    }
 public function updateAuth3(Request $request, $id)
{
    $validated = $request->validate([
        'employee_name'         => 'nullable|string|max:255',
        'sex'                   => 'nullable|string|max:50',
        'parent_name'           => 'nullable|string|max:255',
        'marital_status'        => 'nullable|string|max:50',
        'date_of_birth'         => 'nullable|string|max:50',
        'id_number'             => 'nullable|string|max:255',
        'policy_holder_name'    => 'nullable|string|max:255',
        'home_address'          => 'nullable|string|max:255',
        'telephone'             => 'nullable|string|max:255',
        'occupation'            => 'nullable|string|max:255',
        'designation'           => 'nullable|string|max:255',
        'employer_name'         => 'nullable|string|max:255',
        'business_address'      => 'nullable|string|max:255',
        'business_telephone'    => 'nullable|string|max:255',
        'dependents'            => 'nullable|array',
        'q1a'                   => 'nullable|string|max:50',
        'q1b'                   => 'nullable|string|max:50',
        'q2a'                   => 'nullable|string|max:50',
        'q2b'                   => 'nullable|string|max:50',
        'q3'                    => 'nullable|string|max:50',
        'q4a'                   => 'nullable|string|max:50',
        'q4b'                   => 'nullable|string|max:50',
        'medical_details'       => 'nullable|string',
    ]);

    // Handle dependents
    if (isset($validated['dependents']) && is_array($validated['dependents'])) {
        // Remove empty values
        $cleanDependents = array_filter($validated['dependents'], fn($v) => !empty($v));

        // Wrap in array to match JSON format
        $validated['dependents'] = !empty($cleanDependents) ? json_encode([$cleanDependents]) : null;
    } else {
        $validated['dependents'] = null;
    }

    // Update the record
    $updated = EmployeeMedicalClaim::where('id', $id)->update($validated);

    if ($updated) {
        return back()->with('success', 'Record updated successfully.');
    }

    return back()->with('error', 'No changes were made or record not found.');
}



}
