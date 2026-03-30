@extends('layouts.master')

@section('title', 'Edit OPD Claim - Atlas Insurance Ltd.')

@section('content')
<div class="content-body">
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white fw-bold text-center">
                        EDIT OUT DOOR TREATMENT (OPD) CLAIM
                    </div>

                    <div class="card-body p-4">

                        {{-- Errors --}}
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('showauth2.update', $enrollment->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Employer --}}
                            <div class="mb-3">
                                <label class="fw-bold small">Full name of Insured (Employer)</label>
                                <input type="text" name="employer_name" class="form-control form-control-sm"
                                       value="{{ old('employer_name', $enrollment->employer_name) }}">
                            </div>

                            {{-- Employee --}}
                            <div class="mb-3">
                                <label class="fw-bold small">Full name of the Insured (Employee)</label>
                                <input type="text" name="employee_name" class="form-control form-control-sm"
                                       value="{{ old('employee_name', $enrollment->employee_name) }}">
                            </div>

                            {{-- Claimant --}}
                            <div class="mb-3">
                                <label class="fw-bold small">Full name of the Claimant</label>
                                <input type="text" name="claimant_name" class="form-control form-control-sm"
                                       value="{{ old('claimant_name', $enrollment->claimant_name) }}">
                            </div>

                            {{-- Patient --}}
                            <div class="mb-3">
                                <label class="fw-bold small">Full name of the Patient</label>
                                <input type="text" name="patient_name" class="form-control form-control-sm"
                                       value="{{ old('patient_name', $enrollment->patient_name) }}">
                            </div>

                            {{-- Relationship --}}
                            @php
                                $relationships = old('patient_relationship');

                                if (!$relationships) {
                                    $relationships = $enrollment->patient_relationship
                                        ? explode(',', $enrollment->patient_relationship)
                                        : [];
                                }
                            @endphp

                            <div class="mb-4">
                                <label class="fw-bold small d-block mb-2">
                                    Patient relationship to Employee / Claimant
                                </label>

                                @foreach(['Employee','Dependent child','Spouse'] as $rel)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="patient_relationship[]"
                                               value="{{ $rel }}"
                                               {{ in_array($rel, $relationships) ? 'checked' : '' }}>
                                        <label class="form-check-label small">{{ $rel }}</label>
                                    </div>
                                @endforeach

                                <div class="mt-2">
                                    <input type="text"
                                           name="patient_relationship_other"
                                           class="form-control form-control-sm"
                                           placeholder="Other (if any)"
                                           value="{{ old('patient_relationship_other', $enrollment->patient_relationship_other) }}">
                                </div>
                            </div>

                            {{-- Health Card --}}
                            <div class="mb-3">
                                <label class="fw-bold small">Health Card / Credit Letter No.</label>
                                <input type="text" name="health_card_no" class="form-control form-control-sm"
                                       value="{{ old('health_card_no', $enrollment->health_card_no) }}">
                            </div>

                            {{-- Clinic --}}
                            <div class="mb-3">
                                <label class="fw-bold small">Clinic / Hospital / Doctor</label>
                                <input type="text" name="clinic_hospital_doctor" class="form-control form-control-sm"
                                       value="{{ old('clinic_hospital_doctor', $enrollment->clinic_hospital_doctor) }}">
                            </div>

                            {{-- Costs --}}
                            @foreach([
                                'consultation_fee' => 'Consultation Fee',
                                'cost_of_medicines' => 'Cost of Medicines',
                                'cost_of_investigation' => 'Cost of Investigation',
                                'total_cost' => 'Total Cost'
                            ] as $field => $label)
                                <div class="mb-3">
                                    <label class="fw-bold small">{{ $label }}</label>
                                    <input type="text" name="{{ $field }}" class="form-control form-control-sm"
                                           value="{{ old($field, $enrollment->$field) }}">
                                </div>
                            @endforeach

                            {{-- Specialized --}}
                            <div class="mb-3">
                                <label class="fw-bold small">Specialized Hospital Name</label>
                                <input type="text" name="specialized_hospital_name" class="form-control form-control-sm"
                                       value="{{ old('specialized_hospital_name', $enrollment->specialized_hospital_name) }}">
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold small">Referring Specialist</label>
                                <input type="text" name="referring_specialist_name" class="form-control form-control-sm"
                                       value="{{ old('referring_specialist_name', $enrollment->referring_specialist_name) }}">
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold small">Investigation / Procedure</label>
                                <input type="text" name="investigation_procedure_name" class="form-control form-control-sm"
                                       value="{{ old('investigation_procedure_name', $enrollment->investigation_procedure_name) }}">
                            </div>

                            {{-- Investigation Checkboxes --}}
                            @foreach([
                                'cat_scan' => 'CAT Scan',
                                'mri' => 'MRI',
                                'nuclear_scan' => 'Nuclear Scan',
                                'angiography' => 'Angiography',
                                'ercp' => 'ERCP'
                            ] as $field => $label)
                                <div class="form-check mb-2">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="{{ $field }}"
                                           value="Yes"
                                           {{ old($field, $enrollment->$field) === 'Yes' ? 'checked' : '' }}>
                                    <label class="form-check-label small">{{ $label }}</label>
                                </div>
                            @endforeach

                            {{-- Submit --}}
                            <div class="mt-4 text-center">
                                <button class="btn btn-success px-5 fw-bold">
                                    UPDATE OPD CLAIM
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
