<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.master_titles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <x-datatable-styles />

    <style>
        .file-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: #6c757d; color: #fff;
            border-radius: 4px; padding: 3px 8px;
            font-size: 0.78rem; margin: 2px 2px 0 0;
        }
        .file-badge .remove-file {
            background: none; border: none; color: #fff;
            font-size: 0.7rem; cursor: pointer; padding: 0; line-height: 1;
        }
        #attachmentPreview { flex-wrap: wrap; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <x-report-header title="Closing Particular" />

        <form method="GET" action="{{ url('/getlast') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3 d-flex align-items-center">
                    <label for="start_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">From Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                        value="{{ request('start_date', $start_date) }}">
                </div>

                <div class="col-md-3 d-flex align-items-center">
                    <label for="end_date" class="form-label me-2" style="white-space: nowrap; width: 100px;">To Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                        value="{{ request('end_date', $end_date) }}">
                </div>

                <div class="col-md-3 d-flex align-items-center">
                    <label for="new_category" class="form-label me-2" style="white-space: nowrap; width: 100px;">Departments</label>
                    <select name="new_category" id="new_category" class="form-control">
                        <option value="">All Departments</option>
                        <option value="Fire"          {{ request('new_category') == 'Fire'          ? 'selected' : '' }}>Fire</option>
                        <option value="Marine"        {{ request('new_category') == 'Marine'        ? 'selected' : '' }}>Marine</option>
                        <option value="Motor"         {{ request('new_category') == 'Motor'         ? 'selected' : '' }}>Motor</option>
                        <option value="Miscellaneous" {{ request('new_category') == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                        <option value="Health"        {{ request('new_category') == 'Health'        ? 'selected' : '' }}>Health</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-primary me-2" title="Filter">
                        <i class="bi bi-funnel-fill"></i> Filter
                    </button>
                    <a href="{{ url('/getlast') }}" class="btn btn-outline-secondary me-2" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        @if(empty($data))
            <div class="alert alert-danger">No data available.</div>
        @else
            <table id="reportsTable" class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>Closing Particular</th>
                        <th>Department Code</th>
                        <th>Serial No</th>
                        <th>Issue Date</th>
                        <th>Commencement Date</th>
                        <th>Expiry Date</th>
                        <th>Reinsurer</th>
                        <th>Reissue Date</th>
                       
                        <th>Total SI</th>
                        <th>Total Premium</th>
                        <th>Reinsurance SI</th>
                        <th>Reinsurance Premium</th>
                        <th>Commission Amount</th>
                        <th>Posting Tag</th>
                        <th>Cancellation Tag</th>
                        <th>Posted By</th>
                        <th>Their Reference No</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                        @php
                            $categoryMapping = [11 => 'Fire', 12 => 'Marine', 13 => 'Motor', 14 => 'Miscellaneous', 16 => 'Health'];
                            $deptCode = is_array($record) ? ($record['PDP_DEPT_CODE'] ?? null) : ($record->PDP_DEPT_CODE ?? null);
                        @endphp
                        <tr data-record='@json($record)'>
                            <td style="white-space:nowrap;">
                                {{-- Email button --}}
                                <button class="btn btn-sm btn-primary send-email-btn" title="Send Email">
                                    <i class="fas fa-envelope"></i>
                                </button>

                                {{-- Upload button — opens hidden file input --}}
                                <button class="btn btn-sm btn-info upload-btn ms-1" title="Attach & Upload Files">
                                    <i class="fas fa-upload"></i>
                                </button>

                                {{--
                                    Hidden file input — "multiple" + no accept restriction
                                    so any file type is allowed (PDF, Word, Excel, images…).
                                    Validation happens server-side.
                                --}}
                                <input type="file" class="file-input d-none" multiple>

                                {{-- Per-file badge preview rendered by JS --}}
                                <div class="file-preview d-flex flex-wrap mt-1"></div>

                                {{-- Upload submit button — shown only after files are chosen --}}
                                <button class="btn btn-sm btn-success upload-submit-btn d-none mt-1" title="Upload selected files">
                                    <i class="fas fa-cloud-upload-alt me-1"></i>Upload
                                </button>
                            </td>
                            <td>{{ $record->GCP_DOC_REFERENCENO ?? 'N/A' }}</td>
                            <td>{{ $categoryMapping[$deptCode] ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_SERIALNO ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_ISSUEDATE ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_COMMDATE ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_EXPIRYDATE ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_REINSURER ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_REISSUEDATE ?? 'N/A' }}</td>
                           
                            <td>{{ $record->GCP_COTOTALSI ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_COTOTALPREM ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_REINSI ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_REINPREM ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_COMMAMOUNT ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_POSTINGTAG ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_CANCELLATIONTAG ?? 'N/A' }}</td>
                            <td>{{ $record->GCP_POST_USER ?? 'N/A' }}</td>
                            <td>{{ $record->GCT_THEIR_REF_NO ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- ── Email Modal ────────────────────────────────────────────────── --}}
        <div id="emailModal" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-envelope me-2"></i>Send Email</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        {{--
                            type="text" on To/CC so comma-separated addresses work.
                            Server-side parseEmails() handles validation.
                        --}}
                        <div class="mb-3">
                            <label class="form-label">To: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="to"
                                   placeholder="recipient@example.com, another@example.com">
                            <div class="form-text text-muted">Separate multiple addresses with a comma.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CC:</label>
                            <input type="text" class="form-control" id="cc"
                                   placeholder="cc@example.com, another@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" placeholder="Email subject">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Body: <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="body" rows="5" placeholder="Email body"></textarea>
                        </div>

                        {{-- Optional file attachments on the email modal too --}}
                        <div class="mb-2">
                            <label class="form-label">
                                Attachments <span class="text-muted small">(optional)</span>:
                            </label>
                            <input type="file" class="form-control" id="emailAttachmentFile"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip"
                                   multiple>
                            <div class="form-text text-muted">
                                Max 10MB per file. You can select multiple files.
                            </div>
                            <div id="emailAttachmentPreview" class="mt-2 d-flex flex-wrap gap-1"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="me-auto text-muted small" id="emailModalStatus"></div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="sendEmailBtn">
                            <i class="bi bi-send-fill me-1"></i>Send Email
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>

    // ── Email modal — selected files (accumulated across multiple picker opens) ──
    var emailSelectedFiles = [];

    function renderEmailFileBadges() {
        var $preview = $('#emailAttachmentPreview').empty();
        emailSelectedFiles.forEach(function(file, index) {
            $preview.append(
                '<span class="file-badge">' +
                    '<i class="bi bi-paperclip"></i>' +
                    '<span>' + file.name + '</span>' +
                    '<button type="button" class="remove-email-file" data-index="' + index + '" title="Remove">&times;</button>' +
                '</span>'
            );
        });
    }

    $(document).on('change', '#emailAttachmentFile', function() {
        Array.from(this.files).forEach(function(f) {
            var dup = emailSelectedFiles.some(function(x) { return x.name === f.name && x.size === f.size; });
            if (!dup) emailSelectedFiles.push(f);
        });
        $(this).val('');
        renderEmailFileBadges();
    });

    $(document).on('click', '.remove-email-file', function() {
        emailSelectedFiles.splice(parseInt($(this).data('index')), 1);
        renderEmailFileBadges();
    });

    $('#emailModal').on('hidden.bs.modal', function() {
        emailSelectedFiles = [];
        $('#emailAttachmentPreview').empty();
        $('#emailAttachmentFile').val('');
        $('#emailModalStatus').html('');
    });

    // ── Row file picker — per-row accumulated files ───────────────────────────
    // Each row keeps its own files[] stored on the DOM element via $.data()
    function getRowFiles($row) {
        if (!$row.data('rowFiles')) $row.data('rowFiles', []);
        return $row.data('rowFiles');
    }

    function renderRowBadges($row) {
        var files   = getRowFiles($row);
        var $preview = $row.find('.file-preview').empty();
        var $submitBtn = $row.find('.upload-submit-btn');

        files.forEach(function(file, index) {
            $preview.append(
                '<span class="file-badge">' +
                    '<i class="bi bi-paperclip"></i>' +
                    '<span>' + file.name + '</span>' +
                    '<button type="button" class="remove-row-file" data-index="' + index + '" title="Remove">&times;</button>' +
                '</span>'
            );
        });

        // Show/hide the Upload button based on whether files are present
        if (files.length > 0) {
            $submitBtn.removeClass('d-none');
        } else {
            $submitBtn.addClass('d-none');
        }
    }

    // Upload button → open file picker
    $(document).on('click', '.upload-btn', function() {
        $(this).closest('td').find('.file-input').click();
    });

    // File picker change → append to row's file list
    $(document).on('change', '.file-input', function() {
        var $row  = $(this).closest('tr');
        var files = getRowFiles($row);

        Array.from(this.files).forEach(function(f) {
            var dup = files.some(function(x) { return x.name === f.name && x.size === f.size; });
            if (!dup) files.push(f);
        });

        $(this).val(''); // reset so same file can be re-added after removal
        renderRowBadges($row);
    });

    // Remove individual file badge from row
    $(document).on('click', '.remove-row-file', function() {
        var $row  = $(this).closest('tr');
        var files = getRowFiles($row);
        files.splice(parseInt($(this).data('index')), 1);
        renderRowBadges($row);
    });

    // Upload submit button → send all files for this row
    $(document).on('click', '.upload-submit-btn', function() {
        var $btn    = $(this);
        var $row    = $btn.closest('tr');
        var record  = $row.data('record');
        var files   = getRowFiles($row);

        if (files.length === 0) {
            alert('Please attach at least one file before uploading.');
            return;
        }

        var formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('record', JSON.stringify({
            referenceNo:        record.GCP_DOC_REFERENCENO  ?? null,
            departmentCode:     record.PDP_DEPT_CODE         ?? null,
            serialNo:           record.GCP_SERIALNO          ?? null,
            issueDate:          record.GCP_ISSUEDATE          ?? null,
            commencementDate:   record.GCP_COMMDATE           ?? null,
            expiryDate:         record.GCP_EXPIRYDATE         ?? null,
            reinsurer:          record.GCP_REINSURER          ?? null,
            reissueDate:        record.GCP_REISSUEDATE        ?? null,
            
            totalSi:            record.GCP_COTOTALSI           ?? null,
            totalPremium:       record.GCP_COTOTALPREM         ?? null,
            reinsuranceSi:      record.GCP_REINSI              ?? null,
            reinsurancePremium: record.GCP_REINPREM            ?? null,
            commissionAmount:   record.GCP_COMMAMOUNT          ?? null,
            postingTag:         record.GCP_POSTINGTAG          ?? null,
            cancellationTag:    record.GCP_CANCELLATIONTAG     ?? null,
            postedBy:           record.GCP_POST_USER           ?? null,
            theirReferenceNo:   record.GCT_THEIR_REF_NO        ?? null
        }));

        // Append ALL selected files as files[]
        files.forEach(function(file) {
            formData.append('files[]', file);
        });

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Uploading...');

        $.ajax({
            url:         '{{ url("/verify-record") }}',
            type:        'POST',
            data:        formData,
            processData: false,
            contentType: false,
            headers:     { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.success) {
                    alert('File(s) uploaded & record verified successfully.');
                    $('#reportsTable').DataTable().row($row).remove().draw();
                } else {
                    alert(response.message || 'Upload failed.');
                    $btn.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt me-1"></i>Upload');
                }
            },
            error: function(xhr) {
                alert('Error: ' + ((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Upload failed.'));
                $btn.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt me-1"></i>Upload');
            }
        });
    });

    $(document).ready(function() {

        $('.select2').select2({
            placeholder: "Select a branch",
            allowClear:  true,
            width:       '69%'
        });

        var table = $('#reportsTable').DataTable({
            paging:         false,
            searching:      true,
            ordering:       true,
            info:           true,
            scrollX:        true,
            scrollY:        "500px",
            scrollCollapse: false,
            fixedHeader:    { header: true, footer: true },
            autoWidth:      true,
            dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Outstanding Report',
                    footer: true,
                    exportOptions: {
                        columns: ':visible:not(:first-child)',
                        format: { body: function(data) { return data; } },
                        modifier: { page: 'current' }
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Outstanding Report',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    footer: true,
                    exportOptions: {
                        columns: ':visible:not(:first-child)',
                        format: { body: function(data) { return data; } },
                        modifier: { page: 'current' }
                    }
                }
            ],
            initComplete: function() {
                this.api().columns.adjust();
                $('.dataTables_filter').css('margin-left', '5px').css('margin-right', '5px');
                $('.dt-buttons').css('margin-left', '5px');
            }
        });

        // ── Email button — open modal ─────────────────────────────────────────
        var currentRowData = null;
        var currentRow     = null;

        $(document).on('click', '.send-email-btn', function() {
            currentRow     = $(this).closest('tr');
            currentRowData = currentRow.data('record');

            $('#to').val('');
            $('#cc').val('');
            $('#subject').val('Reinsurance Request Note: ' + (currentRowData.GCP_DOC_REFERENCENO || 'N/A'));
            $('#body').val(
                'Dear Sir/Madam,\n\n' +
                'Please find below details for Request Note: ' + (currentRowData.GCP_DOC_REFERENCENO || 'N/A') + '\n\n' +
                'Reinsurer: '   + (currentRowData.GCP_REINSURER  || 'N/A') + '\n' +
                'Total Sum Insured: ' + (currentRowData.GCP_COTOTALSI || 'N/A') + '\n\n' +
                'Regards,'
            );

            $('#emailModal').modal('show');
        });

        // ── Send email ────────────────────────────────────────────────────────
        $('#sendEmailBtn').on('click', function() {
            var to      = $('#to').val().trim();
            var cc      = $('#cc').val().trim();
            var subject = $('#subject').val().trim();
            var body    = $('#body').val().trim();

            if (!to)      { alert('Please enter a recipient email address.'); return; }
            if (!subject) { alert('Please enter an email subject.'); return; }
            if (!body)    { alert('Please enter an email body.'); return; }

            var $btn       = $(this);
            var csrfToken  = $('meta[name="csrf-token"]').attr('content');

            var formData = new FormData();
            formData.append('_token',  csrfToken);
            formData.append('to',      to);
            formData.append('cc',      cc);
            formData.append('subject', subject);
            formData.append('body',    body);

            // Map record fields into records[] array format (same shape as r1)
            var records = [{
                reqNote:      currentRowData.GCP_DOC_REFERENCENO ?? null,
                referenceNo:  currentRowData.GCP_DOC_REFERENCENO ?? null,
                dept:         currentRowData.PDP_DEPT_CODE        ?? null,
                serialNo:     currentRowData.GCP_SERIALNO         ?? null,
                docDate:      currentRowData.GCP_ISSUEDATE        ?? null,
                commDate:     currentRowData.GCP_COMMDATE         ?? null,
                expiryDate:   currentRowData.GCP_EXPIRYDATE       ?? null,
                insured:      currentRowData.GCP_REINSURER        ?? null,
                reinsParty:   currentRowData.GCP_REINSURER        ?? null,
                totalSumIns:  currentRowData.GCP_COTOTALSI        ?? null,
                totalPremium: currentRowData.GCP_COTOTALPREM      ?? null,
                riSumIns:     currentRowData.GCP_REINSI           ?? null,
                riPremium:    currentRowData.GCP_REINPREM         ?? null,
                commissionAmount: currentRowData.GCP_COMMAMOUNT   ?? null,
                acceptanceNo: currentRowData.GCT_THEIR_REF_NO     ?? null
            }];

            formData.append('records', JSON.stringify(records));

            // Attach any files selected in the email modal
            emailSelectedFiles.forEach(function(file) {
                formData.append('upload_files[]', file);
            });

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Sending...');
            $('#emailModalStatus').html('<i class="bi bi-hourglass-split me-1"></i> Sending...');

            $.ajax({
                url:         "{{ route('send.email') }}",
                method:      'POST',
                data:        formData,
                processData: false,
                contentType: false,
                headers:     { 'X-CSRF-TOKEN': csrfToken },
                success: function(response) {
                    if (response.success) {
                        $('#emailModal').modal('hide');
                        alert('Email sent successfully!');
                        if (currentRow) {
                            $('#reportsTable').DataTable().row(currentRow).remove().draw();
                        }
                    } else {
                        alert('Error: ' + (response.message || 'Failed to send email.'));
                    }
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unknown error.';
                    alert('Failed to send email: ' + msg);
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="bi bi-send-fill me-1"></i>Send Email');
                    $('#emailModalStatus').html('');
                }
            });
        });

        $('a[title="Reset"]').on('click', function() {
            setTimeout(function() { table.draw(); }, 100);
        });

    });
    </script>
</body>
</html>