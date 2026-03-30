<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>

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
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Edit Employee: {{ $record->employee_name }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('employee.update', $record->id) }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Client Name</label>
                        <input type="text" name="client_name" class="form-control" value="{{ $record->client_name }}" required>
                    </div>
                  
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Policy No</label>
                        <input type="text" name="policy_no" class="form-control" value="{{ $record->policy_no }}">
                    </div>
                     <div class="col-md-4">
                        <label>Effective Date</label>
                        <input type="date" name="effective_date" class="form-control"
                               value="{{ $record->effective_date ? \Carbon\Carbon::parse($record->effective_date)->format('Y-m-d') : '' }}">
                    </div>
                </div>

                

                <div class="d-flex justify-content-between">
                    <a href="{{ route('employee.view', $record->id) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Update Employee
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
