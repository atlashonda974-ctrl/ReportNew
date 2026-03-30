<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.master_titles')

    <!-- Bootstrap & DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
        .active-users { color: #28a745; }
        .inactive-users { color: #dc3545; }
        .total-users { color: #007bff; }

        /* Custom Tooltip Style */
        .login-history-tooltip .tooltip-inner {
            background-color: #1a1a1a;
            color: #fff;
            border-radius: 8px;
            padding: 12px;
            font-size: 13px;
            max-width: 380px;
            text-align: left;
            border: 1px solid #333;
        }
        .login-history-tooltip .tooltip-arrow::before {
            border-top-color: #1a1a1a !important;
        }
        .login-date-hover {
            cursor: help;
            text-decoration: underline dotted #007bff;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <x-report-header title="User’s Activity Today" />

        <!-- Toggle Branch Stats -->
        <button id="toggleBranchStats" class="btn btn-primary mb-3">
            Branch-wise Statistics
        </button>

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
                            <h5 class="card-title">Login Users</h5>
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
                        <div class="stat-card card shadow-sm" 
                             onclick="showBranchDetails('{{ $branchName }}', {{ json_encode($stats['users']) }})">
                            <div class="card-body">
                                <div class="branch-name">{{ $branchName ?: 'Unknown Branch' }}</div>
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <div class="stat-value total-users">{{ $stats['total'] }}</div>
                                        <div class="stat-label">Total</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="stat-value active-users">{{ $stats['active'] }}</div>
                                        <div class="stat-label">Login</div>
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
                                        <th>Login Date</th>
                                        <th>Logout Date</th>
                                    </tr>
                                </thead>
                                <tbody id="branchUsersBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ url('/uio') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3 d-flex align-items-center">
                    <label for="user_status" class="form-label me-2" style="white-space: nowrap;">User Status</label>
                    <select name="user_status" id="user_status" class="form-select">
                        <option value="" {{ request('user_status') === null ? 'selected' : '' }}>All Users</option>
                        <option value="A" {{ request('user_status') === 'A' ? 'selected' : '' }}>Login Users</option>
                        <option value="I" {{ request('user_status') === 'I' ? 'selected' : '' }}>Logout Users</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-center">
                    <label for="start_date" class="form-label me-2" style="white-space: nowrap;">From Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" 
                           value="{{ request('start_date', now()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-3 d-flex align-items-center">
                    <label for="end_date" class="form-label me-2" style="white-space: nowrap;">To Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" 
                           value="{{ request('end_date', now()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-3 d-flex align-items-center">
                    <label class="form-label me-2" style="white-space: nowrap;">Branch</label>
                    <select name="branch" id="branch" class="form-control select2">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch }}" 
                                    {{ request('branch') == $branch ? 'selected' : '' }}>
                                {{ $branch }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-primary me-1" title="Filter">Filter</button>
                    <a href="{{ url('/uio') }}" class="btn btn-outline-secondary me-1" title="Reset">Reset</a>
                </div>
            </div>
        </form>

        @if(empty($data))
            <div class="alert alert-danger">No data available.</div>
        @else
            <table id="reportsTable" class="table table-bordered table-responsive">
                <thead class="table-dark">
                    <tr>
                        <th>User Name</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Login Date</th>
                        <th>Logout Date</th>
                        <th>IP Address</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                        <tr>
                            <td>{{ $record['SAH_USERCODE'] }}</td>
                            <td>{{ $record['PLC_DESC'] }}</td>
                            <td>
                                @if($record['SAH_MESSAGETYPE'] === 'Success')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>
                            <td>
                                @if(!empty($record['SAH_LOGINS']) && count($record['SAH_LOGINS']) > 0)
                                    <?php
                                        try {
                                            $loginDate = \Carbon\Carbon::createFromFormat('d-M-y H.i.s.u', $record['SAH_LOGINS'][0]['SAH_LOGINDATE']);
                                            $formatted = $loginDate->format('d-M-Y h:i:s A');
                                        } catch (Exception $e) {
                                            $formatted = $record['SAH_LOGINS'][0]['SAH_LOGINDATE'];
                                        }
                                    ?>
                                    <span class="login-date-hover" 
                                          data-logins='@json($record['SAH_LOGINS'])'>
                                        {{ $formatted }}
                                    </span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $record['SAH_LOGOUTDATE'] ?? 'N/A' }}</td>
                            <td>{{ $record['SAH_IPADDRESS'] ?? 'N/A' }}</td>
                            <td>{{ $record['SAH_MESSAGE'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#branch').select2({
                placeholder: "Select Branch",
                allowClear: true
            });
        });

        // Global data for fast lookup
        window.allUserLogs = @json($data);

        $(document).ready(function() {
            var table = $('#reportsTable').DataTable({
                "paging": false,
                "pageLength": 50,
                "searching": true,
                "ordering": true,
                "info": true,
                "scrollX": true,
                "scrollY": "600px",
                "fixedHeader": true,
                dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'User Report',
                        footer: true,
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function (data, row, column, node) {
                                    return $('<div>').html(data).text().trim();
                                }
                            }
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        title: 'Users Activity Reports',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        footer: true,
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function (data, row, column, node) {
                                    return $('<div>').html(data).text().trim();
                                }
                            }
                        }
                    }
                ],
                "initComplete": function() {
                    this.api().columns.adjust();
                    $('.dataTables_filter input').attr('placeholder', 'Search...');
                    $('.dataTables_filter').css('margin-left', '5px').css('margin-right', '5px');
                    $('.dt-buttons').css('margin-left', '5px');
                },
                "drawCallback": function() {
                    this.api().columns.adjust();
                }
            });

            $('#toggleBranchStats').click(function() {
                $('#branchStatsSection').toggleClass('d-none');
            });

            // Main Feature: Hover on Login Date → Show Login History
            $(document).on('mouseenter', '.login-date-hover', function() {
                const el = this;
                const logins = $(this).data('logins');

                const count = logins.length;
                let content = `<strong>${logins[0].SAH_USERCODE}</strong> logged in <strong>${count} time${count > 1 ? 's' : ''}</strong> today<br><hr class="my-2">`;

                logins.sort((a, b) => new Date(b.SAH_LOGINDATE) - new Date(a.SAH_LOGINDATE))
                .forEach((log, i) => {
                        const time = new Date(log.SAH_LOGINDATE).toLocaleTimeString('en-US', { hour12: true });
                        const ip = log.SAH_IPADDRESS || 'Unknown IP';
                        const status = log.SAH_MESSAGETYPE === 'Success' ? '✓' : '✗';
                        const color = log.SAH_MESSAGETYPE === 'Success' ? '#28a745' : '#dc3545';
                        const latest = i === 0 ? ' ← Latest' : '';
                        content += `<div style="color:${color}"><small>${status} ${time} | ${ip} | ${log.SAH_MESSAGE || 'No message'}${latest}</small></div>`;
                    });

                const oldTip = bootstrap.Tooltip.getInstance(el);
                if (oldTip) oldTip.dispose();

                new bootstrap.Tooltip(el, {
                    title: content,
                    html: true,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body',
                    customClass: 'login-history-tooltip'
                }).show();
            });

            // Clean up tooltip on mouse leave
            $(document).on('mouseleave', '.login-date-hover', function() {
                const tip = bootstrap.Tooltip.getInstance(this);
                if (tip) setTimeout(() => tip.dispose(), 100);
            });
        });

        // Branch Modal Functions
        function showBranchDetails(branchName, users) {
            $('#branchModalLabel').text('Users in ' + (branchName || 'Unknown Branch'));
            const tbody = $('#branchUsersBody').empty();

            users.forEach(user => {
                const status = user.SAH_MESSAGETYPE === 'Success' ? 'Active' : 'Logout';
                const statusClass = user.SAH_MESSAGETYPE === 'Success' ? 'bg-success' : 'bg-danger';
                const loginDate = user.SAH_LOGINDATE ? new Date(user.SAH_LOGINDATE).toLocaleString() : 'N/A';
                const logoutDate = user.SAH_LOGOUTDATE ? new Date(user.SAH_LOGOUTDATE).toLocaleString() : 'N/A';

                tbody.append(`
                    <tr>
                        <td>${user.SAH_USERCODE || 'N/A'}</td>
                        <td><span class="badge ${statusClass}">${status}</span></td>
                        <td>${loginDate}</td>
                        <td>${logoutDate}</td>
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
                row.style.display = userName.includes(filter) ? '' : 'none';
            });
        }
    </script>

    @stack('scripts')
</body>
</html>