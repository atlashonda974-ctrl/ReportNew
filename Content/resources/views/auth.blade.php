@extends('layouts.master')

@section('title', 'In-Patient Medical Claim Form - Atlas Insurance Ltd.')

@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
    }
    .remove-row-btn { 
        width: 32px; 
        height: 32px; 
        padding: 0; 
    }
</style>

<div class="content-body">
    <div class="container-fluid py-4">

        <!-- Form Title & Instructions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center fs-4 fw-bold" style="letter-spacing: 2px;">
                        IN-PATIENT MEDICAL CLAIM FORM
                    </div>
                    <div class="card-body text-center py-2 small text-secondary bg-primary bg-opacity-10">
                        Do not leave any blanks, unanswered questions, medical reports, dates and/or signatures, wherever applicable
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('employee.store.auth') }}" method="POST" enctype="multipart/form-data" id="claimForm">
                            @csrf

                            <!-- Section A Header -->
                            <div class="bg-primary text-white text-center py-2 fw-bold mb-4">
                                SECTION A – CLAIM INFORMATION - TO BE COMPLETED BY THE CLAIMANT AND THE PATIENT
                            </div>

                            <div class="bg-primary text-white text-center py-2 mb-4">
                                <small class="fw-bold">Medical / Surgical</small>
                            </div>

                            <!-- Question 1 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-3 col-form-label fw-bold small">1. Full name of Insured (Employer)</label>
                                <div class="col-md-9">
                                    <input type="text" name="insured_name" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <!-- Question 2 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-3 col-form-label fw-bold small">2. Full name of claimant</label>
                                <div class="col-md-9">
                                    <input type="text" name="claimant_name" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <!-- Question 3 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-3 col-form-label fw-bold small">3. Full name of Employee</label>
                                <div class="col-md-5">
                                    <input type="text" name="employee_name" class="form-control form-control-sm" required>
                                </div>
                                <label class="col-md-2 col-form-label small text-end">Telephone No.</label>
                                <div class="col-md-2">
                                    <input type="text" name="employee_telephone" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- Question 4 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-2 col-form-label fw-bold small">4. Policy No.</label>
                                <div class="col-md-2">
                                    <input type="text" name="policy_no" class="form-control form-control-sm">
                                </div>
                                <label class="col-md-4 col-form-label small text-end">Patient's Health Card/Credit letter No.</label>
                                <div class="col-md-4">
                                    <input type="text" name="health_card_no" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- Question 5 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-3 col-form-label fw-bold small">5. Patient's relationship to claimant:</label>
                                <div class="col-md-9 d-flex align-items-center flex-wrap gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="patient_relationship[]" value="Employee" id="employee">
                                        <label class="form-check-label small" for="employee">Employee</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="patient_relationship[]" value="Dependent Child" id="dependent">
                                        <label class="form-check-label small" for="dependent">Dependent Child</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="patient_relationship[]" value="Spouse" id="spouse">
                                        <label class="form-check-label small" for="spouse">Spouse</label>
                                    </div>
                                    <span class="small">Other - please describe</span>
                                    <div class="flex-grow-1" style="max-width: 250px;">
                                        <input type="text" name="relationship_other" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Question 6 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-3 col-form-label fw-bold small">6. Full name of patient:</label>
                                <div class="col-md-9">
                                    <input type="text" name="patient_name" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <!-- Question 7 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-2 col-form-label fw-bold small">7. Date of birth</label>
                                <div class="col-md-2">
                                    <input type="date" name="patient_dob" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3 d-flex align-items-center gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="patient_gender" id="male" value="Male">
                                        <label class="form-check-label small" for="male">Male</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="patient_gender" id="female" value="Female">
                                        <label class="form-check-label small" for="female">Female</label>
                                    </div>
                                </div>
                                <label class="col-md-2 col-form-label small text-end">CNIC/Passport No.</label>
                                <div class="col-md-3">
                                    <input type="text" name="patient_cnic_passport" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- Question 8 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-3 col-form-label fw-bold small">8. Usual Country of Residence</label>
                                <div class="col-md-4">
                                    <input type="text" name="country_residence" class="form-control form-control-sm">
                                </div>
                                <label class="col-md-2 col-form-label small text-end">Nationality</label>
                                <div class="col-md-3">
                                    <input type="text" name="nationality" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- Question 9 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-3 col-form-label fw-bold small">9. Full address of patient's employer</label>
                                <div class="col-md-6">
                                    <input type="text" name="employer_address_line1" class="form-control form-control-sm">
                                </div>
                                <label class="col-md-1 col-form-label small text-end">Tel. No.</label>
                                <div class="col-md-2">
                                    <input type="text" name="employer_telephone" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="offset-md-3 col-md-9">
                                    <input type="text" name="employer_address_line2" class="form-control form-control-sm" placeholder="Address line 2">
                                </div>
                            </div>

                            <!-- Question 10 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">10. State the nature of the injury, illness or medical condition</label>
                                <div class="col-md-8">
                                    <input type="text" name="illness_description" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <!-- Question 11 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-5 col-form-label fw-bold small">11. a) On what date did the symptoms first occur?</label>
                                <div class="col-md-7">
                                    <input type="date" name="symptoms_first_date" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-5 col-form-label fw-bold small ps-5">b) the patient last work day?</label>
                                <div class="col-md-7">
                                    <input type="date" name="last_work_date" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- Question 12 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">12. Does treatment relate to an accident?</label>
                                <div class="col-md-8 d-flex align-items-center gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_accident" value="1" id="acc_yes">
                                        <label class="form-check-label small" for="acc_yes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_accident" value="0" id="acc_no">
                                        <label class="form-check-label small" for="acc_no">No</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-5 col-form-label small ps-4">If yes: a) what was the date of the accident?</label>
                                <div class="col-md-7">
                                    <input type="date" name="accident_date" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-5 col-form-label small ps-5">b) give brief details of where and how the accident happened?</label>
                                <div class="col-md-7">
                                    <input type="text" name="accident_details" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- Question 13 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-3 col-form-label fw-bold small">13. Name and address of usual doctor</label>
                                <div class="col-md-6">
                                    <input type="text" name="usual_doctor_name_address" class="form-control form-control-sm">
                                </div>
                                <label class="col-md-1 col-form-label small text-end">Tel. No.</label>
                                <div class="col-md-2">
                                    <input type="text" name="usual_doctor_telephone" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="offset-md-3 col-md-9">
                                    <input type="text" name="usual_doctor_address_line2" class="form-control form-control-sm" placeholder="Address line 2">
                                </div>
                            </div>

                            <!-- Question 14 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-7 col-form-label fw-bold small">14. Has the patient consulted any doctor for the present or any related medical condition?</label>
                                <div class="col-md-5 d-flex align-items-center gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="consulted_other_doctor" value="1" id="doc_yes">
                                        <label class="form-check-label small" for="doc_yes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="consulted_other_doctor" value="0" id="doc_no">
                                        <label class="form-check-label small" for="doc_no">No</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Table: Consultations -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="small fw-bold mb-2">
                                        If yes, for each doctor and hospital consulted state name, full address and dates first consulted.
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-3 no-print" onclick="addConsultRow()">Add Row</button>
                                    </div>
                                    <table class="table table-bordered table-sm" id="consultTable">
                                        <thead class="table-primary">
                                            <tr class="text-center">
                                                <th class="small fw-bold" style="width: 20%;">Date</th>
                                                <th class="small fw-bold" style="width: 48%;">Name & Address</th>
                                                <th class="small fw-bold" style="width: 27%;">Treatment / Consultation</th>
                                                <th class="small fw-bold no-print" style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input type="date" name="consultations[][consult_date]" class="form-control form-control-sm"></td>
                                                <td><input type="text" name="consultations[][name_address]" class="form-control form-control-sm"></td>
                                                <td><input type="text" name="consultations[][treatment_consultation]" class="form-control form-control-sm"></td>
                                                <td class="text-center no-print">
                                                    <button type="button" class="btn btn-danger btn-sm remove-row-btn" title="Remove Row"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Question 15 -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="small fw-bold mb-3">15. If we require an independent medical examination:</div>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-5 col-form-label small ps-4">a) Where is the patient now located?</label>
                                <div class="col-md-7">
                                    <input type="text" name="patient_location" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-5 col-form-label small ps-4">b) Who should be contacted to make the necessary arrangements?</label>
                                <div class="col-md-7">
                                    <input type="text" name="contact_for_exam" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- Question 16 -->
                            <div class="row mb-3">
                                <label class="col-md-4 col-form-label fw-bold small">16. Give details of any other health, medical or travel insurance...</label>
                                <div class="col-md-8">
                                    <textarea name="other_insurance_details" class="form-control form-control-sm" rows="4"></textarea>
                                </div>
                            </div>

                            <!-- Question 17 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-5 col-form-label fw-bold small">17. Is this a continuation of a previous or current treatment?</label>
                                <div class="col-md-7 d-flex align-items-center gap-3 flex-wrap">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_continuation" value="1" id="cont_yes">
                                        <label class="form-check-label small" for="cont_yes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_continuation" value="0" id="cont_no">
                                        <label class="form-check-label small" for="cont_no">No</label>
                                    </div>
                                    <span class="small">If yes, please give brief details:</span>
                                    <div class="flex-grow-1">
                                        <input type="text" name="continuation_details" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Table: Expenses -->
                            <div class="row mb-5">
                                <div class="col-12">
                                    <div class="small fw-bold mb-2">
                                        18. List of expenses for which reimbursement is now claimed
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-3 no-print" onclick="addExpenseRow()">Add Row</button>
                                    </div>
                                    <table class="table table-bordered table-sm" id="expensesTable">
                                        <thead class="table-primary">
                                            <tr class="text-center">
                                                <th class="small fw-bold" style="width: 23%;">Date of treatment</th>
                                                <th class="small fw-bold" style="width: 45%;">List of expenses</th>
                                                <th class="small fw-bold" style="width: 27%;">Currency and amount claimed / paid</th>
                                                <th class="small fw-bold no-print" style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input type="date" name="expenses[][treatment_date]" class="form-control form-control-sm"></td>
                                                <td><input type="text" name="expenses[][expense_description]" class="form-control form-control-sm" placeholder="e.g. Surgeon fee, Room charges..."></td>
                                                <td><input type="text" name="expenses[][amount_claimed]" class="form-control form-control-sm" placeholder="e.g. PKR 25,000"></td>
                                                <td class="text-center no-print">
                                                    <button type="button" class="btn btn-danger btn-sm remove-row-btn" title="Remove Row"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Section B Header -->
                            <div class="bg-primary text-white text-center py-2 fw-bold mb-4 mt-5">
                                SECTION B - TO BE COMPLETED BY THE TREATING PHYSICIAN
                            </div>

                            <!-- B1 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">1. How long have you been the patient's doctor?</label>
                                <div class="col-md-8">
                                    <input type="text" name="physician_duration" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- B2 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">2. How far back in time do your records go?</label>
                                <div class="col-md-8">
                                    <input type="text" name="records_back" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- B3 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">3. Please give the name and address of the referring physician</label>
                                <div class="col-md-8">
                                    <input type="text" name="referring_physician" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="offset-md-4 col-md-8">
                                    <input type="text" name="referring_physician_line2" class="form-control form-control-sm" placeholder="Address line 2">
                                </div>
                            </div>

                            <!-- B4 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">4. On what date were you first consulted for this condition?</label>
                                <div class="col-md-8">
                                    <input type="date" name="first_consulted_date" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- B5 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">5. Please give your diagnosis</label>
                                <div class="col-md-8">
                                    <input type="text" name="diagnosis" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="offset-md-4 col-md-8">
                                    <input type="text" name="diagnosis_line2" class="form-control form-control-sm" placeholder="Diagnosis continued...">
                                </div>
                            </div>

                            <!-- B6 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">6. If an accident is involved, how did it happen?</label>
                                <div class="col-md-8">
                                    <input type="text" name="accident_happen_details" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="offset-md-4 col-md-8">
                                    <input type="text" name="accident_happen_details_line2" class="form-control form-control-sm" placeholder="Details continued...">
                                </div>
                            </div>

                            <!-- B7 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">7. Please give details of the treatment given or prescribed:</label>
                                <div class="col-md-8">
                                    <input type="text" name="treatment_given" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="offset-md-4 col-md-8">
                                    <input type="text" name="treatment_given_line2" class="form-control form-control-sm" placeholder="Treatment details continued...">
                                </div>
                            </div>

                            <!-- B8 -->
                            <div class="row mb-3">
                                <label class="col-md-4 col-form-label fw-bold small">8. Please give a brief history of the present condition</label>
                                <div class="col-md-8">
                                    <textarea name="condition_history" class="form-control form-control-sm" rows="4"></textarea>
                                </div>
                            </div>

                            <!-- Dynamic Table: Previous Treatments -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="small fw-bold mb-2">
                                        9. Have you any reason to believe that the same or any related medical condition has been diagnosed or treated previously?
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-3 no-print" onclick="addPreviousTreatmentRow()">Add Row</button>
                                    </div>
                                    <table class="table table-bordered table-sm" id="previousTreatmentTable">
                                        <thead class="table-primary">
                                            <tr class="text-center">
                                                <th class="small fw-bold" style="width: 30%;">Dates</th>
                                                <th class="small fw-bold" style="width: 65%;">Treatment / Consultation</th>
                                                <th class="small fw-bold no-print" style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input type="text" name="previous_treatments[][dates]" class="form-control form-control-sm" placeholder="e.g. 15-03-2023"></td>
                                                <td><input type="text" name="previous_treatments[][treatment_consultation]" class="form-control form-control-sm"></td>
                                                <td class="text-center no-print">
                                                    <button type="button" class="btn btn-danger btn-sm remove-row-btn" title="Remove Row"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <small class="fw-bold">Maternity:- Please mention Normal / C-Section, D&C, Abortion</small>
                                </div>
                            </div>

                            <!-- B10 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-6 col-form-label fw-bold small">10. In respect of claims for maternity care, please state expected delivery date:</label>
                                <div class="col-md-6">
                                    <input type="date" name="expected_delivery_date" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-6 col-form-label small ps-5">and the date you were first consulted for this condition:</label>
                                <div class="col-md-6">
                                    <input type="date" name="maternity_first_consulted" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- B11 -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-3 col-form-label fw-bold small">11. Please PRINT your name:</label>
                                <div class="col-md-9">
                                    <input type="text" name="physician_name" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-2 col-form-label fw-bold small">Address</label>
                                <div class="col-md-7">
                                    <input type="text" name="physician_address_line1" class="form-control form-control-sm">
                                </div>
                                <label class="col-md-1 col-form-label small text-end">Tel. No.</label>
                                <div class="col-md-2">
                                    <input type="text" name="physician_telephone" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row mb-5">
                                <div class="offset-md-2 col-md-10">
                                    <input type="text" name="physician_address_line2" class="form-control form-control-sm" placeholder="Address line 2">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row mb-5">
                                <div class="col-12 text-center no-print">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">SUBMIT FORM</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- How to Go About Making a Claim -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center fw-bold py-3" style="letter-spacing: 1px;">
                        HOW TO GO ABOUT MAKING A CLAIM
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <p class="small mb-1"><strong>1.</strong> Responsibility lies with the employee to inform us and his employer about his intended hospitalization / surgery.</p>
                                </div>

                                <div class="mb-3">
                                    <p class="small mb-1"><strong>Emergency Cases :</strong></p>
                                    <p class="small">In an event of emergency the patient could rush to any hospital whether it is or is not a part of our Panel Hospital Network (PHN). The patient / insured employee is required to intimate Atlas Insurance within 24 hours of hospitalization through phone or fax. The charges incurred by the insured will be reimbursed Provided that the total expense falls within the limit allocated to him / her.</p>
                                </div>

                                <div class="mb-3">
                                    <p class="small">If the hospital falls under the PHN list then the insured could utilize his / her credit facility only by producing his / her OCL or a copy of which the hospital will retain, or a Atlas identification card. The employee will be served as a private patient until he / she gives a OCL or a Atlas Identification. Being a part of the PHN the hospital expenses will be settled directly by Atlas insurance and no cash outlay would be required by the insured.</p>
                                </div>

                                <div class="mb-3">
                                    <p class="small mb-1"><strong>Non Emergency Cases :</strong></p>
                                    <p class="small">When going for a planned surgery or hospitalization the insured has to inform Atlas insurance beforehand. Atlas insurance will issue relevant claim forms which should be submitted to the company prior to undertaking in patient hospital treatment and supporting medical information not later than 30 days thereafter. On receipt of the completed forms, Atlas insurance may counsel with any hospital about the Patient illness and treatment together with all other relevant details. Thereupon the employee will be issued a credit letter / treatment plan. When the insured goes to the hospital he must submit the original credit letter / treatment plan without which he / she will not be entertained. Being a part of the PHN the hospital expenses will be settled directly by Atlas insurance and no cash outlay would be required by the insured.</p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <p class="small mb-1"><strong>2.</strong> In emergency / non emergency cases, if the treatment is availed in the non-PHN, than the claim will be submitted to Atlas Insurance on prescribed forms with the following guidelines.</p>
                                </div>

                                <div class="mb-2 ms-3">
                                    <p class="small mb-1"><strong>2 i.</strong> Use a new Claim Form for each separate claim or course of treatment.</p>
                                </div>

                                <div class="mb-2 ms-3">
                                    <p class="small mb-1"><strong>2 ii</strong> The insured Person or his / her legal representatives must complete all questions in section A of the Claim Form and signs it.</p>
                                </div>

                                <div class="mb-2 ms-3">
                                    <p class="small mb-1"><strong>2 iii.</strong> The treating Physician must complete all questions in Section of the Claim Forms, rubber stamp and sign it.</p>
                                </div>

                                <div class="mb-3 ms-3">
                                    <p class="small mb-1"><strong>2 iv.</strong> Send the Claim Form, fully completed by the Insured Person and the treating Physician, together with all relevant documents to the Company.</p>
                                </div>

                                <div class="mb-3">
                                    <p class="small mb-1"><strong>3</strong> &nbsp;&nbsp;&nbsp;&nbsp; Outpatient Services are not subject to payment guarantees, and covered claims will be settled on a reimbursement basis.</p>
                                </div>

                                <div class="border border-dark p-3 mt-4">
                                    <p class="small mb-1"><strong class="text-primary">✓</strong> Incomplete Claim Forms cannot be accepted for processing of payments.</p>
                                    <p class="small mb-1"><strong class="text-primary">✓</strong> Attach originals of all relevant documents and bills.</p>
                                    <p class="small mb-0"><strong class="text-primary">✓</strong> Photocopies are not acceptable for processing a claim.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- JavaScript for Dynamic Rows -->
<script>
    function addConsultRow() {
        const tbody = document.querySelector('#consultTable tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="date" name="consultations[][consult_date]" class="form-control form-control-sm"></td>
            <td><input type="text" name="consultations[][name_address]" class="form-control form-control-sm"></td>
            <td><input type="text" name="consultations[][treatment_consultation]" class="form-control form-control-sm"></td>
            <td class="text-center no-print">
                <button type="button" class="btn btn-danger btn-sm remove-row-btn" title="Remove Row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    }

    function addExpenseRow() {
        const tbody = document.querySelector('#expensesTable tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="date" name="expenses[][treatment_date]" class="form-control form-control-sm"></td>
            <td><input type="text" name="expenses[][expense_description]" class="form-control form-control-sm" placeholder="e.g. Surgeon fee, Room charges..."></td>
            <td><input type="text" name="expenses[][amount_claimed]" class="form-control form-control-sm" placeholder="e.g. PKR 25,000"></td>
            <td class="text-center no-print">
                <button type="button" class="btn btn-danger btn-sm remove-row-btn" title="Remove Row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    }

    function addPreviousTreatmentRow() {
        const tbody = document.querySelector('#previousTreatmentTable tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="previous_treatments[][dates]" class="form-control form-control-sm" placeholder="e.g. 15-03-2023"></td>
            <td><input type="text" name="previous_treatments[][treatment_consultation]" class="form-control form-control-sm"></td>
            <td class="text-center no-print">
                <button type="button" class="btn btn-danger btn-sm remove-row-btn" title="Remove Row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    }

    // Universal remove row functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row-btn')) {
            const button = e.target.closest('.remove-row-btn');
            const row = button.closest('tr');
            const tbody = row.parentElement;
            if (tbody.children.length > 1) {  // keep at least one row
                row.remove();
            }
        }
    });
</script>

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@endsection