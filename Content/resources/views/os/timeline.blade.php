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

        <form method="GET" action="{{ url('/ost') }}" class="mb-4">
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
                    <a href="{{ url('/ost') }}" class="btn btn-outline-secondary me-2" title="Reset">
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
                <th title="0-30 Count">0-30 (C)</th>
                <th title="0-30 Amount">0-30 (A)</th>
                <th title="31-60 Count">31-60 (C)</th>
                <th title="31-60 Amount">31-60 (A)</th>
                <th title="61-90 Count">61-90 (C)</th>
                <th title="61-90 Amount">61-90 (A)</th>
                <th title="91-120 Count">91-120 (C)</th>
                <th title="91-120 Amount">91-120 (A)</th>
                <th title="121-180 Count">121-180 (C)</th>
                <th title="121-180 Amount">121-180 (A)</th>
                <th title="180+ Count">180+ (C)</th>
                <th title="180+ Amount">180+ (A)</th>
                <th>Total Count</th>
                <th>Outstanding</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $record)
                <tr>
                    <td>{{ $record['PPS_PARTY_CODE'] ?? 'N/A' }}</td>
                    <td>{{ Str::limit($record['PPS_DESC'] ?? 'N/A', 30, '...') }}</td>
                    <td style="text-align: right;">{{ $record['Counts']['0-30'] }}</td>
                    <td style="text-align: right;">{{ number_format($record['Amounts']['0-30']) }}</td>
                    <td style="text-align: right;">{{ $record['Counts']['31-60'] }}</td>
                    <td style="text-align: right;">{{ number_format($record['Amounts']['31-60']) }}</td>
                    <td style="text-align: right;">{{ $record['Counts']['61-90'] }}</td>
                    <td style="text-align: right;">{{ number_format($record['Amounts']['61-90']) }}</td>
                    <td style="text-align: right;">{{ $record['Counts']['91-120'] }}</td>
                    <td style="text-align: right;">{{ number_format($record['Amounts']['91-120']) }}</td>
                    <td style="text-align: right;">{{ $record['Counts']['121-180'] }}</td>
                    <td style="text-align: right;">{{ number_format($record['Amounts']['121-180']) }}</td>
                    <td style="text-align: right;">{{ $record['Counts']['180+'] }}</td>
                    <td style="text-align: right;">{{ number_format($record['Amounts']['180+']) }}</td>
                    <td style="text-align: right;">{{ $record['Counts']['Total'] }}</td>
                    <td style="text-align: right;">{{ number_format($record['Outstanding']) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" style="text-align: right;">Totals:</th>
                <th id="total0to30" style="text-align: right;"></th>
                <th id="totalAmount0to30" style="text-align: right;"></th>
                <th id="total31to60" style="text-align: right;"></th>
                <th id="totalAmount31to60" style="text-align: right;"></th>
                <th id="total61to90" style="text-align: right;"></th>
                <th id="totalAmount61to90" style="text-align: right;"></th>
                <th id="total91to120" style="text-align: right;"></th>
                <th id="totalAmount91to120" style="text-align: right;"></th>
                <th id="total121to180" style="text-align: right;"></th>
                <th id="totalAmount121to180" style="text-align: right;"></th>
                <th id="total180plus" style="text-align: right;"></th>
                <th id="totalAmount180plus" style="text-align: right;"></th>
                <th id="totalCounts" style="text-align: right;"></th>
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
                return typeof i === 'string' ?
                    parseFloat(i.replace(/[^\d.-]/g, '')) || 0 :
                    typeof i === 'number' ? i : 0;
            };

            // Calculate totals for counts and amounts for each age bracket
            for (var i = 2; i <= 12; i += 2) { // Count columns
                var totalCount = api
                    .column(i, { page: 'current' })
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                $(api.column(i).footer()).html(totalCount);
            }

            for (var j = 3; j <= 13; j += 2) { // Amount columns
                var totalAmount = api
                    .column(j, { page: 'current' })
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                $(api.column(j).footer()).html(totalAmount.toLocaleString('en-US'));
            }

            // Calculate total outstanding
            var totalOutstanding = api
                .column(15, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            $(api.column(15).footer()).html(totalOutstanding.toLocaleString('en-US'));
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