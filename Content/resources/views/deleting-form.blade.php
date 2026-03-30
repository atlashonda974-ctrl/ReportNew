<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>For Deleting Employee</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* Custom styling to match table header */
        .table-header-info {
            background-color: #d1e7dd; /* Light blue/green for info rows */
            border-color: #dee2e6;
        }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    
    <!-- Header Card -->
    <div class="card shadow">
        <div class="card-header text-center bg-primary text-white p-3">
            <h4 class="mb-0">For Deleting Employee</h4>
        </div>
        
        <div class="card-body p-0">
            <!-- Client & Policy Info -->
            <table class="table table-bordered mb-0">
                <tbody>
                    <tr class="table-info">
                        <td class="fw-bold" style="width: 20%;">Client Name</td>
                        <td>YKK Pakistan (Pvt) Ltd</td>
                    </tr>
                    <tr class="table-info">
                        <td class="fw-bold">Policy No.</td>
                        <td>2023LBHHIDP00000</td>
                    </tr>
                </tbody>
            </table>

            <!-- Dynamic Form Table -->
            <form id="employee-addition-form" action="{{ route('employee.store.del') }}" method="POST">
                @csrf 

                <!-- Hidden Inputs for Client & Policy (filled automatically) -->
                <input type="hidden" id="client_name" name="client_name">
                <input type="hidden" id="policy_no" name="policy_no">
                
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0" id="employee-table">
                        <thead class="table-light" style="white-space: nowrap;">
                            <tr>
                                <th style="width: 4%;">Sr. No.</th>
                                <th style="width: 19%;">Employee Name</th>
                                <th style="width: 19%;">Gender</th>
                                <th style="width: 9%;">Employee Code</th>
                                <th style="width: 7%;">Folio No</th>
                                <th style="width: 12%;">Effective Date</th>
                                <th style="width: 10%;">Remarks</th>
                                <th style="width: 6%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="employee-table-body">
                            <!-- Dynamic rows go here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="10" class="text-end p-2 border-top-0">
                                    <button type="button" class="btn btn-primary btn-sm" id="add-row-btn">
                                        <i class="fas fa-plus"></i> Add New Row
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="p-3 border-top text-end">
                    <button type="submit" class="btn btn-success btn-lg">Submit All Employees</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Automatically bind Client & Policy to hidden fields
    let clientName = $('td:contains("Client Name")').next().text().trim();
    let policyNo = $('td:contains("Policy No.")').next().text().trim();
    $('#client_name').val(clientName);
    $('#policy_no').val(policyNo);

    let rowCount = 0; // Tracks the number of employee rows

    // Function to add new employee row
    function addEmployeeRow() {
        const index = rowCount;
        rowCount++;
        const newRow = `
            <tr>
                <td>${rowCount}</td>
                <td><input type="text" name="employees[${index}][employee_name]" class="form-control form-control-sm" required></td>
               
                 <td>
                    <select name="employees[${index}][gender]" class="form-select form-select-sm" required>
                        <option value="">Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </td>
                <td><input type="text" name="employees[${index}][employee_code]" class="form-control form-control-sm"></td>
                <td><input type="text" name="employees[${index}][folio_no]" class="form-control form-control-sm"></td>
                <td><input type="date" name="employees[${index}][effective_date_of_deletion]" class="form-control form-control-sm" required></td>
                <td><input type="text" name="employees[${index}][remarks]" class="form-control form-control-sm"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-row-btn" title="Remove Row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#employee-table-body').append(newRow);
    }

    // Update Sr. No. after a row is removed
    function updateRowNumbers() {
        $('#employee-table-body tr').each(function(i) {
            $(this).find('td:first').text(i + 1);
        });
        rowCount = $('#employee-table-body tr').length;
    }

    // Load one initial row on page load
    addEmployeeRow();

    // Add new row on button click
    $('#add-row-btn').on('click', function() {
        addEmployeeRow();
    });

    // Remove row dynamically
    $(document).on('click', '.remove-row-btn', function() {
        $(this).closest('tr').remove();
        updateRowNumbers();
    });
    
});
</script>

</body>
</html>
