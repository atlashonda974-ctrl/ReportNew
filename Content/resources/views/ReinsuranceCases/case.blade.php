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
        <x-report-header title="Reinsurance Case" />
<!-- Form in the Blade view, e.g., resources/views/ReinsuranceCases/case.blade.php -->
<form method="GET" action="{{ url('/c') }}" class="mb-4">
    <div class="row g-3">
        <!-- From Date -->
        <div class="col-md-3 d-flex align-items-center">
            <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $start_date }}">
        </div>

        <!-- To Date -->
        <div class="col-md-3 d-flex align-items-center">
            <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $end_date }}">
        </div>

        <!-- Departments -->
        <div class="col-md-3 d-flex align-items-center">
            <label for="new_category" class="form-label me-2" style="white-space: nowrap; width: 100px;">Departments</label>
            <select name="new_category" id="new_category" class="form-control">
                <option value="">All Departments</option>
                <option value="Fire" {{ request('new_category') == 'Fire' ? 'selected' : '' }}>Fire</option>
                <option value="Marine" {{ request('new_category') == 'Marine' ? 'selected' : '' }}>Marine</option>
                <option value="Motor" {{ request('new_category') == 'Motor' ? 'selected' : '' }}>Motor</option>
                <option value="Miscellaneous" {{ request('new_category') == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                <option value="Health" {{ request('new_category') == 'Health' ? 'selected' : '' }}>Health</option>
            </select>
        </div>

        <!-- Broker Selection -->
        <div class="col-md-3 d-flex align-items-center">
            <label for="broker_code" class="form-label me-2" style="white-space: nowrap;">Select Broker</label>
            <select name="broker_code" id="broker_code" class="form-control select2" >
                <option value="All" {{ request('broker_code') == 'All' ? 'selected' : '' }}>All</option>
                @foreach($brokers as $broker)
                    <option value="{{ $broker['PPS_PARTY_CODE'] }}"
                        {{ request('broker_code') == $broker['PPS_PARTY_CODE'] ? 'selected' : '' }}>
                        {{ $broker['PPS_PARTY_NAME'] ?? $broker['PPS_DESC'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Client Type -->
        <div class="col-md-3 d-flex align-items-center">
            <label for="client_type" class="form-label me-2" style="white-space: nowrap; width: 100px;">Client Type</label>
            <select name="client_type" id="client_type" class="form-control">
                <option value="All" {{ request('client_type') == 'All' ? 'selected' : '' }}>ALL</option>
                <option value="new" {{ request('client_type') == 'new' ? 'selected' : '' }}>New</option>
            </select>
        </div>
        
        <!-- Business Class -->
        <div class="col-md-3 d-flex align-items-center">
            <label for="business_class" class="form-label me-2" style="white-space: nowrap; width: 100px;">Business Class</label>
            <select name="business_class" id="business_class" class="form-control select2">
                <option value="All" {{ request('business_class') == 'All' ? 'selected' : '' }}>ALL</option>
                @foreach($businessClasses as $businessClass)
                    <option value="{{ $businessClass['PBC_BUSICLASS_CODE'] }}"
                        {{ request('business_class') == $businessClass['PBC_BUSICLASS_CODE'] ? 'selected' : '' }}>
                        {{ $businessClass['PBC_DESC'] }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <!-- Sum -->
        <div class="col-md-3 d-flex align-items-center">
            <label for="sum" class="form-label me-2" style="white-space: nowrap; width: 100px;">Sum</label>
            <input type="number" name="sum" id="sum" class="form-control" 
                   value="{{ request('sum',1000000000) }}">
        </div>

        <!-- Filter Button -->
        <div class="col-md-3 d-flex align-items-center">
            <button type="submit" class="btn btn-outline-primary me-1" title="Filter">
                <i class="bi bi-funnel-fill"></i>
            </button>
            <a href="{{ url('/c') }}" class="btn btn-outline-secondary me-1" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
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
            <th>Actions</th>
            <th>Document Ref No</th>
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
        </tr>
    </thead>
    <tbody>
        @foreach($data as $record)
        <tr>
            <td>
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
            @endphp
            <button class="btn btn-success btn-sm" onclick="fetchData('{{ $record->GDH_DOC_REFERENCE_NO ?? 'N/A' }}', '{{ $code ?? 'N/A' }}', this)">
                <i class="fas fa-check"></i>
            </button>
        </td>
            <td>{{ $record->GDH_DOC_REFERENCE_NO ?? 'N/A' }}</td>
            <td>{{ $department }}</td>
            <td>
                @php
                    $date = null;
                    if (!empty($record->GDH_ISSUEDATE)) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d-M-y', $record->GDH_ISSUEDATE)->format('d-m-Y');
                        } catch (\Exception $e) {
                            try {
                                $date = \Carbon\Carbon::createFromFormat('j-M-y', $record->GDH_ISSUEDATE)->format('d-m-Y');
                            } catch (\Exception $e) {
                                $date = null;
                            }
                        }
                    }
                @endphp
                {{ $date ?? 'N/A' }}
            </td>
            <td>
                @php
                    $date = null;
                    if (!empty($record->GDH_COMMDATE)) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d-M-y', $record->GDH_COMMDATE)->format('d-m-Y');
                        } catch (\Exception $e) {
                            try {
                                $date = \Carbon\Carbon::createFromFormat('j-M-y', $record->GDH_COMMDATE)->format('d-m-Y');
                            } catch (\Exception $e) {
                                $date = null;
                            }
                        }
                    }
                @endphp
                {{ $date ?? 'N/A' }}
            </td>
            <td>
                @php
                    $date = null;
                    if (!empty($record->GDH_EXPIRYDATE)) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d-M-y', $record->GDH_EXPIRYDATE)->format('d-m-Y');
                        } catch (\Exception $e) {
                            try {
                                $date = \Carbon\Carbon::createFromFormat('j-M-y', $record->GDH_EXPIRYDATE)->format('d-m-Y');
                            } catch (\Exception $e) {
                                $date = null;
                            }
                        }
                    }
                @endphp
                {{ $date ?? 'N/A' }}
            </td>
            <td title="{{ $record->PPS_DESC ?? 'N/A' }}">
    {{ \Illuminate\Support\Str::limit($record->PPS_DESC ?? 'N/A', 5, '...') }}
</td>
           <td title="{{ $record->PLC_LOCADESC ?? 'N/A' }}">
    {{ \Illuminate\Support\Str::limit($record->PLC_LOCADESC ?? 'N/A', 5, '...') }}
</td>
            <td>{{ $record->PLC_LOCADESC ?? 'N/A' }}</td>
            <td class="numeric" style="text-align: right;">
                {{
                    trim($record->GDH_TOTALSI ?? '') === '&nbsp;' || empty(trim($record->GDH_TOTALSI ?? ''))
                        ? 'N/A'
                        : (is_numeric($record->GDH_TOTALSI) ? number_format($record->GDH_TOTALSI) : $record->GDH_TOTALSI)
                }}
            </td>
            <td class="numeric" style="text-align: right;">
                {{
                    trim($record->GDH_GROSSPREMIUM ?? '') === '&nbsp;' || empty(trim($record->GDH_GROSSPREMIUM ?? ''))
                        ? 'N/A'
                        : (is_numeric($record->GDH_GROSSPREMIUM) ? number_format($record->GDH_GROSSPREMIUM) : $record->GDH_GROSSPREMIUM)
                }}
            </td>
            <td class="numeric" style="text-align: right;">
                {{
                    trim($record->GDH_NETPREMIUM ?? '') === '&nbsp;' || empty(trim($record->GDH_NETPREMIUM ?? ''))
                        ? 'N/A'
                        : (is_numeric($record->GDH_NETPREMIUM) ? number_format($record->GDH_NETPREMIUM) : $record->GDH_NETPREMIUM)
                }}
            </td>
           
        </tr>
        @endforeach
    </tbody>
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
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select a location",
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
        text: '<i class="fas fa-file-excel custom-icon"></i> Excel',
        className: 'btn btn-success btn-sm',
        title: 'Reinsurance Case Report',
        footer: true,
        exportOptions: {
            columns: ':visible:not(:first-child)', // Skip the first column (Actions)
            format: {
                body: function (data, row, column, node) {
                    // Return full text for columns 6 and 7
                    if (column === 5 || column === 6) {
                        return $(node).attr('title') || $(node).text().trim(); // Full text
                    }
                    return $(node).text().trim(); // For other columns
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
            columns: ':visible:not(:first-child)', // Skip the first column (Actions)
            format: {
                body: function (data, row, column, node) {
                    // Return full text for columns 6 and 7
                    if (column === 5 || column === 6) {
                        return $(node).attr('title') || $(node).text().trim(); // Full text
                    }
                    return $(node).text().trim(); // For other columns
                }
            }
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

                    // Column 9: Gross Premium
                    var totalGrossPrem = api.column(9, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Column 10: Sum Insured
                    var totalSumInsured = api.column(10, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Column 11: Net Premium
                    var totalNetPrem = api.column(11, {page: 'current'}).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    // Update footer
                    $('#totalGrossPrem').html(totalGrossPrem.toLocaleString('en-US'));
                    $('#totalSumInsured').html(totalSumInsured.toLocaleString('en-US'));
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
<script>
function fetchData(uw_doc, dept_code, button) {
    if (!uw_doc || uw_doc === 'N/A' || !dept_code || dept_code === 'N/A') {
        alert('Error: Document reference number or department code is invalid or missing');
        console.error('Invalid data: uw_doc=', uw_doc, 'dept_code=', dept_code);
        return;
    }

    if (!confirm('Are you sure you want to insert this record?')) {
        return; // User canceled
    }

    const routeUrl = '{{ route("fetch.reinsurance.data") }}';
    console.log('Route URL:', routeUrl);
    console.log('Document Reference:', uw_doc);
    console.log('Department Code:', dept_code);

    $.ajax({
        url: routeUrl,
        method: 'POST',
        data: {
            uw_doc: uw_doc,
            dept: dept_code, // Send the code (e.g., 11)
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            alert(response.message);
            if (response.message === 'Record added successfully') {
                // Remove the row from the table
                $(button).closest('tr').remove();
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Failed to connect to server';
            alert(`Error: ${xhr.status} - ${errorMessage}`);
            console.error('Error Response:', xhr.responseText);
        }
    });
}
</script>
</body>
</html>