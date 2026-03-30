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
    .bullet {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    .card-body li:last-child {
        border-bottom: none !important;
    }
</style>
</style>
</head>
<body>
    <div class="container mt-5">
        
        <div class="container mt-5">
    <x-report-header title="Renewal Report" />
    
    <!-- Single Summary Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Renewal Summary</h5>
        </div>
        <div class="card-body p-0">
            <div class="row g-0 text-center">
                <!-- Total Documents -->
                <div class="col-md-3 border-end p-3">
                    <div class="text-muted small">Total Documents</div>
                    <div class="h4 fw-bold">{{ count($data) }}</div>
                </div>
                
                <!-- Total Sum Insured -->
                <div class="col-md-3 border-end p-3">
                    <div class="text-muted small">Total Sum Insured</div>
                    <div class="h4 fw-bold">
                        {{ number_format($data->sum(function($item) { return (float)($item['GDH_TOTALSI'] ?? 0); })) }}
                    </div>
                </div>
                
                <!-- Gross Premium -->
                <div class="col-md-3 border-end p-3">
                    <div class="text-muted small">Gross Premium</div>
                    <div class="h4 fw-bold">
                        {{ number_format($data->sum(function($item) { return (float)($item['GDH_GROSSPREMIUM'] ?? 0); })) }}
                    </div>
                </div>
                
                <!-- Net Premium -->
                <div class="col-md-3 p-3">
                    <div class="text-muted small">Net Premium</div>
                    <div class="h4 fw-bold">
                        {{ number_format($data->sum(function($item) { return (float)($item['GDH_NETPREMIUM'] ?? 0); })) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Department-wise Summary Card -->
<!-- Corrected Department-wise Summary Card -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-primary text-white rounded-top">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Department Performance</h5>
            <span class="badge bg-white text-primary">5 Departments</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive rounded-bottom">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 30%;">Department</th>
                        <th class="text-end" style="width: 20%;">Documents</th>
                        <th class="text-end" style="width: 25%;">Gross Premium</th>
                        <th class="text-end pe-4" style="width: 25%;">% of Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Define department mapping directly in the view
                        $departments = [
                            'Fire' => ['code' => 11, 'icon' => 'fire', 'color' => 'danger'],
                            'Marine' => ['code' => 12, 'icon' => 'ship', 'color' => 'info'],
                            'Motor' => ['code' => 13, 'icon' => 'car', 'color' => 'success'],
                            'Miscellaneous' => ['code' => 14, 'icon' => 'shapes', 'color' => 'warning'],
                            'Health' => ['code' => 16, 'icon' => 'heartbeat', 'color' => 'primary']
                        ];
                        
                        $totalGross = $data->sum('GDH_GROSSPREMIUM');
                    @endphp

                    @foreach($departments as $name => $dept)
                        @php
                            $deptData = $data->filter(function($item) use ($dept) {
                                return ($item['PDP_DEPT_CODE'] ?? null) == $dept['code'];
                            });
                            $docCount = $deptData->count();
                            $grossPremium = $deptData->sum('GDH_GROSSPREMIUM');
                            $percentage = $totalGross > 0 ? ($grossPremium / $totalGross) * 100 : 0;
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-{{ $dept['color'] }} rounded-pill me-2">
                                    <i class="fas fa-{{ $dept['icon'] }}"></i>
                                </span>
                                <strong>{{ $name }}</strong>
                            </td>
                            <td class="text-end">
                                <span class="text-muted">{{ number_format($docCount) }}</span>
                            </td>
                            <td class="text-end">
                                <strong>{{ number_format($grossPremium) }}</strong>
                            </td>
                            <td class="pe-4">
                                <div class="d-flex align-items-center justify-content-end">
                                    <span class="me-2">{{ number_format($percentage, 1) }}%</span>
                                    <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                        <div class="progress-bar bg-{{ $dept['color'] }}" 
                                             role="progressbar" 
                                             style="width: {{ $percentage }}%" 
                                             aria-valuenow="{{ $percentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th class="ps-4">Total</th>
                        <th class="text-end">{{ number_format($data->count()) }}</th>
                        <th class="text-end">{{ number_format($totalGross) }}</th>
                        <th class="pe-4 text-end">100%</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
        <form method="GET" action="{{ url('/uw') }}" class="mb-4">
            <div class="row g-3">
                <!-- Start Date -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="start_date" class="form-label me-2" style="white-space: nowrap;">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                        value="{{ request('start_date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                </div>

                <!-- End Date -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="end_date" class="form-label me-2" style="white-space: nowrap;">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                        value="{{ request('end_date', \Carbon\Carbon::now()->addDays(30)->format('Y-m-d')) }}">
                </div>

                <!-- Category Dropdown -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="new_category" class="form-label me-2" style="white-space: nowrap;">Departments</label>
                    <select name="new_category" id="new_category" class="form-control">
                        <option value="">All Departments</option>
                        <option value="Fire" {{ request('new_category') == 'Fire' ? 'selected' : '' }}>Fire</option>
                        <option value="Marine" {{ request('new_category') == 'Marine' ? 'selected' : '' }}>Marine</option>
                        <option value="Motor" {{ request('new_category') == 'Motor' ? 'selected' : '' }}>Motor</option>
                        <option value="Miscellaneous" {{ request('new_category') == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                        <option value="Health" {{ request('new_category') == 'Health' ? 'selected' : '' }}>Health</option>
                    </select>
                </div>

                <!-- Branches Dropdown -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="location_category" class="form-label me-2" style="white-space: nowrap;">Branches</label>
                    <select name="location_category" id="location_category" class="form-control select2">
                        <option value="">All Branches</option>
                        @foreach($uniqueCategories as $category)
                            <option value="{{ $category }}" {{ request('location_category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-3 d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-primary me-1" title="Filter">
                        <i class="bi bi-funnel-fill"></i>
                    </button>
                    <a href="{{ url('/uw') }}" class="btn btn-outline-secondary me-1" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>

        @if(empty($data))
            <div class="alert alert-danger">No data available.</div>
        @else
            @php
                $categoryMapping = [
                    11 => 'Fire',
                    12 => 'Marine',
                    13 => 'Motor',
                    14 => 'Miscellaneous',
                    16 => 'Health',
                ];
            @endphp
            
            <table id="reportsTable" class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Base Document</th>
                            <th>Document No</th>
                            <th>Business Class</th>
                            <th>Issue Date</th>
                            <th>Branches</th>
                            <th>Department Code</th>
                            <th>Abbrevation</th>
                            <th>Insured</th>
                            <th>Client Code</th>
                            <th>Comm. Date</th>
                            <th>Expiry Date</th>
                            <th>Total Sum Insured</th>
                            <th>Gross Premium</th>
                            <th>Net Premium</th>
                            <th>Broker</th>
                        </tr>
                    </thead>
                <tbody>
                    @foreach($data as $record)
                        <tr>
    <td>{{ $record['GDH_BASEDOCUMENTNO'] ?? '' }}</td>
    <td>{{ $record['GDH_DOC_REFERENCE_NO'] ?? '' }}</td>
    <td>{{ $record['PBC_BUSICLASS_CODE'] ?? '' }}</td>
    <td>{{ \Carbon\Carbon::createFromFormat('d-M-y', $record['GDH_ISSUEDATE'])->format('d-m-Y') ?? '' }}</td>
    <td>
        <span class="truncate-text" title="{{ $record['PLC_LOCADESC'] ?? '' }}">
            {{ \Illuminate\Support\Str::limit($record['PLC_LOCADESC'] ?? '', 8, '...') }}
        </span>
    </td>
    @php
        $deptCode = $record['PDP_DEPT_CODE'] ?? null;
    @endphp
    <td>{{ $categoryMapping[$deptCode] ?? 'N/A' }}</td>
    <td>{{ $record['PLC_LOC_GIAS2'] ?? '' }}</td>
    <td>
    <span class="truncate-text" title="{{ html_entity_decode(strip_tags($record['PPS_DESC'] ?? 'N/A')) }}">
        {{ \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($record['PPS_DESC'] ?? 'N/A')), 8, '...') }}
    </span>
</td>
    <td>{{ $record['PPS_PARTY_CODE'] ?? '' }}</td>
    <td>{{ \Carbon\Carbon::createFromFormat('d-M-y', $record['GDH_COMMDATE'])->format('d-m-Y') ?? '' }}</td>
    <td>{{ \Carbon\Carbon::createFromFormat('d-M-y', $record['GDH_EXPIRYDATE'])->format('d-m-Y') ?? '' }}</td>
    <td class="numeric" style="text-align: right;">
        {{ number_format((float) $record['GDH_TOTALSI']) }}
    </td>
    <td class="numeric" style="text-align: right;">
        {{ number_format((float) $record['GDH_GROSSPREMIUM']) }}
    </td>
    <td class="numeric" style="text-align: right;">
        {{ number_format((float) $record['GDH_NETPREMIUM']) }}
    </td>
    <td>
        <span class="truncate-text" title="{{ $record['BROKER'] ?? '' }}">
            {{ \Illuminate\Support\Str::limit($record['BROKER'] ?? '', 8, '...') }}
        </span>
    </td>
</tr>

                    @endforeach
                </tbody>
                <tfoot>
    <tr>
        <th colspan="11" style="text-align: right;">Totals:</th>
        <th id="totalSumInsured" style="text-align: right;"></th>
        <th id="totalGrossPremium" style="text-align: right;"></th>
        <th id="totalNetPremium" style="text-align: right;"></th>
        <th></th>
    </tr>
</tfoot>

            </table>
        @endif
    </div>

    <!-- JavaScript -->
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
                        title: 'Request Note Report',
                        footer: true,
                        exportOptions: {
                            columns: ':visible', // Export all visible columns
                            format: {
                                body: function(data, row, column, node) {
                                    // Handle truncated text columns by getting full text from title attribute
                                    if (node) {
                                        var $node = $(node);
                                        // Check if this cell has a title attribute (full text)
                                        var titleText = $node.attr('title');
                                        if (titleText) {
                                            return titleText;
                                        }
                                        
                                        // Check for truncate-text elements with title
                                        var $truncateElement = $node.find('.truncate-text');
                                        if ($truncateElement.length > 0) {
                                            var truncateTitle = $truncateElement.attr('title');
                                            if (truncateTitle) {
                                                return truncateTitle;
                                            }
                                        }
                                        
                                        // Return plain text content, removing HTML tags
                                        return $node.text().trim();
                                    }
                                    
                                    // Fallback: clean HTML tags from data
                                    if (typeof data === 'string') {
                                        return data.replace(/<[^>]*>/g, '').trim();
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
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        title: 'Request Note Report',
                        orientation: 'landscape',
                        pageSize: 'A2', // Changed to A2 for more space
                        exportOptions: {
                            columns: function(idx, data, node) {
                                // Force export all columns (0 to 14 for 15 columns)
                                return idx >= 0 && idx <= 14;
                            },
                            stripHtml: false, // Keep HTML for processing
                            format: {
                                body: function(data, row, column, node) {
                                    // Safely handle undefined data
                                    if (!data) return '';
                                    
                                    // Handle truncated text by getting full text from title attribute
                                    if (node) {
                                        var $node = $(node);
                                        // Check if this cell has a title attribute (full text)
                                        var titleText = $node.attr('title');
                                        if (titleText) {
                                            return titleText;
                                        }
                                        
                                        // Check for truncate-text elements with title
                                        var $truncateElement = $node.find('.truncate-text');
                                        if ($truncateElement.length > 0) {
                                            var truncateTitle = $truncateElement.attr('title');
                                            if (truncateTitle) {
                                                return truncateTitle;
                                            }
                                        }
                                    }
                                    
                                    // Convert to string and remove HTML tags
                                    var cleanData = data.toString().replace(/<[^>]*>/g, '').trim();
                                    return cleanData;
                                }
                            }
                        },
                        customize: function(doc) {
                            // Force all 15 columns to be included
                            if (doc.content[1] && doc.content[1].table) {
                                // Ensure we have exactly 15 columns
                                doc.content[1].table.widths = [
                                    '6%', '6%', '6%', '6%', '8%', '6%', '6%', '7%', 
                                    '7%', '7%', '7%', '7%', '7%', '7%', '8%'
                                ];
                                
                                // Check if we have the right number of columns
                                if (doc.content[1].table.body.length > 0) {
                                    var actualColCount = doc.content[1].table.body[0].length;
                                    console.log('PDF Export - Actual columns:', actualColCount);
                                    
                                    // If we don't have 15 columns, use equal width
                                    if (actualColCount !== 15) {
                                        doc.content[1].table.widths = Array(actualColCount).fill('*');
                                    }
                                }
                            }
                            
                            // Very compact styling to fit more columns
                            doc.defaultStyle = {
                                fontSize: 6,
                                alignment: 'left'
                            };
                            
                            // Style the header
                            if (doc.content[1] && doc.content[1].table && doc.content[1].table.body[0]) {
                                doc.content[1].table.body[0].forEach(function(cell) {
                                    cell.fillColor = '#f2f2f2';
                                    cell.bold = true;
                                    cell.fontSize = 6;
                                });
                            }
                            
                            // Set page margins to maximize space
                            doc.pageMargins = [10, 10, 10, 10];
                        }
                    }
                ],
                "columnDefs": [
                    {
                        "targets": [4,7,14], 
                        "render": function(data, type, row) {
                            // Only apply truncation for display, not for export
                            if (type === 'display') {
                                return data; // Return as-is for display (your existing truncation should handle this)
                            }
                            // For sorting, filtering, and export, return full text
                            if (type === 'type' || type === 'sort' || type === 'export') {
                                // Try to get full text from title attribute if available
                                var $temp = $('<div>').html(data);
                                var title = $temp.attr('title') || $temp.find('.truncate-text').attr('title');
                                return title || $temp.text() || data;
                            }
                            return data;
                        }
                    }
                ],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();

                    var intVal = function (i) {
                        return typeof i === 'string' ? 
                            i.replace(/[^\d.-]/g, '') * 1 : 
                            typeof i === 'number' ? i : 0;
                    };

                    // Column 7: Total Sum Insured (index 7 because columns are 0-based)
                    var totalSumInsured = api.column(7, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Column 8: Gross Premium
                    var totalGrossPremium = api.column(8, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Column 9: Net Premium
                    var totalNetPremium = api.column(9, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    // Column 11: Total Sum Insured
                    var totalSumInsured = api.column(11, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Column 12: Gross Premium
                    var totalGrossPremium = api.column(12, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Column 13: Net Premium
                    var totalNetPremium = api.column(13, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);


                    // Update footer
                    $('#totalSumInsured').html(totalSumInsured.toLocaleString('en-US'));
                    $('#totalGrossPremium').html(totalGrossPremium.toLocaleString('en-US'));
                    $('#totalNetPremium').html(totalNetPremium.toLocaleString('en-US'));
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