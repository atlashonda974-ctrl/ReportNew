@extends('layouts.report-master')

@section('title', 'Reinsurance Report') 

@section('content') 
    <div class="container mt-5">
        <x-report-header title="Reinsurance Report" />

        <form method="GET" action="{{ url('/reinsurace') }}" class="mb-4">
            @if (!empty($error))
                <div class="alert alert-danger">
                    {{ $error }}
                </div>
            @endif
            @if(isset($debug) && config('app.debug'))
                <div class="card mt-4">
                    <div class="card-header">Debug Information</div>
                    <div class="card-body">
                        <pre>{{ print_r($debug, true) }}</pre>
                    </div>
                </div>
            @endif

            @if(isset($data) && count($data) > 0)
                <!-- Your data display code -->
            @else
                <div class="alert alert-info">No data is available for the selected criteria because the date range exceeds the limit of 60 days.</div>
            @endif

            @if(isset($recordCount))
                <p>Total Records: {{ $recordCount }}</p>
            @endif

            <div class="row g-3 align-items-center">
                <!-- From Date -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                        value="{{ old('start_date', $startInput ?? date('Y-m-01')) }}">
                </div>

                <!-- To Date -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                        value="{{ old('end_date', $endInput ?? date('Y-m-d')) }}">
                </div>

                <!-- Buttons -->
                <div class="col-md-3 d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-primary me-1" title="Filter">
                        <i class="bi bi-funnel-fill"></i>
                    </button> 
                    <a href="{{ url('/reinsurace') }}" class="btn btn-outline-secondary me-1" title="Reset">
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
                        <th>Policy No Fac.AB</th>
                        <th>Insured</th>
                        <th>Issue Date</th>
                        <th>Comma Date</th>
                        <th>Exp Date</th>
                        <th>Sum Insured</th>
                        <th>Fac.Loc</th>
                        <th>Fac.Ab</th>
                        <th>Gross Prem</th>
                        <th>Fac.Loc</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                        <tr>
                            <td>
                                <a href="#" class="policy-link" 
                                   data-id="{{ $record['GSI_DOC_REFERENCE_NO'] }}" 
                                   data-expiry="{{ isset($record['GSI_EXPIRYDATE']) ? \Carbon\Carbon::createFromFormat('j-M-y', $record['GSI_EXPIRYDATE'])->format('d-m-Y') : 'N/A' }}" 
                                   data-from="{{ old('start_date', isset($startInput) ? \Carbon\Carbon::parse($startInput)->format('d-m-Y') : now()->startOfMonth()->format('d-m-Y')) }}"
                                   data-to="{{ old('end_date', isset($endInput) ? \Carbon\Carbon::parse($endInput)->format('d-m-Y') : now()->format('d-m-Y')) }}"
                                   data-insured="{{ $record['PPS_DESC'] ?? 'N/A' }}"
                                   data-sumInsured="{{ number_format($record['GSI_COTOTALSI'] ?? 0, 0, '.', ',') }}">
                                    {{ $record['GSI_DOC_REFERENCE_NO'] ?? 'N/A' }}
                                </a>
                            </td>
                            <td>{{ $record['PPS_DESC'] ?? 'N/A' }}</td>
                            <td>
                                {{ isset($record['GSI_ISSUEDATE']) ? \Carbon\Carbon::createFromFormat('j-M-y', $record['GSI_ISSUEDATE'])->format('d-m-Y') : 'N/A' }}
                            </td>
                            <td>
                                {{ isset($record['GSI_COMMDATE']) ? \Carbon\Carbon::createFromFormat('j-M-y', $record['GSI_COMMDATE'])->format('d-m-Y') : 'N/A' }}
                            </td>
                            <td>
                                {{ isset($record['GSI_EXPIRYDATE']) ? \Carbon\Carbon::createFromFormat('j-M-y', $record['GSI_EXPIRYDATE'])->format('d-m-Y') : 'N/A' }}
                            </td>
                            <td class="numeric" style="text-align: right;">
                                {{ number_format($record['GSI_COTOTALSI'] ?? 0, 0, '.', ',') }}
                            </td>
                            <td class="numeric" style="text-align: right;">
                                {{ ($record['GSI_FACULTSI'] ?? 0) > 0 ? number_format($record['GSI_FACULTSI'], 0, '.', ',') : '' }}
                            </td>
                            <td class="numeric" style="text-align: right;">
                                {{ ($record['GSI_FOREIGN_FACULTSI'] ?? 0) > 0 ? number_format($record['GSI_FOREIGN_FACULTSI'], 0, '.', ',') : '' }}
                            </td>
                            <td class="numeric" style="text-align: right;">
                                {{ number_format($record['GSI_COTOTALPREM'] ?? 0, 0, '.', ',') }}
                            </td>
                            <td class="numeric" style="text-align: right;">
                                {{ ($record['GSI_FACULTPREM'] ?? 0) > 0 ? number_format($record['GSI_FACULTPREM'], 0, '.', ',') : '' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" style="text-align: right;">Totals</th>
                        <th id="totalSumInsured" style="text-align: right;"></th> 
                        <th id="totalFacLoc" style="text-align: right;"></th>
                        <th id="totalFacAb" style="text-align: right;"></th>
                        <th id="totalGrossPrem" style="text-align: right;"></th>
                        <th id="totalFacLoc2" style="text-align: right;"></th>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>

    <!-- Modal -->
    <div class="modal fade" id="policyModal" tabindex="-1" aria-labelledby="policyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="policyModalLabel">Policy Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="policyDetails">
                        <!-- Complete policy data will be displayed here -->
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <!-- <tr>
                                <th>Sr. No</th>
                                <th>Insured</th>
                                <th>Exp Date</th>
                                <th>Sum Insured</th>
                                <th>Fac.Loc</th>
                                <th>Fac.Ab</th>
                                <th>Gross Prem</th>
                            </tr> -->
                        </thead>
                        <tbody id="modalPolicyGridBody">
                            <!-- Dynamic rows will be added here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#reportsTable').DataTable({
            "paging": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "scrollX": true,
            "scrollY": "210px",
            "scrollCollapse": false,
            "fixedHeader": {
                header: true,
                footer: true
            },
            "autoWidth": true,
            dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel custom-icon"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Reinsurance Report',
                    footer: true,
                    exportOptions: {
                        modifier: {
                            page: 'current'
                        }
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf custom-icon"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Reinsurance Report',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    footer: true,
                    exportOptions: {
                        columns: ':visible',
                        modifier: {
                            page: 'current'
                        }
                    }
                }
            ],
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();

                var intVal = function (i) {
                    return typeof i === 'string' ? 
                        i.replace(/[^\d.-]/g, '') * 1 : 
                        typeof i === 'number' ? i : 0;
                };

                var totalSumInsured = api.column(5, {page: 'current'}).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                var totalFacLoc = api.column(6, {page: 'current'}).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                var totalFacAb = api.column(7, {page: 'current'}).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                var totalGrossPrem = api.column(8, {page: 'current'}).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                var totalFacLoc2 = api.column(9, {page: 'current'}).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

                $('#totalSumInsured').html(totalSumInsured.toLocaleString('en-US'));
                $('#totalFacLoc').html(totalFacLoc.toLocaleString('en-US'));
                $('#totalFacAb').html(totalFacAb.toLocaleString('en-US'));
                $('#totalGrossPrem').html(totalGrossPrem.toLocaleString('en-US'));
                $('#totalFacLoc2').html(totalFacLoc2.toLocaleString('en-US'));
            },
            initComplete: function() {
                this.api().columns.adjust();
                $('.dataTables_filter').css('margin-left', '5px').css('margin-right', '5px');
                $('.dt-buttons').css('margin-left', '5px');
            },
            drawCallback: function() {
                this.api().columns.adjust();
            }
        });
    });
    </script>
<script>
    $(document).on('click', '.policy-link', function(e) {
        e.preventDefault();
        
        // Get data from clicked policy link
        const documentNo = $(this).data('id');
        const expiryDate = $(this).data('expiry');
        const insured = $(this).data('insured');
        const sumInsured = $(this).data('suminsured');
        const issueDate = $(this).closest('tr').find('td:eq(2)').text(); // Issue Date
        const commaDate = $(this).closest('tr').find('td:eq(3)').text(); // Comma Date
        const fromDate = $(this).data('from');
        const toDate = $(this).data('to');
        
        // Display complete header info with full record details
        $('#policyDetails').html(`
            <div class="row">
                <div class="col-md-6">
                    <p style="margin:0"><strong>Document Number:</strong> ${documentNo}</p>
                    <p style="margin:0"><strong>Insured:</strong> ${insured}</p>
                    <p style="margin:0"><strong>Sum Insured:</strong> ${sumInsured}</p>
                    <p style="margin:0"><strong>Policy Period:</strong>From: ${commaDate} To: ${expiryDate}</p>
                </div>
            </div>
            <p style="margin:0"><strong>Total Records:</strong> {{ count($data) }}</p>
        `);

        // Clear previous grid rows
        $('#modalPolicyGridBody').empty();

        // Loop through all $data records and populate modal table
        // @foreach($data as $index => $record)
        //     $('#modalPolicyGridBody').append(`
        //         <tr>
        //             <td>{{ $index + 1 }}</td>
        //             <td>{{ $record['PPS_DESC'] ?? 'N/A' }}</td>
        //             <td>
        //                 @if(isset($record['GSI_EXPIRYDATE']))
        //                     {{ \Carbon\Carbon::createFromFormat('j-M-y', $record['GSI_EXPIRYDATE'])->format('d-m-Y') }}
        //                 @else
        //                     N/A
        //                 @endif
        //             </td>
        //             <td style="text-align: right;">
        //                 {{ number_format($record['GSI_COTOTALSI'] ?? 0, 0, '.', ',') }}
        //             </td>
        //             <td style="text-align: right;">
        //                 @if(($record['GSI_FACULTSI'] ?? 0) > 0)
        //                     {{ number_format($record['GSI_FACULTSI'], 0, '.', ',') }}
        //                 @endif
        //             </td>
        //             <td style="text-align: right;">
        //                 @if(($record['GSI_FOREIGN_FACULTSI'] ?? 0) > 0)
        //                     {{ number_format($record['GSI_FOREIGN_FACULTSI'], 0, '.', ',') }}
        //                 @endif
        //             </td>
        //             <td style="text-align: right;">
        //                 {{ number_format($record['GSI_COTOTALPREM'] ?? 0, 0, '.', ',') }}
        //             </td>
        //         </tr>
        //     `);
        // @endforeach

        // Show the modal
        $('#policyModal').modal('show');
    });
</script>
@endsection