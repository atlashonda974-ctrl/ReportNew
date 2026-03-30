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
        <x-report-header title="Renewal Report" />
        <form method="GET" action="{{ url('/renewal') }}" class="mb-4">
            <div class="row g-3">
                <!-- From Date -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $start_date }}">
                </div>
                <!-- To Date -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $end_date }}">
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <label for="new_category" class="form-label me-2" style="white-space: nowrap; width: 100px;">Category</label>
                    <select name="new_category" id="new_category" class="form-control">
                        <option value="">All Categories</option>
                        <option value="Fire" {{ request('new_category') == 'Fire' ? 'selected' : '' }}>Fire</option>
                        <option value="Marine" {{ request('new_category') == 'Marine' ? 'selected' : '' }}>Marine</option>
                        <option value="Motor" {{ request('new_category') == 'Motor' ? 'selected' : '' }}>Motor</option>
                        <option value="Miscellaneous" {{ request('new_category') == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                        <option value="Health" {{ request('new_category') == 'Health' ? 'selected' : '' }}>Health</option>
                    </select>
                </div>
                <!-- In your Blade template -->
                <div class="row g-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="broker_code" class="form-label me-2" style="white-space: nowrap;">Select Broker</label>
                        <select name="broker_code" id="broker_code" class="form-control select2" onchange="this.form.submit()">
                            <option value="">All Brokers</option> <!-- Always show this option -->
                            @foreach($brokers as $broker)
                                <option value="{{ $broker['PPS_PARTY_CODE'] }}"
                                    {{ (request('broker_code', $currentBrokerCode) == $broker['PPS_PARTY_CODE']) ? 'selected' : '' }}>
                                    {{ $broker['PPS_PARTY_NAME'] ?? $broker['PPS_DESC'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Filter Button -->
                    <div class="col-md-3 d-flex align-items-center">
                        <button type="submit" class="btn btn-outline-primary me-1" title="Filter">
                            <i class="bi bi-funnel-fill"></i>
                        </button>
                        <a href="{{ url('/renewal') }}" class="btn btn-outline-secondary me-1" title="Reset">
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
            </div>
        </form>
        @if(empty($data))
            <div class="alert alert-danger">No data available.</div>
        @else
            <table id="reportsTable" class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Document reference Number</th>
                        <th>Department</th>
                        <th>Insured</th>
                        <th>Policy Issue Date</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Gross Premium</th>
                        <th>Sum Insured</th>
                        <th>Net Premium</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                    <tr>
                        <td>{{ $record['GDH_DOC_REFERENCE_NO'] ?? 'N/A' }}</td>
                        @php
                            $categoryMapping = [
                                11 => 'Fire',
                                12 => 'Marine',
                                13 => 'Motor',
                                14 => 'Miscellaneous',
                                16 => 'Health',
                            ];
                            $code = $record['PDP_DEPT_CODE'] ?? null;
                        @endphp
                        <td>{{ $categoryMapping[$code] ?? 'N/A' }}</td>
                        <td>{{ $record['PPS_DESC'] ?? 'N/A' }}</td>
                        <td>
                            @php
                                $date = null;
                                if (!empty($record['GDH_ISSUEDATE'])) {
                                    $date = \Carbon\Carbon::createFromFormat('d-M-y', $record['GDH_ISSUEDATE'])->format('d-m-Y');
                                } elseif (!empty($record['GSI_ISSUEDATE'])) {
                                    $date = \Carbon\Carbon::createFromFormat('j-M-y', $record['GSI_ISSUEDATE'])->format('d-m-Y');
                                }
                            @endphp
                            {{ $date ?? 'N/A' }}
                        </td>
                        <td>
                            @php
                                $date = null;
                                if (!empty($record['GDH_COMMDATE'])) {
                                    $date = \Carbon\Carbon::createFromFormat('d-M-y', $record['GDH_COMMDATE'])->format('d-m-Y');
                                } elseif (!empty($record['GDH_COMMDATE'])) {
                                    $date = \Carbon\Carbon::createFromFormat('j-M-y', $record['GDH_COMMDATE'])->format('d-m-Y');
                                }
                            @endphp
                            {{ $date ?? 'N/A' }}
                        </td>
                        <td>
                            @php
                                $date = null;
                                if (!empty($record['GDH_EXPIRYDATE'])) {
                                    try {
                                        $date = \Carbon\Carbon::createFromFormat('d-M-y', $record['GDH_EXPIRYDATE'])->format('d-m-Y');
                                    } catch (\Exception $e) {
                                        try {
                                            $date = \Carbon\Carbon::createFromFormat('j-M-y', $record['GDH_EXPIRYDATE'])->format('d-m-Y');
                                        } catch (\Exception $e) {
                                            $date = null;
                                        }
                                    }
                                }
                            @endphp
                            {{ $date ?? 'N/A' }}
                        </td>
                        <td class="numeric" style="text-align: right;">
                            {{
                                trim($record['TOT_PRE'] ?? '') === '&nbsp;' || empty(trim($record['TOT_PRE'] ?? ''))
                                    ? 'N/A'
                                    : (is_numeric($record['TOT_PRE']) ? number_format($record['TOT_PRE']) : $record['TOT_PRE'])
                            }}
                        </td>
                        <td class="numeric" style="text-align: right;">
                            {{
                                trim($record['GDH_TOTALSI'] ?? '') === '&nbsp;' || empty(trim($record['GDH_TOTALSI'] ?? ''))
                                    ? 'N/A'
                                    : (is_numeric($record['GDH_TOTALSI']) ? number_format($record['GDH_TOTALSI']) : $record['GDH_TOTALSI'])
                            }}
                        </td>
                        <td class="numeric" style="text-align: right;">
                            {{
                                trim($record['GDH_NETPREMIUM'] ?? '') === '&nbsp;' || empty(trim($record['GDH_NETPREMIUM'] ?? ''))
                                    ? 'N/A'
                                    : (is_numeric($record['GDH_NETPREMIUM']) ? number_format($record['GDH_NETPREMIUM']) : $record['GDH_NETPREMIUM'])
                            }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" style="text-align: right;">Totals</th>
                        <th id="totalGrossPrem" style="text-align: right;"></th>
                        <th id="totalSumInsured" style="text-align: right;"></th>
                        <th id="totalNetPrem" style="text-align: right;"></th>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>
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
                        title: 'Renewal Report',
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
                        title: 'Renewal Report',
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

                    // Column 6: Gross Premium
                    var totalGrossPrem = api.column(6, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Column 7: Sum Insured
                    var totalSumInsured = api.column(7, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Column 8: Net Premium
                    var totalNetPrem = api.column(8, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Update footer
                    $('#totalGrossPrem').html(totalGrossPrem.toLocaleString('en-US'));
                    $('#totalSumInsured').html(totalSumInsured.toLocaleString('en-US'));
                    $('#totalNetPrem').html(totalNetPrem.toLocaleString('en-US'));
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