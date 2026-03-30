@extends('layouts.report-master')

@section('title', 'Assets Transfer Report') <!-- Define the title section -->

@section('content') <!-- Start content section -->
    <div class="container mt-5">
        <x-report-header title="Assets Transfer Report" />

        <form method="GET" action="{{ url('/transfer') }}" class="mb-4">
            <div class="row g-3 align-items-center">
                <!-- From Date -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                        value="{{ request('start_date', date('Y-m-01')) }}">
                </div>

                <!-- To Date -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                        value="{{ request('end_date', date('Y-m-d')) }}">
                </div>

                <!-- Buttons -->
                <div class="col-md-3 d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-primary me-1" title="Filter">
                        <i class="bi bi-funnel-fill"></i>
                    </button> 
                    <a href="{{ url('/register') }}" class="btn btn-outline-secondary me-1" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                    <a href="#" class="btn btn-secondary me-1" title="History">
                        <i class="bi bi-record-circle"></i> History
                    </a>
                    <a href="#" class="btn btn-secondary" title="Bug">
                        <i class="bi bi-bug"></i> Bug
                    </a>
                </div>
            </div>
        </form>

        @if(empty($data))
            <div class="alert alert-danger">No data available.</div>
        @else
            <table id="reportsTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Asset Code</th>
                        <th>Asset Description</th>
                        <th>Date of Purchase</th>
                        <th>Date of Transfer</th>
                        <th>From Location</th>
                        <th>To Location</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                        <tr>
                            <td>{{ $record['Asset Code'] ?? 'N/A' }}</td>
                            <td>{{ $record['Asset Description'] ?? 'N/A' }}</td>
                            <td>{{ $record['Date of Purchase'] ?? 'N/A' }}</td>
                            <td>{{ $record['Date of Transfer'] ?? 'N/A' }}</td>
                            <td>{{ $record['From Location'] ?? 'N/A' }}</td>
                            <td>{{ $record['To Location'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4"></th>
                        <th></th>
                        <th id="pageTotalAmount" style="text-align: right; height: 21px;"></th> 
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>
@endsection
@include('partials.datatables-scripts')
