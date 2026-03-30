@extends('layouts.master')

@section('title', 'Edit Enrollment Application')

@section('content')
<style>
    .section-title {
        background-color: #f8f9fa;
        padding: 10px;
        border-left: 5px solid #0d6efd;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .dependents-table th,
    .dependents-table td {
        font-size: 0.8rem;
        padding: 6px;
        white-space: nowrap;
    }
</style>

@php
    // Handle dependents safely (string OR array)
    $rawDependents = is_string($declaration->dependents)
        ? json_decode($declaration->dependents, true)
        : $declaration->dependents;

    // Fix broken structure into one dependent
    $dependent = [];
    if (is_array($rawDependents)) {
        foreach ($rawDependents as $item) {
            if (is_array($item)) {
                $dependent = array_merge($dependent, $item);
            }
        }
    }
@endphp

<div class="content-body">
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-11">
                <div class="card shadow-sm">

                    <div class="card-header bg-primary text-white text-center fs-4 fw-bold">
                        EDIT ENROLLMENT APPLICATION
                    </div>

                    <div class="card-body p-4">

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('showauth3.update', $declaration->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- PERSONAL INFO --}}
                            <div class="section-title">Personal Information</div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Employee Name</label>
                                    <input type="text" name="employee_name" class="form-control form-control-sm"
                                           value="{{ old('employee_name', $declaration->employee_name) }}" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold small">Gender</label>
                                    <select name="sex" class="form-select form-select-sm">
                                        <option value="male" {{ $declaration->sex === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ $declaration->sex === 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold small">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control form-control-sm"
                                           value="{{ old('date_of_birth', optional($declaration->date_of_birth)->format('Y-m-d')) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">ID / CNIC Number</label>
                                    <input type="text" name="id_number" class="form-control form-control-sm"
                                           value="{{ old('id_number', $declaration->id_number) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Employer Name</label>
                                    <input type="text" name="employer_name" class="form-control form-control-sm"
                                           value="{{ old('employer_name', $declaration->employer_name) }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small">Home Address</label>
                                    <textarea name="home_address" class="form-control form-control-sm" rows="2">
                                        {{ old('home_address', $declaration->home_address) }}
                                    </textarea>
                                </div>
                            </div>

                            {{-- DEPENDENTS --}}
                            <div class="section-title mt-5">Dependents Information</div>

                            <table class="table table-bordered dependents-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Date of Birth</th>
                                        <th>CNIC</th>
                                        <th>Gender</th>
                                        <th>Relation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" name="dependents[name]"
                                                   class="form-control form-control-sm"
                                                   value="{{ old('dependents.name', $dependent['name'] ?? '') }}">
                                        </td>
                                        <td>
                                            <input type="date" name="dependents[dob]"
                                                   class="form-control form-control-sm"
                                                   value="{{ old('dependents.dob', $dependent['dob'] ?? '') }}">
                                        </td>
                                        <td>
                                            <input type="text" name="dependents[nic]"
                                                   class="form-control form-control-sm"
                                                   value="{{ old('dependents.nic', $dependent['nic'] ?? '') }}">
                                        </td>
                                        <td>
                                            <select name="dependents[sex]" class="form-select form-select-sm">
                                                <option value="">Select</option>
                                                <option value="male" {{ ($dependent['sex'] ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ ($dependent['sex'] ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="dependents[relation]"
                                                   class="form-control form-control-sm"
                                                   value="{{ old('dependents.relation', $dependent['relation'] ?? '') }}">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            {{-- HEALTH QUESTIONS --}}
                            <div class="section-title mt-5">Health Declaration Questions</div>

                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Question</th>
                                        <th style="width: 160px;">Answer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @php
                                    $questions = [
                                        'q1a' => 'Do you smoke or use tobacco products?',
                                        'q1b' => 'Do you consume alcohol?',
                                        'q2a' => 'Have you had any past illnesses?',
                                        'q2b' => 'Are you currently under treatment?',
                                        'q3'  => 'Is there any significant family medical history?',
                                        'q4a' => 'Are you currently pregnant? (If female)',
                                        'q4b' => 'Has any insurance company ever refused your application?'
                                    ];
                                @endphp

                                @foreach($questions as $key => $label)
                                    <tr>
                                        <td class="small">{{ $label }}</td>
                                        <td>
                                            <label class="me-2">
                                                <input type="radio" name="{{ $key }}" value="yes"
                                                    {{ strtolower($declaration->$key) === 'yes' ? 'checked' : '' }}> Yes
                                            </label>
                                            <label>
                                                <input type="radio" name="{{ $key }}" value="no"
                                                    {{ strtolower($declaration->$key) !== 'yes' ? 'checked' : '' }}> No
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <div class="mt-3">
                                <label class="form-label fw-bold small">Medical Details / Explanation</label>
                                <textarea name="medical_details" class="form-control" rows="3">
                                    {{ old('medical_details', $declaration->medical_details) }}
                                </textarea>
                            </div>

                            <hr class="my-5">

                            <div class="mt-4">
                                <button type="submit" class="btn btn-success">Update Record</button>
                                <a href="{{ route('showauth3') }}" class="btn btn-secondary ms-2">Back</a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
