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
        <x-report-header title="DO Report" />

        @if(request('uw_doc'))
        <div class="alert alert-info">
            <strong>Debug Info:</strong><br>
            Document Number: {{ request('uw_doc') }}<br>
        </div>
        @endif

        <form method="GET" action="{{ url('/do') }}" class="mb-4">
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
                    <a href="{{ url('/do') }}" class="btn btn-outline-secondary me-2" title="Reset">
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
                        <th>DO Code</th>
                        <th>DO</th>
                        <th>Branch</th>
                        <th>Fire</th>
                        <th>Marine</th>
                        <th>Motor</th>
                        <th>Miscellaneous</th>
                        <th>Health</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                    
                        <tr>
                            <td>{{ $record['PDO_DEVOFFCODE'] ?? 'N/A' }}</td>
                            <td>{{ $record['PDO_DEVOFFDESC'] ?? 'N/A' }}</td>
                            <td>{{ $record['PLC_LOCADESC'] ?? 'N/A' }}</td>
                            <td class="numeric" style="text-align: right;">{{ number_format($record['FIRE_PRE'] ?? 0) }}</td>
                            <td class="numeric" style="text-align: right;">{{ number_format($record['MAR_PRE'] ?? 0) }}</td>
                            <td class="numeric" style="text-align: right;">{{ number_format($record['MOT_PRE'] ?? 0) }}</td>
                            <td class="numeric" style="text-align: right;">{{ number_format($record['MISC_PRE'] ?? 0) }}</td>
                            <td class="numeric" style="text-align: right;">{{ number_format($record['HLT_PRE'] ?? 0) }}</td>
                            <td class="numeric" style="text-align: right;">{{ number_format($record['TOT_PRE'] ?? 0) }}</td>
                            
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="8" style="text-align: right;">Totals:</th>
                        <th id="totalSum" style="text-align: right;"></th>
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

                    // Calculate total for "Total" column
                    var totalSum = api.column(8, { page: 'current' }).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Update footer
                    $(api.column(8).footer()).html(totalSum.toLocaleString('en-US'));
                },
                "initComplete": function() {
                    $('#totalSum').html('0');
                    this.api().draw();
                    this.api().columns.adjust();
                    $('.dataTables_filter input').attr('placeholder', 'Search...');
                    $('.dataTables_filter').css('margin-left', '5px').css('margin-right', '5px');
                    $('.dt-buttons').css('margin-left', '5px');
                },
                "drawCallback": function() {
                    this.api().columns.adjust();
                }
            });

            $('a[title="Reset"]').on('click', function() {
                setTimeout(function() {
                    table.draw();
                }, 100);
            });
        });
</script>
</body>
</html>