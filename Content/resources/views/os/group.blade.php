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
        <x-report-header title="Outstanding Report" />

        @if(request('uw_doc'))
        <div class="alert alert-info">
            <strong>Debug Info:</strong><br>
            Document Number: {{ request('uw_doc') }}<br>
        </div>
        @endif

        <form method="GET" action="{{ url('/osg') }}" class="mb-4">
            <div class="row g-3">
                <!-- From Date -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" 
                        value="{{ request('start_date', $start_date) }}">
                </div>

                <!-- To Date -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" 
                        value="{{ request('end_date', $end_date) }}">
                </div>

                <!-- Branch -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="location_category" class="form-label me-2" style="white-space: nowrap;">Branches</label>
                    <select name="location_category" id="location_category" class="form-control select2">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option 
                                value="{{ $branch->fbracode }}" 
                                {{ request('location_category') == $branch->fbracode ? 'selected' : '' }}
                            >
                                {{ $branch->fbradsc }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <!-- Buttons -->
                <div class="col-md-4 d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-primary me-2" title="Filter">
                        <i class="bi bi-funnel-fill"></i> Filter
                    </button>
                    <a href="{{ url('/osg') }}" class="btn btn-outline-secondary me-2" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        @if(empty($data))
            <div class="alert alert-danger">No data available.</div>
        @else
        <table id="reportsTable" class="table table-bordered table-responsive">
<thead>
    <tr>
        <th>Insured Code</th>
        <th>Insured</th>
        <th title="2025 Count">2025 (C)</th>
        <th title="2025 Amount">2025 (A)</th>
        <th title="2024 Count">2024 (C)</th>
        <th title="2024 Amount">2024 (A)</th>
        <th title="2023 Count">2023 (C)</th>
        <th title="2023 Amount">2023 (A)</th>
        <th title="2022 Count">2022 (C)</th>
        <th title="2022 Amount">2022 (A)</th>
        <th title="Total Count">Total (C)</th>
        <th title="Total Amount">Total (A)</th>
    </tr>
</thead>
<tbody>
    @foreach($data as $record)
        <tr>
            <td>{{ $record['PPS_PARTY_CODE'] ?? 'N/A' }}</td>
            <td data-search="{{ $record['PPS_DESC'] ?? 'N/A' }}">
                <span class="truncate-text" title="{{ $record['PPS_DESC'] ?? 'N/A' }}">
                    {{ Str::limit($record['PPS_DESC'] ?? 'N/A', 30, '...') }}
                </span>
            </td>
            <!-- 2025 data - Count first -->
            <td style="text-align: right;">{{ $record['2025_count'] }}</td>
            <td style="text-align: right;">{{ number_format($record['2025_amt']) }}</td>
            <!-- 2024 data - Count first -->
            <td style="text-align: right;">{{ $record['2024_count'] }}</td>
            <td style="text-align: right;">{{ number_format($record['2024_amt']) }}</td>
            <!-- 2023 data - Count first -->
            <td style="text-align: right;">{{ $record['2023_count'] }}</td>
            <td style="text-align: right;">{{ number_format($record['2023_amt']) }}</td>
            <!-- 2022 data - Count first -->
            <td style="text-align: right;">{{ $record['2022_count'] }}</td>
            <td style="text-align: right;">{{ number_format($record['2022_amt']) }}</td>
            <!-- Total data - Count first -->
            <td style="text-align: right;">{{ $record['DocumentCount'] }}</td>
            <td style="text-align: right;">{{ number_format($record['Outstanding']) }}</td>
        </tr>
    @endforeach
</tbody>
<tfoot>
    <tr>
        <th colspan="2" style="text-align: right;">Totals:</th>
        <!-- 2025 totals - Count first -->
        <th id="total2025Count" style="text-align: right;"></th>
        <th id="total2025Amt" style="text-align: right;"></th>
        <!-- 2024 totals - Count first -->
        <th id="total2024Count" style="text-align: right;"></th>
        <th id="total2024Amt" style="text-align: right;"></th>
        <!-- 2023 totals - Count first -->
        <th id="total2023Count" style="text-align: right;"></th>
        <th id="total2023Amt" style="text-align: right;"></th>
        <!-- 2022 totals - Count first -->
        <th id="total2022Count" style="text-align: right;"></th>
        <th id="total2022Amt" style="text-align: right;"></th>
        <!-- Grand totals - Count first -->
        <th id="totalDocuments" style="text-align: right;"></th>
        <th id="totalOutstanding" style="text-align: right;"></th>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select a branch",
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
        text: '<i class="fas fa-file-excel"></i> Excel',
        className: 'btn btn-success btn-sm',
        title: 'Outstanding Report',
        footer: true,
        exportOptions: {
            columns: ':visible',
            format: {
                body: function(data, row, column, node) {
                    if (column === 1) { // Adjust this index if needed
                        return $(node).find('span').attr('title') || data;
                    }
                    return data;
                }
            },
            modifier: {
                page: 'current'
            }
        }
    },
    {
        extend: 'pdf',
        text: '<i class="fas fa-file-pdf"></i> PDF',
        className: 'btn btn-danger btn-sm',
        title: 'Outstanding Report',
        orientation: 'landscape',
        pageSize: 'A4',
        footer: true,
        exportOptions: {
            columns: ':visible',
            format: {
                body: function(data, row, column, node) {
                    if (column === 1) { // Adjust this index if needed
                        return $(node).find('span').attr('title') || data;
                    }
                    return data;
                }
            },
            modifier: {
                page: 'current'
            }
        }
    }
],
"footerCallback": function (row, data, start, end, display) {
    var api = this.api();

    var intVal = function (i) {
        if (i === 'N/A') return 0;
        return typeof i === 'string' ?
            parseFloat(i.replace(/[^\d.-]/g, '')) || 0 :
            typeof i === 'number' ? i : 0;
    };

    // Calculate totals for each column
    for (var i = 2; i <= 11; i++) {
        var total = api
            .column(i, { page: 'current' })
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        // Format all numbers with commas
        $(api.column(i).footer()).html(total.toLocaleString('en-US'));
    }
},

                
                initComplete: function() {
                    this.api().columns.adjust();
                    $('.dataTables_filter input').attr('placeholder', 'Search...');
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