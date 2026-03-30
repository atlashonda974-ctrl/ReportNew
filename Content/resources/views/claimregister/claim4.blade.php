<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.master_titles')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <x-datatable-styles />

    <style>
        .time-filter-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .time-tab {
            flex: 1;
            min-width: 80px;   
            max-width: 100px; 
            border-radius: 8px;
            padding: 8px 5px;
            text-align: center;
            transition: all 0.2s;
            text-decoration: none;
            font-size: 14px;
        }
        .time-tab:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .time-tab.active {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white !important;
        }
        .dt-ellipsis {
            max-width: 100px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            cursor: pointer;
        }
        #emailHistoryContent .card {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        #emailHistoryContent .card-header {
            font-size: 14px;
        }
        #emailHistoryContent .card-body {
            font-size: 13px;
        }
        .view-email-history-btn {
            transition: all 0.2s;
        }
        .view-email-history-btn:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <x-report-header title="O/S Settlement Claims Report" />

        <form method="GET" action="{{ url('/cr4') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3 d-flex align-items-center">
                    <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', $start_date) }}">
                </div>

                <div class="col-md-3 d-flex align-items-center">
                    <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', $end_date) }}">
                </div>

                <div class="col-md-3 d-flex align-items-center">
                    <label for="new_category" class="form-label me-2" style="white-space: nowrap; width: 100px;">Departments</label>
                    <select name="new_category" id="new_category" class="form-control">
                        <option value="" {{ $selected_category == '' ? 'selected' : '' }}>All Departments</option>
                        <option value="Fire"   {{ $selected_category == 'Fire'   ? 'selected' : '' }}>Fire</option>
                        <option value="Marine" {{ $selected_category == 'Marine' ? 'selected' : '' }}>Marine</option>
                        <option value="Motor"  {{ $selected_category == 'Motor'  ? 'selected' : '' }}>Motor</option>
                        <option value="Miscellaneous" {{ $selected_category == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                        <option value="Health" {{ $selected_category == 'Health' ? 'selected' : '' }}>Health</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-center">
                    <label for="location_category" class="form-label me-2" style="white-space: nowrap;">Branches</label>
                    <select name="location_category" id="location_category" class="form-control select2">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->fbracode }}" {{ request('location_category') == $branch->fbracode ? 'selected' : '' }}>
                                {{ $branch->fbradsc }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-center">
                    <label for="insu" class="form-label me-2" style="white-space: nowrap;">Insurance Type</label>
                    <select name="insu[]" id="insu" class="form-control select2-insurance" multiple>
                        <option value="D" {{ in_array('D', request('insu', [])) ? 'selected' : '' }}>Direct</option>
                        <option value="I" {{ in_array('I', request('insu', [])) ? 'selected' : '' }}>Inward</option>
                        <option value="O" {{ in_array('O', request('insu', [])) ? 'selected' : '' }}>Outward</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-primary me-2" title="Filter">
                        <i class="bi bi-funnel-fill"></i> Filter
                    </button>
                    <a href="{{ url('/cr4') }}" class="btn btn-outline-secondary" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        @php
            $filters = [
                'all'     => 'All',
                '2days'   => '2 Days',
                '5days'   => '5 Days',
                '7days'   => '7 Days',
                '10days'  => '10 Days',
                '15days'  => '15 Days',
                '15plus'  => '15+ Days',
            ];
        @endphp

        <div class="d-flex justify-content-start mb-3">
            <div class="card time-filter-card">
                <div class="card-body py-2 px-3">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($filters as $key => $label)
                            <a href="{{ request()->fullUrlWithQuery(['time_filter' => $key]) }}"
                               class="time-tab {{ request('time_filter', 'all') == $key ? 'active' : 'bg-light' }}">
                                <div class="fw-bold">{{ $label }}</div>
                                <div class="small">{{ $counts[$key] ?? 0 }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if($data->isEmpty())
            <div class="alert alert-info">No records found for the selected filters.</div>
        @else
            <table id="reportsTable" class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Send Email</th>
                        <th>Upload Report</th>
                        <th>Email Logs</th>
                        <th>Department</th>
                        <th>Claim No</th>
                        <th>Policy No</th>
                        <th>Intimation Date</th>
                        <th>Surveyor Report Date</th>
                        <th>Entry No</th>
                        <th>Insured</th>
                        <th>Cause of Loss</th>
                        <th>Loss Adjusted Amount</th>
                        <th>Surveyor Name</th>
                        <th>Insurance Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                        @php
                            $categoryMapping = [11 => 'Fire', 12 => 'Marine', 13 => 'Motor', 14 => 'Miscellaneous', 16 => 'Health'];
                            $department = $categoryMapping[$record->PDP_DEPT_CODE] ?? 'N/A';

                            $insuranceTypeMapping = ['D' => 'Direct', 'I' => 'Inward', 'O' => 'Outward'];
                            $insuranceType = $insuranceTypeMapping[$record->PIY_INSUTYPE ?? ''] ?? 'N/A';

                            $claimNo    = trim($record->GIH_DOC_REF_NO ?? 'N/A');
                            $reportName = $record->report_name ?? 'surveyor report';
                            $emailCount = $record->email_send_count ?? 0;

                            $alreadyUploaded = \App\Models\ClaimDoc::where('doc_num', $claimNo)
                                                                ->where('repname', $reportName)
                                                                ->exists();
                        @endphp
                        <tr>
                            <td>
                                <button type="button" class="btn btn-info btn-sm send-report-email-btn"
                                        data-claim-no="{{ $claimNo }}"
                                        data-policy="{{ $record->GID_BASEDOCUMENTNO ?? 'N/A' }}"
                                        data-insured="{{ $record->PPS_DESC ?? 'N/A' }}"
                                        data-loss-desc="{{ $record->POC_LOSSDESC ?? 'N/A' }}"
                                        data-surveyor="{{ $record->PSR_SURV_NAME ?? 'N/A' }}"
                                        data-report-date="{{ $record->GUD_REPORT_DATE ?? 'N/A' }}"
                                        data-loss-adjusted="{{ $record->GIH_LOSSADJUSTED ?? 0 }}"
                                        data-report-name="{{ $reportName }}">
                                    <i class="bi bi-envelope"></i> Send
                                </button>
                            </td>

                            <td>
                                @if($alreadyUploaded)
                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Uploaded</span>
                                @else
                                    <button type="button" class="btn btn-outline-primary btn-sm upload-trigger-btn"
                                            data-claim-no="{{ $claimNo }}"
                                            data-report-name="{{ $reportName }}">
                                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload Approval
                                    </button>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($emailCount > 0)
                                    <button type="button" class="btn btn-sm btn-outline-primary view-email-history-btn"
                                            data-claim-no="{{ $claimNo }}"
                                            data-report-name="{{ $reportName }}"
                                            data-history="{{ json_encode($record->email_history ?? []) }}">
                                        <i class="bi bi-envelope-open"></i> <strong>{{ $emailCount }}</strong>
                                    </button>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>

                            <td>{{ $department }}</td>
                            <td>{{ $claimNo }}</td>
                            <td>{{ $record->GID_BASEDOCUMENTNO ?? 'N/A' }}</td>
                            <td>{{ $record->GIH_INTIMATIONDATE ?? 'N/A' }}</td>
                            <td>{{ $record->GUD_REPORT_DATE ?? 'N/A' }}</td>
                            <td>{{ $record->{'MAX(CLM_INTHD.GIH_INTI_ENTRYNO)'} ?? 'N/A' }}</td>
                            <td>{{ $record->PPS_DESC ?? 'N/A' }}</td>
                            <td>{{ $record->POC_LOSSDESC ?? 'N/A' }}</td>
                            <td class="text-end">{{ number_format($record->GIH_LOSSADJUSTED ?? 0, 2) }}</td>
                            <td>{{ $record->PSR_SURV_NAME ?? 'N/A' }}</td>
                            <td>{{ $insuranceType }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="9" class="text-end pe-3">Total Loss Adjusted Amount</td>
                        <td class="text-end total-cell" id="totalLossAdjusted">
                            {{ number_format($data->sum('GIH_LOSSADJUSTED'), 2) }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>

    <!-- Send Email Modal -->
    <div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendEmailModalLabel">Send Email here</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="emailSurveyorForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">To:</label>
                                <input type="email" class="form-control" id="emailTo" required placeholder="Branch@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CC (optional):</label>
                                <input type="text" class="form-control" id="emailCc" placeholder="manager@example.com, another@domain.com">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Subject:</label>
                            <input type="text" class="form-control" id="emailSubject" required>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Message:</label>
                            <textarea class="form-control" id="emailBody" rows="10" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSendSurveyorEmail">
                        <i class="bi bi-send-fill"></i> Send Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Email History Modal -->
    <div class="modal fade" id="emailHistoryModal" tabindex="-1" aria-labelledby="emailHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="emailHistoryModalLabel">
                        <i class="bi bi-clock-history"></i> Email History
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="emailHistoryContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

   <!-- Upload Modal (Bank 1 + Bank 2 + File )+ Remarks updated -->
<div class="modal fade" id="uploadDetailsModal" tabindex="-1" aria-labelledby="uploadDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDetailsModalLabel">Upload Approval & Bank Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadDetailsForm">
                    <div class="mb-3">
                        <label class="form-label">Claim Number</label>
                        <input type="text" class="form-control" id="uploadClaimNo" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bank 1 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="bank1" placeholder="Enter Bank 1 name / reference" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bank 2 (optional)</label>
                        <input type="text" class="form-control" id="bank2" placeholder="Enter Bank 2 name / reference">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks (optional)</label>
                        <textarea class="form-control" id="remarks" rows="3" placeholder="Enter any additional remarks or notes"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="fileInput" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                    </div>
                    <input type="hidden" id="uploadReportName" value="surveyor report">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnConfirmUpload">
                    <i class="bi bi-check-circle"></i> Save & Upload
                </button>
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.3.2/js/dataTables.fixedHeader.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#location_category').select2({ placeholder: "Select a branch", allowClear: true });
            $('#insu').select2({ placeholder: "Select insurance type(s)", allowClear: true });

            var table = $('#reportsTable').DataTable({
                paging: false,
                searching: true,
                ordering: true,
                info: true,
                scrollX: true,
                scrollY: "500px",
                scrollCollapse: false,
                fixedHeader: { header: true, footer: true },
                autoWidth: true,
                dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
                buttons: [
                    { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-success btn-sm', footer: true },
                    { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-danger btn-sm', footer: true, orientation: 'landscape' }
                ],
                footerCallback: function() {
                    var api = this.api();
                    function intVal(i) {
                        return typeof i === 'string' ? parseFloat(i.replace(/[^\d.-]/g, '')) || 0 : (typeof i === 'number' ? i : 0);
                    }
                    var total = api.column(11, {page: 'current'}).data().reduce(function(a, b) { return intVal(a) + intVal(b); }, 0);
                    $('#totalLossAdjusted').html(total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                },
                initComplete: function() {
                    this.api().columns.adjust();
                },
                drawCallback: function() {
                    this.api().columns.adjust();
                }
            });

            let currentUploadData = null;

            $(document).on('click', '.upload-trigger-btn', function() {
                currentUploadData = {
                    claimNo: $(this).data('claim-no'),
                    reportName: $(this).data('report-name') || 'surveyor report'
                };

                $('#uploadClaimNo').val(currentUploadData.claimNo);
                $('#bank1').val('').removeClass('is-invalid');
                $('#bank2').val('');
                $('#remarks').val('');
                $('#fileInput').val('');
                $('#uploadReportName').val(currentUploadData.reportName);

                $('#uploadDetailsModal').modal('show');
            });

            $('#btnConfirmUpload').on('click', function() {
                const bank1 = $('#bank1').val().trim();
                 const remarks = $('#remarks').val().trim();
                const fileInput = $('#fileInput')[0];

                if (!bank1) {
                    $('#bank1').addClass('is-invalid');
                    alert('Bank 1 is required.');
                    return;
                }

                if (!fileInput.files || fileInput.files.length === 0) {
                    alert('Please select a file to upload.');
                    return;
                }

                const bank2 = $('#bank2').val().trim();

                $('#uploadDetailsModal').modal('hide');

                setTimeout(() => {
                    const formData = new FormData();
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    formData.append('claim_no', currentUploadData.claimNo);
                    formData.append('report_name', currentUploadData.reportName);
                    formData.append('bank1', bank1);
                    if (bank2) formData.append('bank2', bank2);
                     if (remarks) formData.append('remarks', remarks);
                    formData.append('file_name', fileInput.files[0]);

                    const btn = $(`.upload-trigger-btn[data-claim-no="${currentUploadData.claimNo}"]`);
                    btn
                        .removeClass('btn-outline-primary')
                        .addClass('btn-info')
                        .html('<span class="spinner-border spinner-border-sm"></span> Uploading...')
                        .prop('disabled', true);

                    $.ajax({
                        url: "{{ route('claim.upload.document') }}",
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                alert('File and bank details saved successfully!');
                                location.reload();
                            } else {
                                alert('Failed to save: ' + (response.message || 'Unknown error'));
                            }
                        },
                        error: function() {
                            alert('Server error while saving data.');
                        },
                        complete: function() {
                            btn
                                .removeClass('btn-info')
                                .addClass('btn-outline-primary')
                                .html('<i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload Approval')
                                .prop('disabled', false);
                        }
                    });
                }, 300);
            });

            let currentClaimData = null;

            $(document).on('click', '.send-report-email-btn', function() {
                currentClaimData = {
                    claimNo: $(this).data('claim-no'),
                    policy: $(this).data('policy'),
                    insured: $(this).data('insured'),
                    lossDesc: $(this).data('loss-desc'),
                    surveyor: $(this).data('surveyor'),
                    reportDate: $(this).data('report-date'),
                    lossAdjusted: $(this).data('loss-adjusted'),
                    reportName: $(this).data('report-name')
                };

                $('#emailSubject').val(`Surveyor Report - Claim No: ${currentClaimData.claimNo}`);

                const defaultBody = `Dear Sir/Madam,\n\nPlease find attached surveyor report details:\n\nClaim Number        : ${currentClaimData.claimNo}\nPolicy Number       : ${currentClaimData.policy}\nInsured             : ${currentClaimData.insured}\nLoss Description    : ${currentClaimData.lossDesc}\nSurveyor            : ${currentClaimData.surveyor}\nReport Date         : ${currentClaimData.reportDate}\nLoss Adjusted Amount: ${parseFloat(currentClaimData.lossAdjusted).toLocaleString('en-US', {minimumFractionDigits: 2})}\n\nPlease review and process accordingly.\n\nThank you,\nClaims Department\nAtlas Insurance Limited`;

                $('#emailBody').val(defaultBody);
                $('#emailTo').val('');
                $('#emailCc').val('');

                $('#sendEmailModal').modal('show');
            });

            $('#btnSendSurveyorEmail').on('click', function() {
                const to = $('#emailTo').val().trim();
                const cc = $('#emailCc').val().trim();
                const subject = $('#emailSubject').val().trim();
                const body = $('#emailBody').val().trim();

                if (!to || !subject || !body) {
                    alert('Please fill required fields (To, Subject, Message)');
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Sending...');

                $.ajax({
                    url: "{{ route('send.surveyor.email') }}",
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        to, cc, subject, body,
                        claim_data: currentClaimData
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Email sent successfully!');
                            $('#sendEmailModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error: ' + (response.message || 'Failed to send email'));
                        }
                    },
                    error: function() {
                        alert('Server error occurred while sending email.');
                    },
                    complete: function() {
                        btn.prop('disabled', false).html('<i class="bi bi-send-fill"></i> Send Email');
                    }
                });
            });

            $(document).on('click', '.view-email-history-btn', function() {
                const claimNo = $(this).data('claim-no');
                const history = $(this).data('history') || [];

                $('#emailHistoryModalLabel').html(`<i class="bi bi-clock-history"></i> Email History - Claim: ${claimNo}`);

                let html = '';
                if (history.length === 0) {
                    html = '<div class="alert alert-info">No email history found.</div>';
                } else {
                    history.forEach((email, i) => {
                        const isLatest = i === 0;
                        html += `
                        <div class="card mb-3 ${isLatest ? 'border-primary' : ''}">
                            <div class="card-header ${isLatest ? 'bg-primary text-white' : 'bg-light'}">
                                <strong>${isLatest ? 'Latest Email' : `Email #${history.length - i}`}</strong>
                                <span class="badge ${isLatest ? 'bg-warning text-dark' : 'bg-secondary'} float-end">
                                    ${email.datetime || 'N/A'}
                                </span>
                            </div>
                            <div class="card-body">
                                <div><strong>From:</strong> ${email.created_by || 'N/A'}</div>
                                <div><strong>To:</strong> ${email.sent_to || 'N/A'}</div>
                                ${email.sent_cc ? `<div><strong>CC:</strong> ${email.sent_cc}</div>` : ''}
                                <div><strong>Subject:</strong> ${email.subject || 'N/A'}</div>
                                <div class="mt-2"><strong>Message:</strong><pre class="bg-light p-2 mt-1" style="white-space: pre-wrap;">${email.body || 'N/A'}</pre></div>
                            </div>
                        </div>`;
                    });
                }

                $('#emailHistoryContent').html(html);
                $('#emailHistoryModal').modal('show');
            });
        });
    </script>
</body>
</html>