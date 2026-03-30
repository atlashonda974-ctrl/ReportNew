@extends('layouts.master')

@section('title', 'Pre-Authorization Request Form')

@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
    }
</style>

<div class="content-body">
    <div class="container-fluid py-4">

        <!-- Form Title -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center fs-4 fw-bold" style="letter-spacing: 2px;">
                        PRE-AUTHORIZATION REQUEST FORM
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('employee.store.authreqp') }}" method="POST" enctype="multipart/form-data" id="preAuthForm">
                            @csrf

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Health Card / Credit Letter No.</label>
                                <div class="col-md-8">
                                    <input type="text" name="health_card_no" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Policy Holder's Name</label>
                                <div class="col-md-8">
                                    <input type="text" name="policy_holder_name" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Policy No.</label>
                                <div class="col-md-8">
                                    <input type="text" name="policy_no" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Employee's Name</label>
                                <div class="col-md-8">
                                    <input type="text" name="employee_name" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Patient's Name / Age & Relationship</label>
                                <div class="col-md-8">
                                    <input type="text" name="patient_name_age_relation" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Hospital Name</label>
                                <div class="col-md-8">
                                    <input type="text" name="hospital_name" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">MR No. / Patient No.</label>
                                <div class="col-md-8">
                                    <input type="text" name="mr_patient_no" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Expected Date of Admission</label>
                                <div class="col-md-8">
                                    <input type="date" name="expected_admission_date" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Room / Bed No.</label>
                                <div class="col-md-8">
                                    <input type="text" name="room_bed_no" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Presenting Complaints</label>
                                <div class="col-md-8">
                                    <textarea name="presenting_complaints" class="form-control form-control-sm" rows="2" required></textarea>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">History of Presenting Illness</label>
                                <div class="col-md-8">
                                    <textarea name="history_illness" class="form-control form-control-sm" rows="2" required></textarea>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Final Diagnosis / Procedure</label>
                                <div class="col-md-8">
                                    <textarea name="diagnosis_procedure" class="form-control form-control-sm" rows="2" required></textarea>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Expected Length of Stay</label>
                                <div class="col-md-8">
                                    <input type="text" name="expected_stay" class="form-control form-control-sm" placeholder="e.g. 3 Days" required>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Expected Cost of Treatment</label>
                                <div class="col-md-8">
                                    <input type="text" name="expected_cost" class="form-control form-control-sm" placeholder="PKR 150,000" required>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Attending Doctor's Name / Contact</label>
                                <div class="col-md-8">
                                    <input type="text" name="doctor_name_contact" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-bold small">Doctor's Signature / Stamp</label>
                                <div class="col-md-8">
                                    <input type="text" name="doctor_signature" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row mt-4 mb-4">
                                <div class="col-12 text-center no-print">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        SUBMIT PRE-AUTHORIZATION REQUEST
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Combined Notes Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="row">
                            <!-- Note To Hospitals / Doctors -->
                            <div class="col-md-6">
                                <h6 class="fw-bold text-primary mb-3">Note To Hospitals / Doctors</h6>
                                <ul class="small mb-0">
                                    <li class="mb-2">Each column should be filled properly before sending to Atlas Insurance.</li>
                                    <li class="mb-0">Prior approval must be obtained at least 2 days before admission in non-emergency cases.</li>
                                </ul>
                            </div>

                            <!-- Note To Insured Member -->
                            <div class="col-md-6 border-start">
                                <h6 class="fw-bold text-primary mb-3">Note To Insured Member</h6>
                                <ul class="small mb-0">
                                    <li class="mb-2">Use this form only for planned (non-emergency) hospitalizations.</li>
                                    <li class="mb-2">Submit this form at least 2 working days before admission.</li>
                                    <li class="mb-2">Accurate information ensures faster approval.</li>
                                    <li class="mb-0">Photocopy of properly filled form is acceptable.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection