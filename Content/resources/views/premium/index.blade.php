<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.master_titles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <x-datatable-styles />   
</head>
<body>
    <div class="container mt-5">
        <x-report-header title="Premium Outstanding Report" />
        <form method="GET" action="{{ url('/premium') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3 d-flex align-items-center">
                    <label for="end_date" class="form-label me-2" style="white-space: nowrap;">As on Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', date('Y-m-d')) }}">
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <label for="new_category" class="form-label me-2" style="white-space: nowrap;">Category</label>
                    <select name="new_category" id="new_category" class="form-control">
                        <option value="">All Categories</option>
                        <option value="Fire" {{ request('new_category') == 'Fire' ? 'selected' : '' }}>Fire</option>
                        <option value="Marine" {{ request('new_category') == 'Marine' ? 'selected' : '' }}>Marine</option>
                        <option value="Motor" {{ request('new_category') == 'Motor' ? 'selected' : '' }}>Motor</option>
                        <option value="Miscellaneous" {{ request('new_category') == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                        <option value="Health" {{ request('new_category') == 'Health' ? 'selected' : '' }}>Health</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <label for="outstanding_filter" class="form-label me-2" style="white-space: nowrap;">Filter By</label>
                    <select name="outstanding_filter" id="outstanding_filter" class="form-control" onchange="this.form.submit()">
                        <option value="outstanding" {{ request('outstanding_filter') == 'outstanding' ? 'selected' : '' }}>Outstanding Only</option>
                        <option value="all" {{ request('outstanding_filter') == 'all' ? 'selected' : '' }}>All Records</option>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="broker_code" class="form-label me-2" style="white-space: nowrap;">Select Broker</label>
                        <select name="broker_code" id="broker_code" class="form-control select2" onchange="this.form.submit()">
                            <option value="">All Brokers</option>
                            @foreach($brokers as $broker)
                                <option value="{{ $broker['PPS_PARTY_CODE'] }}"
                                    {{ (request('broker_code', $currentBrokerCode) == $broker['PPS_PARTY_CODE']) ? 'selected' : '' }}>
                                    {{ $broker['PPS_PARTY_NAME'] ?? $broker['PPS_DESC'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-center">
                        <button type="submit" class="btn btn-outline-primary me-1" title="Filter">
                            <i class="bi bi-funnel-fill"></i>
                        </button> 
                        <a href="{{ url('/premium') }}" class="btn btn-outline-secondary me-1" title="Reset">
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
            </div>
        </form>

        @if(empty($data))
            <div class="alert alert-danger">No data available.</div>
        @else
            @php
                $categoryMapping = [
                    11 => 'Fire',
                    12 => 'Marine',
                    13 => 'Motor',
                    14 => 'Miscellaneous',
                    16 => 'Health',
                ];
            @endphp
            
            <table id="reportsTable" class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Document Number</th>
                        <th>Department</th>
                        <th>Insured</th>
                        <th>Policy Issued</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Sum Insured</th>
                        <th>Gross Premium</th>
                        <th>Net Premium</th>
                        <th>Collection Amount</th>
                        <th>Outstanding Premium</th>
                        <th>Last Collection Date</th>   
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                    @php
                        // Get department code
                        $code = $record['PDP_DEPT_CODE'] ?? null;
                        
                        // Calculate outstanding premium
                        $netPremium = is_numeric(str_replace(',', '', $record['GDH_NETPREMIUM'] ?? 0)) ? 
                                     floatval(str_replace(',', '', $record['GDH_NETPREMIUM'])) : 0;
                        $collectionAmount = is_numeric(str_replace(',', '', $record['KNOCKOFFAMOUNT'] ?? 0)) ? 
                                           floatval(str_replace(',', '', $record['KNOCKOFFAMOUNT'])) : 0;
                        $outstandingPremium = $netPremium - $collectionAmount;
                        
                        // Format dates
                        $issueDate = null;
                        if (!empty($record['GDH_ISSUEDATE'])) {
                            try {
                                $issueDate = \Carbon\Carbon::createFromFormat('d-M-y', $record['GDH_ISSUEDATE'])->format('d-m-Y');
                            } catch (\Exception $e) {
                                try {
                                    $issueDate = \Carbon\Carbon::createFromFormat('j-M-y', $record['GDH_ISSUEDATE'])->format('d-m-Y');
                                } catch (\Exception $e) {
                                    $issueDate = null;
                                }
                            }
                        }
                        
                        $fromDate = null;
                        if (!empty($record['GDH_COMMDATE'])) {
                            try {
                                $fromDate = \Carbon\Carbon::createFromFormat('d-M-y', $record['GDH_COMMDATE'])->format('d-m-Y');
                            } catch (\Exception $e) {
                                try {
                                    $fromDate = \Carbon\Carbon::createFromFormat('j-M-y', $record['GDH_COMMDATE'])->format('d-m-Y');
                                } catch (\Exception $e) {
                                    $fromDate = null;
                                }
                            }
                        }
                        
                        $toDate = null;
                        if (!empty($record['GDH_EXPIRYDATE'])) {
                            try {
                                $toDate = \Carbon\Carbon::createFromFormat('d-M-y', $record['GDH_EXPIRYDATE'])->format('d-m-Y');
                            } catch (\Exception $e) {
                                try {
                                    $toDate = \Carbon\Carbon::createFromFormat('j-M-y', $record['GDH_EXPIRYDATE'])->format('d-m-Y');
                                } catch (\Exception $e) {
                                    $toDate = null;
                                }
                            }
                        }
                        
                        $lastCollectionDate = null;
                        if (!empty($record['VCHDATE'])) {
                            try {
                                $lastCollectionDate = \Carbon\Carbon::createFromFormat('d-M-y', $record['VCHDATE'])->format('d-m-Y');
                            } catch (\Exception $e) {
                                try {
                                    $lastCollectionDate = \Carbon\Carbon::createFromFormat('j-M-y', $record['VCHDATE'])->format('d-m-Y');
                                } catch (\Exception $e) {
                                    $lastCollectionDate = null;
                                }
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ $record['GDH_DOC_REFERENCE_NO'] ?? '' }}</td>
                        <td>{{ $categoryMapping[$code] ?? '' }}</td>
                        <td>{{ $record['PPS_DESC'] ?? '' }}</td>
                        <td>{{ $issueDate ?? '' }}</td>
                        <td>{{ $fromDate ?? 'N/A' }}</td>
                        <td>{{ $toDate ?? '' }}</td>
                        <td class="numeric" style="text-align: right;">
                            {{ trim($record['GDH_TOTALSI'] ?? '') === '&nbsp;' || empty(trim($record['GDH_TOTALSI'] ?? '')) ? '' : number_format((float) $record['GDH_TOTALSI']) }}
                        </td>
                        <td class="numeric" style="text-align: right;">
                            {{ trim($record['TOT_PRE'] ?? '') === '&nbsp;' || empty(trim($record['TOT_PRE'] ?? '')) ? '' : number_format((float) $record['TOT_PRE']) }}
                        </td>
                        <td class="numeric" style="text-align: right;">
                            {{ trim($record['GDH_NETPREMIUM'] ?? '') === '&nbsp;' || empty(trim($record['GDH_NETPREMIUM'] ?? '')) ? '' : number_format((float) $record['GDH_NETPREMIUM']) }}
                        </td>    
                        <td class="numeric" style="text-align: right;">
                            {{ isset($record['KNOCKOFFAMOUNT']) ? number_format((float) $record['KNOCKOFFAMOUNT']) : '' }}
                        </td>
                        <td class="numeric" style="text-align: right;">
                            {{ isset($record['OUTSTANDING_PREMIUM']) ? number_format((float) $record['OUTSTANDING_PREMIUM']) : 0 }}
                        </td>
                        <td>{{ $lastCollectionDate ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" style="text-align: right;">Totals</th>
                        <th id="totalSumInsured" style="text-align: right;"></th>
                        <th id="totalGrossPrem" style="text-align: right;"></th>
                        <th id="totalNetPrem" style="text-align: right;"></th>
                        <th id="totalCollection" style="text-align: right;"></th>
                        <th id="totalOutstanding" style="text-align: right;"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>

    <!-- JavaScript remains the same -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.3.2/js/dataTables.fixedHeader.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select a broker",
                allowClear: true,
                width: '69%'
            });
            var table = $('#reportsTable').DataTable({
                "paging": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "scrollX": true,
                "scrollY": "500px",
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
                        title: 'Premium Outstanding Report',
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
                        title: 'Premium Outstanding Report',
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

                    // Column 6: Sum Insured
                    var totalSumInsured = api.column(6, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Column 7: Gross Premium
                    var totalGrossPrem = api.column(7, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Column 8: Net Premium
                    var totalNetPrem = api.column(8, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Column 9: Collection Amount
                    var totalCollection = api.column(9, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Column 10: Outstanding Premium (Net Premium - Collection Amount)
                    var totalOutstanding = totalNetPrem - totalCollection;

                    // Update footer
                    $('#totalSumInsured').html(totalSumInsured.toLocaleString('en-US'));
                    $('#totalGrossPrem').html(totalGrossPrem.toLocaleString('en-US'));
                    $('#totalNetPrem').html(totalNetPrem.toLocaleString('en-US'));
                    $('#totalCollection').html(totalCollection.toLocaleString('en-US'));
                    $('#totalOutstanding').html(totalOutstanding.toLocaleString('en-US'));
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
</body>
</html>