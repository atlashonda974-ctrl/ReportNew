@extends('layouts.master')

@section('title', 'Out Door Treatment (OPD) Claim Form - Atlas Insurance Ltd.')

@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
    }
</style>

<div class="content-body">
    <div class="container-fluid py-4">

        <!-- Form Title & Instructions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center fs-4 fw-bold" style="letter-spacing: 2px;">
                        OUT DOOR TREATMENT (OPD) CLAIM FORM
                    </div>
                    <div class="card-body text-center py-2 small text-secondary bg-primary bg-opacity-10">
                        Please attach Itemized bill, Original Prescriptions, Lab reports and receipts.
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('employee.store.authpf') }}" method="POST" enctype="multipart/form-data" id="preAuthForm">
                            @csrf

                            <!-- Employer Name -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Full name of Insured (Employer)</label>
                                <div class="col-md-8">
                                    <input type="text" name="employer_name" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <!-- Employee Name -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Full name of the Insured (Employee)</label>
                                <div class="col-md-8">
                                    <input type="text" name="employee_name" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <!-- Claimant Name -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Full name of the Claimant</label>
                                <div class="col-md-8">
                                    <input type="text" name="claimant_name" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <!-- Patient Name -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Full name of the Patient</label>
                                <div class="col-md-8">
                                    <input type="text" name="patient_name" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <!-- Patient Relationship -->
                            <div class="row mb-4 mt-4">
                                <div class="col-12">
                                    <div class="small fw-bold mb-3">Patient relationship to Employee / Claimant</div>
                                </div>
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-md-2 offset-md-1">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="patient_relationship[]" value="Employee" id="employee">
                                                <label class="form-check-label small" for="employee">Employee</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="patient_relationship[]" value="Dependent child" id="dependent">
                                                <label class="form-check-label small" for="dependent">Dependent child</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="patient_relationship[]" value="Spouse" id="spouse">
                                                <label class="form-check-label small" for="spouse">Spouse</label>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="patient_relationship[]" value="Other" id="other">
                                                    <label class="form-check-label small" for="other">Other - Please describe</label>
                                                </div>
                                                <input type="text" name="patient_relationship_other" class="form-control form-control-sm" style="max-width: 200px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Health Card -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Health Card / Credit letter No.</label>
                                <div class="col-md-8">
                                    <input type="text" name="health_card_no" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- Clinic / Doctor Name -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Name of Clinic / Hospital / Doctor</label>
                                <div class="col-md-8">
                                    <input type="text" name="clinic_hospital_doctor" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- Fees and Costs -->
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Consultation fee</label>
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rs.</span>
                                        <input type="text" name="consultation_fee" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Cost of medicines</label>
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rs.</span>
                                        <input type="text" name="cost_of_medicines" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Cost of Investigation / Lab Test</label>
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rs.</span>
                                        <input type="text" name="cost_of_investigation" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Total Cost</label>
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text fw-bold">Rs.</span>
                                        <input type="text" name="total_cost" class="form-control form-control-sm fw-bold">
                                    </div>
                                </div>
                            </div>

                            <!-- Specialized Investigation Section -->
                            <div class="bg-primary text-white text-center py-2 fw-bold mb-4 mt-5" style="letter-spacing: 2px;">
                                SPECIALIZED INVESTIGATION
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Name of the Hospital / Institution</label>
                                <div class="col-md-8">
                                    <input type="text" name="specialized_hospital_name" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Name of Referring specialist / consultant</label>
                                <div class="col-md-8">
                                    <input type="text" name="referring_specialist_name" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Name of Investigation / Procedure</label>
                                <div class="col-md-8">
                                    <input type="text" name="investigation_procedure_name" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- Instruction -->
                            <div class="row mb-4">
                                <div class="col-12 text-center small text-secondary">
                                    Please Tick whichever is applicable
                                </div>
                            </div>

                            <!-- Checkboxes for Procedures -->
                            <div class="row mb-3">
                                <div class="col-md-10 offset-md-1">
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                        <span class="small">1. CAT SCAN (Computerized Axial Tomography)</span>
                                        <div class="form-check form-check-inline mb-0">
                                            <input class="form-check-input" type="checkbox" name="cat_scan" value="Yes" id="cat_scan" style="width: 20px; height: 20px;">
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                        <span class="small">2. MRI (Magnetic Resonance Imaging)</span>
                                        <div class="form-check form-check-inline mb-0">
                                            <input class="form-check-input" type="checkbox" name="mri" value="Yes" id="mri" style="width: 20px; height: 20px;">
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                        <span class="small">3. NUCLEAR SCAN</span>
                                        <div class="form-check form-check-inline mb-0">
                                            <input class="form-check-input" type="checkbox" name="nuclear_scan" value="Yes" id="nuclear_scan" style="width: 20px; height: 20px;">
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                        <span class="small">4. ANGIOGRAPHY</span>
                                        <div class="form-check form-check-inline mb-0">
                                            <input class="form-check-input" type="checkbox" name="angiography" value="Yes" id="angiography" style="width: 20px; height: 20px;">
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                        <span class="small">5. ERCP (Endoscopic Retrograde Cholangio-Pancreatography)</span>
                                        <div class="form-check form-check-inline mb-0">
                                            <input class="form-check-input" type="checkbox" name="ercp" value="Yes" id="ercp" style="width: 20px; height: 20px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row mt-5 mb-4">
                                <div class="col-12 text-center no-print">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold">SUBMIT CLAIM</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection