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
        <x-report-header title="Get Survey Report" />
        <form method="GET" action="{{ url('/cr9') }}" class="mb-4">
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
                    <a href="{{ url('/cr9') }}" class="btn btn-outline-secondary me-2" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>
        @php
    $filters = [
        'all' => 'All',
        '0_100k' => '0 - 100K',
        '100k_200k' => '- 200K',
        '200k_500k' => ' - 500K',
        '500k_1m' => '- 1M',
        '1m_2m' => ' - 2M',
        '2m_5m' => '- 5M',
        '5m_plus' => '5M+',
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
                        <div class="small">{{ $counts[$key] ?? 0 }}</div>
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
                        <th>Surveyor Code</th>
                        <th>Surveyor Name</th>
                        <th>Fire Amount</th>
                        <th>Marine Amount</th>
                        <th>Motor Amount</th>
                        <th>Miscellaneous Amount</th>
                        <th>Total Payee Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                        <tr>
                            <td>{{ $record->PSR_SURV_CODE ?? 'N/A' }}</td>
                            <td>{{ $record->PSR_SURV_NAME ?? 'N/A' }}</td>
                            <td style="text-align: right;">
                                {{ isset($record->FIRE_AMT) ? number_format($record->FIRE_AMT) : '0' }}
                            </td>
                            <td style="text-align: right;">
                                {{ isset($record->MARINE_AMT) ? number_format($record->MARINE_AMT) : '0' }}
                            </td>
                            <td style="text-align: right;">
                                {{ isset($record->MOTOR_AMT) ? number_format($record->MOTOR_AMT) : '0' }}
                            </td>
                            <td style="text-align: right;">
                                {{ isset($record->MISC_AMT) ? number_format($record->MISC_AMT) : '0' }}
                            </td>
                            <td style="text-align: right;">
                                {{ isset($record->GPD_PAYEE_AMOUNT) ? number_format($record->GPD_PAYEE_AMOUNT) : '0' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" style="text-align: right;">Total GPD_PAYEE_AMOUNT</th>
                        <th style="text-align: right;">
                            {{ number_format($data->sum(function ($record) {
                                return isset($record->GPD_PAYEE_AMOUNT) ? $record->GPD_PAYEE_AMOUNT : 0;
                            })) }}
                        </th>
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
                    var intVal = function (i) {
                        if (i === 'N/A' || !i) return 0;
                        return typeof i === 'string' ?
                            parseFloat(i.replace(/[^\d.-]/g, '')) || 0 :
                            typeof i === 'number' ? i : 0;
                    };

                    var totalLossClaimed = api
                        .column(6, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

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