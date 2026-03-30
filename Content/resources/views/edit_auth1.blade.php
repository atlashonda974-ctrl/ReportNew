@extends('layouts.master')

@section('title', 'Edit Pre-Authorization Request')

@section('content')
<div class="content-body">
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white fw-bold">
                        Edit Pre-Authorization Request
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                          <form action="{{ route('showauth1.update', $employee->id) }}" method="POST">                           
                             @csrf
                             @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Health Card No.</label>
                                    <input type="text" name="health_card_no" class="form-control" value="{{ old('health_card_no', $employee->health_card_no) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Policy Holder</label>
                                    <input type="text" name="policy_holder_name" class="form-control" value="{{ old('policy_holder_name', $employee->policy_holder_name) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Policy No.</label>
                                    <input type="text" name="policy_no" class="form-control" value="{{ old('policy_no', $employee->policy_no) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Employee Name</label>
                                    <input type="text" name="employee_name" class="form-control" value="{{ old('employee_name', $employee->employee_name) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Patient Name/Age/Relation</label>
                                    <input type="text" name="patient_name_age_relation" class="form-control" value="{{ old('patient_name_age_relation', $employee->patient_name_age_relation) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Hospital</label>
                                    <input type="text" name="hospital_name" class="form-control" value="{{ old('hospital_name', $employee->hospital_name) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">MR Patient No.</label>
                                    <input type="text" name="mr_patient_no" class="form-control" value="{{ old('mr_patient_no', $employee->mr_patient_no) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Expected Admission Date</label>
                                    <input type="date" name="expected_admission_date" class="form-control" value="{{ old('expected_admission_date', $employee->expected_admission_date) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Room/Bed No.</label>
                                    <input type="text" name="room_bed_no" class="form-control" value="{{ old('room_bed_no', $employee->room_bed_no) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Presenting Complaints</label>
                                    <input type="text" name="presenting_complaints" class="form-control" value="{{ old('presenting_complaints', $employee->presenting_complaints) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">History of Illness</label>
                                    <input type="text" name="history_illness" class="form-control" value="{{ old('history_illness', $employee->history_illness) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Diagnosis/Procedure</label>
                                    <input type="text" name="diagnosis_procedure" class="form-control" value="{{ old('diagnosis_procedure', $employee->diagnosis_procedure) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Expected Stay</label>
                                    <input type="text" name="expected_stay" class="form-control" value="{{ old('expected_stay', $employee->expected_stay) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Expected Cost</label>
                                    <input type="text" name="expected_cost" class="form-control" value="{{ old('expected_cost', $employee->expected_cost) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Doctor Name/Contact</label>
                                    <input type="text" name="doctor_name_contact" class="form-control" value="{{ old('doctor_name_contact', $employee->doctor_name_contact) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Doctor Signature</label>
                                    <input type="text" name="doctor_signature" class="form-control" value="{{ old('doctor_signature', $employee->doctor_signature) }}">
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
