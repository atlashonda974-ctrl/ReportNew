<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets Deletion Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        table.dataTable {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        /* Column styling */
        th, td {
            font-size: 14px !important;
            font-family: Calibri, sans-serif !important;
            padding: 2px !important;
            border-collapse: collapse !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Header styling */
        th {
            text-align: center !important;
            border: 1px solid blue !important;
            background-color: blue !important;
            color: white !important;
            font-weight: bold !important;
        }

        /* Cell styling */
        td {
            border: 1px solid black !important;
            vertical-align: top !important;
            text-align: left;
        }

        /* Footer styling */
        tfoot {
            display: table-row-group;
        }
        tfoot th, tfoot td {
            border: 1px solid black !important;
            background-color: blue !important;
            color: white !important;
            text-align: center !important;
            width: auto;
            /* Ensures footer cells span the entire table width */
            box-sizing: border-box; 
        }

        /* Ensure the footer spans the full width (for scrollable tables) */
        .dataTables_scrollFoot {
            width: 100% !important;
        }
        .dataTables_scrollFootInner {
            width: 100% !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            th, td {
                font-size: 12px !important;
            }
        }

        /* DataTables wrapper adjustments */
        .dataTables_wrapper {
            margin-top: 0px;
            margin-left: 0;
            width: 100%;
            padding: 0;
        }

        /* Search input styling */
        .dataTables_filter input {
            width: 250px !important;
            font-size: 12px !important;
            border: 2px solid #007bff !important;
            border-radius: 5px !important;
            padding: 5px !important;
            margin-left: 0px !important;
        }

        /* Buttons container */
        .dt-buttons {
            margin-bottom: -5px !important;
            padding: 0 !important;
        }

        /* Button styling */
        .dt-buttons .btn {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            margin-right: 3px !important;
        }

        /* Top container */
        .dataTables_wrapper .top {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 10px;
            margin-bottom: 2px;
            padding: 0;
            flex-wrap: wrap;
        }

        /* Breadcrumb styling */
        .breadcrumb {
            background-color: #f8f9fa;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 14px;
        }

        /* Select2 styling */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .select2-container .select2-dropdown {
            border-color: #ced4da;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .custom-icon {
            color: black !important;
        }
    </style>
</head>
<body>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select a branch",
                allowClear: true,
                width: '100%'
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
                "autoWidth": false,
                dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'Assets Deletion Report',
                        footer: true,
                        exportOptions: {
                            modifier: {
                                page: 'current'
                            }
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        title: 'Assets Deletion  Report',
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
                initComplete: function () {
                    this.api().columns.adjust();
                    $('.dataTables_filter').css('margin-left', '5px').css('margin-right', '5px');
                    $('.dt-buttons').css('margin-left', '5px');
                },
                drawCallback: function () {
                    var api = this.api();
                    var info = api.page.info();
                    console.log('Showing ' + info.recordsDisplay + ' records (filtered from ' + info.recordsTotal + ' total records)');
                    api.columns.adjust();
                }
            });

            // Adjust columns on window resize
            $(window).on('resize', function () {
                table.columns.adjust();
            });
        });
    </script>
</body>
</html>