<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Employee Report</title>

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<!-- DataTables Core + Extensions -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.dataTables.min.css">

<!-- Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Custom DataTable Styles (Blade Component) -->
<x-datatable-styles />

<!-- Moment.js for date formatting -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
</head>

<body>
<div class="container mt-5">
    <h3 class="mb-4">Employee Report new</h3>

    @if(empty($data) || $data->isEmpty())
        <div class="alert alert-danger text-center">
            <strong>No data available.</strong>
        </div>
    @else
        <div class="table-responsive">
            <table id="reportsTable" class="table table-bordered table-striped display nowrap" style="width:100%">
                <thead class="table-dark">
                    <tr>
                        <th>Client Name</th>
                        <th>Policy No</th>
                        <th>Effective Date</th>
                        <th>No of Employees</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grouped = $data->groupBy('transaction_id');
                    @endphp

                    @foreach($grouped as $transactionId => $records)
                        @php $first = $records->first(); @endphp
                        <tr>
                            <td>{{ $first->client_name ?? '-' }}</td>
                            <td>{{ $first->policy_no ?? '-' }}</td>
                            <td>{{ $first->effective_date_of_deletion ? \Carbon\Carbon::parse($first->effective_date_of_deletion)->format('d-m-Y') : '-' }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm view-documents-btn"
                                    data-transaction="{{ $transactionId }}">
                                    <i class="bi bi-file-earmark-text"></i>Employes ({{ $records->count() }})
                                </button>
                            </td>
                            <td>
                                <a href="{{ route('employee.edit.del', $first->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Documents Modal -->
<div class="modal fade" id="documentsModal" tabindex="-1" aria-labelledby="documentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="documentsModalLabel">Documents</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Client Name:</strong> <span id="modalClientName"></span></p>
        <p><strong>Policy No:</strong> <span id="modalPolicyNo"></span></p>

        <div id="modalDocumentsBody"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.3.2/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

<script>
var employeeData = @json($data);

$(document).ready(function() {
    $('#reportsTable').DataTable({
        paging: false,
        searching: true,
        ordering: true,
        info: true,
        scrollX: true,
        scrollY: "300px",
        fixedHeader: true,
        autoWidth: true,
        dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
        buttons: [
            { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-success btn-sm', title: 'Employee Report', footer: true, exportOptions: { columns: ':visible' } },
            { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-danger btn-sm', title: 'Employee Report', orientation: 'landscape', pageSize: 'A4', footer: true, exportOptions: { columns: ':visible' } }
        ],
        initComplete: function() {
            this.api().columns.adjust();
            $('.dataTables_filter').css('margin-left', '5px').css('margin-right', '5px');
            $('.dt-buttons').css('margin-left', '5px');
        }
    });

    // Handle Documents Modal
    $(document).on('click', '.view-documents-btn', function() {
        let transactionId = $(this).data('transaction');
        let filtered = employeeData.filter(e => e.transaction_id == transactionId);
        let first = filtered[0];

        $('#modalClientName').text(first.client_name);
        $('#modalPolicyNo').text(first.policy_no);

        let rows = '';
        filtered.forEach((e, index) => {
            rows += `
<div class="card mb-2 p-2">
    <h6>Employee ${index + 1}</h6>
    <div class="row g-2">
        <div class="col-md-3">
            <label>Name</label>
            <input type="text" class="form-control form-control-sm" name="employees[${index}][employee_name]" value="${e.employee_name ?? ''}" readonly>
        </div>
        <div class="col-md-3">
            <label>Gender</label>
            <input type="text" class="form-control form-control-sm" name="employees[${index}][gender]" value="${e.gender ?? ''}" readonly>
        </div>
        <div class="col-md-3">
            <label>Employee Code</label>
            <input type="text" class="form-control form-control-sm" name="employees[${index}][employee_code]" value="${e.employee_code ?? ''}" readonly>
        </div>
        <div class="col-md-3">
            <label>Folio No</label>
            <input type="text" class="form-control form-control-sm" name="employees[${index}][folio_no]" value="${e.folio_no ?? ''}" readonly>
        </div>
        <div class="col-md-3">
    <label>Effective Date of Deletion</label>
    <input type="date" 
           class="form-control form-control-sm" 
           name="employees[${index}][effective_date_of_deletion]" 
           value="${e.effective_date_of_deletion ? new Date(e.effective_date_of_deletion).toISOString().split('T')[0] : ''}" 
           readonly>
</div>

        <div class="col-md-3">
            <label>Remarks</label>
            <input type="text" class="form-control form-control-sm" name="employees[${index}][remarks]" value="${e.remarks ?? ''}" readonly>
        </div>
       
</div>

</div>`;
        });

        $('#modalDocumentsBody').html(rows);

        // Show Modal
        var modal = new bootstrap.Modal(document.getElementById('documentsModal'));
        modal.show();
    });
});
</script>
</body>
</html>
