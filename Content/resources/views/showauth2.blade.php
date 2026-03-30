@extends('layouts.master')

@section('title', 'OPD Claims - Atlas Insurance Ltd.')

@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
    }
</style>

<div class="content-body">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white fw-bold">
                        Submitted OPD Claims
                    </div>
                    <div class="card-body p-4">

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle text-center table-sm">
                                <thead class="table-primary sticky-top">
                                    <tr style="white-space: nowrap;">
                                        <th>#</th>
                                        <th>Submitted On</th>
                                        <th>Employer Name</th>
                                        <th>Employee Name</th>
                                        <th>Claimant Name</th>
                                        <th>Patient Name</th>
                                        <th>Relationship</th>
                                        <th>Relationship Other</th>
                                        <th>Health Card No.</th>
                                        <th>Clinic / Hospital / Doctor</th>
                                        <th>Consultation Fee</th>
                                        <th>Cost of Medicines</th>
                                        <th>Cost of Investigation</th>
                                        <th>Total Cost</th>
                                        <th>Specialized Hospital</th>
                                        <th>Referring Specialist</th>
                                        <th>Investigation / Procedure</th>
                                        <th>Cat Scan</th>
                                        <th>MRI</th>
                                        <th>Nuclear Scan</th>
                                        <th>Angiography</th>
                                        <th>ERCP</th>
                                        <th class="no-print">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($enrollments as $claim)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $claim->created_at ? $claim->created_at->format('d-m-Y') : '-' }}</td>
                                            <td>{{ $claim->employer_name ?? '-' }}</td>
                                            <td>{{ $claim->employee_name ?? '-' }}</td>
                                            <td>{{ $claim->claimant_name ?? '-' }}</td>
                                            <td>{{ $claim->patient_name ?? '-' }}</td>
                                            <td>
                                                @if(is_array($claim->patient_relationship))
                                                    {{ implode(', ', $claim->patient_relationship) }}
                                                @else
                                                    {{ $claim->patient_relationship ?? '-' }}
                                                @endif
                                            </td>
                                            <td>{{ $claim->patient_relationship_other ?? '-' }}</td>
                                            <td>{{ $claim->health_card_no ?? '-' }}</td>
                                            <td>{{ $claim->clinic_hospital_doctor ?? '-' }}</td>
                                            <td>{{ $claim->consultation_fee ?? '-' }}</td>
                                            <td>{{ $claim->cost_of_medicines ?? '-' }}</td>
                                            <td>{{ $claim->cost_of_investigation ?? '-' }}</td>
                                            <td>{{ $claim->total_cost ?? '-' }}</td>
                                            <td>{{ $claim->specialized_hospital_name ?? '-' }}</td>
                                            <td>{{ $claim->referring_specialist_name ?? '-' }}</td>
                                            <td>{{ $claim->investigation_procedure_name ?? '-' }}</td>
                                            <td>{{ $claim->cat_scan ?? '-' }}</td>
                                            <td>{{ $claim->mri ?? '-' }}</td>
                                            <td>{{ $claim->nuclear_scan ?? '-' }}</td>
                                            <td>{{ $claim->angiography ?? '-' }}</td>
                                            <td>{{ $claim->ercp ?? '-' }}</td>
                                            <td class="no-print">
                                                <a href="{{ route('showauth2.edit', $claim->id) }}" class="btn btn-sm btn-warning">
                                                    Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="23" class="text-center text-muted py-4">
                                                No OPD claims submitted yet.
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
