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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
     <meta name="csrf-token" content="{{ csrf_token() }}">
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
     <style>
        .stat-card {
            cursor: pointer;
            transition: transform 0.2s;
            min-height: 120px;
        }
        .stat-card:hover {
            transform: scale(1.02);
        }
        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
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
                        <h5 class="card-title">Inactive Users</h5>
                        <p class="card-text display-4">{{ $inactiveCount }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-info mb-3 stat-card" onclick="showLoginModal()">
                    <div class="card-body text-center">
                        <h5 class="card-title">Logins Today</h5>
                        <p class="card-text display-4">{{ $loginsTodayCount }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3 stat-card" onclick="showLogoutModal()">
                    <div class="card-body text-center">
                        <h5 class="card-title">Logouts Today</h5>
                        <p class="card-text display-4">{{ $logoutsTodayCount }}</p>
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
                                <div class="stat-label">Inactive</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Users Logged In</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Filter by Date:</label>
                            <input type="date" id="loginDateFilter" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Search User:</label>
                            <input type="text" id="loginSearch" class="form-control" placeholder="Search...">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-secondary me-2" onclick="resetLoginFilters()">Reset</button>
                            <button class="btn btn-primary" onclick="applyLoginFilters()">Apply</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>User Name</th>
                                    <th>User Code</th>
                                    <th>Branch</th>
                                    <th>Login Date/Time</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody id="loginUsersBody">
                                @foreach($loginsToday as $index => $login)
                                @php
                                    $cleanedDate = preg_replace('/\.\d+ /', ' ', $login['SUL_LOGINDATE']);
                                    $loginDate = \Carbon\Carbon::createFromFormat('d-M-y h.i.s A', $cleanedDate);
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $login['SUS_NAME'] }}</td>
                                    <td>{{ $login['SUS_USERCODE'] }}</td>
                                    <td>{{ $login['PLC_DESC'] ?? 'N/A' }}</td>
                                    <td>{{ $loginDate->format('d-M-Y h:i A') }}</td>
                                    <td>{{ $login['SUL_IPADDRESS'] ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Users Logged Out</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Filter by Date:</label>
                            <input type="date" id="logoutDateFilter" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Search User:</label>
                            <input type="text" id="logoutSearch" class="form-control" placeholder="Search...">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-secondary me-2" onclick="resetLogoutFilters()">Reset</button>
                            <button class="btn btn-primary" onclick="applyLogoutFilters()">Apply</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>User Name</th>
                                    <th>User Code</th>
                                    <th>Branch</th>
                                    <th>Logout Date/Time</th>
                                </tr>
                            </thead>
                            <tbody id="logoutUsersBody">
                                @foreach($logoutsToday as $index => $logout)
                                    @php
                                        $loginDate = isset($logout['SUS_LASTLOGIN']) 
                                            ? \Carbon\Carbon::createFromFormat('d-M-y', $logout['SUS_LASTLOGIN'])->format('d-M-Y') 
                                            : 'N/A';
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $logout['SUS_NAME'] }}</td>
                                        <td>{{ $logout['SUS_USERCODE'] }}</td>
                                        <td>{{ $logout['PLC_DESC'] ?? 'N/A' }}</td>
                                        <td>{{ $loginDate }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
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
                    <option value="I" {{ request('user_status') === 'I' ? 'selected' : '' }}>Inactive Users</option>
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
                <a href="#" class="btn btn-secondary me-1" title="History">
                    <i class="bi bi-record-circle"></i> History
                </a>
                <a href="#" class="btn btn-secondary" title="Bug">
                    <i class="bi bi-bug"></i> Bug
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
                                <span class="badge bg-danger">Inactive</span>
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

<script>
$(document).ready(function() {
    // Set CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

// Show Login Modal
function showLoginModal() {
    const modal = new bootstrap.Modal(document.getElementById('loginModal'));
    modal.show();
    resetLoginFilters(); // Load all login records initially
}

// Show Logout Modal
function showLogoutModal() {
    const modal = new bootstrap.Modal(document.getElementById('logoutModal'));
    modal.show();
    resetLogoutFilters(); // Load all logout records initially
}

// Apply Login Filters
function applyLoginFilters() {
    const selectedDate = $('#loginDateFilter').val();
    const searchTerm = $('#loginSearch').val().toLowerCase();
    fetchLoginData(selectedDate, searchTerm);
}

// Reset Login Filters
function resetLoginFilters() {
    $('#loginDateFilter').val('');
    $('#loginSearch').val('');
    fetchLoginData('', ''); // Fetch all records without filters
}

// Fetch Login Data (Filtered or Full)
function fetchLoginData(date, search) {
    showLoading(true, '#loginUsersBody');

    $.ajax({
        url: "{{ route('logins.by.date') }}",
        type: 'GET',
        data: { 
            date: date,
            search: search 
        },
        success: function(data) {
            updateLoginTable(data.data);
            showLoading(false, '#loginUsersBody');
        },
        error: function(xhr, status, error) {
            console.error('Error fetching logins:', error);
            showLoading(false, '#loginUsersBody');
            $('#loginUsersBody').html('<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

// Update Login Table
function updateLoginTable(logins) {
    const tbody = $('#loginUsersBody');
    tbody.empty();

    if (!logins || logins.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center">No records found</td></tr>');
        return;
    }

    logins.forEach((login, index) => {
        try {
            const dateField = login.SUL_LOGINDATE || login.SUS_LASTLOGIN;
            const formattedDate = formatDate(dateField);
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${login.SUS_NAME || 'N/A'}</td>
                    <td>${login.SUS_USERCODE || 'N/A'}</td>
                    <td>${login.PLC_DESC || 'N/A'}</td>
                    <td>${formattedDate}</td>
                    <td>${login.SUL_IPADDRESS || 'N/A'}</td>
                </tr>
            `;
            tbody.append(row);
        } catch (e) {
            console.error('Error formatting login data:', e);
            tbody.append(`<tr class="text-danger"><td colspan="6">Error displaying record: ${e.message}</td></tr>`);
        }
    });
}

// Apply Logout Filters
function applyLogoutFilters() {
    const selectedDate = $('#logoutDateFilter').val();
    const searchTerm = $('#logoutSearch').val().toLowerCase();
    fetchLogoutData(selectedDate, searchTerm);
}

// Reset Logout Filters
function resetLogoutFilters() {
    $('#logoutDateFilter').val('');
    $('#logoutSearch').val('');
    fetchLogoutData('', ''); // Fetch all records without filters
}

// Fetch Logout Data (Filtered or Full)
function fetchLogoutData(date, search) {
    showLoading(true, '#logoutUsersBody');

    $.ajax({
        url: "{{ route('logouts.by.date') }}",
        type: 'GET',
        data: { 
            date: date,
            search: search 
        },
        success: function(data) {
            updateLogoutTable(data.data);
            showLoading(false, '#logoutUsersBody');
        },
        error: function(xhr, status, error) {
            console.error('Error fetching logouts:', error);
            showLoading(false, '#logoutUsersBody');
            $('#logoutUsersBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

// Update Logout Table
function updateLogoutTable(logouts) {
    const tbody = $('#logoutUsersBody');
    tbody.empty();

    if (!logouts || logouts.length === 0) {
        tbody.append('<tr><td colspan="5" class="text-center">No records found</td></tr>');
        return;
    }

    logouts.forEach((logout, index) => {
        try {
            const dateField = logout.SUL_LOGOUTDATE || logout.SUS_LASTLOGIN;
            const formattedDate = formatDate(dateField);
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${logout.SUS_NAME || 'N/A'}</td>
                    <td>${logout.SUS_USERCODE || 'N/A'}</td>
                    <td>${logout.PLC_DESC || 'N/A'}</td>
                    <td>${formattedDate}</td>
                </tr>
            `;
            tbody.append(row);
        } catch (e) {
            console.error('Error formatting logout data:', e);
            tbody.append(`<tr class="text-danger"><td colspan="5">Error displaying record: ${e.message}</td></tr>`);
        }
    });
}

// Utility Function: Format Date
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const cleanedDate = dateString.replace(/\.\d+ /, ' ');
    return moment(cleanedDate, 'DD-MMM-YY h.mm.ss A').format('DD-MMM-YYYY h:mm A');
}

// Utility Function: Show Loading Spinner
function showLoading(show, selector) {
    if (show) {
        $(selector).html('<tr><td colspan="6" class="text-center"><div class="spinner-border" role="status"></div></td></tr>');
    }
}
</script>

@stack('scripts')
</body>
</html>