<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Deletion Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Your existing styles go here */
        table.dataTable {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        th, td {
            font-size: 14px !important;
            font-family: Calibri, sans-serif !important;
            padding: 2px !important;
            border-collapse: collapse !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        th {
            text-align: center !important;
            border: 1px solid blue !important;
            background-color: blue !important;
            color: white !important;
            font-weight: bold !important;
        }
        td {
            border: 1px solid black !important;
            vertical-align: top !important;
            text-align: left;
        }
        tfoot {
            display: table-row-group;
        }
        tfoot th, tfoot td {
            border: 1px solid black !important;
            background-color: blue !important;
            color: white !important;
            text-align: center !important;
            width: auto;
        }
        .dataTables_wrapper {
            margin-top: 0px;
            width: 100%;
            padding: 0;
        }
        .dataTables_filter input {
            width: 250px !important;
            font-size: 12px !important;
            border: 2px solid #007bff !important;
            border-radius: 5px !important;
            padding: 5px !important;
        }
        .dt-buttons {
            margin-bottom: 0 !important;
            padding: 0 !important;
        }
        .modal-header, .modal-footer {
            border: none;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <p class="text-sm flex items-center gap-2 mb-2 pb-1 text-gray-900 border-b-2 border-blue-400">
            <a>
                <img class="logo-abbr" src="images/atlas.png" alt="Logo" style="width: 24px; height: 24px;">
            </a>
            <span style="font-family: 'Great Vibes', cursive; font-size: 1.4rem; font-weight: 300; color: #3B3B3B; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);">
                Assets Addition Report
            </span>
            <span class="text-gray-400 text-sm">/</span>
            <a href="{{ url('/') }}" class="text-blue-500 text-sm" style="text-decoration: none;">Home</a>
        </p>
        
        <form method="GET" action="{{ url('/dep') }}" class="mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-3 d-flex align-items-center">
                    <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', date('Y-m-01')) }}">
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', date('Y-m-d')) }}">
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-primary me-1" title="Filter">
                        <i class="bi bi-funnel-fill"></i>
                    </button> 
                    <a href="{{ url('/register') }}" class="btn btn-outline-secondary me-1" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                    <button type="button" class="btn btn-secondary me-1" title="History" data-bs-toggle="modal" data-bs-target="#historyModal">
                        <i class="bi bi-record-circle"></i> History
                    </button>
                    <button type="button" class="btn btn-danger" title="Report Bug" data-bs-toggle="modal" data-bs-target="#bugModal">
                        <i class="bi bi-bug"></i> Bug
                    </button>
                </div>
            </div>
        </form>

        @if(empty($data))
            <div class="alert alert-danger">No data available.</div>
        @else
            <table id="reportsTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Asset Code</th>
                        <th>Asset Description</th>
                        <th>Date of Purchase</th>
                        <th>Date of Sale</th>
                        <th>Original Cost</th>
                        <th>Acc_Depreciation</th>
                        <th>Sale Proceed</th>
                        <th>Net Book Value</th>
                        <th>Gain/Loss</th>
                        <th>Party Member</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                        <tr>
                            <td>{{ $record['Asset Code'] ?? 'N/A' }}</td>
                            <td>{{ $record['Asset Description'] ?? 'N/A' }}</td>
                            <td>{{ $record['Date of Purchase'] ?? 'N/A' }}</td>
                            <td>{{ $record['Date of Sale'] ?? 'N/A' }}</td>
                            <td class="align-right">{{ number_format($record['Original Cost'] ?? 0, 2, '.', ',') }}</td>
                            <td class="align-right">{{ number_format($record['Accumulated Depreciation'] ?? 0, 2, '.', ',') }}</td>
                            <td class="align-right">{{ number_format($record['Sale Proceed'] ?? 0, 2, '.', ',') }}</td>
                            <td class="align-right">{{ number_format(($record['Original Cost'] ?? 0) - ($record['Accumulated Depreciation'] ?? 0), 2, '.', ',') }}</td>
                            <td class="align-right">{{ number_format((($record['Original Cost'] ?? 0) - ($record['Accumulated Depreciation'] ?? 0)) - ($record['Sale Proceed'] ?? 0), 2, '.', ',') }}</td>
                            <td>{{ $record['Party Member '] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Totals</th>
                        <th id="totalOriginalCost">{{ number_format($totalOriginalCost, 2, '.', ',') }}</th>
                        <th id="totalAccumulatedDepreciation">{{ number_format($totalAccumulatedDepreciation, 2, '.', ',') }}</th>
                        <th id="totalSaleProceed">{{ number_format($totalSaleProceed, 2, '.', ',') }}</th>
                        <th id="totalNetBookValue">{{ number_format($totalNetBookValue, 2, '.', ',') }}</th>
                        <th id="totalGainLoss">{{ number_format($totalGainLoss, 2, '.', ',') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        @endif

        <!-- History Modal -->
        <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="historyModalLabel">Change History</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Change Date</th>
                                    <th>Changed By</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Populate with history data -->
                                <tr>
                                    <td>2023-04-01</td>
                                    <td>Admin</td>
                                    <td>Added new asset</td>
                                </tr>
                                <tr>
                                    <td>2023-04-05</td>
                                    <td>User</td>
                                    <td>Updated asset description</td>
                                </tr>
                                <!-- Add more historical records as required -->
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bug Report Modal -->
        <div class="modal fade" id="bugModal" tabindex="-1" aria-labelledby="bugModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bugModalLabel">Report a Bug</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ url('/report-bug') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="bug_description" class="form-label">Bug Description</label>
                                <textarea class="form-control" id="bug_description" name="bug_description" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit Bug</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#reportsTable').DataTable({
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
                }
            });
        });
    </script>
</body>
</html>