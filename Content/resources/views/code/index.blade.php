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
    <x-datatable-styles />   
</head>
<body>
<div class="container mt-5">
    <x-report-header title="Broker code Report" />
    <form method="GET" action="{{ url('/code') }}" class="mb-4">
    <div class="row g-3">
        <!-- From Date -->
        <div class="col-md-3 d-flex align-items-center">
            <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', date('Y-m-01')) }}">
        </div>
        <!-- To Date -->
        <div class="col-md-3 d-flex align-items-center">
            <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', date('Y-m-d')) }}">
        </div>
        
        <!-- Supplier Filter -->
        <div class="col-md-3 d-flex align-items-center">
            <label for="supplier" class="form-label me-2" style="white-space: nowrap; width: 100px;">Supplier</label>
            <select name="supplier" id="supplier" class="form-control select2">
                <option value="" selected>All Suppliers</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier }}" {{ request('supplier') == $supplier ? 'selected' : '' }}>{{ $supplier }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Button -->
        <div class="col-md-3 d-flex align-items-center">
            <button type="submit" class="btn btn-outline-primary me-1" title="Filter">
                <i class="bi bi-funnel-fill"></i>
            </button> 
            <a href="{{ url('/code') }}" class="btn btn-outline-secondary me-1" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
            <!-- History Button -->
            <a href="#" class="btn btn-secondary me-1" title="History">
                <i class="bi bi-record-circle"></i> History
            </a>
            <!-- Bug Button -->
            <a href="#" class="btn btn-secondary" title="Bug">
                <i class="bi bi-bug"></i> Bug
            </a>
        </div>
    </div>
</form>

    @if(isset($error))
        <div class="alert alert-danger">
            {{ $error }}
            @if(isset($raw_response))
                <pre class="mt-2">{{ $raw_response }}</pre>
            @endif
        </div>
    @elseif(empty($data))
        <div class="alert alert-danger">No data available.</div>
    @else
        <table id="reportsTable" class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th>Broker Code</th>
                    <th>Broker Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $broker)
                    <tr>
                    <td>{{ $broker['PPS_PARTY_CODE'] }}</td>
                    <td>{{ $broker['PPS_DESC'] }}</td>
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
<script>
$(document).ready(function() {
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
                title: 'Broker Code Report',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Broker Code Report',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        page: 'current'
                    }
                }
            }
        ],
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