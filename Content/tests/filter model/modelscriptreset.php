<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Active Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-info mb-3 stat-card" onclick="showLoginModal()">
                <div class="card-body text-center">
                    <h5 class="card-title">Logins Today</h5>
                    <p class="card-text display-4">{{ $loginsTodayCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-warning mb-3 stat-card" onclick="showLogoutModal()">
                <div class="card-body text-center">
                    <h5 class="card-title">Logouts Today</h5>
                    <p class="card-text display-4">{{ $logoutsTodayCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
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
                                    $cleanedDate = preg_replace('/\.\d+ /', ' ', $logout['SUL_LOGOUTDATE']);
                                    $logoutDate = \Carbon\Carbon::createFromFormat('d-M-y h.i.s A', $cleanedDate);
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $logout['SUS_NAME'] }}</td>
                                    <td>{{ $logout['SUS_USERCODE'] }}</td>
                                    <td>{{ $logout['PLC_DESC'] ?? 'N/A' }}</td>
                                    <td>{{ $logoutDate->format('d-M-Y h:i A') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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


</body>
</html>