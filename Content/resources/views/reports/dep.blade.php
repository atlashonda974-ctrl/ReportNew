@extends('layouts.report-master')

@section('title', 'Assets Deletion Report') <!-- Define the title section -->

@section('content') <!-- Start content section -->
<div class="container mt-5">
        <!-- Breadcrumb Navigation -->
        <x-report-header title="Assets Deletion Report" />

        <form method="GET" action="{{ url('/dep') }}" class="mb-4">
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
                    <!-- History Button with Elegant Icon -->
                    <a href="#" class="btn btn-secondary me-1" title="History">
                        <i class="bi bi-record-circle"></i> History
                    </a>
                    <!-- Bug Button with Icon -->
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
                        <th>Date of Sale</th>
                        <th>Original Cost</th>
                        <th>Acc_Depreciation</th>
                        <th>Sale Proceed</th>
                        <th>Net Book Value</th>
                        <th>Gain/Loss</th>
                        <th>Party Member</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                        <tr>
                            <td>{{ $record['Asset Code'] ?? 'N/A' }}</td>
                            <td>{{ $record['Asset Description'] ?? 'N/A' }}</td>
                            <td>{{ $record['Date of Purchase'] ?? 'N/A' }}</td>
                            <td>{{ $record['Date of Sale'] ?? 'N/A' }}</td>
                            <!-- Right-aligned numeric values -->
                          <!-- Right-aligned numeric values -->
                            <td class="numeric" style="text-align: right;">{{ number_format($record['Original Cost'] ?? 0, 0, '.', ',') }}</td>
                            <td class="numeric" style="text-align: right;">{{ number_format($record['Accumulated Depreciation'] ?? 0, 0, '.', ',') }}</td>
                            <td class="numeric" style="text-align: right;">{{ number_format($record['Sale Proceed'] ?? 0, 0, '.', ',') }}</td>
                            <td class="numeric" style="text-align: right;">{{ rtrim(rtrim(number_format(($record['Original Cost'] ?? 0) - ($record['Accumulated Depreciation'] ?? 0), 2, '.', ','), '0'), '.') }}</td>
                            <td class="numeric" style="text-align: right;">{{ rtrim(rtrim(number_format((($record['Original Cost'] ?? 0) - ($record['Accumulated Depreciation'] ?? 0)) - ($record['Sale Proceed'] ?? 0), 2, '.', ','), '0'), '.') }}</td>
                            <td class="text-right">{{ $record['Party Member '] ?? 'N/A' }}</td>


                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Totals</th>
                        <th id="totalOriginalCost">{{ number_format($totalOriginalCost, 2, '.', ',') }}</th>
                        <th id="totalAccumulatedDepreciation">{{ number_format($totalAccumulatedDepreciation, 2, '.', ',') }}</th>
                        <th id="totalSaleProceed">{{ number_format($totalSaleProceed, 2, '.', ',') }}</th>
                        <th id="totalNetBookValue">{{ number_format($totalNetBookValue, 2, '.', ',') }}</th>
                        <th id="totalGainLoss">{{ number_format($totalGainLoss, 2, '.', ',') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>
@endsection
    <script>

          footerCallback: function (row, data, start, end, display) {
                    let api = this.api();
                    let intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/,/g, '') * 1 : typeof i === 'number' ? i : 0;
                    };

                    let columnsToSum = [4, 5, 6, 7, 8]; // Columns to sum

                    columnsToSum.forEach(function (colIndex) {
                        let pageTotal = api.column(colIndex, { page: 'current' }).data()
                            .reduce((a, b) => intVal(a) + intVal(b), 0);

                        $(api.column(colIndex).footer()).html(
                            pageTotal.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            })
                        );
                    });
                },
    </script>
@include('partials.datatables-scripts')
