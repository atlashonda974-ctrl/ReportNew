@extends('master')
@section('title', 'Reports')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <x-datatable-styles />
@endpush

@section('content')
    <x-report-header title="CR Report" />

    @if(request('uw_doc'))
    <div class="alert alert-info">
        <strong>Debug Info:</strong><br>
        Document Number: {{ request('uw_doc') }}<br>
    </div>
    @endif

    <form method="GET" action="{{ url('/do-gd') }}" class="mb-4">
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
            <div class="col-md-3 d-flex align-items-center">
                <button type="submit" class="btn btn-outline-primary me-2" title="Filter">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                <a href="{{ url('/do-gd') }}" class="btn btn-outline-secondary me-2" title="Reset">
                    <i class="bi bi-arrow-clockwise"></i> Reset
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
                        <th>Voucher NO</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Branch</th>
                        <th>Insured</th>
                        <th>Claim No</th>
                        <th>Document No</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                        <tr>
                            <td>{{ $record['LVH_VCHDNO'] ?? 'N/A' }}</td>
                            <td>{{ $record['LVH_VCHDDATE'] ?? 'N/A' }}</td>
                            <td>{{ $record['PVT_VCHTTYPE'] ?? 'N/A' }}</td>
                            <td>{{ $record['TOT_CLM'] ?? 'N/A' }}</td>
                            <td>{{ $record['PLC_LOCADESC'] ?? 'N/A' }}</td>
                            <td>{{ $record['PPS_DESC'] ?? 'N/A' }}</td>
                            <td>{{ $record['GDH_DOC_REFERENCE_NO'] ?? 'N/A' }}</td>
                            <td>{{ $record['DOCREF'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align: right;">Totals:</th>
                        <th id="totalSum" style="text-align: right;">0.00</th>
                        <th colspan="4"></th>
                    </tr>
                </tfoot>
            </table>
    @endif
@endsection

@push('scripts')
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
                    if (typeof i === 'string') {
                        if (i === 'N/A') return 0;
                        return parseFloat(i.replace(/[^\d.-]/g, '')) || 0;
                    }
                    return typeof i === 'number' ? i : 0;
                };

                var totalSum = api
                    .column(3, { page: 'current' })
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(3).footer()).html(
                    totalSum.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                );
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
@endpush