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
    <x-report-header title="Outstanding Report" />

    @if(request('uw_doc'))
    <div class="alert alert-info">
        <strong>Debug Info:</strong><br>
        Document Number: {{ request('uw_doc') }}<br>
    </div>
    @endif

    <form method="GET" action="{{ url('/os') }}" class="mb-4">
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

            <!-- Category -->
            <div class="col-md-3 d-flex align-items-center">
                <label for="new_category" class="form-label me-2" style="white-space: nowrap; width: 100px;">Department</label>
                <select name="new_category" id="new_category" class="form-control">
                    <option value="">All Departments</option>
                    <option value="Fire" {{ request('new_category') == 'Fire' ? 'selected' : '' }}>Fire</option>
                    <option value="Marine" {{ request('new_category') == 'Marine' ? 'selected' : '' }}>Marine</option>
                    <option value="Motor" {{ request('new_category') == 'Motor' ? 'selected' : '' }}>Motor</option>
                    <option value="Miscellaneous" {{ request('new_category') == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                    <option value="Health" {{ request('new_category') == 'Health' ? 'selected' : '' }}>Health</option>
                </select>
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
                <a href="{{ url('/os') }}" class="btn btn-outline-secondary me-2" title="Reset">
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
                    <th>Doc No</th>
                    <th>Issue Date</th>
                    <th>Insured</th>
                    <th>Dept.</th>
                    <th>Premium</th>
                    <th>Collection</th>
                    <th>Outstanding</th>
                    <th>Branch</th>
                    <th>Doc. Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $record)
                    @php
                        $premium = is_numeric($record['TOT_PRE'] ?? null) ? (float) $record['TOT_PRE'] : 0;
                        $collection = is_numeric($record['TOT_COL'] ?? null) ? (float) $record['TOT_COL'] : 0;
                        $outstanding = $premium - $collection;
                    @endphp
                    <tr>
                        <td>{{ $record['GDH_DOC_REFERENCE_NO'] ?? 'N/A' }}</td>
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
                        <td data-search="{{ $record['PPS_DESC'] ?? 'N/A' }}">
                            <span class="truncate-text" title="{{ $record['PPS_DESC'] ?? 'N/A' }}">
                                {{ Str::limit($record['PPS_DESC'] ?? 'N/A', 15, '...') }}
                            </span>
                        </td>

                        @php
                            $categoryMapping = [
                                11 => 'Fire',
                                12 => 'Marine',
                                13 => 'Motor',
                                14 => 'Miscellaneous',
                                16 => 'Health',
                            ];
                            $deptCode = $record['PDP_DEPT_CODE'] ?? null;
                        @endphp
                        <td>{{ $categoryMapping[$deptCode] ?? 'N/A' }}</td>
                        <td class="numeric" style="text-align: right;">
                            {{ $premium ? number_format($premium) : 'N/A' }}
                        </td>
                        <td class="numeric" style="text-align: right;">
                            {{ $collection ? number_format($collection) : 'N/A' }}
                        </td>
                        <td class="numeric" style="text-align: right;">
                            {{ number_format($outstanding) }}
                        </td>
                        <td data-search="{{ $record['PLC_LOCADESC'] ?? 'N/A' }}">
                            <span class="truncate-text" title="{{ $record['PLC_LOCADESC'] ?? 'N/A' }}">
                                {{ \Illuminate\Support\Str::limit($record['PLC_LOCADESC'] ?? 'N/A', 15, '...') }}
                            </span>
                        </td>
                        <td>{{ $record['DOC_DESC'] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align: right;">Totals:</th>
                    <th id="totalSumInsured" style="text-align: right;"></th>
                    <th id="totalCollection" style="text-align: right;"></th>
                    <th id="totalOutstanding" style="text-align: right;"></th>
                    <th colspan="2"></th>
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
                                if (column === 2 || column === 7) {
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
                                if (column === 2 || column === 7) {
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

                // Calculate totals for current page/filtered data
                var premiumTotal = api
                    .column(4, {page: 'current'})
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var collectionTotal = api
                    .column(5, {page: 'current'})
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var outstandingTotal = api
                    .column(6, {page: 'current'})
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(4).footer()).html(premiumTotal.toLocaleString('en-US'));
                $(api.column(5).footer()).html(collectionTotal.toLocaleString('en-US'));
                $(api.column(6).footer()).html(outstandingTotal.toLocaleString('en-US'));
            },
            "initComplete": function() {
                // Initialize footer with 0 values
                $('#totalSumInsured, #totalCollection, #totalOutstanding').html('0');
                
                // Force initial footer calculation
                this.api().draw();
                
                // Adjust columns
                this.api().columns.adjust();
                
                // Style adjustments
                $('.dataTables_filter input').attr('placeholder', 'Search...');
                $('.dataTables_filter').css('margin-left', '5px').css('margin-right', '5px');
                $('.dt-buttons').css('margin-left', '5px');
                
                // Recalculate on any filter change
                this.api().on('draw.dt', function() {
                    table.rows({ search: 'applied' }).footerCallback();
                });
            },
            "drawCallback": function() {
                this.api().columns.adjust();
            }
        });

        // Reset button handler
        $('a[title="Reset"]').on('click', function() {
            setTimeout(function() {
                table.draw(); // This will trigger the footer callback
            }, 100);
        });
    });
    </script>
@endpush