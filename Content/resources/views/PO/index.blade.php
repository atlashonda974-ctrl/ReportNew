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
        <x-report-header title="Get PO" />

        <form method="GET" action="{{ url('/po') }}" class="mb-4">
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

                <!-- Filter Dropdown -->
                <div class="col-md-3 d-flex align-items-center">
                    <label for="status_filter" class="form-label me-2" style="white-space: nowrap; width: 100px;">Status</label>
                    <select name="status_filter" id="status_filter" class="form-control select2">
                        <option value="all" {{ request('status_filter', 'all') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="outstanding" {{ request('status_filter') == 'outstanding' ? 'selected' : '' }}>Outstanding</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-3 d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-primary me-2" title="Filter">
                        <i class="bi bi-funnel-fill"></i> Filter
                    </button>
                    <a href="{{ url('/po') }}" class="btn btn-outline-secondary me-2" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        @if(empty($data))
            <div class="alert alert-danger">No data available.</div>
        @else
<table id="reportsTable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Reference No</th>   <!-- 0 -->
            <th>Gross Premium</th>  <!-- 1 -->
            <th>Net Premium</th>    <!-- 2 -->
            <th>Total SI</th>       <!-- 3 -->
            <th>Total Collection</th> <!-- 4 -->
            <th>Outstanding</th>    <!-- 5 -->
        </tr>
    </thead>
    <tbody>
        @foreach($data as $record)
            <tr>
                <td>{{ $record->GDH_DOC_REFERENCE_NO ?? 'N/A' }}</td>
                <td>{{ $record->GDH_GROSSPREMIUM ?? 0 }}</td>
                <td>{{ $record->GDH_NETPREMIUM ?? 0 }}</td>
                <td>{{ $record->GDH_TOTALSI ?? 0 }}</td>
                <td>{{ $record->TOT_COL ?? 0 }}</td>
                <td>{{ ($record->GDH_GROSSPREMIUM ?? 0) - ($record->TOT_COL ?? 0) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th></th>  <!-- Reference No -->
            <th></th>  <!-- Gross Premium -->
            <th></th>  <!-- Net Premium -->
            <th></th>  <!-- Total SI -->
            <th></th>  <!-- Total Collection -->
            <th></th>  <!-- Outstanding -->
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
    // Initialize Select2
    $('.select2').select2({
        placeholder: "Select a status",
        allowClear: true,
        width: '69%'
    });

    // Function to format numbers for export
    function formatLargeNumber(data) {
        if (typeof data !== 'string' && typeof data !== 'number') {
            return data;
        }
        const cleanedData = data.toString().replace(/[^0-9.-]+/g, "");
        const num = parseFloat(cleanedData);
        if (isNaN(num)) {
            return data;
        }
        return num.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Initialize DataTable
    const table = $('#reportsTable').DataTable({
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
        extend: 'excelHtml5',
        text: '<i class="fas fa-file-excel"></i> Excel',
        className: 'btn btn-success btn-sm',
        title: 'Outstanding Report',
        exportOptions: {
            columns: ':visible',
            format: {
                body: function (data, row, column, node) {
                    // Format only for columns 1–5
                    if ([1, 2, 3, 4, 5].includes(column)) {
                        const value = parseFloat($(node).text().replace(/,/g, ''));
                        return isNaN(value) ? '0' : value.toLocaleString('en-US');
                    }
                    return $(node).text().trim();
                }
            }
        },
        customizeData: function (data) {
            const intVal = i => typeof i === 'string'
                ? i.replace(/[^\d.-]/g, '') * 1
                : (typeof i === 'number' ? i : 0);

            const totalCols = [1, 2, 3, 4, 5]; // Columns to sum
            let totals = new Array(data.body[0].length).fill('');

            totalCols.forEach(col => {
                let sum = data.body.reduce((acc, row) => acc + intVal(row[col]), 0);
                totals[col] = sum.toLocaleString('en-US');
            });

            totals[0] = 'Totals:'; // First column label
            data.body.push(totals);
        }
    },
    {
        extend: 'pdfHtml5',
        text: '<i class="fas fa-file-pdf"></i> PDF',
        className: 'btn btn-danger btn-sm',
        title: 'Outstanding Report',
        orientation: 'landscape',
        pageSize: 'A4',
        exportOptions: {
            columns: ':visible',
            format: {
                body: function (data, row, column, node) {
                    if ([1, 2, 3, 4, 5].includes(column)) {
                        const value = parseFloat($(node).text().replace(/,/g, ''));
                        return isNaN(value) ? '0' : value.toLocaleString('en-US');
                    }
                    return $(node).text().trim();
                }
            }
        }
    }
],

        "footerCallback": function(row, data, start, end, display) {
            const api = this.api();
            const intVal = function(i) {
                return typeof i === 'string' ?
                    parseFloat(i.replace(/[^\d.-]/g, '')) || 0 :
                    typeof i === 'number' ? i : 0;
            };

            const columnsToTotal = [1, 2, 3, 4, 5];
            columnsToTotal.forEach(colIndex => {
                const total = api.column(colIndex, {
                    page: 'current'
                }).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                $(api.column(colIndex).footer()).html(
                    '<strong>' + total.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + '</strong>'
                );
            });
        },
        "initComplete": function() {
            $('#totalSum').html('0');
            this.api().draw();
            this.api().columns.adjust();
            $('.dataTables_filter input').attr('placeholder', 'Search...');
            $('.dataTables_filter').css({
                'margin-left': '5px',
                'margin-right': '5px'
            });
            $('.dt-buttons').css('margin-left', '5px');

            const statusFilter = "{{ request('status_filter', 'all') }}";
            if (statusFilter === 'outstanding') {
                this.api().column(5).search('^\\s*[^0]\\d*\\.?\\d*\\s*$', true, false).draw();
            }
        },
        "drawCallback": function() {
            this.api().columns.adjust();
        }
    });

    $('#status_filter').on('change', function() {
        const value = $(this).val();
        if (value === 'outstanding') {
            table.column(5).search('^\\s*[^0]\\d*\\.?\\d*\\s*$', true, false).draw();
        } else {
            table.column(5).search('').draw();
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