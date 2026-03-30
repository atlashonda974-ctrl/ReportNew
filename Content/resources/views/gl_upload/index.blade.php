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
                        <h5 style="color:#333;font-weight:600;font-size:1.25rem;" id="preloaderTitle">Uploading...</h5>
                        <p style="color:#666;margin-bottom:20px;" id="preloaderMessage">Please wait...</p>
                        <div class="progress mt-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width:100%"></div>
                        </div>
                        <p class="mt-2 text-muted small" id="preloaderProgress"></p>
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

            <!-- FILE UPLOAD MODAL -->
            <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title" id="uploadModalLabel">
                                <i class="fa fa-file-upload  text-primary me-2" ></i>
                                <span id="modalTitleText"> Upload Claim Payment File</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="singlePaymentDetails" class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fa fa-info-circle me-2"></i>Payment Details</h6>
                                </div>
                                <div class="card-body py-2">
                                    <div class="row g-2 small">
                                        <div class="col-6"><strong>Claim No:</strong> <span id="modalDocNum">-</span></div>
                                        <div class="col-6"><strong>Payee Name:</strong> <span id="modalRepName">-</span></div>
                                        <div class="col-6"><strong>Payee Type:</strong> <span id="modalPartyType">-</span></div>
                                        <div class="col-6"><strong>Gross Amount:</strong> <span id="modalAmount">-</span></div>
                                        <div class="col-6"><strong>Created Date:</strong> <span id="modalCreatedAt">-</span></div>
                                       
                                        <div class="col-6"><strong>Code:</strong> <span id="modalCode">-</span></div>
                                        <div class="col-6"><strong>Branch:</strong> <span id="modalLocationCode">-</span></div>
                                        <div class="col-6"><strong>Voucher No:</strong> <span id="modalVoucherNo">-</span></div>
                                    </div>
                                </div>
                            </div>
                            <div id="bulkPaymentDetails" style="display:none;">
                                <div class="card mb-2">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fa fa-layer-group me-2"></i>
                                            Bulk Upload:
                                            <span id="bulkDocCount" class="badge bg-warning textwhite ms-1">0</span> document(s) selected
                                        </h6>
                                    </div>
                                    <div class="card-body py-2">
                                        <div id="bulkDocList" style="max-height:140px;overflow-y:auto;font-size:0.82rem;"></div>
                                    </div>
                                </div>
                                <div class="alert alert-primary text-dark py-2 small mb-3">
                                    <i class="fa fa-exclamation-triangle me-1"></i>
                                    The uploaded file(s) will mark <strong>all payments</strong> for the selected document(s) as done.
                                </div>
                            </div>
                            <input type="file" id="fileInput" class="d-none"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple>
                            <label for="fileInput" id="dropZone"
                                class="border border-2 rounded text-center p-4 mb-3 d-block w-100"
                                style="cursor:pointer;border-color:#6c757d;border-style:dashed;transition:border-color .2s;">
                                <i class="fas fa-cloud-upload-alt fa-2x text-secondary mb-2 d-block"></i>
                                <p class="mb-1 text-muted">Drag &amp; drop files here, or <strong>click to browse</strong></p>
                                <p class="small text-muted mb-0">PDF, Word, Excel,JPG,JPEG,PNG &mdash; max 5 MB each</p>
                            </label>
                            <div id="fileListWrapper" style="display:none;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-semibold small text-muted">
                                        <i class="fas fa-paperclip me-1"></i>Attached Files:
                                        <span id="fileCountBadge" class="badge bg-warning text-dark ms-1">0</span>
                                    </span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAttachMore"
                                        style="font-size:0.78rem;padding:3px 12px;">
                                        <i class="fas fa-plus me-1"></i>Attach More
                                    </button>
                                </div>
                                <div id="fileList" class="border rounded p-2"
                                    style="background:#f8f9fa;max-height:180px;overflow-y:auto;"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelUploadBtn">
                                <i class="fa fa-times me-1"></i>Cancel
                            </button>
                            <button type="button" class="btn btn-primary" id="btnUpload" disabled>
                                <i class="fa fa-upload me-1"></i>Upload
                                <span id="btnUploadCount" class="badge bg-light text-primary ms-1" style="display:none;"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VIEW DOCUMENT MODAL -->
            <div class="modal fade" id="viewDocModal" tabindex="-1" role="dialog" aria-labelledby="viewDocModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title" id="viewDocModalLabel">
                                <i class="fa fa-eye  text-primary me-2"></i> View Document
                                <small class="ms-2 fw-normal" id="viewDocTitle" style="font-size:0.85rem;opacity:0.85;"></small>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

            <!-- MAIN TABLE CARD -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0 pb-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h4 class="card-title mb-0">Claim Payments</h4>
                            <!-- Bulk toolbar -->
                            <div id="bulkToolbar" style="display:none;">
                                <span class="me-2 text-muted small fw-semibold">
                                    <i class="fa fa-check-square me-1 text-primary"></i>
                                    <span id="bulkSelectedCount">0</span> row(s) selected
                                </span>
                                <button type="button" class="btn btn-sm btn-success me-1" id="btnBulkUpload">
                                    <i class="fa fa-upload me-1"></i>Upload for Selected
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearSelection">
                                    <i class="fa fa-times me-1"></i>Clear
                                </button>
                            </div>
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
                                                <th style="width:40px;text-align:center;">
                                                    <input type="checkbox" id="checkAll" title="Select all visible rows"
                                                        style="cursor:pointer;width:16px;height:16px;">
                                                </th>
                                                <th style="width:50px;text-align:center;">SR#</th>
                                                <th style="width:110px;text-align:center;">Action</th>
                                                <th>Created Date</th>
                                                <th>Claim No</th>
                                                <th>Voucher No</th>
                                                <th>Code</th>
                                                <th>Payee Name</th>
                                                <th>Payee Type</th>
                                                <th>Gross Amount</th>
                                                <th>Branch</th>
                                                <th>Department</th>
                                               
                                                <th>_branch_raw</th>
                                                <th>_dept_raw</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $deptNames = [
                                                    '11' => 'Fire',
                                                    '12' => 'Marine',
                                                    '13' => 'Motor',
                                                    '14' => 'Miscellaneous',
                                                    '16' => 'Health',
                                                ];
                                            @endphp
                                            @foreach($records as $index => $row)
                                            @php
                                                $deptCode = $row['DEPT'] ?? '';
                                                $deptName = $deptNames[$deptCode] ?? $deptCode;
                                                $branch   = $row['PLC_LOCACODE'] ?? '';
                                            @endphp
                                            <tr id="row-{{ $index }}"
                                                data-doc-num="{{ $row['LVD_VCDTNARRATION1'] ?? '' }}"
                                                data-gl-doc-id="{{ $row['gl_doc_id'] ?? '' }}"
                                                data-file-name="{{ $row['file_name'] ?? '' }}">

                                                <td class="text-center" style="vertical-align:middle;">
                                                    <input type="checkbox" class="row-check"
                                                        style="cursor:pointer;width:16px;height:16px;"
                                                        data-index="{{ $index }}"
                                                        data-doc-num="{{ $row['LVD_VCDTNARRATION1'] ?? '' }}"
                                                        data-repname="{{ $row['PARTY_DESCRIPTION'] ?? '' }}"
                                                        data-amount-formatted="{{ number_format((float)($row['LSB_SDTLCRAMTFC'] ?? 0), 2) }}"
                                                        data-voucher-serial="{{ $row['LVD_VCDTVOUCHSR'] ?? '' }}">
                                                </td>

                                                <td class="serial-number text-center">{{ $index + 1 }}</td>

                                                <td class="text-center" style="white-space:nowrap;">
                                                    <button type="button"
                                                        class="btn btn-sm btn-primary btn-upload"
                                                        title="Upload file for this payment only"
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
                                                        data-location-code="{{ $branch }}"
                                                        data-voucher-no="{{ $row['LVH_VCHDNO'] ?? '' }}">
                                                        <i class="fa fa-upload"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-info btn-view-doc ms-1"
                                                        title="View Document"
                                                        data-doc="{{ $row['LVD_VCDTNARRATION1'] ?? '' }}">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </td>

                                                <td>{{ $row['CREATED_AT'] ?? 'N/A' }}</td>
                                                <td><strong>{{ $row['LVD_VCDTNARRATION1'] ?? 'N/A' }}</strong></td>
                                                <td>{{ $row['LVH_VCHDNO'] ?? 'N/A' }}</td>
                                                <td>{{ $row['PSA_SACTACCOUNT'] ?? 'N/A' }}</td>
                                                <td>{{ $row['PARTY_DESCRIPTION'] ?? 'N/A' }}</td>
                                                <td>
                                                    @if(!empty($row['PARTY_TYPE']))
                                                        <span class="badge bg-info">{{ $row['PARTY_TYPE'] }}</span>
                                                    @else N/A @endif
                                                </td>
                                                <td class="text-right">{{ number_format((float)($row['LSB_SDTLCRAMTFC'] ?? 0), 2) }}</td>
                                               
                                                <td class="text-center">
                                                    @if($branch)
                                                        <span class="badge-branch" >{{ $branch }}</span>
                                                    @else <span class="text-muted">-</span> @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($deptName)
                                                        <span class="badge-dept dept-{{ $deptCode }}">{{ $deptName }}</span>
                                                    @else <span class="text-muted">-</span> @endif
                                                </td>
                                             
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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <style>
        
     #glTable thead th {
    background-color: #4783b5 !important;
    color: #fff !important;
    font-weight: 600;
    white-space: nowrap;
    font-size: 0.85rem;
    text-align: center;
}

#glTable td {
    font-size: 0.85rem;
    vertical-align: middle;
}

#glTable tbody tr.row-selected {
    background-color: #d0e8ff !important;
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
    letter-spacing: 0.3px;
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

#glTable_wrapper .dataTables_filter {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
    flex-wrap: wrap;
}

#glTable_wrapper .dataTables_filter label {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 0;
    font-size: 0.85rem;
    order: 99;
}

#glTable_wrapper .dataTables_filter input {
    height: 34px;
    font-size: 0.82rem;
}

#filterIcon {
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
    box-shadow: 0 0 0 2px rgba(71,131,181,.18);
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

#dropZone:hover {
    border-color: #0d6efd !important;
}

.file-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 8px;
    border-radius: 6px;
    background: #fff;
    border: 1px solid #dee2e6;
    margin-bottom: 5px;
    font-size: 0.82rem;
}

.file-item:last-child {
    margin-bottom: 0;
}

.file-item .file-item-left {
    display: flex;
    align-items: center;
    gap: 8px;
    overflow: hidden;
}

.file-item .file-item-name {
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 260px;
}

.file-item .file-item-size {
    color: #888;
    font-size: 0.75rem;
    white-space: nowrap;
}

.file-item .btn-remove-file {
    border: none;
    background: none;
    color: #dc3545;
    cursor: pointer;
    padding: 0 4px;
    font-size: 0.85rem;
    flex-shrink: 0;
}

.bulk-doc-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 4px 6px;
    border-bottom: 1px solid #e9ecef;
    font-size: 0.82rem;
}

.bulk-doc-item:last-child {
    border-bottom: none;
}

.bulk-doc-item .doc-num {
    font-weight: 600;
    color: #0d6efd;
}

.bulk-doc-item .doc-party {
    color: #555;
    flex: 1;
    padding: 0 8px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.bulk-doc-item .doc-amount {
    color: #198754;
    font-weight: 500;
    white-space: nowrap;
}

#viewDocFrame iframe {
    width: 100%;
    height: 70vh;
    border: none;
    display: block;
}

#viewDocFrame img {
    max-width: 100%;
    max-height: 70vh;
    margin: auto;
    display: block;
    padding: 20px;
}

#bulkToolbar {
    animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-4px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.toast {
    min-width: 300px;
}
    </style>

    <script>
        var glDataTable        = null;
        var currentPaymentData = {};
        var selectedDocNums    = [];
        var attachedFiles      = [];
        var isBulkMode         = false;
        var isUploading        = false;

        var DEPT_MAP = { '11':'Fire','12':'Marine','13':'Motor','14':'Miscellaneous','16':'Health' };

        @if(!$apiError && count($records) > 0)
        document.addEventListener('DOMContentLoaded', function () { initializeDataTable(); });
        @endif

        $(document).ready(function () {
            $('[data-bs-dismiss="modal"]').on('click', function () {
                var mid = $(this).closest('.modal').attr('id');
                if (mid) { $('#' + mid).modal('hide'); }
            });
        });

// tablke
        function initializeDataTable() {
            if (!window.jQuery || !$.fn.DataTable) return;
            var $t = $('#glTable');
            if (!$t.length) return;
            if ($.fn.dataTable.isDataTable($t)) $t.DataTable().destroy();

            glDataTable = $t.DataTable({
                pageLength: 25,
                lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'All']],
                order: [[1,'asc']],
                responsive: true,
                dom: "<'row'<'col-sm-6'B><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
                language: {
                    search: '', searchPlaceholder: 'Search in all columns...',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoFiltered: '(filtered from _MAX_ total entries)',
                    zeroRecords: 'No matching records found',
                },
                columnDefs: [
                    { targets: [0],      orderable: false, searchable: false, width: '40px',  className: 'text-center' },
                    { targets: [1],      orderable: false, searchable: false, width: '50px',  className: 'text-center' },
                    { targets: [2],      orderable: false, searchable: false, width: '110px', className: 'text-center' },
                    { targets: [9],      className: 'text-right' },
                    
                    { targets: [10, 12, 13], visible: false, searchable: true },
                ],
                buttons: [
                    {
                        extend: 'excel', text: '<i class="fa fa-file-excel me-2"></i>Excel',
                        className: 'btn-excel-custom btn-sm', filename: 'Claim Payments',
                        exportOptions: { columns: [1,3,4,5,6,7,8,9,10,11] }
                    },
                    {
                        extend: 'print', text: '<i class="fa fa-print me-2"></i>Print',
                        className: 'btn-print-custom btn-sm',
                        exportOptions: { columns: [1,3,4,5,6,7,8,9,10,11] }
                    },
                ],
                                drawCallback: function (settings) {
                    var api = this.api(), start = api.page.info().start;
                    api.rows({ page: 'current' }).nodes().each(function (node, i) {
                        $(node).find('.serial-number').text(start + i + 1);
                    });
                    syncCheckboxUI();
                },
                initComplete: function () {
                    var api = this.api();
                    api.buttons().container().appendTo('#glTable_wrapper .col-sm-6:eq(0)');

                    // Branch lookup map from DB 
var BRANCH_MAP = @json($branchMap ?? []);

var branches = {};
api.column(12, { search: 'none' }).data().each(function (v) {
    var val = $.trim(v); if (val) branches[val] = true;
});
var branchOpts = '<option value="">All Branches</option>';
Object.keys(branches).sort().forEach(function (code) {
    var label = BRANCH_MAP[code] ? code + ' - ' + BRANCH_MAP[code] : code;
    branchOpts += '<option value="' + code + '">' + label + '</option>';
});

                  
                    var depts = {};
                    api.column(13, { search: 'none' }).data().each(function (v) {
                        var val = $.trim(v); if (val) depts[val] = true;
                    });
                    var deptOpts = '<option value="">All Departments</option>';
                    Object.keys(depts).sort(function (a,b){ return +a - +b; }).forEach(function (code) {
                        deptOpts += '<option value="' + code + '">' + (DEPT_MAP[code] || code) + '</option>';
                    });

                   // filters
                    var filterHtml =
                        '<span id="filterIcon"><i class="fas fa-filter me-1"></i>Filters:</span>' +
                        '<span class="filter-group" id="fgBranch">' +
                            '<span class="filter-group-label">Branch</span>' +
                            '<select id="filterBranch" class="filter-select">' + branchOpts + '</select>' +
                        '</span>' +
                        '<span class="filter-group" id="fgDept">' +
                            '<span class="filter-group-label">Dept</span>' +
                            '<select id="filterDept" class="filter-select">' + deptOpts + '</select>' +
                        '</span>' +
                        '<button type="button" id="btnClearFilters" class="btn-clear-filters" style="display:none;">' +
                            '<i class="fas fa-times me-1"></i>Clear' +
                        '</button>';

                    $('#glTable_wrapper .dataTables_filter').prepend(filterHtml);
                },
            });

           
            $(document).on('change', '#filterBranch', applyFilters);
            $(document).on('change', '#filterDept',   applyFilters);
            $(document).on('click',  '#btnClearFilters', function () {
                $('#filterBranch').val('');
                $('#filterDept').val('');
                applyFilters();
            });
        }

    
        function applyFilters() {
            if (!glDataTable) return;
            var bVal = $('#filterBranch').val();
            var dVal = $('#filterDept').val();

            glDataTable
                .column(12).search(bVal ? '^' + $.fn.dataTable.util.escapeRegex(bVal) + '$' : '', true, false)
                .column(13).search(dVal ? '^' + $.fn.dataTable.util.escapeRegex(dVal) + '$' : '', true, false)
                .draw();

         
            $('#filterBranch').closest('.filter-group').toggleClass('active', bVal !== '');
            $('#filterDept').closest('.filter-group').toggleClass('active', dVal !== '');

            $('#btnClearFilters').toggle(bVal !== '' || dVal !== '');
        }

        // Checkbox
        $(document).on('change', '#checkAll', function () {
            var checked = this.checked;
            $('#glTable tbody tr:visible .row-check').each(function () {
                this.checked = checked;
                updateRowHighlight($(this).closest('tr'), checked);
            });
            refreshBulkToolbar();
        });

        $(document).on('change', '.row-check', function () {
            updateRowHighlight($(this).closest('tr'), this.checked);
            var total   = $('#glTable tbody .row-check').length;
            var checked = $('#glTable tbody .row-check:checked').length;
            $('#checkAll').prop('indeterminate', checked > 0 && checked < total)
                         .prop('checked', checked === total && total > 0);
            refreshBulkToolbar();
        });

        function updateRowHighlight($tr, on) { $tr.toggleClass('row-selected', on); }

        function syncCheckboxUI() {
            $('#glTable tbody .row-check').each(function () {
                updateRowHighlight($(this).closest('tr'), this.checked);
            });
            refreshBulkToolbar();
        }

        function refreshBulkToolbar() {
            var c = $('#glTable tbody .row-check:checked').length;
            if (c > 0) { $('#bulkSelectedCount').text(c); $('#bulkToolbar').show(); }
            else { $('#bulkToolbar').hide(); }
        }

        $('#btnClearSelection').on('click', function () {
            $('#glTable tbody .row-check').prop('checked', false);
            $('#checkAll').prop('checked', false).prop('indeterminate', false);
            $('#glTable tbody tr').removeClass('row-selected');
            $('#bulkToolbar').hide();
        });

       
        $('#cancelUploadBtn').on('click', resetAllFiles);
        $('#uploadModal').on('hidden.bs.modal', function () {
            resetAllFiles();
            if (!isUploading) { currentPaymentData = {}; isBulkMode = false; selectedDocNums = []; }
        });

        //Drop zone 
        var $zone = $('#dropZone');
        $zone.on('dragover',  function (e) { e.preventDefault(); $zone.css('border-color','#0d6efd'); });
        $zone.on('dragleave', function (e) { e.preventDefault(); $zone.css('border-color','#6c757d'); });
        $zone.on('drop', function (e) {
            e.preventDefault(); $zone.css('border-color','#6c757d');
            if (e.originalEvent.dataTransfer.files.length) addFiles(e.originalEvent.dataTransfer.files);
        });
        $('#fileInput').on('change', function () {
            if (this.files && this.files.length) addFiles(this.files);
            $(this).val('');
        });
        $('#btnAttachMore').on('click', function (e) { e.preventDefault(); $('#fileInput').trigger('click'); });

        function addFiles(fileList) {
            var maxSize = 5*1024*1024, rejected = [];
            for (var i = 0; i < fileList.length; i++) {
                var f = fileList[i];
                if (f.size > maxSize) { rejected.push(f.name+' (exceeds 5 MB)'); continue; }
                if (attachedFiles.some(function(x){ return x.name===f.name && x.size===f.size; }))
                    { rejected.push(f.name+' (already attached)'); continue; }
                attachedFiles.push(f);
            }
            if (rejected.length) showErrorToast('Skipped: '+rejected.join(', '));
            renderFileList();
        }

        function renderFileList() {
            var $list = $('#fileList');
            $list.empty();
            if (!attachedFiles.length) {
                $('#fileListWrapper').hide();
                $('#btnUpload').prop('disabled', true);
                $('#btnUploadCount').hide();
                return;
            }
            var icons = { pdf:'fa-file-pdf text-danger',doc:'fa-file-word text-primary',docx:'fa-file-word text-primary',
                xls:'fa-file-excel text-success',xlsx:'fa-file-excel text-success',
                jpg:'fa-file-image text-warning',jpeg:'fa-file-image text-warning',png:'fa-file-image text-warning' };
            attachedFiles.forEach(function (f, idx) {
                var ext = f.name.split('.').pop().toLowerCase();
                var ico = icons[ext]||'fa-file text-secondary';
                var sz  = f.size<1048576?(f.size/1024).toFixed(1)+' KB':(f.size/1048576).toFixed(1)+' MB';
                $list.append(
                    '<div class="file-item"><div class="file-item-left"><i class="fas '+ico+' fa-lg"></i>'+
                    '<div><div class="file-item-name" title="'+f.name+'">'+f.name+'</div>'+
                    '<div class="file-item-size">'+sz+'</div></div></div>'+
                    '<button type="button" class="btn-remove-file" data-idx="'+idx+'" title="Remove">'+
                    '<i class="fas fa-times-circle"></i></button></div>'
                );
            });
            $list.find('.btn-remove-file').on('click', function () {
                attachedFiles.splice(parseInt($(this).data('idx')), 1); renderFileList();
            });
            $('#fileCountBadge').text(attachedFiles.length);
            $('#fileListWrapper').show();
            $('#btnUpload').prop('disabled', false);
            if (attachedFiles.length > 1) $('#btnUploadCount').text(attachedFiles.length+' files').show();
            else $('#btnUploadCount').hide();
        }

        function resetAllFiles() {
            attachedFiles = [];
            $('#fileInput').val('');
            $('#fileList').empty();
            $('#fileListWrapper').hide();
            $('#btnUpload').prop('disabled', true);
            $('#btnUploadCount').hide();
        }

        //Bulk upload modal 
        $('#btnBulkUpload').on('click', function () {
            var checked = $('#glTable tbody .row-check:checked');
            if (!checked.length) return;
            var docMap = {};
            checked.each(function () {
                var $cb = $(this), dn = $cb.data('doc-num');
                if (dn && !docMap[dn]) docMap[dn] = { repname: $cb.data('repname'), amount_formatted: $cb.data('amount-formatted') };
            });
            selectedDocNums = Object.keys(docMap);
            isBulkMode = true; currentPaymentData = {};
            var html = '';
            selectedDocNums.forEach(function (dn) {
                var d = docMap[dn];
                html += '<div class="bulk-doc-item"><span class="doc-num">'+dn+'</span>'+
                    '<span class="doc-party">'+(d.repname||'-')+'</span>';
            });
            $('#bulkDocCount').text(selectedDocNums.length);
            $('#bulkDocList').html(html);
            $('#modalTitleText').text('Bulk Uploads');
            $('#singlePaymentDetails').hide();
            $('#bulkPaymentDetails').show();
            resetAllFiles();
            $('#uploadModal').modal('show');
        });

        // Single upload modal
        $(document).on('click', '.btn-upload', function () {
            var $b = $(this);
            isBulkMode = false; selectedDocNums = [];
            currentPaymentData = {
                id: $b.data('id'), gl_doc_id: $b.data('gl-doc-id'), doc_num: $b.data('doc'),
                repname: $b.data('repname'), amount: $b.data('amount'),
                amount_formatted: $b.data('amount-formatted'), code: $b.data('code'),
                party_type: $b.data('party-type'), created_at: $b.data('created-at'),
                voucher_serial: $b.data('voucher-serial'),
                location_code: $b.data('location-code'), voucher_no: $b.data('voucher-no'),
            };
            $('#modalDocNum').text(currentPaymentData.doc_num||'-');
            $('#modalRepName').text(currentPaymentData.repname||'-');
            $('#modalPartyType').text(currentPaymentData.party_type||'-');
            $('#modalAmount').text(currentPaymentData.amount_formatted||'-');
            $('#modalCreatedAt').text(currentPaymentData.created_at||'-');
            $('#modalVoucherSerial').text(currentPaymentData.voucher_serial||'-');
            $('#modalCode').text(currentPaymentData.code||'-');
            $('#modalLocationCode').text(currentPaymentData.location_code||'-');
            $('#modalVoucherNo').text(currentPaymentData.voucher_no||'-');
            $('#modalTitleText').text('Upload Claim Payment File');
            $('#singlePaymentDetails').show(); $('#bulkPaymentDetails').hide();
            resetAllFiles();
            $('#uploadModal').modal('show');
        });

       
        $('#btnUpload').on('click', function () {
            if (!attachedFiles.length) return;
            var files = attachedFiles.slice(), total = files.length;
            isUploading = true;
            $('#uploadModal').modal('hide');
            document.getElementById('preloader').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            if (isBulkMode) processBulkDocs(selectedDocNums.slice(), files, total, 0);
            else uploadSingleFiles(files, 0, total);
        });

        //Single upload 
        function uploadSingleFiles(files, idx, total) {
            if (idx >= files.length) {
                document.getElementById('preloader').style.display = 'none';
                document.body.style.overflow = 'auto';
                isUploading = false;
                removeSingleRow(currentPaymentData.id);
                showSuccessToast('File(s) uploaded for '+currentPaymentData.doc_num);
                currentPaymentData = {};
                if (glDataTable && glDataTable.rows().count() === 0) showNoRecordsMessage();
                return;
            }
            var f = files[idx];
            $('#preloaderTitle').text('Uploading File '+(idx+1)+' of '+total+'...');
            $('#preloaderMessage').text(f.name);
            $('#preloaderProgress').text('Claim: '+currentPaymentData.doc_num);
            var fd = new FormData();
            fd.append('gl_file', f); fd.append('doc_num', currentPaymentData.doc_num);
            fd.append('gl_doc_id', currentPaymentData.gl_doc_id); fd.append('file_index', idx);
            fd.append('total_files', total); fd.append('_token', '{{ csrf_token() }}');
            $.ajax({
                url: '{{ url("claim-payment") }}/'+encodeURIComponent(currentPaymentData.doc_num),
                method:'POST', data:fd, processData:false, contentType:false,
                success: function(res) {
                    if (!res.success) {
                        document.getElementById('preloader').style.display='none';
                        document.body.style.overflow='auto'; isUploading=false;
                        showErrorToast(res.message||'Upload failed.'); currentPaymentData={}; return;
                    }
                    uploadSingleFiles(files, idx+1, total);
                },
                error: function(xhr) {
                    document.getElementById('preloader').style.display='none';
                    document.body.style.overflow='auto'; isUploading=false;
                    var msg='Upload failed.';
                    try { var j=xhr.responseJSON;
                        if(j&&j.errors){var e=[];$.each(j.errors,function(f,m){e.push(f+': '+m.join(', '));});msg=e.join(' | ');}
                        else if(j&&j.message) msg=j.message; } catch(e){}
                    showErrorToast('File '+(idx+1)+' error: '+msg); currentPaymentData={};
                },
            });
        }

        function removeSingleRow(rowId) {
            var node = document.getElementById('row-'+rowId);
            if (node && glDataTable) {
                glDataTable.row(node).remove().draw(false);
                var s = glDataTable.page.info().start;
                glDataTable.rows({page:'current'}).nodes().each(function(node,i){ $(node).find('.serial-number').text(s+i+1); });
            }
        }

        //Bulk upload
        function processBulkDocs(docs, files, total, di) {
            if (di >= docs.length) {
                document.getElementById('preloader').style.display='none';
                document.body.style.overflow='auto'; isUploading=false;
                showSuccessToast('Bulk upload complete for '+docs.length+' document(s)!');
                $('#glTable tbody .row-check').prop('checked',false);
                $('#checkAll').prop('checked',false).prop('indeterminate',false);
                $('#glTable tbody tr').removeClass('row-selected');
                $('#bulkToolbar').hide(); selectedDocNums=[];
                if (glDataTable && glDataTable.rows().count()===0) showNoRecordsMessage();
                return;
            }
            var dn = docs[di];
            $('#preloaderProgress').text('Document '+(di+1)+' of '+docs.length+': '+dn);
            uploadBulkFiles(dn, files, 0, total, function() {
                removeRowsByDocNum(dn);
                setTimeout(function(){ processBulkDocs(docs,files,total,di+1); }, 50);
            });
        }

        function removeRowsByDocNum(dn) {
            if (!glDataTable) return;
            var toRemove=[];
            glDataTable.rows().every(function(){ if($(this.node()).data('doc-num')==dn) toRemove.push(this.index()); });
            if (toRemove.length) {
                toRemove.sort().reverse().forEach(function(i){ glDataTable.row(i).remove(); });
                glDataTable.draw(false);
                var s=glDataTable.page.info().start;
                glDataTable.rows({page:'current'}).every(function(ri){ $(this.node()).find('.serial-number').text(s+ri+1); });
                syncCheckboxUI();
            }
        }

        function uploadBulkFiles(dn, files, fi, total, cb) {
            if (fi >= files.length) { cb(); return; }
            var f = files[fi];
            $('#preloaderTitle').text('Uploading File '+(fi+1)+' of '+total+'...');
            $('#preloaderMessage').text(f.name);
            var fd = new FormData();
            fd.append('gl_file',f); fd.append('doc_num',dn);
            fd.append('file_index',fi); fd.append('total_files',total); fd.append('_token','{{ csrf_token() }}');
            $.ajax({
                url:'{{ url("claim-payment/bulk") }}', method:'POST', data:fd, processData:false, contentType:false,
                success:function(res){
                    if(!res.success){
                        document.getElementById('preloader').style.display='none';
                        document.body.style.overflow='auto'; isUploading=false;
                        showErrorToast(res.message||'Upload failed for '+dn); return;
                    }
                    uploadBulkFiles(dn,files,fi+1,total,cb);
                },
                error:function(xhr){
                    document.getElementById('preloader').style.display='none';
                    document.body.style.overflow='auto'; isUploading=false;
                    var msg='Upload failed.'; try{msg=xhr.responseJSON.message||msg;}catch(e){}
                    showErrorToast('File '+(fi+1)+' error: '+msg);
                },
            });
        }

        function showNoRecordsMessage() {
            $('.card-body').html(`<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i>All records have been processed. No pending GL uploads.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
        }

        //View Document Modal 
        $(document).on('click', '.btn-view-doc', function () {
            var dn = $(this).data('doc');
            if (!dn) { showErrorToast('No document number found.'); return; }
            $('#viewDocTitle').text(': '+dn);
            $('#viewDocLoading').show(); $('#viewDocFrame').hide().empty();
            $('#viewDocFallback').hide(); $('#viewDocMeta').hide();
            $('#viewDocDownloadBtn,#viewDocOpenNewTab').hide().attr('href','#');
            $('#viewDocModal').modal('show');
            $.ajax({
                url:'{{ route("gl.upload.doc.info") }}', method:'GET', data:{doc_num:dn},
                success:function(res){
                    $('#viewDocLoading').hide();
                    if(!res.success||!res.file_name){ $('#viewDocFallbackName').text(res.message||'No document found'); $('#viewDocFallback').show(); return; }
                    var fn=res.file_name, url=res.file_url, ext=fn.split('.').pop().toLowerCase();
                    var imgs=['jpg','jpeg','png','gif','bmp','webp'];
                    $('#viewDocTitle').text(': '+fn);
                    $('#viewDocOpenNewTab').show().attr('href',url);
                    $('#viewDocDownloadBtn').show().attr('href',url+'?download=1');
                    if(res.uploaded_at){$('#viewDocUploadedAt').text(new Date(res.uploaded_at).toLocaleString());$('#viewDocUploadedBy').text(res.uploaded_by||'N/A');$('#viewDocMeta').show();}
                    if(ext==='pdf') $('#viewDocFrame').html('<iframe src="'+url+'"></iframe>').show();
                    else if(imgs.indexOf(ext)!==-1) $('#viewDocFrame').html('<img src="'+url+'" style="max-width:100%;max-height:72vh;margin:auto;display:block;padding:20px;" />').show();
                    else { $('#viewDocFallbackName').text(fn); $('#viewDocFallbackDownloadBtn').attr('href',url+'?download=1'); $('#viewDocFallback').show(); }
                },
                error:function(xhr){
                    $('#viewDocLoading').hide();
                    var msg='Failed to load document.'; try{msg=xhr.responseJSON.message||msg;}catch(e){}
                    $('#viewDocFallbackName').text(msg); $('#viewDocFallback').show();
                },
            });
        });

        $('#viewDocModal').on('hidden.bs.modal', function () {
            $('#viewDocFrame').empty().hide(); $('#viewDocLoading').show();
            $('#viewDocFallback').hide(); $('#viewDocMeta').hide();
            $('#viewDocDownloadBtn,#viewDocOpenNewTab').hide();
        });

        function showSuccessToast(msg) {
            $('#toastMessage').text(msg);
            new bootstrap.Toast(document.getElementById('successToast'),{autohide:true,delay:6000}).show();
        }
        function showErrorToast(msg) {
            $('#errorToastMessage').text(msg);
            new bootstrap.Toast(document.getElementById('errorToast'),{autohide:true,delay:6000}).show();
        }
    </script>
@endsection
@endsection