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
        min-width: 80px;   /* control size */
        max-width: 100px;  /* prevent too wide */
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
<div class="container mt-4">
    <h2 class="text-center mb-4">Reinsurance</h2>

    <div class="row mb-4">
        <!-- Filter Form (Left Side) -->
        <div class="col-md-5 pe-md-4 border-end">
            <form method="GET" action="{{ url('/show') }}">
                <div class="card time-filter-card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="new_category" class="form-label fw-bold">Departments</label>
                            <select name="new_category" id="new_category" class="form-select">
                                <option value="">All Departments</option>
                                <option value="Fire" {{ $selected_department == 'Fire' ? 'selected' : '' }}>Fire</option>
                                <option value="Marine" {{ $selected_department == 'Marine' ? 'selected' : '' }}>Marine</option>
                                <option value="Motor" {{ $selected_department == 'Motor' ? 'selected' : '' }}>Motor</option>
                                <option value="Miscellaneous" {{ $selected_department == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                                <option value="Health" {{ $selected_department == 'Health' ? 'selected' : '' }}>Health</option>
                            </select>
                            <input type="hidden" name="time_filter" value="{{ $selected_time_filter }}">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel-fill me-1"></i> Filter
                            </button>
                            <a href="{{ url('/show') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabs (Right Side) -->
        <div class="col-md-7 ps-md-4">
            <div class="card time-filter-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Time Filter</h5>
                    <div class="d-flex flex-wrap gap-2 justify-content-between">
                        <a href="{{ url('/show?new_category=' . urlencode($selected_department) . '&time_filter=all') }}" 
                        class="time-tab {{ $selected_time_filter == 'all' ? 'active' : 'bg-light' }}">
                            <div class="fw-bold">All</div>
                            <div class="small">{{ $counts['all'] }}</div>
                        </a>
                        <a href="{{ url('/show?new_category=' . urlencode($selected_department) . '&time_filter=2days') }}" 
                        class="time-tab {{ $selected_time_filter == '2days' ? 'active' : 'bg-light' }}">
                            <div class="fw-bold">2 Days</div>
                            <div class="small">{{ $counts['2days'] }}</div>
                        </a>
                        <a href="{{ url('/show?new_category=' . urlencode($selected_department) . '&time_filter=5days') }}" 
                        class="time-tab {{ $selected_time_filter == '5days' ? 'active' : 'bg-light' }}">
                            <div class="fw-bold">5 Days</div>
                            <div class="small">{{ $counts['5days'] }}</div>
                        </a>
                        <a href="{{ url('/show?new_category=' . urlencode($selected_department) . '&time_filter=7days') }}" 
                        class="time-tab {{ $selected_time_filter == '7days' ? 'active' : 'bg-light' }}">
                            <div class="fw-bold">7 Days</div>
                            <div class="small">{{ $counts['7days'] }}</div>
                        </a>
                        <a href="{{ url('/show?new_category=' . urlencode($selected_department) . '&time_filter=10days') }}" 
                        class="time-tab {{ $selected_time_filter == '10days' ? 'active' : 'bg-light' }}">
                            <div class="fw-bold">10 Days</div>
                            <div class="small">{{ $counts['10days'] }}</div>
                        </a>
                        <a href="{{ url('/show?new_category=' . urlencode($selected_department) . '&time_filter=15days') }}" 
                        class="time-tab {{ $selected_time_filter == '15days' ? 'active' : 'bg-light' }}">
                            <div class="fw-bold">15 Days</div>
                            <div class="small">{{ $counts['15days'] }}</div>
                        </a>
                        <a href="{{ url('/show?new_category=' . urlencode($selected_department) . '&time_filter=15plus') }}" 
                        class="time-tab {{ $selected_time_filter == '15plus' ? 'active' : 'bg-light' }}">
                            <div class="fw-bold">15+ Days</div>
                            <div class="small">{{ $counts['15plus'] }}</div>
                        </a>
                    </div>
                </div>
            </div>
       </div>

    </div>

    <!-- Data Table -->
    <div class="row mt-4">
        <div class="col-12">
            @if($records->isEmpty())
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle me-2"></i> No data available for the selected filters.
                </div>
            @else
                <div class="table-responsive">
                    <table id="reportsTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Document Ref</th>
                                <th>Department</th>
                                <th>Policy Issue Date</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Insured</th>
                                <th>Location</th>
                                <th>Business Class</th>
                                <th>Sum Insured</th>
                                <th>Gross Premium</th>
                                <th>Net Premium</th>
                                <th>Created At</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                <tr @if($record->days_old > 7) class="highlight-red" @endif>
                                    <td>{{ $record->uw_doc ?? 'N/A' }}</td>
                                    @php
                                        $categoryMapping = [
                                            11 => 'Fire',
                                            12 => 'Marine',
                                            13 => 'Motor',
                                            14 => 'Miscellaneous',
                                            16 => 'Health',
                                        ];
                                        $code = $record->dept ?? null;
                                    @endphp
                                    <td>{{ $categoryMapping[$code] ?? 'N/A' }}</td>
                                    <td>
                                        @if(!empty($record->issue_date))
                                            {{ \Carbon\Carbon::parse($record->issue_date)->format('d-m-Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($record->comm_date))
                                            {{ \Carbon\Carbon::parse($record->comm_date)->format('d-m-Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($record->expiry_date))
                                            {{ \Carbon\Carbon::parse($record->expiry_date)->format('d-m-Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td title="{{ $record->insured ?? 'N/A' }}">
                                        {{ \Illuminate\Support\Str::limit($record->insured ?? 'N/A', 5, '...') }}
                                    </td>
                                    <td title="{{ $record->location ?? 'N/A' }}">
                                        {{ \Illuminate\Support\Str::limit($record->location ?? 'N/A', 5, '...') }}
                                    </td>
                                    <td>{{ $record->business_class ?? 'N/A' }}</td>
                                    <td class="numeric text-end">
                                        @if(empty($record->sum_insured) || $record->sum_insured === null)
                                            N/A
                                        @else
                                            {{ number_format($record->sum_insured, 2) }}
                                        @endif
                                    </td>
                                    <td class="numeric text-end">
                                        @if(empty($record->gross_premium) || $record->gross_premium === null)
                                            N/A
                                        @else
                                            {{ number_format($record->gross_premium, 2) }}
                                        @endif
                                    </td>
                                    <td class="numeric text-end">
                                        @if(empty($record->net_premium) || $record->net_premium === null)
                                            N/A
                                        @else
                                            {{ number_format($record->net_premium, 2) }}
                                        @endif
                                    </td>
<td>{{ $record->created_at ? $record->created_at->format('d-m-Y H:i:s') : now()->format('d-m-Y H:i:s') }}</td>
                                    <td>{{ $record->created_by ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
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
<script>
    $(document).ready(function() {
        $('#new_category').select2({
            placeholder: "Select a department",
            allowClear: true,
            width: '100%'
        });

        var table = $('#reportsTable').DataTable({
            paging: false,
            searching: true,
            ordering: true,
            info: true,
            scrollX: true,
            scrollY: "500px",
            scrollCollapse: false,
            fixedHeader: {
                header: true,
                footer: true
            },
            autoWidth: true,
            dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
            buttons: [
    {
        extend: 'excel',
        text: '<i class="fas fa-file-excel custom-icon"></i> Excel',
        className: 'btn btn-success btn-sm',
        title: 'Reinsurance Case Report',
        footer: true,
        exportOptions: {
            columns: ':visible', // export all visible columns including first
            format: {
                body: function (data, row, column, node) {
                    if (column === 5 || column === 6) {
                        return $(node).attr('title') || $(node).text().trim();
                    }
                    return $(node).text().trim();
                }
            }
        }
    },
    {
        extend: 'pdf',
        text: '<i class="fas fa-file-pdf custom-icon"></i> PDF',
        className: 'btn btn-danger btn-sm',
        title: 'Reinsurance Case Report',
        orientation: 'landscape',
        pageSize: 'A4',
        footer: true,
        exportOptions: {
            columns: ':visible', // export all visible columns including first
            format: {
                body: function (data, row, column, node) {
                    if (column === 5 || column === 6) {
                        return $(node).attr('title') || $(node).text().trim();
                    }
                    return $(node).text().trim();
                }
            }
        }
    }
]
,
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();

                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[^\d.-]/g, '') * 1 :
                        typeof i === 'number' ? i : 0;
                };

                // Column 8: Sum Insured
                var totalSumInsured = api.column(8, {page: 'current'}).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

                // Column 9: Gross Premium
                var totalGrossPrem = api.column(9, {page: 'current'}).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

                // Column 10: Net Premium
                var totalNetPrem = api.column(10, {page: 'current'}).data().reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

                // Update footer (assuming footer exists, modify if needed)
                $('#totalSumInsured').html(totalSumInsured.toLocaleString('en-US'));
                $('#totalGrossPrem').html(totalGrossPrem.toLocaleString('en-US'));
                $('#totalNetPrem').html(totalNetPrem.toLocaleString('en-US'));
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
    });
</script>
</body>
</html>