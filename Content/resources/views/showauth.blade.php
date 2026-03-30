@extends('layouts.master')

@section('title', 'Medical Reimbursement Claims')

@section('content')
<div class="content-body">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white fw-bold">
                        Medical Reimbursement Claims - All Details
                    </div>
                    <div class="card-body p-4">

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Full Table with All Columns -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-sm text-center align-middle">
                                <thead class="table-primary sticky-top" style="font-size: 0.85rem;">
                                    <tr style="white-space: nowrap;">
                                        <th>#</th>
                                        
                                        <th>Insured (Employer)</th>
                                        <th>Claimant Name</th>
                                        <th>Employee Name</th>
                                        <th>Employee Tel</th>
                                        <th>Policy No.</th>
                                        <th>Health Card No.</th>
                                        <th>Patient Name</th>
                                        <th>Relationship</th>
                                        <th>Other Relation</th>
                                        <th>Patient DOB</th>
                                        <th>Gender</th>
                                        <th>CNIC/Passport</th>
                                        <th>Country Residence</th>
                                        <th>Nationality</th>
                                        <th>Employer Address</th>
                                        <th>Employer Tel</th>
                                        <th>Illness Description</th>
                                        <th>Symptoms First Date</th>
                                        <th>Last Work Date</th>
                                        <th>Is Accident?</th>
                                        <th>Accident Date</th>
                                        <th>Accident Details</th>
                                        <th>Usual Doctor</th>
                                        <th>Usual Doctor Tel</th>
                                        <th>Consulted Other?</th>
                                        <th>Patient Location</th>
                                        <th>Contact for Exam</th>
                                        <th>Other Insurance</th>
                                        <th>Continuation?</th>
                                        <th>Continuation Details</th>
                                        <th>Physician Duration</th>
                                        <th>Records Back</th>
                                        <th>Referring Physician</th>
                                        <th>First Consulted Date</th>
                                        <th>Diagnosis</th>
                                        <th>Accident Details (Physician)</th>
                                        <th>Treatment Given</th>
                                        <th>Condition History</th>
                                        <th>Expected Delivery Date</th>
                                        <th>Maternity First Consulted</th>
                                        <th>Treating Physician Name</th>
                                        <th>Physician Address</th>
                                        <th>Physician Tel</th>
                                        <th class="no-print">Actions</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.8rem;">
                                    @forelse($employees as $claim)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            
                                            <td>{{ Str::limit($claim->insured_name ?? '-', 20) }}</td>
                                            <td>{{ Str::limit($claim->claimant_name ?? '-', 20) }}</td>
                                            <td>{{ Str::limit($claim->employee_name ?? '-', 20) }}</td>
                                            <td>{{ $claim->employee_telephone ?? '-' }}</td>
                                            <td>{{ $claim->policy_no ?? '-' }}</td>
                                            <td>{{ $claim->health_card_no ?? '-' }}</td>
                                            <td>{{ $claim->patient_name ?? '-' }}</td>
                                            <td>{{ $claim->patient_relationship ?? '-' }}</td>
                                            <td>{{ $claim->relationship_other ?? '-' }}</td>
                                            <td>{{ $claim->patient_dob ? \Carbon\Carbon::parse($claim->patient_dob)->format('d-m-Y') : '-' }}</td>
                                            <td>{{ ucfirst($claim->patient_gender ?? '-') }}</td>
                                            <td>{{ $claim->patient_cnic_passport ?? '-' }}</td>
                                            <td>{{ $claim->country_residence ?? '-' }}</td>
                                            <td>{{ $claim->nationality ?? '-' }}</td>
                                            <td>
                                                {{ Str::limit($claim->employer_address_line1 ?? '', 25) }}<br>
                                                <small>{{ $claim->employer_address_line2 ?? '' }}</small>
                                            </td>
                                            <td>{{ $claim->employer_telephone ?? '-' }}</td>
                                            <td>{{ Str::limit($claim->illness_description ?? '-', 30) }}</td>
                                            <td>{{ $claim->symptoms_first_date ? \Carbon\Carbon::parse($claim->symptoms_first_date)->format('d-m-Y') : '-' }}</td>
                                            <td>{{ $claim->last_work_date ? \Carbon\Carbon::parse($claim->last_work_date)->format('d-m-Y') : '-' }}</td>
                                            <td>
                                                @if($claim->is_accident)
                                                    <span class="badge bg-danger">Yes</span>
                                                @else
                                                    <span class="badge bg-success">No</span>
                                                @endif
                                            </td>
                                            <td>{{ $claim->accident_date ? \Carbon\Carbon::parse($claim->accident_date)->format('d-m-Y') : '-' }}</td>
                                            <td>{{ Str::limit($claim->accident_details ?? '-', 30) }}</td>
                                            <td>
                                                {{ Str::limit($claim->usual_doctor_name_address ?? '', 25) }}<br>
                                                <small>{{ $claim->usual_doctor_address_line2 ?? '' }}</small>
                                            </td>
                                            <td>{{ $claim->usual_doctor_telephone ?? '-' }}</td>
                                            <td>
                                                @if($claim->consulted_other_doctor)
                                                    <span class="badge bg-info">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($claim->patient_location ?? '-', 25) }}</td>
                                            <td>{{ Str::limit($claim->contact_for_exam ?? '-', 25) }}</td>
                                            <td>{{ Str::limit($claim->other_insurance_details ?? '-', 30) }}</td>
                                            <td>
                                                @if($claim->is_continuation)
                                                    <span class="badge bg-warning">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($claim->continuation_details ?? '-', 30) }}</td>
                                            <td>{{ $claim->physician_duration ?? '-' }}</td>
                                            <td>{{ $claim->records_back ?? '-' }}</td>
                                            <td>
                                                {{ Str::limit($claim->referring_physician ?? '', 25) }}<br>
                                                <small>{{ $claim->referring_physician_line2 ?? '' }}</small>
                                            </td>
                                            <td>{{ $claim->first_consulted_date ? \Carbon\Carbon::parse($claim->first_consulted_date)->format('d-m-Y') : '-' }}</td>
                                            <td>
                                                {{ Str::limit($claim->diagnosis ?? '', 30) }}<br>
                                                <small>{{ $claim->diagnosis_line2 ?? '' }}</small>
                                            </td>
                                            <td>
                                                {{ Str::limit($claim->accident_happen_details ?? '', 30) }}<br>
                                                <small>{{ $claim->accident_happen_details_line2 ?? '' }}</small>
                                            </td>
                                            <td>
                                                {{ Str::limit($claim->treatment_given ?? '', 30) }}<br>
                                                <small>{{ $claim->treatment_given_line2 ?? '' }}</small>
                                            </td>
                                            <td>{{ Str::limit($claim->condition_history ?? '-', 40) }}</td>
                                            <td>{{ $claim->expected_delivery_date ? \Carbon\Carbon::parse($claim->expected_delivery_date)->format('d-m-Y') : '-' }}</td>
                                            <td>{{ $claim->maternity_first_consulted ? \Carbon\Carbon::parse($claim->maternity_first_consulted)->format('d-m-Y') : '-' }}</td>
                                            <td>{{ $claim->physician_name ?? '-' }}</td>
                                            <td>
                                                {{ Str::limit($claim->physician_address_line1 ?? '', 25) }}<br>
                                                <small>{{ $claim->physician_address_line2 ?? '' }}</small>
                                            </td>
                                            <td>{{ $claim->physician_telephone ?? '-' }}</td>
                                           <td>
                                                <a href="{{ route('showauth.edit', $claim->id) }}" class="btn btn-sm btn-warning">
                                                    Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="46" class="text-center text-muted py-4">
                                                No medical reimbursement claims submitted yet.
                                            </td>
                                        </tr>
                                    @endforelse
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