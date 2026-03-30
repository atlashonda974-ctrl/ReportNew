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
    <style>
        .time-filter-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .time-tab {
            flex: 1;
            min-width: 80px;   
            max-width: 100px; 
            border-radius: 8px;
            padding: 8px 5px;
            text-align: center;
            transition: all 0.2s;
            text-decoration: none;
            font-size: 14px;
        }
        .time-tab:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .time-tab.active {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white !important;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <x-report-header title="Get Claim Intimation Detail" />
        <form method="GET" action="{{ url('/cr6') }}" class="mb-4">
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

                <!-- Department Filter -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="new_category" class="form-label me-2" style="white-space: nowrap; width: 100px;">Departments</label>
                    <select name="new_category" id="new_category" class="form-control">
                        <option value="" {{ $selected_category == '' ? 'selected' : '' }}>All Departments</option>
                        <option value="Fire" {{ $selected_category == 'Fire' ? 'selected' : '' }}>Fire</option>
                        <option value="Marine" {{ $selected_category == 'Marine' ? 'selected' : '' }}>Marine</option>
                        <option value="Motor" {{ $selected_category == 'Motor' ? 'selected' : '' }}>Motor</option>
                        <option value="Miscellaneous" {{ $selected_category == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                        <option value="Health" {{ $selected_category == 'Health' ? 'selected' : '' }}>Health</option>
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

                <!-- Business Type Filter -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="business_type" class="form-label me-2" style="white-space: nowrap; width: 100px;">Business Type</label>
                    <select name="business_type" id="business_type" class="form-control">
                        <option value="all" {{ request('business_type', $selected_business_type ?? 'all') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="takaful" {{ request('business_type', $selected_business_type ?? 'all') == 'takaful' ? 'selected' : '' }}>Takaful</option>
                        <option value="conventional" {{ request('business_type', $selected_business_type ?? 'all') == 'conventional' ? 'selected' : '' }}>Conventional</option>
                    </select>
                </div>

                <!-- Insurance Type -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="insu" class="form-label me-2" style="white-space: nowrap;">Insurance Type</label>
                    <select name="insu[]" id="insu" class="form-control select2-insurance" multiple>
                        <option value="D">Direct</option>
                        <option value="I">Inward</option>
                        <option value="O">Outward</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-4 d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-primary me-2" title="Filter">
                        <i class="bi bi-funnel-fill"></i> Filter
                    </button>
                    <a href="{{ url('/cr6') }}" class="btn btn-outline-secondary me-2" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        @php
            $insuValues = request('insu', []);
            $insuQuery = is_array($insuValues) ? implode(',', $insuValues) : $insuValues;
        @endphp

        @php
            $filters = [
                'all'     => 'All',
                '2days'   => '2 Days',
                '5days'   => '5 Days',
                '7days'   => '7 Days',
                '10days'  => '10 Days',
                '15days'  => '15 Days',
                '15plus'  => '15+ Days',
            ];
        @endphp

        <div class="d-flex justify-content-start mb-3">
            <div class="card time-filter-card">
                <div class="card-body py-2 px-3">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($filters as $key => $label)
                            <a href="{{ request()->fullUrlWithQuery(['time_filter' => $key]) }}"
                            class="time-tab {{ $selected_time_filter == $key ? 'active' : 'bg-light' }}">
                                <div class="fw-bold">{{ $label }}</div>
                                <div class="small">{{ $counts[$key] }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if($data->isEmpty())
            <div class="alert alert-danger">No data available.</div>
        @else 
            <table id="reportsTable" class="table table-bordered table-responsive"> 
                <thead>
                    <tr>
                        <th>Claim No</th>
                        <th>Int Date</th>
                        <th>Rev No</th>
                        <th>Insured</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Doc No</th>
                        <th>Dept</th>
                        <th>Loss Amt</th>
                        <th>INSURE TYPE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                        <tr>
                            @php
                                $categoryMapping = [
                                    11 => 'Fire',
                                    12 => 'Marine',
                                    13 => 'Motor',
                                    14 => 'Miscellaneous',
                                    16 => 'Health',
                                ];
                                $code = $record->PDP_DEPT_CODE ?? null;
                                $department = $categoryMapping[$code] ?? 'N/A';
                                
                                $insuranceTypeMapping = [
                                    'D' => 'Direct',
                                    'I' => 'Inward',
                                    'O' => 'Outward',
                                ];
                                $insuCode = $record->PIY_INSUTYPE ?? null;
                                $insuranceType = $insuranceTypeMapping[$insuCode] ?? $insuCode;
                            @endphp
                            <td>{{ trim(strip_tags($record->GIH_DOC_REF_NO ?? 'N/A')) }}</td>

                            <td>{{ $record->GIH_INTIMATIONDATE ?? 'N/A' }}</td>
                            <td>{{ $record->GIH_INTI_ENTRYNO ?? 'N/A' }}</td>
                            <td title="{{ $record->PPS_DESC ?? 'N/A' }}">
                                {{ Str::limit($record->PPS_DESC, 30, '...') ?? 'N/A' }}
                            </td>

                            <td>{{ $record->PPS_MOBILE_NO ?? 'N/A' }}</td>
                            <td>{{ $record->PPS_EMAIL_ADDRESS ?? 'N/A' }}</td>
                            <td>{{ $record->GID_BASEDOCUMENTNO?? 'N/A' }}</td>
                            <td>{{ $department }}</td>
                            <td style="text-align: right;">
                                {{ $record->GIH_LOSSCLAIMED ? number_format($record->GIH_LOSSCLAIMED, 2) : 'N/A' }}
                            </td>
                            <td>{{ $insuranceType }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="8" style="text-align: right;">Total Loss Amount</th>
                        <th style="text-align: right;" id="totalLossClaimed">
                            {{ number_format($data->sum('GIH_LOSSCLAIMED'), 2) }}
                        </th>
                        <th></th>
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
            // Initialize Select2 for branch dropdown
            $('#location_category').select2({
                placeholder: "Select a branch",
                allowClear: true
            });
            $('#insu').select2({
                placeholder: "Choose type",
                allowClear: true,
                width: '150%'
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
                            }
                        }
                    }
                ],
                "footerCallback": function (row, data, start, end, display) {
                    var api = this.api();

                    // Function to parse numbers from formatted strings
                    var intVal = function (i) {
                        if (i === 'N/A' || !i) return 0;
                        return typeof i === 'string' ?
                            parseFloat(i.replace(/[^\d.-]/g, '')) || 0 :
                            typeof i === 'number' ? i : 0;
                    };

                    // Calculate total for Loss Amount column (index 8)
                    var totalLossClaimed = api
                        .column(8, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Update footer with formatted total
                    $('#totalLossClaimed').html(
                        '<strong>' + totalLossClaimed.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + '</strong>'
                    );
                },
                "initComplete": function() {
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