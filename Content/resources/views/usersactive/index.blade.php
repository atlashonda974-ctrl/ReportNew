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
    <style>
        .stat-card {
            cursor: pointer;
            transition: transform 0.2s;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
    <style>
        .stat-card {
            cursor: pointer;
            transition: transform 0.2s;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .branch-name {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
        }
        .stat-label {
            font-size: 14px;
            color: #6c757d;
        }
        .active-users {
            color: #28a745;
        }
        .inactive-users {
            color: #dc3545;
        }
        .total-users {
            color: #007bff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <x-report-header title="User Active Dashboard" />
  

    <!-- Button to Toggle Branch Statistics --> 
    <button id="toggleBranchStats" class="btn btn-primary mb-3">Branch-wise Statistics</button>

    <!-- Branch Statistics Section -->
    <div id="branchStatsSection" class="d-none">
        <h4 class="mb-3">Branch-wise Statistics</h4>
             <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text display-4">{{ $totalCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Active Users</h5>
                    <p class="card-text display-4">{{ $activeCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Logout Users</h5>
                    <p class="card-text display-4">{{ $inactiveCount }}</p>
                </div>
            </div>
        </div>
    </div>
        <div class="row mb-4">
            @foreach($branchStats as $branchName => $stats)
            <div class="col-md-4 mb-3">
                <div class="stat-card card shadow-sm" onclick="showBranchDetails('{{ $branchName }}', {{ json_encode($stats['users']) }})">
                    <div class="card-body">
                        <div class="branch-name">{{ $branchName ?: 'Unknown Branch' }}</div>
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="stat-value total-users">{{ $stats['total'] }}</div>
                                <div class="stat-label">Total</div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-value active-users">{{ $stats['active'] }}</div>
                                <div class="stat-label">Active</div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-value inactive-users">{{ $stats['inactive'] }}</div>
                                <div class="stat-label">Logout</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Modal for Branch Details -->
    <div class="modal fade" id="branchModal" tabindex="-1" aria-labelledby="branchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="branchModalLabel">Branch Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="userSearchInput" class="form-control mb-3" placeholder="Search users..." onkeyup="filterUsers()">
                    <div id="branchUsersTable" class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>User Name</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                </tr>
                            </thead>
                            <tbody id="branchUsersBody">
                                <!-- User rows will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Filter Form -->
     <form method="GET" action="{{ url('/u_active') }}" class="mb-4">
        <div class="row g-3">
        

            <!-- User Status Dropdown -->
            <div class="col-md-3 d-flex align-items-center">
                <label for="user_status" class="form-label me-2" style="white-space: nowrap;">User Status</label>
                <select name="user_status" id="user_status" class="form-select">
                    <option value="" {{ request('user_status') === null ? 'selected' : '' }}>All Users</option>
                    <option value="A" {{ request('user_status') === 'A' ? 'selected' : '' }}>Active Users</option>
                    <option value="I" {{ request('user_status') === 'I' ? 'selected' : '' }}>Logout Users</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-center">
                <label for="location" class="form-label me-2" style="white-space: nowrap; width: 100px;">Branch</label>
                <select name="location" id="location" class="form-control select2">
                    <option value="">All Branches</option>
                    @foreach($locations as $location)
                        <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>{{ $location }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Date Filter Dropdown -->
           <div class="col-md-3 d-flex align-items-center">
    <label for="login_timeframe" class="form-label me-2" style="white-space: nowrap;">Login Timeframe</label>
    <select name="login_timeframe" id="login_timeframe" class="form-select">
        <option value="">All Timeframes</option>
        <option value="today" {{ request('login_timeframe') == 'today' ? 'selected' : '' }}>Logged in Today</option>
        <option value="yesterday" {{ request('login_timeframe') == 'yesterday' ? 'selected' : '' }}>Logged in Yesterday</option>
        <option value="2days" {{ request('login_timeframe') == '2days' ? 'selected' : '' }}>Logged in 2 Days Ago</option>
        <option value="3days" {{ request('login_timeframe') == '3days' ? 'selected' : '' }}>Logged in 3 Days Ago</option>
        <option value="2w" {{ request('login_timeframe') == '2w' ? 'selected' : '' }}>Logged in last 2 weeks</option>
        <option value="3w" {{ request('login_timeframe') == '3w' ? 'selected' : '' }}>Logged in last 3 weeks</option>
        <option value="1m" {{ request('login_timeframe') == '1m' ? 'selected' : '' }}>Logged in last 1 month</option>
        <option value="more1m" {{ request('login_timeframe') == 'more1m' ? 'selected' : '' }}>Logged in more than 1 month ago</option>
    </select>
</div>

            <div class="col-md-3 d-flex align-items-center">
                <button type="submit" class="btn btn-outline-primary me-1" title="Filter">
                    <i class="bi bi-funnel-fill"></i>
                </button> 
                <a href="{{ url('/u_active') }}" class="btn btn-outline-secondary me-1" title="Reset">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </div>

        <!-- Filter Button Row -->
        <div class="row g-3 mt-3">
            <!-- Branch Dropdown -->
            

           
        </div>
    </form>

    @if(empty($data))
        <div class="alert alert-danger">No data available.</div>
    @else
        <table id="reportsTable" class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Location Description</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Last Pwd Update</th>
                    <th>Login Date</th>
                    <th>IP Address</th>
                
                </tr>
            </thead>
            <tbody>
                @foreach($data as $record)
                    <tr>
                        <td>{{ $record['SUS_NAME'] ?? 'N/A' }}</td>
                        <td>{{ $record['PLC_DESC'] ?? 'N/A' }}</td>
                        <td>
                            @if(($record['SUS_ACTIVE'] ?? '') === 'A')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Logout</span>
                            @endif
                        </td>
                        <td>{{ isset($record['SUS_LASTLOGIN']) ? \Carbon\Carbon::parse($record['SUS_LASTLOGIN'])->format('d-m-y') : 'N/A' }}</td>
                        <td>{{ isset($record['SUS_LASTPASSCHANGE']) ? \Carbon\Carbon::parse($record['SUS_LASTPASSCHANGE'])->format('d-m-y') : 'N/A' }}</td>
                        <td>{{ $record['SUL_LOGINDATE'] ?? 'N/A' }}</td>
                        <td>{{ $record['SUL_IPADDRESS'] ?? 'N/A' }}</td>
                   
                    </tr>
                @endforeach
            </tbody>
                <tr>
                    <td colspan="25" style="height: 18px;"></td>
                </tr>
            </tfoot>
        </table>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
        dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex ms-2"B>>>rt<"bottom"ip><"clear">', 
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel custom-icon"></i> Excel',
                className: 'btn btn-success btn-sm ms-2', // Added ms-2 for spacing
                title: 'Broker Report',
                footer: true,
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf custom-icon"></i> PDF',
                className: 'btn btn-danger btn-sm ms-2', // Added ms-2 for spacing
                title: 'Broker Report',
                orientation: 'landscape',
                pageSize: 'A4',
                footer: true,
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        page: 'current'
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
        },
        initComplete: function() {
            this.api().columns.adjust();
            $('.dataTables_filter').css('margin-right', '10px');
            $('.dt-buttons').css('margin-left', '10px');
        },
        drawCallback: function() {
            this.api().columns.adjust();
        }
    });
});

    </script>
<script>
$(document).ready(function() {
    $('#toggleBranchStats').click(function() {
        $('#branchStatsSection').toggleClass('d-none');
    });
});

function showBranchDetails(branchName, users) {
    $('#branchModalLabel').text('Users in ' + branchName);
    const tbody = $('#branchUsersBody').empty();
    users.forEach(user => {
        const status = user['SUS_ACTIVE'] === 'A' ? 'Active' : 'Inactive';
        const statusClass = user['SUS_ACTIVE'] === 'A' ? 'bg-success' : 'bg-danger';
        const lastLogin = user['SUS_LASTLOGIN'] ? new Date(user['SUS_LASTLOGIN']).toLocaleDateString() : 'N/A';
        tbody.append(`
            <tr>
                <td>${user['SUS_NAME'] ?? 'N/A'}</td>
                <td><span class="badge ${statusClass}">${status}</span></td>
                <td>${lastLogin}</td>
            </tr>
        `);
    });
    $('#branchModal').modal('show');
}

function filterUsers() {
    const input = document.getElementById('userSearchInput');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#branchUsersBody tr');

    rows.forEach(row => {
        const userName = row.cells[0].textContent.toLowerCase();
        if (userName.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

@stack('scripts')
</body>
</html>