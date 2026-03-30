@extends('AutosecMaster.master')
@section('content')

    <div class="content-body">
        <div class="container-fluid">

            <!-- Preloader -->
            <div id="preloader"
                style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(255,255,255,0.95);z-index:9999;justify-content:center;align-items:center;">
                <div style="text-align:center;background:white;padding:40px;border-radius:15px;box-shadow:0 10px 40px rgba(0,0,0,0.2);max-width:450px;width:90%;border:1px solid #e0e0e0;">
                    <div class="spinner-border text-primary" role="status" style="width:4rem;height:4rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div style="margin-top:25px;">
                        <h5 style="color:#333;font-weight:600;font-size:1.25rem;" id="preloaderTitle">Uploading GL File...</h5>
                        <p style="color:#666;margin-bottom:20px;" id="preloaderMessage">Please wait while the file is being uploaded...</p>
                        <div class="progress mt-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width:100%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Toast -->
            <div class="position-fixed top-0 end-0 p-3" style="z-index:11000;">
                <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body"><i class="fa fa-check-circle me-2"></i><span id="toastMessage"></span></div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>

            <!-- Error Toast -->
            <div class="position-fixed top-0 end-0 p-3" style="z-index:11000;">
                <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body"><i class="fa fa-exclamation-circle me-2"></i><span id="errorToastMessage"></span></div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>

            <!-- File UPLOAD MODAL -->
            <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-light text-white">
                            <h5 class="modal-title" id="uploadModalLabel">
                                <i class="fa fa-file-upload me-2"></i> Upload Claim Payment File
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fa fa-info-circle me-2"></i>Payment Details</h6>
                                </div>
                                <div class="card-body py-2">
                                    <div class="row g-2 small">
                                        <div class="col-6"><strong>Doc No:</strong> <span id="modalDocNum">-</span></div>
                                        <div class="col-6"><strong>Party:</strong> <span id="modalRepName">-</span></div>
                                        <div class="col-6"><strong>Party Type:</strong> <span id="modalPartyType">-</span></div>
                                        <div class="col-6"><strong>Amount:</strong> <span id="modalAmount">-</span></div>
                                        <div class="col-6"><strong>Created Date:</strong> <span id="modalCreatedAt">-</span></div>
                                        <div class="col-6"><strong>Voucher Serial:</strong> <span id="modalVoucherSerial">-</span></div>
                                        <div class="col-6"><strong>Code:</strong> <span id="modalCode">-</span></div>
                                        <div class="col-6"><strong>Location Code:</strong> <span id="modalLocationCode">-</span></div>
                                        <div class="col-6"><strong>Voucher No:</strong> <span id="modalVoucherNo">-</span></div>
                                    </div>
                                </div>
                            </div>

                            <input type="file" id="fileInput" class="d-none"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                            <label for="fileInput" id="dropZone"
                                class="border border-2 rounded text-center p-4 mb-3 d-block w-100"
                                style="cursor:pointer;border-color:#6c757d;border-style:dashed;transition:border-color .2s;">
                                <i class="fas fa-cloud-upload-alt fa-2x text-secondary mb-2 d-block"></i>
                                <p class="mb-1 text-muted">Drag &amp; drop a file here, or <strong>click to browse</strong></p>
                                <p class="small text-muted mb-0">PDF, Word, Excel, Images: max 5 MB</p>
                            </label>

                            <div id="filePreview" class="d-none border rounded p-2 d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i id="fileIcon" class="fas fa-file fa-lg text-secondary"></i>
                                    <div>
                                        <div id="fileName" class="fw-semibold small"></div>
                                        <div id="fileSize" class="text-muted" style="font-size:.75rem;"></div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="clearFile">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="cancelUploadBtn">
                                <i class="fa fa-times me-1"></i>Cancel
                            </button>
                            <button type="button" class="btn btn-primary" id="btnUpload" disabled>
                                <i class="fa fa-upload me-1"></i>Upload
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VIEW DOCUMENT MODAL -->
            <div class="modal fade" id="viewDocModal" tabindex="-1" role="dialog" aria-labelledby="viewDocModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-light text-white">
                            <h5 class="modal-title" id="viewDocModalLabel">
                                <i class="fa fa-eye me-2"></i>View Document
                                <small class="ms-2 fw-normal" id="viewDocTitle" style="font-size:0.85rem;opacity:0.85;"></small>
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0" style="min-height:500px;">
                            <div id="viewDocLoading" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;"></div>
                                <p class="mt-3 text-muted">Loading document...</p>
                            </div>
                            <div id="viewDocFrame" style="display:none;width:100%;"></div>
                            <div id="viewDocFallback" class="text-center py-5" style="display:none;">
                                <i class="fa fa-file fa-4x text-secondary mb-3 d-block"></i>
                                <h5 class="text-muted" id="viewDocFallbackName"></h5>
                                <p class="text-muted mb-4">This file type cannot be previewed in the browser.</p>
                                <a id="viewDocFallbackDownloadBtn" href="#" class="btn btn-primary">
                                    <i class="fa fa-download me-2"></i>Download File
                                </a>
                            </div>
                            <div id="viewDocMeta" class="p-3 bg-light border-top" style="display:none;">
                                <div class="row small">
                                    <div class="col-md-6"><strong>Uploaded At:</strong> <span id="viewDocUploadedAt"></span></div>
                                    <div class="col-md-6"><strong>Uploaded By:</strong> <span id="viewDocUploadedBy"></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a id="viewDocDownloadBtn" href="#" class="btn btn-success" style="display:none;">
                                <i class="fa fa-download me-1"></i>Download
                            </a>
                            <a id="viewDocOpenNewTab" href="#" target="_blank" class="btn btn-outline-primary" style="display:none;">
                                <i class="fa fa-external-link-alt me-1"></i>Open in New Tab
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fa fa-times me-1"></i>Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN CARD WHERE DATATABLE IS SHOWING -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <h4 class="card-title">
                                Claim Payments
                            </h4>
                        </div>
                        <div class="card-body">

                            @if($apiError)
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fa fa-exclamation-triangle me-2"></i>{{ $apiError }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>

                            @elseif(count($records) === 0)
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fa fa-check-circle me-2"></i>
                                    All records have been processed. No pending GL uploads.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>

                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="glTable" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th style="width:50px;text-align:center;">SR#</th>
                                                <th style="width:110px;text-align:center;">Action</th>
                                                <th>Created Date</th>
                                                <th>Doc No</th>
                                                <th>Code</th>
                                                <th>Party</th>
                                                <th>Party Type</th>
                                                <th>Amount</th>
                                                <th>Voucher Serial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($records as $index => $row)
                                            <tr id="row-{{ $index }}" data-gl-doc-id="{{ $row['gl_doc_id'] ?? '' }}">
                                                <td class="serial-number text-center">{{ $index + 1 }}</td>
                                                <td class="text-center" style="white-space:nowrap;">
                                                    <button type="button"
                                                        class="btn btn-sm btn-primary btn-upload"
                                                        title="Upload Claim Payment"
                                                        data-id="{{ $index }}"
                                                        data-gl-doc-id="{{ $row['gl_doc_id'] ?? '' }}"
                                                        data-doc="{{ $row['LVD_VCDTNARRATION1'] ?? 'N/A' }}"
                                                        data-repname="{{ $row['PARTY_DESCRIPTION'] ?? 'N/A' }}"
                                                        data-amount="{{ $row['LSB_SDTLCRAMTFC'] ?? 0 }}"
                                                        data-amount-formatted="{{ number_format((float)($row['LSB_SDTLCRAMTFC'] ?? 0), 2) }}"
                                                        data-code="{{ $row['PSA_SACTACCOUNT'] ?? '' }}"
                                                        data-party-type="{{ $row['PARTY_TYPE'] ?? '' }}"
                                                        data-created-at="{{ $row['CREATED_AT'] ?? '' }}"
                                                        data-voucher-serial="{{ $row['LVD_VCDTVOUCHSR'] ?? '' }}"
                                                        data-location-code="{{ $row['PLC_LOCACODE'] ?? '' }}"
                                                        data-voucher-no="{{ $row['LVH_VCHDNO'] ?? '' }}">
                                                        <i class="fa fa-upload"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-info btn-view-doc ms-1"
                                                        title="View Uploaded Document"
                                                        data-doc="{{ $row['LVD_VCDTNARRATION1'] ?? '' }}"
                                                        data-voucher-serial="{{ $row['LVD_VCDTVOUCHSR'] ?? '' }}"
                                                        data-amount="{{ $row['LSB_SDTLCRAMTFC'] ?? 0 }}"
                                                        data-code="{{ $row['PSA_SACTACCOUNT'] ?? '' }}"
                                                        data-party-type="{{ $row['PARTY_TYPE'] ?? '' }}">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </td>
                                                <td>{{ $row['CREATED_AT'] ?? 'N/A' }}</td>
                                                <td><strong>{{ $row['LVD_VCDTNARRATION1'] ?? 'N/A' }}</strong></td>
                                                <td>{{ $row['PSA_SACTACCOUNT'] ?? 'N/A' }}</td>
                                                <td>{{ $row['PARTY_DESCRIPTION'] ?? 'N/A' }}</td>
                                                <td>
                                                    @if(!empty($row['PARTY_TYPE']))
                                                        <span class="badge bg-info">{{ $row['PARTY_TYPE'] }}</span>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td class="text-right">{{ number_format((float)($row['LSB_SDTLCRAMTFC'] ?? 0), 2) }}</td>
                                                <td>{{ $row['LVD_VCDTVOUCHSR'] ?? 'N/A' }}</td>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <style>
        #preloader 
        { 
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background-color:rgba(255,255,255,0.95);
            z-index:9999;
            display:flex;
            justify-content:center;
            align-items:center; 
        }
        #glTable 
        { 
            border-collapse:collapse !important; 
        }
        #glTable thead th 
        { 
            background-color:#4783b5 !important;
            color:#fff !important;
            font-weight:600;
            white-space:nowrap;
            font-size:0.85rem;
            text-align:center;
        }
        #glTable td  
        { 
            font-size:0.85rem;
            vertical-align:middle; 
        }
        .table-hover tbody tr:hover {
            background-color: #e3f2fd !important; 
        }
        .text-right  
        { 
            text-align:right !important; 
        }
        .dataTables_filter input 
        { 
            border:1px solid #ced4da;
            border-radius:4px;
            padding:6px 12px;
            background-color:white; 
        }
        .dt-buttons .btn 
        { 
            margin-left:5px;
            padding:6px 12px;
            font-size:14px; 
        }
        .toast
        { 
            min-width:300px;
            box-shadow:0 4px 12px rgba(0,0,0,0.15); 
        }
        .toast-body 
        { 
            font-size:0.95rem;
            padding:12px; 
        }
        #dropZone:hover 
        { 
            border-color:#0d6efd !important; 
        }
        #viewDocFrame iframe 
        { 
            width:100%;
            height:72vh;
            border:none;
            display:block;
        }
        #viewDocFrame img   
        { 
            max-width:100%;
            max-height:72vh;
            margin:auto;
            display:block;
            padding:20px; 
        }
        .btn-excel-custom {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: #ffffff !important;
            margin: 0 2px !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            border-radius: 0.25rem !important;
            display: inline-block !important;
            font-weight: 400 !important;
            text-align: center !important;
            vertical-align: middle !important;
            border: 1px solid transparent !important;
            cursor: pointer !important;
        }
        .btn-print-custom {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #212529 !important;
            margin: 0 2px !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            border-radius: 0.25rem !important;
            display: inline-block !important;
            font-weight: 400 !important;
            text-align: center !important;
            vertical-align: middle !important;
            border: 1px solid transparent !important;
            cursor: pointer !important;
        }
        .btn-excel-custom:hover {
            background-color: #218838 !important;
            border-color: #1e7e34 !important;
        }
        .btn-print-custom:hover {
            background-color: #e0a800 !important;
            border-color: #d39e00 !important;
        }
        .payment-progress-badge {
            background-color: #17a2b8;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            margin-left: 5px;
        }
        .badge.bg-info {
            background-color: #17a2b8 !important;
            color: white;
            padding: 4px 8px;
            font-weight: 500;
        }
    </style>

    <script>
        var glDataTable = null;
        var currentPaymentData = {};

        @if(!$apiError && count($records) > 0)
        document.addEventListener('DOMContentLoaded', function () { initializeDataTable(); });
        @endif

        // DataTable Initialization
        function initializeDataTable() {
            try {
                if (typeof jQuery === 'undefined' || typeof $.fn.DataTable === 'undefined') return;
                var table = $('#glTable');
                if (!table.length) return;
                if ($.fn.dataTable.isDataTable(table)) table.DataTable().destroy();

                glDataTable = table.DataTable({
                    pageLength: 25,
                    lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'All']],
                    order: [[0,'asc']],
                    responsive: true,
                    dom: "<'row'<'col-sm-6'B><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
                    language: {
                        search: '', searchPlaceholder: 'Search in all columns...',
                        lengthMenu: 'Show _MENU_ entries',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                        infoEmpty: 'Showing 0 to 0 of 0 entries',
                        infoFiltered: '(filtered from _MAX_ total entries)',
                        zeroRecords: 'No matching records found',
                        paginate: { first:'First', last:'Last', next:'Next', previous:'Previous' }
                    },
                    columnDefs: [
                        { targets: [0], orderable: false, searchable: false, width: '50px', className: 'text-center' },
                        { targets: [1], orderable: false, searchable: false, width: '110px', className: 'text-center' },
                        { targets: [7], className: 'text-right' } // Amount column (index 7)
                    ],
                    buttons: [
                        {
                            extend: 'excel',
                            text: '<i class="fa fa-file-excel me-2"></i> Excel',
                            className: 'btn-excel-custom btn-sm',
                            filename: 'Claim Payments',
                            title: 'Atlas Insurance - Claim Payments',
                            exportOptions: { columns: [0,2,3,4,5,6,7] } // Include all data columns
                        },
                        {
                            extend: 'print',
                            text: '<i class="fa fa-print me-2"></i> Print',
                            className: 'btn-print-custom btn-sm',
                            title: 'Claim Payments',
                            exportOptions: { columns: [0,2,3,4,5,6,7] }
                        }
                    ],
                    drawCallback: function (settings) {
                        var api = this.api(), start = api.page.info().start;
                        api.rows({ page:'current' }).every(function (ri, tl, rl) {
                            $(this.node()).find('.serial-number').text(start + rl + 1);
                        });
                    },
                    initComplete: function () {
                        this.api().buttons().container().appendTo('#glTable_wrapper .col-sm-6:eq(0)');
                    }
                });
            } catch (e) { console.error('DataTable init error:', e); }
        }

        // Upload Modal - Click Handler
        $(document).on('click', '.btn-upload', function () {
            var $btn = $(this);
            
            currentPaymentData = {
                id: $btn.data('id'),
                gl_doc_id: $btn.data('gl-doc-id'),
                doc_num: $btn.data('doc'),
                repname: $btn.data('repname'),
                amount: $btn.data('amount'),
                amount_formatted: $btn.data('amount-formatted'),
                code: $btn.data('code'),
                party_type: $btn.data('party-type'),
                created_at: $btn.data('created-at'),
                voucher_serial: $btn.data('voucher-serial'),
                location_code: $btn.data('location-code'),
                voucher_no: $btn.data('voucher-no')
            };

            // Update modal display
            $('#modalDocNum').text(currentPaymentData.doc_num || '-');
            $('#modalRepName').text(currentPaymentData.repname || '-');
            $('#modalPartyType').text(currentPaymentData.party_type || '-');
            $('#modalAmount').text(currentPaymentData.amount_formatted || '-');
            $('#modalCreatedAt').text(currentPaymentData.created_at || '-');
            $('#modalVoucherSerial').text(currentPaymentData.voucher_serial || '-');
            $('#modalCode').text(currentPaymentData.code || '-');
            $('#modalLocationCode').text(currentPaymentData.location_code || '-');
            $('#modalVoucherNo').text(currentPaymentData.voucher_no || '-');

            // Ensure file input is completely reset
            resetFile();
            $('#uploadModal').modal('show');
        });

        // Cancel Upload Button
        $('#cancelUploadBtn').on('click', function () { 
            resetFile();
            $('#uploadModal').modal('hide'); 
        });

        // Handle modal hidden event to ensure cleanup
        $('#uploadModal').on('hidden.bs.modal', function () {
            resetFile();
            currentPaymentData = {};
        });

        // Clear file button
        $('#clearFile').on('click', function() {
            resetFile();
        });

        // View Document Modal 
        $(document).on('click', '.btn-view-doc', function () {
            var $btn = $(this);
            var docNum = $btn.data('doc');
            var voucherSerial = $btn.data('voucher-serial');
            var amount = $btn.data('amount');
            var code = $btn.data('code');
            var partyType = $btn.data('party-type');

            if (!docNum) { 
                showErrorToast('No document number found.'); 
                return; 
            }

            $('#viewDocTitle').text(': ' + docNum + ' (Serial: ' + voucherSerial + ')');
            $('#viewDocLoading').show();
            $('#viewDocFrame').hide().empty();
            $('#viewDocFallback').hide();
            $('#viewDocMeta').hide();
            $('#viewDocDownloadBtn').hide().attr('href', '#');
            $('#viewDocOpenNewTab').hide().attr('href', '#');
            $('#viewDocModal').modal('show');

            $.ajax({
                url: '{{ route("gl.upload.doc.info") }}',
                method: 'GET',
                data: { 
                    doc_num: docNum,
                    voucher_serial: voucherSerial,
                    amount: amount,
                    code: code,
                    party_type: partyType
                },
                success: function (res) {
                    $('#viewDocLoading').hide();
                    
                    if (!res.success || !res.file_name) {
                        $('#viewDocFallbackName').text(res.message || 'No document found');
                        $('#viewDocFallback').show();
                        return;
                    }

                    var fileName = res.file_name;
                    var fileUrl  = res.file_url;
                    var ext      = fileName.split('.').pop().toLowerCase();
                    var imgExts  = ['jpg','jpeg','png','gif','bmp','webp'];

                    $('#viewDocTitle').text(': ' + fileName);
                    $('#viewDocOpenNewTab').show().attr('href', fileUrl);
                    $('#viewDocDownloadBtn').show().attr('href', fileUrl + '?download=1');
                    
                    // Show metadata if available
                    if (res.uploaded_at) {
                        $('#viewDocUploadedAt').text(new Date(res.uploaded_at).toLocaleString());
                        $('#viewDocUploadedBy').text(res.uploaded_by || 'N/A');
                        $('#viewDocMeta').show();
                    }

                    if (ext === 'pdf') {
                        $('#viewDocFrame').html('<iframe src="' + fileUrl + '"></iframe>').show();
                    } else if (imgExts.indexOf(ext) !== -1) {
                        $('#viewDocFrame').html('<img src="' + fileUrl + '" style="max-width:100%;max-height:72vh;margin:auto;display:block;padding:20px;" />').show();
                    } else {
                        $('#viewDocFallbackName').text(fileName);
                        $('#viewDocFallbackDownloadBtn').attr('href', fileUrl + '?download=1');
                        $('#viewDocFallback').show();
                    }
                },
                error: function (xhr) {
                    $('#viewDocLoading').hide();
                    var msg = 'Failed to load document.';
                    try { msg = xhr.responseJSON.message || msg; } catch (e2) {}
                    $('#viewDocFallbackName').text(msg);
                    $('#viewDocFallback').show();
                }
            });
        });

        // Handle view modal hidden event for cleanup
        $('#viewDocModal').on('hidden.bs.modal', function () {
            $('#viewDocFrame').empty().hide();
            $('#viewDocLoading').show();
            $('#viewDocFallback').hide();
            $('#viewDocMeta').hide();
            $('#viewDocDownloadBtn').hide();
            $('#viewDocOpenNewTab').hide();
        });

        // File Drop Zone Handlers
        var $zone = $('#dropZone');
        $zone.on('dragover', function (e) { 
            e.preventDefault(); 
            $zone.css('border-color', '#0d6efd'); 
        });
        $zone.on('dragleave', function (e) { 
            e.preventDefault(); 
            $zone.css('border-color', '#6c757d'); 
        });
        $zone.on('drop', function (e) {
            e.preventDefault(); 
            $zone.css('border-color', '#6c757d');
            var file = e.originalEvent.dataTransfer.files[0];
            if (file) setFile(file);
        });
        
        $('#fileInput').on('change', function () { 
            if (this.files.length) setFile(this.files[0]); 
        });
        
        $('#clearFile').on('click', resetFile);

        // Set File Function
        function setFile(file) {
            var maxSize = 5 * 1024 * 1024; // 5MB

            if (file.size > maxSize) {
                showErrorToast("File size must be less than 5 MB.");
                resetFile();
                return;
            }

            var extIcons = {
                pdf: 'fa-file-pdf text-danger',
                doc: 'fa-file-word text-primary',   
                docx: 'fa-file-word text-primary',
                xls: 'fa-file-excel text-success',  
                xlsx: 'fa-file-excel text-success',
                jpg: 'fa-file-image text-warning',  
                jpeg: 'fa-file-image text-warning',
                png: 'fa-file-image text-warning'
            };

            var ext = file.name.split('.').pop().toLowerCase();
            var size = file.size < 1048576
                ? (file.size / 1024).toFixed(1) + ' KB'
                : (file.size / 1048576).toFixed(1) + ' MB';

            $('#fileIcon').attr('class', 'fas ' + (extIcons[ext] || 'fa-file text-secondary') + ' fa-lg');
            $('#fileName').text(file.name);
            $('#fileSize').text(size);
            $('#filePreview').removeClass('d-none');
            $('#btnUpload').prop('disabled', false);

            // Properly set the file
            var dt = new DataTransfer();
            dt.items.add(file);
            $('#fileInput')[0].files = dt.files;
        }

        // Reset File Function - improved
        function resetFile() {
            // Clear the file input
            $('#fileInput').val('');
            
            // Reset the file input's files using DataTransfer
            var dt = new DataTransfer();
            $('#fileInput')[0].files = dt.files;
            
            // Hide preview and disable upload button
            $('#filePreview').addClass('d-none');
            $('#btnUpload').prop('disabled', true);
            
            // Reset file info
            $('#fileName').text('');
            $('#fileSize').text('');
            $('#fileIcon').attr('class', 'fas fa-file fa-lg text-secondary');
        }

        // Upload Button Click Handler
        $('#btnUpload').on('click', function () {
            var file = $('#fileInput')[0].files[0];
            if (!file || !currentPaymentData.doc_num) return;

            // Update preloader message
            $('#preloaderTitle').text('Uploading Payment...');
            $('#preloaderMessage').text('Please wait while payment ' + currentPaymentData.voucher_serial + ' is being uploaded...');

            var fd = new FormData();
            fd.append('gl_file', file);
            fd.append('doc_num', currentPaymentData.doc_num);
            fd.append('gl_doc_id', currentPaymentData.gl_doc_id);
            fd.append('voucher_serial', currentPaymentData.voucher_serial);
            fd.append('amount', currentPaymentData.amount);
            fd.append('code', currentPaymentData.code);
            fd.append('party_type', currentPaymentData.party_type);
            fd.append('_token', '{{ csrf_token() }}');

            $('#uploadModal').modal('hide');
            document.getElementById('preloader').style.display = 'flex';
            document.body.style.overflow = 'hidden';

            $.ajax({
                url: '{{ url("claim-payment") }}/' + encodeURIComponent(currentPaymentData.doc_num),
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                success: function (res) {
                    document.getElementById('preloader').style.display = 'none';
                    document.body.style.overflow = 'auto';
                    
                    if (res.success) {
                        // Remove the uploaded row
                        var rowNode = document.getElementById('row-' + currentPaymentData.id);
                        if (rowNode && glDataTable) { 
                            glDataTable.row(rowNode).remove().draw(false); 
                        }
                        
                        showSuccessToast(res.message);
                        
                        // If fully paid, show special message
                        if (res.fully_paid) {
                            setTimeout(function() {
                                showSuccessToast(' Document fully paid! All payments completed.');
                            }, 1000);
                        }
                    } else {
                        showErrorToast(res.message || 'Upload failed.');
                    }
                    
                    // Reset current payment data
                    currentPaymentData = {};
                },
                error: function (xhr) {
                    document.getElementById('preloader').style.display = 'none';
                    document.body.style.overflow = 'auto';
                    var msg = 'Upload failed.';
                    try { 
                        msg = xhr.responseJSON.message || msg; 
                    } catch (e) {}
                    showErrorToast(msg);
                    
                    // Reset current payment data
                    currentPaymentData = {};
                }
            });
        });

        // Toast Functions
        function showSuccessToast(msg) {
            $('#toastMessage').text(msg);
            var toast = new bootstrap.Toast(document.getElementById('successToast'), { 
                autohide: true, 
                delay: 5000 
            });
            toast.show();
        }
        
        function showErrorToast(msg) {
            $('#errorToastMessage').text(msg);
            var toast = new bootstrap.Toast(document.getElementById('errorToast'), { 
                autohide: true, 
                delay: 5000 
            });
            toast.show();
        }
    </script>
@endsection
@endsection