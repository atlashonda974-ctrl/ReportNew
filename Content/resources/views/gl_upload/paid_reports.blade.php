@extends('AutosecMaster.master')
@section('content')

    <div class="content-body">
        <div class="container-fluid">

            <!-- Preloader -->
            <div id="preloader"
                style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(255,255,255,0.95);z-index:9999;justify-content:center;align-items:center;">
                <div
                    style="text-align:center;background:white;padding:40px;border-radius:15px;box-shadow:0 10px 40px rgba(0,0,0,0.2);max-width:450px;width:90%;border:1px solid #e0e0e0;">
                    <div class="spinner-border text-primary" role="status" style="width:4rem;height:4rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div style="margin-top:25px;">
                        <h5 style="color:#333;font-weight:600;font-size:1.25rem;">Loading Report...</h5>
                        <p style="color:#666;margin-bottom:20px;">Please wait while the data is being loaded...</p>
                        <div class="progress mt-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                style="width:100%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Toast -->
            <div class="position-fixed top-0 end-0 p-3" style="z-index:11000;">
                <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
                    aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body"><i class="fa fa-check-circle me-2"></i><span id="toastMessage"></span></div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>

            <!-- Error Toast -->
            <div class="position-fixed top-0 end-0 p-3" style="z-index:11000;">
                <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert"
                    aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body"><i class="fa fa-exclamation-circle me-2"></i><span
                                id="errorToastMessage"></span></div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>

            <!-- VIEW PAID DOCUMENTS MODAL -->
            <div class="modal fade" id="viewDocModal" tabindex="-1" role="dialog" aria-labelledby="viewDocModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content" style="min-height:620px;">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title" id="viewDocModalLabel">
                                <i class="fa fa-eye me-2 text-primary"></i> View Payment Documents
                                <small class="ms-2 fw-normal text-muted" id="viewDocTitle"
                                    style="font-size:0.85rem;"></small>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0" style="display:flex;min-height:540px;overflow:hidden;">
                            <!-- LEFT SIDEBAR -->
                            <div id="viewDocSidebar"
                                style="width:235px;min-width:235px;border-right:1px solid #dee2e6;
                                       background:#f8f9fa;padding:12px 10px;overflow-y:auto;
                                       display:flex;flex-direction:column;">
                                <div
                                    style="font-size:0.75rem;font-weight:700;text-transform:uppercase;
                                            letter-spacing:.5px;color:#080808;margin-bottom:8px;padding:0 2px;">
                                    <i class="fa fa-paperclip me-1"></i>Uploaded Files
                                    <span id="viewDocFileCount" class="badge bg-warning text-white ms-1">0</span>
                                </div>
                                <div id="viewDocFileList" style="flex:1;"></div>
                            </div>
                            <!-- RIGHT PANE -->
                            <div style="flex:1;display:flex;flex-direction:column;min-width:0;">
                                <div id="viewDocLoading"
                                    style="flex:1;display:flex;flex-direction:column;
                                           align-items:center;justify-content:center;padding:40px;">
                                    <div class="spinner-border text-primary" role="status"
                                        style="width:3rem;height:3rem;display:none;" id="viewDocSpinner"></div>
                                    <div id="viewDocIdleMsg" style="text-align:center;">
                                        <i class="fa fa-hand-point-left fa-2x text-secondary mb-3 d-block"></i>
                                        <p class="text-muted mb-0">Select a file from the list to preview.</p>
                                    </div>
                                </div>
                                <div id="viewDocFrame" style="display:none;flex:1;"></div>
                                <div id="viewDocFallback"
                                    style="display:none;flex:1;flex-direction:column;
                                           align-items:center;justify-content:center;padding:40px;text-align:center;">
                                    <i class="fa fa-file fa-4x text-secondary mb-3 d-block"></i>
                                    <h5 class="text-muted" id="viewDocFallbackName"></h5>
                                    <p class="text-muted mb-4">This file type cannot be previewed in the browser.</p>
                                    <a id="viewDocFallbackDownloadBtn" href="#" class="btn btn-primary">
                                        <i class="fa fa-download me-2"></i>Download File
                                    </a>
                                </div>
                                <div id="viewDocMeta" class="p-2 px-3 bg-light border-top"
                                    style="display:none;font-size:0.8rem;color:#555;">
                                    <i class="fa fa-clock me-1 text-secondary"></i>
                                    <strong>Uploaded At:</strong> <span id="viewDocUploadedAt"></span>
                                    &nbsp;&nbsp;
                                    <i class="fa fa-user me-1 text-secondary"></i>
                                    <strong>Uploaded By:</strong> <span id="viewDocUploadedBy"></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer py-2">
                            <a id="viewDocDownloadBtn" href="#" class="btn btn-success btn-sm"
                                style="display:none;">
                                <i class="fa fa-download me-1"></i>Download
                            </a>
                            <a id="viewDocOpenNewTab" href="#" target="_blank"
                                class="btn btn-outline-primary btn-sm" style="display:none;">
                                <i class="fa fa-external-link-alt me-1"></i>Open in New Tab
                            </a>
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                <i class="fa fa-times me-1"></i>Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN CARD -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <h4 class="card-title">Paid Claim Payments Report</h4>
                        </div>
                        <div class="card-body">

                            @if ($error)
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fa fa-exclamation-triangle me-2"></i>{{ $error }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @elseif(count($records) === 0)
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fa fa-check-circle me-2"></i>No paid records found.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @else
                                @php
                                    $deptNames = [
                                        '11' => 'Fire',
                                        '12' => 'Marine',
                                        '13' => 'Motor',
                                        '14' => 'Miscellaneous',
                                        '16' => 'Health',
                                    ];
                                @endphp

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="paidTable"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th style="width:50px;text-align:center;">SR#</th>
                                                <th style="width:80px;text-align:center;">Action</th>
                                                <th>Claim No</th>
                                                <th>Payee Name</th>
                                                <th>Payee Type</th>
                                                <th>Code</th>
                                                <th>Gross Amount</th>
                                                <th>Voucher No</th>
                                                
                                                <th>Dept</th>
                                                <th>Uploaded At</th>
                                                <th>Uploaded By</th>

                                                <th>_branch_raw</th>
                                                <th>_dept_raw</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($records as $index => $row)
                                                @php
                                                    $fileNames = is_array($row['gl_file_names'])
                                                        ? $row['gl_file_names']
                                                        : [];
                                                    if (empty($fileNames) && !empty($row['gl_file_name'])) {
                                                        $fileNames = [$row['gl_file_name']];
                                                    }
                                                    $fileCount = count($fileNames);
                                                    $branch = $row['location_code'] ?? 'N/A';
                                                    $deptCode = $row['dept'] ?? 'N/A';
                                                    $deptName = $deptNames[$deptCode] ?? 'N/A';
                                                @endphp
                                                <tr id="row-{{ $index }}">

                                                    <td class="serial-number text-center">{{ $index + 1 }}</td>


                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-info btn-view-doc"
                                                            title="View {{ $fileCount }} file(s)"
                                                            data-doc="{{ $row['doc_num'] }}"
                                                            data-filenames="{{ json_encode($fileNames) }}"
                                                            data-uploaded-at="{{ $row['uploaded_at'] ?? '' }}"
                                                            data-uploaded-by="{{ $row['uploaded_by'] ?? '' }}">
                                                            <i class="fa fa-eye"></i>
                                                            @if ($fileCount > 1)
                                                                <span class="badge bg-light text-dark ms-1"
                                                                    style="font-size:0.7rem;">{{ $fileCount }}</span>
                                                            @endif
                                                        </button>
                                                    </td>


                                                    <td><strong>{{ $row['doc_num'] }}</strong></td>


                                                    <td>{{ $row['party_description'] }}</td>


                                                    <td>
                                                        @if (!empty($row['party_type']))
                                                            <span class="badge bg-info">{{ $row['party_type'] }}</span>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>


                                                    <td>{{ $row['code'] }}</td>


                                                    <td class="text-right">{{ number_format($row['amount'], 2) }}</td>


                                                    <td>{{ $row['voucher_no'] ?? 'N/A' }}</td>


                                                  

                                                    <td class="text-center">
                                                        @if ($deptName)
                                                            <span
                                                                class="badge-dept dept-{{ $deptCode }}">{{ $deptName }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>


                                                    <td>
                                                        @if ($row['uploaded_at'])
                                                            {{ \Carbon\Carbon::parse($row['uploaded_at'])->timezone('Asia/Karachi')->format('d-m-Y g:i:s A') }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>


                                                    <td>{{ $row['uploaded_by'] ?? 'N/A' }}</td>


                                                    <td>{{ $branch }}</td>


                                                    <td>{{ $deptCode }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@section('scripts')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <style>
        #paidTable thead th {
            background-color: #4783b5 !important;
            color: #fff !important;
            font-weight: 600;
            white-space: nowrap;
            font-size: 0.85rem;
            text-align: center;
        }

        #paidTable td {
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: #e3f2fd !important;
        }

        .text-right {
            text-align: right !important;
        }

        .badge.bg-info {
            background-color: #17a2b8 !important;
            color: white;
        }


        .badge-branch {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #e8f0fe;
            color: #1a5cb1;
            border: 1px solid #c5d8f8;
        }


        .badge-dept {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-dept.dept-11 {
            background: #fff3e0;
            color: #e65100;
            border: 1px solid #ffcc80;
        }

        .badge-dept.dept-12 {
            background: #e3f2fd;
            color: #0d47a1;
            border: 1px solid #90caf9;
        }

        .badge-dept.dept-13 {
            background: #e8f5e9;
            color: #1b5e20;
            border: 1px solid #a5d6a7;
        }

        .badge-dept.dept-14 {
            background: #f3e5f5;
            color: #6a1b9a;
            border: 1px solid #ce93d8;
        }

        .badge-dept.dept-16 {
            background: #fce4ec;
            color: #880e4f;
            border: 1px solid #f48fb1;
        }


        #paidTable_wrapper .dataTables_filter {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
            flex-wrap: wrap;
        }

        #paidTable_wrapper .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 0;
            font-size: 0.85rem;
            order: 99;
        }

        #paidTable_wrapper .dataTables_filter input {
            height: 34px;
            font-size: 0.82rem;
        }

        #pFilterIcon {
            font-size: 0.78rem;
            color: #4783b5;
            font-weight: 700;
            white-space: nowrap;
        }

        .filter-group {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fff;
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 0 4px 0 8px;
            height: 34px;
            transition: border-color .15s, box-shadow .15s;
        }

        .filter-group:focus-within {
            border-color: #4783b5;
            box-shadow: 0 0 0 2px rgba(71, 131, 181, .18);
        }

        .filter-group.active {
            border-color: #4783b5;
            background: #eef4fb;
        }

        .filter-group-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: #999;
            white-space: nowrap;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            line-height: 1;
        }

        .filter-select {
            border: none;
            background: transparent;
            font-size: 0.82rem;
            color: #333;
            height: 32px;
            padding: 0 2px;
            cursor: pointer;
            min-width: 120px;
            outline: none;
        }

        .filter-group.active .filter-select {
            font-weight: 600;
            color: #1a5a8a;
        }

        .btn-clear-filters {
            display: inline-flex;
            align-items: center;
            height: 34px;
            padding: 0 10px;
            font-size: 0.78rem;
            font-weight: 600;
            border: 1px solid #dc3545;
            border-radius: 6px;
            background: #fff;
            color: #dc3545;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
        }

        .btn-clear-filters:hover {
            background: #dc3545;
            color: #fff;
        }


        .paid-file-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid transparent;
            margin-bottom: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            line-height: 1.35;
            word-break: break-word;
            transition: background .15s, border-color .15s;
            color: #333;
        }

        .paid-file-item:hover {
            background: #e9ecef;
            border-color: #ced4da;
        }

        .paid-file-item.active {
            background: #dbeafe;
            border-color: #93c5fd;
            color: #1d4ed8;
        }

        .paid-file-item i {
            margin-top: 2px;
            flex-shrink: 0;
            font-size: 1rem;
        }

        #viewDocFrame iframe {
            width: 100%;
            height: 68vh;
            border: none;
            display: block;
        }

        #viewDocFrame img {
            max-width: 100%;
            max-height: 68vh;
            margin: auto;
            display: block;
            padding: 20px;
        }


        .btn-excel-custom {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: #fff !important;
            margin: 0 2px !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
            border-radius: 0.25rem !important;
            cursor: pointer !important;
        }

        .btn-print-custom {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #212529 !important;
            margin: 0 2px !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
            border-radius: 0.25rem !important;
            cursor: pointer !important;
        }

        .btn-excel-custom:hover {
            background-color: #218838 !important;
        }

        .btn-print-custom:hover {
            background-color: #e0a800 !important;
        }

        .toast {
            min-width: 300px;
        }
    </style>

   <script>
    var BRANCH_MAP = @json($branchMap ?? []);
    var paidDataTable = null;
    var DEPT_MAP = {
        '11': 'Fire',
        '12': 'Marine',
        '13': 'Motor',
        '14': 'Miscellaneous',
        '16': 'Health'
    };

    @if (!$error && count($records) > 0)
        document.addEventListener('DOMContentLoaded', function() {
            initializeDataTable();
        });
    @endif

    $(document).ready(function() {
        $('[data-bs-dismiss="modal"]').on('click', function() {
            var modalId = $(this).closest('.modal').attr('id');
            if (modalId) {
                $('#' + modalId).modal('hide');
                setTimeout(function() {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('overflow', '');
                }, 300);
            }
        });
    });

    function initializeDataTable() {
        if (typeof jQuery === 'undefined' || typeof $.fn.DataTable === 'undefined') return;
        var $t = $('#paidTable');
        if (!$t.length) return;
        if ($.fn.dataTable.isDataTable($t)) $t.DataTable().destroy();

        paidDataTable = $t.DataTable({
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'All']
            ],
            order: [
                [0, 'asc']
            ],
            responsive: true,
            dom: "<'row'<'col-sm-6'B><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
            language: {
                search: '',
                searchPlaceholder: 'Search in all columns...',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoFiltered: '(filtered from _MAX_ total entries)',
                zeroRecords: 'No matching records found',
            },
            columnDefs: [
                {
                    targets: [0],
                    orderable: false,
                    searchable: false,
                    width: '50px',
                    className: 'text-center'
                },
                {
                    targets: [1],
                    orderable: false,
                    searchable: false,
                    width: '80px',
                    className: 'text-center'
                },
                {
                    targets: [5],
                    className: 'text-right'
                },
                {
                    targets: [11, 12],
                    visible: false,
                    searchable: true
                },
            ],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel me-2"></i>Excel',
                    className: 'btn-excel-custom btn-sm',
                    filename: 'Paid_Claim_Payments_' + new Date().toISOString().slice(0, 10),
                    title: 'Paid Claim Payments Report',
                    exportOptions: {
                        columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print me-2"></i>Print',
                    className: 'btn-print-custom btn-sm',
                    title: 'Paid Claim Payments Report',
                    exportOptions: {
                        columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                    }
                },
            ],
            drawCallback: function(settings) {
                var api = this.api(),
                    start = api.page.info().start;
                api.rows({
                    page: 'current'
                }).every(function(ri, tl, rl) {
                    $(this.node()).find('.serial-number').text(start + rl + 1);
                });
            },
            initComplete: function() {
                var api = this.api();
                api.buttons().container().appendTo('#paidTable_wrapper .col-sm-6:eq(0)');

                var branches = {};
                api.column(11, {
                    search: 'none'
                }).data().each(function(v) {
                    var val = $.trim(v);
                    if (val) branches[val] = true;
                });
                var branchOpts = '<option value="">All Branches</option>';
                Object.keys(branches).sort().forEach(function(c) {
                    var label = BRANCH_MAP[c] ? c + ' - ' + BRANCH_MAP[c] : c;
                    branchOpts += '<option value="' + c + '">' + label + '</option>';
                });

                var depts = {};
                api.column(12, {
                    search: 'none'
                }).data().each(function(v) {
                    var val = $.trim(v);
                    if (val) depts[val] = true;
                });
                var deptOpts = '<option value="">All Departments</option>';
                Object.keys(depts).sort(function(a, b) {
                    return +a - +b;
                }).forEach(function(c) {
                    deptOpts += '<option value="' + c + '">' + (DEPT_MAP[c] || c) + '</option>';
                });

                var html =
                    '<span id="pFilterIcon"><i class="fas fa-filter me-1"></i>Filters:</span>' +
                    '<span class="filter-group" id="pfgBranch">' +
                    '<span class="filter-group-label">Branch</span>' +
                    '<select id="pFilterBranch" class="filter-select">' + branchOpts + '</select>' +
                    '</span>' +
                    '<span class="filter-group" id="pfgDept">' +
                    '<span class="filter-group-label">Dept</span>' +
                    '<select id="pFilterDept" class="filter-select">' + deptOpts + '</select>' +
                    '</span>' +
                    '<button type="button" id="pBtnClearFilters" class="btn-clear-filters" style="display:none;">' +
                    '<i class="fas fa-times me-1"></i>Clear' +
                    '</button>';

                $('#paidTable_wrapper .dataTables_filter').prepend(html);
            },
        });

        $(document).on('change', '#pFilterBranch', applyPaidFilters);
        $(document).on('change', '#pFilterDept', applyPaidFilters);
        $(document).on('click', '#pBtnClearFilters', function() {
            $('#pFilterBranch').val('');
            $('#pFilterDept').val('');
            applyPaidFilters();
        });
    }

    function applyPaidFilters() {
        if (!paidDataTable) return;
        var bVal = $.trim($('#pFilterBranch').val());
        var dVal = $.trim($('#pFilterDept').val());

        paidDataTable
            .column(11).search(bVal ? '^' + $.fn.dataTable.util.escapeRegex(bVal) + '$' : '', true, false)
            .column(12).search(dVal ? '^' + $.fn.dataTable.util.escapeRegex(dVal) + '$' : '', true, false)
            .draw();

        $('#pFilterBranch').closest('.filter-group').toggleClass('active', bVal !== '');
        $('#pFilterDept').closest('.filter-group').toggleClass('active', dVal !== '');
        $('#pBtnClearFilters').toggle(bVal !== '' || dVal !== '');
    }

    var _paidFileList = [];
    var _paidDocNum = '';
    var _paidUploadedAt = '';
    var _paidUploadedBy = '';

    var _extIcons = {
        pdf: 'fa-file-pdf text-danger',
        doc: 'fa-file-word text-primary',
        docx: 'fa-file-word text-primary',
        xls: 'fa-file-excel text-success',
        xlsx: 'fa-file-excel text-success',
        jpg: 'fa-file-image text-warning',
        jpeg: 'fa-file-image text-warning',
        png: 'fa-file-image text-warning',
        gif: 'fa-file-image text-warning',
    };
    var _imgExts = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];

    $(document).on('click', '.btn-view-doc', function() {
        var $btn = $(this);
        _paidDocNum = $btn.data('doc') || '';
        _paidUploadedAt = $btn.data('uploaded-at') || '';
        _paidUploadedBy = $btn.data('uploaded-by') || '';

        var rawNames = $btn.data('filenames');
        try {
            _paidFileList = typeof rawNames === 'string' ?
                JSON.parse(rawNames) :
                (Array.isArray(rawNames) ? rawNames : []);
        } catch (e) {
            _paidFileList = rawNames ? [String(rawNames)] : [];
        }

        if (!_paidFileList.length) {
            showErrorToast('No files uploaded for this document.');
            return;
        }

        $('#viewDocTitle').text(': ' + _paidDocNum);
        $('#viewDocFileCount').text(_paidFileList.length);
        _paidResetPreview(true);

        var $list = $('#viewDocFileList').empty();
        _paidFileList.forEach(function(fileName, idx) {
            var ext = (fileName.split('.').pop() || '').toLowerCase();
            var icon = _extIcons[ext] || 'fa-file text-secondary';
            var $item = $(
                '<div class="paid-file-item" data-idx="' + idx + '">' +
                '<i class="fas ' + icon + ' fa-fw"></i>' +
                '<span>' + _htmlEscape(fileName) + '</span>' +
                '</div>'
            );
            $item.on('click', function() {
                $('.paid-file-item').removeClass('active');
                $(this).addClass('active');
                _paidLoadPreview(_paidDocNum, fileName);
            });
            $list.append($item);
        });

        $('#viewDocModal').modal('show');
        setTimeout(function() {
            $list.find('.paid-file-item:first').trigger('click');
        }, 300);
    });

    function _paidResetPreview(idle) {
        $('#viewDocFrame').hide().empty();
        $('#viewDocFallback').hide();
        $('#viewDocMeta').hide();
        $('#viewDocDownloadBtn').hide().attr('href', '#');
        $('#viewDocOpenNewTab').hide().attr('href', '#');
        if (idle) {
            $('#viewDocSpinner').hide();
            $('#viewDocIdleMsg').show();
        } else {
            $('#viewDocIdleMsg').hide();
            $('#viewDocSpinner').show();
        }
        $('#viewDocLoading').css('display', 'flex');
    }

    function _paidLoadPreview(docNum, fileName) {
        _paidResetPreview(false);
        var fileUrl = '{{ route('paid.serve.file', ['docNum' => ':docNum', 'fileName' => ':fileName']) }}'
            .replace(':docNum', encodeURIComponent(docNum))
            .replace(':fileName', encodeURIComponent(fileName));
        var ext = (fileName.split('.').pop() || '').toLowerCase();

        $('#viewDocOpenNewTab').show().attr('href', fileUrl);
        $('#viewDocDownloadBtn').show().attr('href', fileUrl + '?download=1');

        if (_paidUploadedAt) {
            try {
                $('#viewDocUploadedAt').text(new Date(_paidUploadedAt).toLocaleString());
            } catch (e) {
                $('#viewDocUploadedAt').text(_paidUploadedAt);
            }
            $('#viewDocUploadedBy').text(_paidUploadedBy || 'N/A');
            $('#viewDocMeta').show();
        }

        if (ext === 'pdf') {
            $('#viewDocLoading').hide();
            $('#viewDocFrame').html('<iframe src="' + fileUrl +
                '" style="width:100%;height:68vh;border:none;display:block;"></iframe>').show();
        } else if (_imgExts.indexOf(ext) !== -1) {
            var $img = $('<img />')
                .attr('src', fileUrl)
                .css({
                    maxWidth: '100%',
                    maxHeight: '68vh',
                    margin: 'auto',
                    display: 'block',
                    padding: '20px'
                })
                .on('load', function() {
                    $('#viewDocLoading').hide();
                    $('#viewDocFrame').show();
                })
                .on('error', function() {
                    $('#viewDocLoading').hide();
                    _paidShowFallback(fileName, fileUrl);
                });
            $('#viewDocFrame').empty().append($img);
        } else {
            $('#viewDocLoading').hide();
            _paidShowFallback(fileName, fileUrl);
        }
    }

    function _paidShowFallback(fileName, fileUrl) {
        $('#viewDocFallbackName').text(fileName);
        $('#viewDocFallbackDownloadBtn').attr('href', fileUrl + '?download=1');
        $('#viewDocFallback').css('display', 'flex').show();
    }

    function _htmlEscape(str) {
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    $('#viewDocModal').on('hidden.bs.modal', function() {
        $('#viewDocFrame').empty().hide();
        $('#viewDocFileList').empty();
        $('#viewDocFallback').hide();
        $('#viewDocMeta').hide();
        $('#viewDocDownloadBtn, #viewDocOpenNewTab').hide();
        $('#viewDocSpinner').hide();
        $('#viewDocIdleMsg').show();
        $('#viewDocLoading').css('display', 'flex');
        _paidFileList = [];
        _paidDocNum = '';
        _paidUploadedAt = '';
        _paidUploadedBy = '';

        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '');
    });

    function showSuccessToast(msg) {
        $('#toastMessage').text(msg);
        new bootstrap.Toast(document.getElementById('successToast'), {
            autohide: true,
            delay: 5000
        }).show();
    }

    function showErrorToast(msg) {
        $('#errorToastMessage').text(msg);
        new bootstrap.Toast(document.getElementById('errorToast'), {
            autohide: true,
            delay: 5000
        }).show();
    }
</script>
@endsection
@endsection
