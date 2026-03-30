<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets Addition Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Main table fixes */
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
        tfoot th, tfoot td {
            border: 1px solid black !important;
            background-color: blue !important;
            color: white !important;
            text-align: center !important;
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

        /* Custom icon color */
        .custom-icon {
            color: black !important;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
    <x-report-header title="Assets Addition Report" />

        <form method="GET" action="{{ url('/reports') }}" class="mb-4">
            <div class="row g-3">
                <!-- From Date -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', date('Y-m-01')) }}">
                </div>
                <!-- To Date -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', date('Y-m-d')) }}">
                </div>
                <!-- Branch -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="location" class="form-label me-2" style="white-space: nowrap; width: 100px;">Branch</label>
                    <select name="location" id="location" class="form-control select2">
                        <option value="">All Branches</option>
                        @foreach($locations as $location)
                            <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>{{ $location }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Supplier -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="supplier" class="form-label me-2" style="white-space: nowrap; width: 100px;">Supplier</label>
                    <select name="supplier" id="supplier" class="form-control select2">
                        <option value="" selected>All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier }}" {{ request('supplier') == $supplier ? 'selected' : '' }}>{{ $supplier }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Category -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="category" class="form-label me-2" style="white-space: nowrap; width: 100px;">Category</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">All Categories</option>
                        <option value="Computers" {{ request('category') == 'Computers' ? 'selected' : '' }}>Computers</option>
                        <option value="Furniture and Fixtures" {{ request('category') == 'Furniture and Fixtures' ? 'selected' : '' }}>Furniture and Fixtures</option>
                        <option value="Office Equipment" {{ request('category') == 'Office Equipment' ? 'selected' : '' }}>Office Equipment</option>
                        <option value="Building" {{ request('category') == 'Building' ? 'selected' : '' }}>Building</option>
                        <option value="Vehicles" {{ request('category') == 'Vehicles' ? 'selected' : '' }}>Vehicles</option>
                    </select>
                </div>
                <!-- Amount -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="amount" class="form-label me-2" style="white-space: nowrap; width: 100px;">Amount</label>
                    <select name="amount" id="amount" class="form-control">
                        <option value="">All Amounts</option>
                        <option value="above_25000" {{ request('amount') == 'above_25000' ? 'selected' : '' }}>Above 25,000</option>
                        <option value="below_25000" {{ request('amount') == 'below_25000' ? 'selected' : '' }}>Below 25,000</option>
                    </select>
                </div>
                <!-- Filter Button -->
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
            <div class="table-responsive">
                <table id="reportsTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Date</th>
                            <th>Asset Code</th>
                            <th>Asset Description</th>
                            <th>Supplier</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $record)
                            <tr>
                                <td>{{ $record['LOC'] ?? 'N/A' }}</td>
                                <td>{{ $record['Date'] ?? 'N/A' }}</td>
                                <td>{{ $record['ASSET'] ?? 'N/A' }}</td>
                                <td>{{ $record['ASSET_DESC'] ?? 'N/A' }}</td>
                                <td>{{ $record['SUPPLIER'] ?? 'N/A' }}</td>
                                <td style="text-align: right;">{{ number_format($record['AMOUNT'] ?? 0, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4"></th>
                            <th>Totals:</th>
                            <th id="pageTotalAmount" style="text-align: right;">0.00</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
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

                    // Function to parse values
                    var intVal = function (i) {
                        return typeof i === 'string' ? parseFloat(i.replace(/[^\d.-]/g, '')) : (typeof i === 'number' ? i : 0);
                    };

                    // Calculate total for column 5
                    var total = api
                        .column(5, { search: 'applied', page: 'all' }) 
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Update footer with the total amount for column 5
                    $(api.column(5).footer()).html(
                        total.toLocaleString('en-US', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        })
                    );
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
                table.columns.adjust();
            });
        });
    </script>
</body>
</html>