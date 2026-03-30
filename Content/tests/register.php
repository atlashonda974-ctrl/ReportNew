final register everything
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Register Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.dataTables.min.css">
    
    <style>
        /* Main container */
        .dataTables_wrapper {
            width: 100%;
            position: relative;
        }
         /* Search container */
    .dataTables_filter {
        margin-bottom: 0px;
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
        margin-left: 0 !important;
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


        /* Fixed header */
        .dataTables_scrollHead {
            position: sticky;
            top: 0;
            z-index: 10;
            overflow: hidden !important;
        }
        
        /* Fixed footer */
        .dataTables_scrollFoot {
            position: sticky;
            bottom: 0;
            z-index: 10;
            overflow: hidden !important;
        }

        /* Scroll body container */
        .dataTables_scrollBody {
            overflow: auto !important;
            max-height: 500px !important;
        }

        /* Table styling */
        table.dataTable {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 0 !important;
        }

        /* Headers & Cells */
        th, td {
            font-size: 14px !important;
            font-family: Calibri, sans-serif !important;
            padding: 4px 8px !important;
            border: 1px solid black !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            vertical-align: middle !important;
            text-align: center !important;
            min-width: 100px !important;
        }

        /* Header styling */
        th {
            background-color: blue !important;
            color: white !important;
            font-weight: bold !important;
            border: 1px solid blue !important;
        }

        /* Numeric cells */
        td.numeric {
            text-align: right !important;
            padding-right: 12px !important;
        }

        /* Footer styling */
        tfoot th, tfoot td {
            background-color: blue !important;
            color: white !important;
            border: 1px solid black !important;
            white-space: nowrap !important;
        }

        /* Fixed columns styling */
        .DTFC_LeftBodyWrapper {
            border-right: 2px solid #ddd;
        }
        .DTFC_LeftHeadWrapper, .DTFC_LeftFootWrapper {
            background-color: blue !important;
            z-index: 11 !important; /* Above other fixed elements */
        }
        
        /* Button styling */
        .dt-buttons .btn {
            margin-right: 5px;
        }
        /* Main table fixes */
    table.dataTable {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    /* Header fixes */
    .dataTables_scrollHeadInner {
        width: 100% !important;
    }

    /* Column styling */
    th, td {
        font-size: 14px !important;
        font-family: Calibri, sans-serif !important;
        padding: 1px !important;
        border-collapse: collapse !important;
        white-space: nowrap !important; /* Prevent wrapping */
        overflow: hidden; /* Prevent overflow */
        text-overflow: ellipsis; /* Add ellipsis */
    }

    /* Header styling */
    th {
        text-align: center !important;
        border: 1px solid blue !important;
        background-color: blue !important;
        color: white !important;
        font-weight: bold !important;
        position: relative;
        height: auto !important;
    }

    /* Cell styling */
    td {
        border: 1px solid black !important;
        vertical-align: top !important;
    }

    /* Footer styling */
    tfoot th, tfoot td {
        border: 1px solid black !important;
        background-color: blue !important;
        color: white !important;
        text-align: center !important;
    }
    
    tfoot th:last-child {
        text-align: right !important;
    }

    /* Ensure the footer spans the full width */
    .dataTables_scrollFoot {
        width: 100% !important;
    }

    .dataTables_scrollFootInner {
        width: 100% !important;
    }

    .dataTables_scrollFootInner table {
        width: 100% !important;
        margin-top: -1px !important; /* Fixes double border issue */
    }

    /* DataTables wrapper adjustments */
    .dataTables_wrapper {
        margin-top: 0px;
        margin-left: 0;
        width: 100%;
        padding: 0;
    }

    /* Search container */
    .dataTables_filter {
        margin-bottom: 0px;
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
        margin-left: 0 !important;
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

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .dataTables_filter {
            width: 100% !important;
        }
        
        .dataTables_filter input {
            width: 100% !important;
        }
        
        .dataTables_wrapper .top {
            flex-direction: column;
            align-items: flex-start;
        }
    }
    </style>
</head>
<body>
    <div class="container mt-5">
        <p class="text-sm flex items-center gap-2 mb-2 pb-1 text-gray-900 border-b-2 border-blue-400">
            <span style="font-size: 1.2rem; filter: drop-shadow(1px 1px 1px rgba(0, 0, 0, 0.2));">📃</span>
            <span style="font-family: 'Great Vibes', cursive; font-size: 1.4rem; font-weight: 300; color: #3B3B3B; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);">
                Assets Register Report
            </span>
        </p>
        
        <form method="GET" action="{{ url('/register') }}" class="mb-4">
            <div class="row g-3">
                <!-- Form fields remain the same -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                        value="{{ request('start_date', date('Y-m-01')) }}">
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                        value="{{ request('end_date', date('Y-m-d')) }}">
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <label for="wdv" class="form-label me-2" style="white-space: nowrap; width: 100px;">W.D.V</label>
                    <select name="wdv" id="wdv" class="form-control">
                        <option value="greater_equal_5000" {{ request('wdv') == 'greater_equal_5000' ? 'selected' : '' }}>5000 and above</option>
                        <option value="less_5000" {{ request('wdv') == 'less_5000' ? 'selected' : '' }}>Less than 5,000</option>
                        <option value="between_5000_14999" {{ request('wdv') == 'between_5000_14999' ? 'selected' : '' }}>5,000 to 14,999</option>
                        <option value="between_15000_24999" {{ request('wdv') == 'between_15000_24999' ? 'selected' : '' }}>15,000 to 24,999</option>
                        <option value="above_25000" {{ request('wdv') == 'above_25000' ? 'selected' : '' }}>Above 25,000</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ url('/register') }}" class="btn btn-secondary me-2">Reset</a>
                    <a href="http://192.168.170.24/ReportsNew/" class="btn btn-secondary">Return Back</a>
                </div>
            </div>
        </form>
        
        @if(empty($data))
        <div class="alert alert-danger">No data available.</div>
        @else
        <div style="width: 100%;">
            <table id="reportsTable" class="table table-bordered">
                <!-- Table content remains the same -->
                <thead>
                    <tr>
                        <th>Asset Code</th>
                        <th>Asset Description</th>
                        <th>Vendor Name</th>
                        <th>Purchase Date</th>
                        <th>Depreciation Rate</th>
                        <th>Opening Amount</th>
                        <th>Addition During the Year</th>
                        <th>Sale During the Year</th>
                        <th>Total Amount</th>
                        <th>Opening Depreciation</th>
                        <th>Addition Depreciation</th>
                        <th>On Sale Depreciation</th>
                        <th>For the Year Depreciation</th>
                        <th>Total Depreciation</th>
                        <th>W.D.V</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                    <tr>
                        <td>{{ $record['Asset Code'] ?? 'N/A' }}</td>
                        <td>{{ $record['Asset Description'] ?? 'N/A' }}</td>
                        <td>{{ $record['Vender Name'] ?? 'N/A' }}</td>
                        <td>{{ $record['Purchase Date'] ?? 'N/A' }}</td>
                        <td class="numeric">{{ $record['Dep Rate'] ?? 0 }}%</td>
                        <td class="numeric">{{ number_format($record['Addition Cost'], 2) }}</td>
                        <td class="numeric">{{ number_format($record['Addition During the Year'] ?? 0, 2) }}</td>
                        <td class="numeric">{{ number_format($record['Sale During the Year'] ?? 0, 2) }}</td>
                        <td class="numeric">{{ number_format($record['Total Amount'], 2) }}</td>
                        <td class="numeric">{{ number_format($record['Opening Depreciation'], 2) }}</td>
                        <td class="numeric">{{ number_format($record['Addition Depreciation'] ?? 0, 2) }}</td>
                        <td class="numeric">{{ number_format($record['On Sale Depreciation'] ?? 0, 2) }}</td>
                        <td class="numeric">{{ number_format($record['For the Year Depreciation'], 2) }}</td>
                        <td class="numeric">{{ number_format($record['Total Depreciation'], 2) }}</td>
                        <td class="numeric">{{ number_format($record['W.D.V'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" style="text-align: right;">Totals</th>
                        <th id="totalOpeningAmount" style="text-align: right;"></th>
                        <th id="totalAdditionDuringYear" style="text-align: right;"></th>
                        <th id="totalSaleDuringYear" style="text-align: right;"></th>
                        <th id="totalTotalAmount" style="text-align: right;"></th>
                        <th id="totalOpeningDepreciation" style="text-align: right;"></th>
                        <th id="totalAdditionDepreciation" style="text-align: right;"></th>
                        <th id="totalOnSaleDepreciation" style="text-align: right;"></th>
                        <th id="totalForYearDepreciation" style="text-align: right;"></th>
                        <th id="totalTotalDepreciation" style="text-align: right;"></th>
                        <th id="totalWDV" style="text-align: right;"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
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
    
    <script>
 $(document).ready(function() {
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
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            className: 'btn btn-success btn-sm',
                            title: 'Assets Addition Report',
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
                            title: 'Assets Addition Report',
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
                    
                    // Remove any existing formatting
                    var intVal = function (i) {
                        return typeof i === 'string' ? 
                            i.replace(/[^\d.-]/g, '')*1 : 
                            typeof i === 'number' ? i : 0;
                    };
        
                    // Calculate totals for each column
                    var columns = [5,6,7,8,9,10,11,12,13,14];
                    columns.forEach(function(col) {
                        var total = api
                            .column(col, {page: 'current'})
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                        
                        // Update footer
                        $(api.column(col).footer()).html(
                            total.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            })
                        );
                    });
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
            
            // Adjust columns on window resize
            $(window).on('resize', function() {
                table.columns.adjust().draw();
                $.fn.dataTable.FixedColumns(table).relayout();
            });
        });
    </script>
</body>
</html>