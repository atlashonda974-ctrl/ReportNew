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
    <x-datatable-styles />   
</head>
<body>
    <div class="container mt-5">
    <x-report-header title="Assets Register Report" />
        
        <form method="GET" action="{{ url('/register') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3 d-flex align-items-center">
                    <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', date('Y-m-01')) }}">
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', date('Y-m-d')) }}">
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
            <div style="width: 100%;">
                <table id="reportsTable" class="table table-bordered">
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
                                <td class="numeric" style="text-align: right;">{{ $record['Dep Rate'] ?? 0 }}%</td>
                                <td class="numeric" style="text-align: right;">{{ number_format($record['Addition Cost'], 0) }}</td>
                                <td class="numeric" style="text-align: right;">{{ number_format($record['Addition During the Year'] ?? 0, 0) }}</td>
                                <td class="numeric" style="text-align: right;">{{ number_format($record['Sale During the Year'] ?? 0, 0) }}</td>
                                <td class="numeric" style="text-align: right;">{{ number_format($record['Total Amount'], 0) }}</td>
                                <td class="numeric" style="text-align: right;">{{ number_format($record['Opening Depreciation'], 0) }}</td>
                                <td class="numeric" style="text-align: right;">{{ number_format($record['Addition Depreciation'] ?? 0, 0) }}</td>
                                <td class="numeric" style="text-align: right;">{{ number_format($record['On Sale Depreciation'] ?? 0, 0) }}</td>
                                <td class="numeric" style="text-align: right;">{{ number_format($record['For the Year Depreciation'], 0) }}</td>
                                <td class="numeric" style="text-align: right;">{{ number_format($record['Total Depreciation'], 0) }}</td>
                                <td class="numeric" style="text-align: right;">{{ number_format($record['W.D.V'], 0) }}</td>

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
                        text: '<i class="fas fa-file-excel custom-icon"></i> Excel',
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
                        text: '<i class="fas fa-file-pdf custom-icon"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        title: 'Assets Register Report',
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
                    var columns = [5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
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
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
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