@extends('layouts.master')

@section('title', 'Health Insurance Enrollment Applications')

@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
    }

    .medical-header {
        background-color: #343a40 !important;
        color: white !important;
        font-size: 0.75rem;
        min-width: 100px;
        line-height: 1.2;
    }

    .medical-header small {
        color: #ffc107;
        display: block;
        margin-bottom: 2px;
        font-weight: bold;
    }

    .dependents-table th {
        font-size: 0.7rem;
        padding: 4px;
        white-space: nowrap;
    }

    .dependents-table td {
        font-size: 0.7rem;
        padding: 4px;
        white-space: nowrap;
    }
</style>

<div class="content-body">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">

                    <div class="card-header bg-primary text-white fw-bold">
                        Health Insurance Enrollment Applications
                    </div>

                    <div class="card-body p-4">

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle text-center table-sm">
                                <thead class="table-primary sticky-top">
                                    <tr style="white-space: nowrap;">
                                        <th>#</th>
                                        <th>Submitted On</th>
                                        <th>Employee Name</th>
                                        <th>Gender</th>
                                        <th>Date of Birth</th>
                                        <th>ID / CNIC</th>
                                        <th>Employer</th>
                                        <th>Occupation</th>
                                        <th>Dependents</th>

                                        <th class="medical-header"><small>Q1a</small>Smoking?</th>
                                        <th class="medical-header"><small>Q1b</small>Alcohol?</th>
                                        <th class="medical-header"><small>Q2a</small>Past Illness?</th>
                                        <th class="medical-header"><small>Q2b</small>Treatment?</th>
                                        <th class="medical-header"><small>Q3</small>Family History?</th>
                                        <th class="medical-header"><small>Q4a</small>Pregnancy?</th>
                                        <th class="medical-header"><small>Q4b</small>Refusal?</th>

                                        <th>Medical Details</th>
                                        <th class="no-print">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                @forelse($declarations as $declaration)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        <td>
                                            {{ $declaration->created_at
                                                ? \Carbon\Carbon::parse($declaration->created_at)->format('d-m-Y')
                                                : '-' }}
                                        </td>

                                        <td class="fw-bold">{{ $declaration->employee_name ?? '-' }}</td>

                                        <td>{{ ucfirst($declaration->sex ?? '-') }}</td>

                                        <td>
                                            {{ $declaration->date_of_birth
                                                ? \Carbon\Carbon::parse($declaration->date_of_birth)->format('d-m-Y')
                                                : '-' }}
                                        </td>

                                        <td>{{ $declaration->id_number ?? '-' }}</td>

                                        <td>{{ $declaration->employer_name ?? '-' }}</td>

                                        <td>{{ $declaration->occupation ?? '-' }}</td>

                                        {{-- ✅ DEPENDENTS IN SAME ROW WITH HEADINGS --}}
                                        <td class="p-1">
                                            @php
                                                $rawDependents = is_string($declaration->dependents)
                                                    ? json_decode($declaration->dependents, true)
                                                    : $declaration->dependents;

                                                $dependent = [];
                                                if (is_array($rawDependents)) {
                                                    foreach ($rawDependents as $item) {
                                                        if (is_array($item)) {
                                                            $dependent = array_merge($dependent, $item);
                                                        }
                                                    }
                                                }
                                            @endphp

                                            @if(!empty($dependent))
                                                <table class="table table-bordered table-sm mb-0 dependents-table">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>DOB</th>
                                                            <th>CNIC</th>
                                                            <th>Gender</th>
                                                            <th>Relation</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{ $dependent['name'] ?? '-' }}</td>
                                                            <td>
                                                                {{ isset($dependent['dob'])
                                                                    ? \Carbon\Carbon::parse($dependent['dob'])->format('d-m-Y')
                                                                    : '-' }}
                                                            </td>
                                                            <td>{{ $dependent['nic'] ?? '-' }}</td>
                                                            <td>{{ ucfirst($dependent['sex'] ?? '-') }}</td>
                                                            <td>{{ ucfirst($dependent['relation'] ?? '-') }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            @else
                                                -
                                            @endif
                                        </td>

                                        {{-- Health Questions --}}
                                        @foreach(['q1a','q1b','q2a','q2b','q3','q4a','q4b'] as $field)
                                            @php $ans = strtolower($declaration->$field ?? ''); @endphp
                                            <td>
                                                <span class="badge {{ $ans === 'yes' ? 'bg-danger' : 'bg-success' }}">
                                                    {{ ucfirst($ans ?: 'no') }}
                                                </span>
                                            </td>
                                        @endforeach

                                        <td title="{{ $declaration->medical_details }}">
                                            {{ \Illuminate\Support\Str::limit($declaration->medical_details ?? '-', 20) }}
                                        </td>

                                        <td class="no-print">
                                            <a href="{{ route('showauth3.edit', $declaration->id) }}"
                                               class="btn btn-sm btn-warning">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="18" class="text-center text-muted py-5">
                                            No applications found.
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
