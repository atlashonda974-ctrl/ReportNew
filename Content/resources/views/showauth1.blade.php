@extends('layouts.master')

@section('title', 'Pre-Authorization Requests')

@section('content')
<div class="content-body">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white fw-bold">
                        Pre-Authorization Requests
                    </div>
                    <div class="card-body p-4">

                        {{-- Success message --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Table --}}
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                            <table class="table table-bordered table-striped table-hover align-middle text-center">
                                <thead class="table-light sticky-top">
                                    <tr style="white-space: nowrap;">
                                        <th>#</th>
                                        <th>Health Card No.</th>
                                        <th>Policy Holder</th>
                                        <th>Policy No.</th>
                                        <th>Employee Name</th>
                                        <th>Patient Name/Age/Relation</th>
                                        <th>Hospital</th>
                                        <th>MR Patient No.</th>
                                        <th>Admission Date</th>
                                        <th>Room/Bed No.</th>
                                        <th>Presenting Complaints</th>
                                        <th>History of Illness</th>
                                        <th>Diagnosis/Procedure</th>
                                        <th>Expected Stay</th>
                                        <th>Expected Cost</th>
                                        <th>Doctor Name/Contact</th>
                                        <th>Doctor Signature</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $employee)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $employee->health_card_no ?? '-' }}</td>
                                            <td>{{ $employee->policy_holder_name ?? '-' }}</td>
                                            <td>{{ $employee->policy_no ?? '-' }}</td>
                                            <td>{{ $employee->employee_name ?? '-' }}</td>
                                            <td>{{ $employee->patient_name_age_relation ?? '-' }}</td>
                                            <td>{{ $employee->hospital_name ?? '-' }}</td>
                                            <td>{{ $employee->mr_patient_no ?? '-' }}</td>
                                            <td>{{ $employee->expected_admission_date ?? '-' }}</td>
                                            <td>{{ $employee->room_bed_no ?? '-' }}</td>
                                            <td>{{ $employee->presenting_complaints ?? '-' }}</td>
                                            <td>{{ $employee->history_illness ?? '-' }}</td>
                                            <td>{{ $employee->diagnosis_procedure ?? '-' }}</td>
                                            <td>{{ $employee->expected_stay ?? '-' }}</td>
                                            <td>{{ $employee->expected_cost ?? '-' }}</td>
                                            <td>{{ $employee->doctor_name_contact ?? '-' }}</td>
                                            <td>{{ $employee->doctor_signature ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('showauth1.edit', $employee->id) }}" class="btn btn-sm btn-warning">
                                                    Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
