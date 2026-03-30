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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-datatable-styles />
    <style>
        .time-filter-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            height: 100%;
        }
        .time-tab {
            flex: 1;
            min-width: 70px;
            max-width: 100px;
            border-radius: 8px;
            padding: 8px 5px;
            text-align: center;
            transition: all 0.2s;
            text-decoration: none;
            font-size: 14px;
            color: #333;
        }
        .time-tab:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            color: #333;
        }
        .time-tab.active {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white !important;
        }

        /*
         * Text colour only — NO background changes.
         *
         * row-danger  → first email > 2 days old AND latest resend > 2 days old
         *               dark red bold text (urgent — nobody followed up)
         *
         * row-warning → first email > 2 days old but latest resend ≤ 2 days old
         *               orange text (recently followed up)
         */
        tr.row-danger  td { color: #b30000 !important; font-weight: 600; }
        tr.row-warning td { color: #cc5500 !important; }

        .note-tag-btn { font-size: 0.75rem; padding: 2px 7px; }
    </style>
</head>
<body>

@php
    function formatNumber($value) {
        if (is_numeric($value)) {
            return number_format((float)$value, 2);
        }
        return 'N/A';
    }
    $today = \Carbon\Carbon::today();
@endphp

<div class="container mt-4">
    <h2 class="text-center mb-4">Reinsurance Case 2</h2>

    <div class="row mb-4">

        {{-- LEFT: server-side filters --}}
        <div class="col-md-5 pe-md-3">
            <form method="GET" action="{{ url('/getshow') }}" id="filterForm">
                <input type="hidden" name="time_filter" value="{{ $selected_time_filter }}">
                <div class="card time-filter-card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Filters</h5>
                        <div class="mb-3">
                            <label for="new_category" class="form-label fw-semibold">Department</label>
                            <select name="new_category" id="new_category" class="form-select form-select-sm">
                                <option value="">-- All Departments --</option>
                                @foreach(['Fire','Marine','Motor','Miscellaneous','Health'] as $dept)
                                    <option value="{{ $dept }}"
                                        {{ $selected_department === $dept ? 'selected' : '' }}>
                                        {{ $dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="reins_party" class="form-label fw-semibold">Reins Party</label>
                            <select name="reins_party" id="reins_party" class="form-select form-select-sm">
                                <option value="">-- All Reins Parties --</option>
                                @foreach($reins_party_options as $rp)
                                    <option value="{{ $rp }}"
                                        {{ $selected_reins === $rp ? 'selected' : '' }}>
                                        {{ $rp }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-funnel-fill me-1"></i> Filter
                            </button>
                            <a href="{{ url('/getshow') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-clockwise me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- RIGHT: Time Filter tabs --}}
        <div class="col-md-7 ps-md-3">
            <div class="card time-filter-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Time Filter</h5>
                    <div class="d-flex flex-wrap gap-2 justify-content-between">
                        @foreach ([
                            'all'    => ['label' => 'All',     'count' => $counts['all']],
                            '2days'  => ['label' => '2 Days',  'count' => $counts['2days']],
                            '5days'  => ['label' => '5 Days',  'count' => $counts['5days']],
                            '7days'  => ['label' => '7 Days',  'count' => $counts['7days']],
                            '10days' => ['label' => '10 Days', 'count' => $counts['10days']],
                            '15days' => ['label' => '15 Days', 'count' => $counts['15days']],
                            '15plus' => ['label' => '15+ Days','count' => $counts['15plus']],
                        ] as $key => $tab)
                            <a href="{{ url('/getshow?time_filter=' . $key
                                . '&new_category=' . urlencode($selected_department)
                                . '&reins_party='  . urlencode($selected_reins)) }}"
                               class="time-tab {{ $selected_time_filter === $key ? 'active' : 'bg-light' }}">
                                <div class="fw-bold" >{{ $tab['label'] }}</div>
                                <div class="small"   >{{ $tab['count'] }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

      

    </div>

    <!-- Data Table -->
    <div class="row mt-2">
        <div class="col-12">
            @if($records->isEmpty())
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    No data available for the selected filters.
                </div>
            @else
                <div class="table-responsive">
                    <table id="reportsTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Action</th>            {{-- index 0 --}}
                                <th>Note</th>              {{-- index 1 --}}
                                <th>Email Log</th>         {{-- index 2 --}}
                                <th>Email Date</th>        {{-- index 3 — FIRST email sent --}}
                                <th>Resend Email Date</th> {{-- index 4 — LATEST email sent --}}
                                <th>Dept</th>              {{-- index 5 --}}
                                <th>Business Class</th>
                                <th>Req Note No</th>
                                <th>Req Note Date</th>
                                <th>Insured</th>
                                <th>Reins Party</th>       {{-- index 10 --}}
                                <th>Total Sum Ins</th>
                                <th>Share</th>
                                <th>RI Sum Ins</th>
                                <th>Total Premium</th>
                                <th>RI Premium</th>
                                <th>Commission %</th>
                                <th>Commission Amt</th>
                                <th>Comm Date</th>
                                <th>Expiry Date</th>
                                <th>CP</th>
                                <th>Conventional/Takaful</th>
                                <th>Posted</th>
                                <th>Acceptance No</th>
                                <th>Acceptance Date</th>
                                <th>Warranty Period</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                @php
                                    // first_sent_at > 2 days old = row needs attention
                                    $firstOld  = $record->first_sent_at
                                        && \Carbon\Carbon::parse($record->first_sent_at)->diffInDays($today) > 2;

                                    // latest_sent_at > 2 days old = nobody followed up recently
                                    $latestOld = $record->latest_sent_at
                                        && \Carbon\Carbon::parse($record->latest_sent_at)->diffInDays($today) > 2;

                                    // row-danger  = both old (dark red bold)
                                    // row-warning = only first old (orange)
                                    $rowClass = '';
                                    if ($firstOld && $latestOld) {
                                        $rowClass = 'row-danger';
                                    } elseif ($firstOld) {
                                        $rowClass = 'row-warning';
                                    }
                                @endphp

                                <tr class="{{ $rowClass }}">

                                    {{-- 0: Resend Email --}}
                                    <td>
                                        <button class="btn btn-info btn-sm send-email-btn"
                                                data-req-note="{{ $record->reqnote }}"
                                                data-repname="{{ $record->repname ?? 'N/A' }}"
                                                data-created-by="{{ $record->created_by ?? 'N/A' }}"
                                                data-doc-date="{{ $record->doc_date ?? 'N/A' }}"
                                                data-dept="{{ $record->dept ?? 'N/A' }}"
                                                data-business-desc="{{ $record->business_desc ?? 'N/A' }}"
                                                data-insured="{{ $record->insured ?? 'N/A' }}"
                                                data-reins-party="{{ $record->reins_party ?? 'N/A' }}"
                                                data-total-sum-ins="{{ $record->total_sum_ins ?? 'N/A' }}"
                                                data-ri-sum-ins="{{ $record->ri_sum_ins ?? 'N/A' }}"
                                                data-share="{{ $record->share ?? 'N/A' }}"
                                                data-total-premium="{{ $record->total_premium ?? 'N/A' }}"
                                                data-ri-premium="{{ $record->ri_premium ?? 'N/A' }}"
                                                data-comm-date="{{ $record->comm_date ?? 'N/A' }}"
                                                data-expiry-date="{{ $record->expiry_date ?? 'N/A' }}"
                                                data-cp="{{ $record->cp ?? 'N/A' }}"
                                                data-conv-takaful="{{ $record->conv_takaful }}"
                                                data-posted="{{ $record->posted ? '1' : '0' }}"
                                                data-user-name="{{ $record->user_name ?? 'N/A' }}"
                                                data-acceptance-date="{{ $record->acceptance_date ?? 'N/A' }}"
                                                data-warranty-period="{{ $record->warranty_period ?? 'N/A' }}"
                                                data-commission-percent="{{ $record->commission_percent ?? 'N/A' }}"
                                                data-commission-amount="{{ $record->commission_amount ?? 'N/A' }}"
                                                data-acceptance-no="{{ $record->acceptance_no ?? 'N/A' }}"
                                                data-datetime="{{ $record->latest_sent_at ?? 'N/A' }}">
                                            Resend Email
                                        </button>
                                    </td>

                                    {{-- 1: Note --}}
                                    <td>
                                        <button class="btn btn-sm note-tag-btn"
                                                style="background:#fff3cd; border:1px solid #ffc107; color:#856404;"
                                                data-req-note="{{ $record->reqnote }}"
                                                title="Add Note Tag">
                                            <i class="fas fa-sticky-note"></i> Note
                                        </button>
                                    </td>

                                    {{-- 2: Email Log count --}}
                                    <td>
                                        <a href="#" class="email-log-link"
                                           data-req-note="{{ $record->reqnote }}"
                                           data-datetime="{{ $record->first_sent_at ?? 'N/A' }}">
                                            {{ $record->email_count }}
                                        </a>
                                    </td>

                                    {{-- 3: Email Date = FIRST email sent --}}
                                    <td>
                                        {{ $record->first_sent_at
                                            ? \Carbon\Carbon::parse($record->first_sent_at)->format('m/d/Y, g:i:s A')
                                            : 'N/A' }}
                                    </td>

                                    {{-- 4: Resend Email Date = LATEST email sent --}}
                                    <td>
                                        @if($record->latest_sent_at && $record->latest_sent_at !== $record->first_sent_at)
                                            {{ \Carbon\Carbon::parse($record->latest_sent_at)->format('m/d/Y, g:i:s A') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>

                                    <td>{{ $record->dept          ?? 'N/A' }}</td>
                                    <td>{{ $record->business_desc ?? 'N/A' }}</td>
                                    <td>{{ $record->reqnote       ?? 'N/A' }}</td>
                                    <td>{{ $record->doc_date      ?? 'N/A' }}</td>
                                    <td>{{ $record->insured       ?? 'N/A' }}</td>
                                    <td>{{ $record->reins_party   ?? 'N/A' }}</td>
                                    <td>{{ formatNumber($record->total_sum_ins) }}</td>
                                    <td>{{ formatNumber($record->share) }}</td>
                                    <td>{{ formatNumber($record->ri_sum_ins) }}</td>
                                    <td>{{ formatNumber($record->total_premium) }}</td>
                                    <td>{{ formatNumber($record->ri_premium) }}</td>
                                    <td>{{ formatNumber($record->commission_percent) }}</td>
                                    <td>{{ formatNumber($record->commission_amount) }}</td>
                                    <td>{{ $record->comm_date       ?? 'N/A' }}</td>
                                    <td>{{ $record->expiry_date     ?? 'N/A' }}</td>
                                    <td>{{ $record->cp              ?? 'N/A' }}</td>
                                    <td>{{ $record->conv_takaful  ?? 'N/A' }}</td>
                                    <td>{{ $record->posted       ? 'Yes' : 'No' }}</td>
                                    <td>{{ $record->acceptance_no   ?? 'N/A' }}</td>
                                    <td>{{ $record->acceptance_date ?? 'N/A' }}</td>
                                    <td>{{ $record->warranty_period ?? 'N/A' }}</td>
                                    <td>{{ $record->created_by      ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Resend Email Modal -->
<div id="emailModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resend Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">To:</label>
                    <input type="text" class="form-control" id="to" placeholder="Recipient email(s), comma-separated">
                </div>
                <div class="mb-3">
                    <label class="form-label">CC:</label>
                    <input type="text" class="form-control" id="cc" placeholder="CC email(s), comma-separated">
                </div>
                <div class="mb-3">
                    <label class="form-label">Subject:</label>
                    <input type="text" class="form-control" id="subject">
                </div>
                <div class="mb-3">
                    <label class="form-label">Body:</label>
                    <textarea class="form-control" id="body" rows="5"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="sendEmailBtn">Resend Email</button>
            </div>
        </div>
    </div>
</div>

<!-- Email Log Modal -->
<div id="emailLogModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailLogModalLabel">Email Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>Total Emails Sent: <span id="emailLogCount">0</span></h6>
                <div class="table-responsive">
                    <table id="emailLogTable" class="table table-bordered">
                        <thead>
                            <tr><th>Sent At</th><th>To</th><th>CC</th><th>Subject</th></tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Note Tag Modal -->
<div class="modal fade" id="noteTagModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header" style="background:#fff3cd; border-bottom:1px solid #ffc107;">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fas fa-sticky-note me-2 text-warning"></i>Add Note Tag
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="rounded p-3 mb-4" style="background:#f8f9fa; border-left:4px solid #ffc107;">
            <div class="row g-2">
                <div class="col-12">
                    <small class="text-muted d-block" style="font-size:0.7rem;">REQUEST NOTE #</small>
                    <strong id="ntRefNo" class="text-dark" style="font-size:0.95rem;">—</strong>
                </div>
               </div>
            </div>

                <input type="hidden" id="ntHiddenRef">
                <input type="hidden" id="ntReportName" value="RQN_2">
                        
              
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Action <span class="text-danger">*</span></label>
                    <select class="form-select" id="ntAction">
                        <option value="">— Select Action —</option>
                        <option value="decline">Decline</option>
                    </select>
                    <div class="invalid-feedback">Please select an action.</div>
                </div>
                <div class="mb-1">
                    <label class="form-label fw-semibold">Remarks</label>
                    <textarea class="form-control" id="ntRemarks" rows="4" maxlength="2000"
                              placeholder="Enter remarks (optional)…" style="resize:vertical;"></textarea>
                    <div class="d-flex justify-content-end mt-1">
                        <small class="text-muted"><span id="ntCharCount">0</span>/2000</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#fafafa;">
                <div class="me-auto">
                    <span class="text-danger small d-none" id="ntSubmitError">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        <span id="ntSubmitErrorMsg"></span>
                    </span>
                </div>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Close
                </button>
                <button type="button" class="btn btn-warning fw-bold text-dark" id="ntSubmitBtn">
                    <i class="fas fa-save me-1"></i>Save Note
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.3.2/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
var currentRow    = null;
var noteTargetRow = null;

$(document).ready(function () {

    var table = $('#reportsTable').DataTable({
        paging:        false,
        searching:     true,
        ordering:      true,
        info:          true,
        scrollX:       true,
        scrollY:       '500px',
        scrollCollapse: false,
        fixedHeader:   { header: true, footer: true },
        autoWidth:     true,
        dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel custom-icon"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Renewal Report',
                footer: true,
                exportOptions: { modifier: { page: 'current' } }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf custom-icon"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Renewal Report',
                orientation: 'landscape',
                pageSize: 'A4',
                footer: true,
                exportOptions: { columns: ':visible', modifier: { page: 'current' } }
            }
        ],
        initComplete: function () { this.api().columns.adjust(); },
        drawCallback:  function () { this.api().columns.adjust(); }
    });

    // ----------------------------------------------------------------
    // Note Tag — open
    // ----------------------------------------------------------------
    $(document).on('click', '.note-tag-btn', function () {
        noteTargetRow = $(this).closest('tr')[0];
        var reqNote = $(this).data('req-note');
        $('#ntHiddenRef').val(reqNote);
        $('#ntRefNo').text(reqNote);
        $('#ntAction').val('').removeClass('is-invalid');
        $('#ntRemarks').val('');
        $('#ntCharCount').text('0');
        $('#ntSubmitError').addClass('d-none');
        $('#noteTagModal').modal('show');
    });

    $('#ntRemarks').on('input', function () {
        $('#ntCharCount').text($(this).val().length);
    });

    $('#ntAction').on('change', function () {
        $(this).removeClass('is-invalid');
        $('#ntSubmitError').addClass('d-none');
    });

    // ----------------------------------------------------------------
    // Note Tag — submit → remove row immediately
    // ----------------------------------------------------------------
    $('#ntSubmitBtn').on('click', function () {
        var refNo   = $('#ntHiddenRef').val();
        var action  = $('#ntAction').val();
        var remarks = $('#ntRemarks').val().trim();
        var reportName = $('#ntReportName').val();

        if (!action) {
            $('#ntAction').addClass('is-invalid');
            $('#ntSubmitError').removeClass('d-none');
            $('#ntSubmitErrorMsg').text('Please select an action.');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

        $.ajax({
            url: "{{ route('reins.tag.store') }}",
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: {
                GRH_REFERENCE_NO: refNo,
                report_name:      reportName,
                tag_action:       action,
                remarks:          remarks
            },
            success: function (response) {
                if (response.success) {
                    $('#noteTagModal').modal('hide');
                    if (noteTargetRow) {
                        table.row(noteTargetRow).remove().draw(false);
                        noteTargetRow = null;
                    }
                } else {
                    $('#ntSubmitError').removeClass('d-none');
                    $('#ntSubmitErrorMsg').text(response.message || 'Failed to save note.');
                    $btn.prop('disabled', false)
                        .html('<i class="fas fa-save me-1"></i>Save Note');
                }
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error))
                            ? (xhr.responseJSON.message || xhr.responseJSON.error)
                            : 'Server error. Please try again.';
                $('#ntSubmitError').removeClass('d-none');
                $('#ntSubmitErrorMsg').text(msg);
                $btn.prop('disabled', false)
                    .html('<i class="fas fa-save me-1"></i>Save Note');
            }
        });
    });

    $('#noteTagModal').on('hidden.bs.modal', function () {
        $('#ntSubmitBtn').prop('disabled', false)
                        .html('<i class="fas fa-save me-1"></i>Save Note');
        noteTargetRow = null;
    });

    // ----------------------------------------------------------------
    // Resend Email — open modal
    // ----------------------------------------------------------------
    $(document).on('click', '.send-email-btn', function () {
        currentRow = $(this).closest('tr');
        var d = $(this).data();

        $('#to').val('');
        $('#cc').val('');
        $('#subject').val('Reinsurance Request Note: ' + d.reqNote);
        $('#body').val(
            'Dear Sir/Madam,\n\n' +
            'Please find below details for Request Note: ' + d.reqNote + '\n\n' +
            'Insured: '               + (d.insured      || 'N/A') + '\n' +
            'Reinsurance Party: '    + (d.reinsParty   || 'N/A') + '\n' +
            'Business Description: ' + (d.businessDesc || 'N/A') + '\n\n' +
            'Regards,\n\nAtlas Insurance Ltd.'
        );
        $('#emailModal').modal('show');
    });

    // ----------------------------------------------------------------
    // Resend Email — send
    // No row colour change on send — colour is based on DB dates only
    // ----------------------------------------------------------------
    $('#sendEmailBtn').on('click', function () {
        var to      = $('#to').val().trim();
        var cc      = $('#cc').val().trim();
        var subject = $('#subject').val().trim();
        var body    = $('#body').val().trim();
        var $btn    = $(this);

        if (!to) {
            alert('Please enter a valid email address in the "To" field.');
            return;
        }

        $btn.prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm"></span> Sending...');

        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var rowData   = currentRow.find('.send-email-btn').data();

        var fullRowData = {
            reqNote:           rowData.reqNote,
            repname:           rowData.repname           || 'N/A',
            createdBy:         rowData.createdBy         || 'N/A',
            docDate:           rowData.docDate           || 'N/A',
            dept:              rowData.dept              || 'N/A',
            businessDesc:      rowData.businessDesc      || 'N/A',
            insured:           rowData.insured           || 'N/A',
            reinsParty:        rowData.reinsParty        || 'N/A',
            totalSumIns:       rowData.totalSumIns       || 'N/A',
            riSumIns:          rowData.riSumIns          || 'N/A',
            share:             rowData.share             || 'N/A',
            totalPremium:      rowData.totalPremium      || 'N/A',
            riPremium:         rowData.riPremium         || 'N/A',
            commDate:          rowData.commDate          || 'N/A',
            expiryDate:        rowData.expiryDate        || 'N/A',
            cp:                rowData.cp                || 'N/A',
            convTakaful:       rowData.convTakaful       || '0',
            posted:            rowData.posted            || '0',
            userName:          rowData.userName          || 'N/A',
            acceptanceDate:    rowData.acceptanceDate    || 'N/A',
            warrantyPeriod:    rowData.warrantyPeriod    || 'N/A',
            commissionPercent: rowData.commissionPercent || 'N/A',
            commissionAmount:  rowData.commissionAmount  || 'N/A',
            acceptanceNo:      (rowData.acceptanceNo && rowData.acceptanceNo !== 'N/A')
                                    ? rowData.acceptanceNo : '',
            datetime:          rowData.datetime || 'N/A'
        };

        $.ajax({
            url: "{{ route('send.email') }}",
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: {
                _token:  csrfToken,
                to:      to,
                cc:      cc,
                subject: subject,
                body:    body,
                records: JSON.stringify([fullRowData])
            },
            success: function (response) {
    if (response.success) {
        alert('Email sent successfully!');
        $('#emailModal').modal('hide');

        if (currentRow) {
            // 1. Get the current time string
            var now = new Date().toLocaleString('en-US', { 
                month: '2-digit', day: '2-digit', year: 'numeric', 
                hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true 
            });

            // 2. Update the TEXT of the 'Resend Email Date' column (Index 4)
            // We use .find('td').eq(4) to change the text without touching the <tr> class
            currentRow.find('td').eq(4).text(now);

            // 3. Update the Email Log Count (Index 2)
            var logLink = currentRow.find('.email-log-link');
            var currentCount = parseInt(logLink.text()) || 0;
            logLink.text(currentCount + 1);

            // NOTE: We do NOT call table.draw() here. 
            // Calling .draw() is what usually triggers the row to reset its color.
        }
    } else {
        alert('Error: ' + (response.message || 'Unknown error.'));
    }
},
            
            error: function (xhr) {
                alert((xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message : 'Failed to send email');
            },
            complete: function () {
                $btn.prop('disabled', false).text('Resend Email');
                currentRow = null;
            }
        });
    });

    // ----------------------------------------------------------------
    // Email Log modal
    // ----------------------------------------------------------------
    $(document).on('click', '.email-log-link', function (e) {
        e.preventDefault();
        var reqNote  = $(this).data('req-note');
        var datetime = $(this).data('datetime');

        $('#emailLogModalLabel').text('Email Log for Request Note: ' + reqNote);
        var tbody = $('#emailLogTable tbody');
        tbody.html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
        $('#emailLogCount').text('Loading...');
        $('#emailLogModal').modal('show');

        $.ajax({
            url: "{{ route('get.email.logs') }}",
            method: 'GET',
            data: { reqnote: reqNote, datetime: datetime },
            success: function (response) {
                if (response.success) {
                    var logs = response.logs;
                    $('#emailLogCount').text(logs.length);
                    tbody.empty();
                    if (logs.length === 0) {
                        tbody.append('<tr><td colspan="4" class="text-center">No email logs found.</td></tr>');
                    } else {
                        $.each(logs, function (i, log) {
                            tbody.append(
                                '<tr>' +
                                '<td>' + new Date(log.datetime).toLocaleString() + '</td>' +
                                '<td>' + (log.sent_to || 'N/A') + '</td>' +
                                '<td>' + (log.sent_cc || 'N/A') + '</td>' +
                                '<td>' + (log.subject  || 'N/A') + '</td>' +
                                '</tr>'
                            );
                        });
                    }
                } else {
                    tbody.html('<tr><td colspan="4" class="text-center text-danger">Error loading email logs</td></tr>');
                    $('#emailLogCount').text('Error');
                }
            },
            error: function () {
                tbody.html('<tr><td colspan="4" class="text-center text-danger">Failed to load email logs</td></tr>');
                $('#emailLogCount').text('Error');
            }
        });
    });

});
</script>
</body>
</html>