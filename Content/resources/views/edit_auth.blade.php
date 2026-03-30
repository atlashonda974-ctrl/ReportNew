@extends('layouts.master')

@section('title', 'Edit Medical Reimbursement Claim')

@section('content')
<div class="content-body">
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white fw-bold">
                        Edit Medical Reimbursement Claim
                    </div>
                    <div class="card-body p-4">

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('showauth.update', $claim->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- SECTION A -->
                            <h5 class="mt-4 mb-3 text-primary fw-bold">SECTION A – CLAIM INFORMATION</h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">1. Full name of Insured (Employer)</label>
                                    <input type="text" name="insured_name" class="form-control form-control-sm" value="{{ old('insured_name', $claim->insured_name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">2. Full name of claimant</label>
                                    <input type="text" name="claimant_name" class="form-control form-control-sm" value="{{ old('claimant_name', $claim->claimant_name) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">3. Full name of Employee</label>
                                    <input type="text" name="employee_name" class="form-control form-control-sm" value="{{ old('employee_name', $claim->employee_name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Telephone No.</label>
                                    <input type="text" name="employee_telephone" class="form-control form-control-sm" value="{{ old('employee_telephone', $claim->employee_telephone) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small">4. Policy No.</label>
                                    <input type="text" name="policy_no" class="form-control form-control-sm" value="{{ old('policy_no', $claim->policy_no) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Patient's Health Card/Credit letter No.</label>
                                    <input type="text" name="health_card_no" class="form-control form-control-sm" value="{{ old('health_card_no', $claim->health_card_no) }}">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold small">5. Patient's relationship to claimant</label>
                                    <div class="d-flex flex-wrap gap-4 align-items-center">
                                        @php
                                            $relationships = [];
                                            if ($claim->patient_relationship) {
                                                $relationships = is_array($claim->patient_relationship)
                                                    ? $claim->patient_relationship
                                                    : explode(',', str_replace(' ', '', $claim->patient_relationship));
                                            }
                                        @endphp
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="patient_relationship[]" value="Employee" id="rel_employee"
                                                {{ in_array('Employee', $relationships) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="rel_employee">Employee</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="patient_relationship[]" value="Dependent Child" id="rel_child"
                                                {{ in_array('Dependent Child', $relationships) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="rel_child">Dependent Child</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="patient_relationship[]" value="Spouse" id="rel_spouse"
                                                {{ in_array('Spouse', $relationships) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="rel_spouse">Spouse</label>
                                        </div>
                                        <span class="small">Other - please describe</span>
                                        <input type="text" name="relationship_other" class="form-control form-control-sm" style="width: 250px;"
                                            value="{{ old('relationship_other', $claim->relationship_other) }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">6. Full name of patient</label>
                                    <input type="text" name="patient_name" class="form-control form-control-sm" value="{{ old('patient_name', $claim->patient_name) }}" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label small">7. Date of birth</label>
                                    <input type="date" name="patient_dob" class="form-control form-control-sm" value="{{ old('patient_dob', $claim->patient_dob) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Gender</label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="patient_gender" value="Male" id="male"
                                                {{ old('patient_gender', $claim->patient_gender) == 'Male' ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="male">Male</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="patient_gender" value="Female" id="female"
                                                {{ old('patient_gender', $claim->patient_gender) == 'Female' ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="female">Female</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">CNIC/Passport No.</label>
                                    <input type="text" name="patient_cnic_passport" class="form-control form-control-sm" value="{{ old('patient_cnic_passport', $claim->patient_cnic_passport) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small">8. Usual Country of Residence</label>
                                    <input type="text" name="country_residence" class="form-control form-control-sm" value="{{ old('country_residence', $claim->country_residence) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Nationality</label>
                                    <input type="text" name="nationality" class="form-control form-control-sm" value="{{ old('nationality', $claim->nationality) }}">
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label fw-bold small">9. Full address of patient's employer</label>
                                    <input type="text" name="employer_address_line1" class="form-control form-control-sm" value="{{ old('employer_address_line1', $claim->employer_address_line1) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Tel. No.</label>
                                    <input type="text" name="employer_telephone" class="form-control form-control-sm" value="{{ old('employer_telephone', $claim->employer_telephone) }}">
                                </div>
                                <div class="col-md-12">
                                    <input type="text" name="employer_address_line2" class="form-control form-control-sm" placeholder="Address line 2"
                                        value="{{ old('employer_address_line2', $claim->employer_address_line2) }}">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold small">10. State the nature of the injury, illness or medical condition</label>
                                    <textarea name="illness_description" class="form-control form-control-sm" rows="3" required>{{ old('illness_description', $claim->illness_description) }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small">11. a) On what date did the symptoms first occur?</label>
                                    <input type="date" name="symptoms_first_date" class="form-control form-control-sm" value="{{ old('symptoms_first_date', $claim->symptoms_first_date) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">b) the patient last work day?</label>
                                    <input type="date" name="last_work_date" class="form-control form-control-sm" value="{{ old('last_work_date', $claim->last_work_date) }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">12. Does treatment relate to an accident?</label>
                                    <div class="d-flex gap-4 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="is_accident" value="1" id="acc_yes"
                                                {{ old('is_accident', $claim->is_accident) == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="acc_yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="is_accident" value="0" id="acc_no"
                                                {{ old('is_accident', $claim->is_accident) == '0' ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="acc_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">If yes: a) Accident date</label>
                                    <input type="date" name="accident_date" class="form-control form-control-sm" value="{{ old('accident_date', $claim->accident_date) }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small">b) Brief details of accident</label>
                                    <textarea name="accident_details" class="form-control form-control-sm" rows="2">{{ old('accident_details', $claim->accident_details) }}</textarea>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label fw-bold small">13. Name and address of usual doctor</label>
                                    <input type="text" name="usual_doctor_name_address" class="form-control form-control-sm" value="{{ old('usual_doctor_name_address', $claim->usual_doctor_name_address) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Tel. No.</label>
                                    <input type="text" name="usual_doctor_telephone" class="form-control form-control-sm" value="{{ old('usual_doctor_telephone', $claim->usual_doctor_telephone) }}">
                                </div>
                                <div class="col-md-12">
                                    <input type="text" name="usual_doctor_address_line2" class="form-control form-control-sm" placeholder="Address line 2"
                                        value="{{ old('usual_doctor_address_line2', $claim->usual_doctor_address_line2) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">14. Has the patient consulted any doctor for the present or related condition?</label>
                                    <div class="d-flex gap-4 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="consulted_other_doctor" value="1" id="doc_yes"
                                                {{ old('consulted_other_doctor', $claim->consulted_other_doctor) == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="doc_yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="consulted_other_doctor" value="0" id="doc_no"
                                                {{ old('consulted_other_doctor', $claim->consulted_other_doctor) == '0' ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="doc_no">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small">15. a) Patient current location</label>
                                    <input type="text" name="patient_location" class="form-control form-control-sm" value="{{ old('patient_location', $claim->patient_location) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">b) Contact person for exam arrangements</label>
                                    <input type="text" name="contact_for_exam" class="form-control form-control-sm" value="{{ old('contact_for_exam', $claim->contact_for_exam) }}">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label small">16. Details of any other health/medical/travel insurance</label>
                                    <textarea name="other_insurance_details" class="form-control form-control-sm" rows="3">{{ old('other_insurance_details', $claim->other_insurance_details) }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">17. Is this a continuation of previous treatment?</label>
                                    <div class="d-flex gap-4 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="is_continuation" value="1" id="cont_yes"
                                                {{ old('is_continuation', $claim->is_continuation) == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="cont_yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="is_continuation" value="0" id="cont_no"
                                                {{ old('is_continuation', $claim->is_continuation) == '0' ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="cont_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">If yes, brief details</label>
                                    <input type="text" name="continuation_details" class="form-control form-control-sm" value="{{ old('continuation_details', $claim->continuation_details) }}">
                                </div>
                            </div>

                            <!-- SECTION B -->
                            <h5 class="mt-5 mb-3 text-primary fw-bold">SECTION B - TO BE COMPLETED BY THE TREATING PHYSICIAN</h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small">1. How long have you been the patient's doctor?</label>
                                    <input type="text" name="physician_duration" class="form-control form-control-sm" value="{{ old('physician_duration', $claim->physician_duration) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">2. How far back in time do your records go?</label>
                                    <input type="text" name="records_back" class="form-control form-control-sm" value="{{ old('records_back', $claim->records_back) }}">
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label small">3. Name and address of referring physician</label>
                                    <input type="text" name="referring_physician" class="form-control form-control-sm" value="{{ old('referring_physician', $claim->referring_physician) }}">
                                </div>
                                <div class="col-md-12">
                                    <input type="text" name="referring_physician_line2" class="form-control form-control-sm" placeholder="Address line 2"
                                        value="{{ old('referring_physician_line2', $claim->referring_physician_line2) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small">4. Date first consulted for this condition</label>
                                    <input type="date" name="first_consulted_date" class="form-control form-control-sm" value="{{ old('first_consulted_date', $claim->first_consulted_date) }}">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label small">5. Diagnosis</label>
                                    <input type="text" name="diagnosis" class="form-control form-control-sm" value="{{ old('diagnosis', $claim->diagnosis) }}">
                                    <input type="text" name="diagnosis_line2" class="form-control form-control-sm" placeholder="Diagnosis continued..." value="{{ old('diagnosis_line2', $claim->diagnosis_line2) }}">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label small">6. If accident involved, how did it happen?</label>
                                    <input type="text" name="accident_happen_details" class="form-control form-control-sm" value="{{ old('accident_happen_details', $claim->accident_happen_details) }}">
                                    <input type="text" name="accident_happen_details_line2" class="form-control form-control-sm" placeholder="Details continued..." value="{{ old('accident_happen_details_line2', $claim->accident_happen_details_line2) }}">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label small">7. Details of treatment given or prescribed</label>
                                    <input type="text" name="treatment_given" class="form-control form-control-sm" value="{{ old('treatment_given', $claim->treatment_given) }}">
                                    <input type="text" name="treatment_given_line2" class="form-control form-control-sm" placeholder="Treatment continued..." value="{{ old('treatment_given_line2', $claim->treatment_given_line2) }}">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label small">8. Brief history of the present condition</label>
                                    <textarea name="condition_history" class="form-control form-control-sm" rows="4">{{ old('condition_history', $claim->condition_history) }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small">10. Expected delivery date (Maternity)</label>
                                    <input type="date" name="expected_delivery_date" class="form-control form-control-sm" value="{{ old('expected_delivery_date', $claim->expected_delivery_date) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Date first consulted for maternity</label>
                                    <input type="date" name="maternity_first_consulted" class="form-control form-control-sm" value="{{ old('maternity_first_consulted', $claim->maternity_first_consulted) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small">11. Physician Name (PRINT)</label>
                                    <input type="text" name="physician_name" class="form-control form-control-sm" value="{{ old('physician_name', $claim->physician_name) }}">
                                </div>

                                <div class="col-md-7">
                                    <label class="form-label small">Physician Address</label>
                                    <input type="text" name="physician_address_line1" class="form-control form-control-sm" value="{{ old('physician_address_line1', $claim->physician_address_line1) }}">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small">Tel. No.</label>
                                    <input type="text" name="physician_telephone" class="form-control form-control-sm" value="{{ old('physician_telephone', $claim->physician_telephone) }}">
                                </div>
                                <div class="col-md-12">
                                    <input type="text" name="physician_address_line2" class="form-control form-control-sm" placeholder="Address line 2"
                                        value="{{ old('physician_address_line2', $claim->physician_address_line2) }}">
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-success">Update Record</button>
                               
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection