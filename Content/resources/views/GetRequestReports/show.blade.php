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
        .time-filter-card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,.1); }
        .time-tab         { flex: 1; min-width: 80px; max-width: 100px; border-radius: 8px; padding: 8px 5px; text-align: center; transition: all .2s; text-decoration: none; font-size: 14px; }
        .time-tab:hover   { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,.15); }
        .time-tab.active  { background: linear-gradient(45deg,#007bff,#0056b3); color:#fff !important; }
      /* Declined rows remain gray background */
tr.row-declined {
    background-color: #f0f0f0 !important;
    opacity: .85;
}

/* Pending 2+ days old → text red */
tr.highlight-red td {
    color: red !important;
}
        
        tr.row-declined   { background-color: #f0f0f0 !important; opacity: .85; }

        .risk-btn { min-width: 70px; font-weight: 600; letter-spacing: .5px; }
        .risk-btn.active-y { background: #198754; color: #fff; border-color: #198754; }
        .risk-btn.active-d { background: #dc3545; color: #fff; border-color: #dc3545; }
        .risk-btn.active-n { background: #6c757d; color: #fff; border-color: #6c757d; }
        .risk-btn.active-all { background: #0d6efd; color: #fff; border-color: #0d6efd; }
    </style>
</head>
<body>

<div class="container mt-4">
    <x-report-header title="Reinsurance Case" />

    <div class="row mb-4">
        <div class="col-md-5 pe-md-4 border-end">
            <form method="GET" action="{{ url('/getsshow') }}" id="filterForm">
                <div class="card time-filter-card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="new_category" class="form-label fw-bold">Department</label>
                            <select name="new_category" id="new_category" class="form-select">
                                <option value="">All Departments</option>
                                @foreach(['Fire','Marine','Motor','Miscellaneous','Health'] as $dept)
                                    <option value="{{ $dept }}" {{ $selected_department == $dept ? 'selected' : '' }}>
                                        {{ $dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Risk Status</label>
                            <div class="d-flex gap-2">
                                @foreach(['Y' => ['label'=>'Pending', 'cls'=>'active-y'], 'D' => ['label'=>'Declined', 'cls'=>'active-d'], 'N' => ['label'=>'Rejected', 'cls'=>'active-n'], 'ALL' => ['label'=>'All', 'cls'=>'active-all']] as $val => $opt)
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary risk-btn {{ $selected_risk == $val ? $opt['cls'] : '' }}"
                                            onclick="setRisk('{{ $val }}')">
                                        {{ $opt['label'] }}
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" name="risk_filter" id="riskFilterInput" value="{{ $selected_risk }}">
                            <input type="hidden" name="time_filter" value="{{ $selected_time_filter }}">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel-fill me-1"></i> Filter
                            </button>
                            <a href="{{ url('/show') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-7 ps-md-4">
            <div class="card time-filter-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Time Filter</h5>
                    <div class="d-flex flex-wrap gap-2 justify-content-between">
                        @foreach(['all'=>'All','2days'=>'2 Days','5days'=>'5 Days','7days'=>'7 Days','10days'=>'10 Days','15days'=>'15 Days','15plus'=>'15+ Days'] as $key => $label)
                            <a href="{{ url('/show?new_category='.urlencode($selected_department).'&time_filter='.$key.'&risk_filter='.urlencode($selected_risk)) }}"
                               class="time-tab {{ $selected_time_filter == $key ? 'active' : 'bg-light' }}">
                                <div class="fw-bold">{{ $label }}</div>
                                <div class="small">{{ $counts[$key] ?? 0 }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            @if($records->isEmpty())
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle me-2"></i> No data available for the selected filters.
                </div>
            @else
                <div class="table-responsive">
                    <table id="reportsTable" class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Action</th>
                                
                                <th>Created At</th>
                                <th>Created By</th>
                                <th>Document Ref</th>
                                 <th>Base Doc</th>
                                  <th>Request Note status</th>
                                <th>Department</th>
                                <th>Policy Issue Date</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>posting</th>

                                <th>Insured</th>
                                 <th>IAP</th>
                                <th>Sum Insured</th>
                                <th>Gross Premium</th>
                               
                                <th>Location</th>
                               
                               
                                <th>Risk</th>

                              
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                @php
                                    $deptMap = [
                                        11 => 'Fire', 12 => 'Marine', 13 => 'Motor',
                                        14 => 'Miscellaneous', 16 => 'Health',
                                    ];
                                    $isDeclined = ($record->riskMarked === 'D');
                                    $isN        = ($record->riskMarked === 'N');
                                    $isPending = (!$isDeclined && !$isN);
                                    
                                    // Highlight red if pending and 2 or more days old
                                    $isOverdue = ($isPending && $record->days_old >= 2);
                                    $rowClass = $isDeclined ? 'row-declined' : ($isOverdue ? 'highlight-red' : '');
                                @endphp

                                <tr data-uw-doc="{{ $record->uw_doc ?? '' }}" class="{{ $rowClass }}">
                                    
                                    <td class="text-center">
                                        @if($isDeclined)
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                <i class="bi bi-check-circle me-1"></i> Declined
                                            </button>
                                        @elseif($isN)
                                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                                <i class="bi bi-dash-circle me-1"></i> N
                                            </button>
                                        @else
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger decline-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#declineModal"
                                                    data-uw-doc="{{ $record->uw_doc ?? 'N/A' }}"
                                                    data-insured="{{ addslashes($record->insured ?? 'N/A') }}"
                                                    data-dept="{{ $deptMap[$record->dept ?? ''] ?? 'N/A' }}"
                                                    data-created-at="{{ $record->created_at ? $record->created_at->format('d-m-Y H:i') : 'N/A' }}">
                                                <i class="bi bi-x-circle"></i> Decline
                                            </button>
                                        @endif
                                    </td>
                                    <td>{{ $record->created_at ? $record->created_at->format('d-m-Y H:i:s') : now()->format('d-m-Y H:i:s') }}</td>
                                    <td>{{ $record->created_by ?? 'N/A' }}</td>
                                    <td>{{ $record->uw_doc ?? 'N/A' }}</td>
                                     <td>{{ $record->GDH_BASEDOCUMENTNO }}</td>
                                     <td>{{ $record->RQN_STS }}</td>
                                    <td>{{ $deptMap[$record->dept ?? ''] ?? 'N/A' }}</td>
                                    <td>{{ !empty($record->issue_date)  ? \Carbon\Carbon::parse($record->issue_date)->format('d-m-Y')  : 'N/A' }}</td>
                                    <td>{{ !empty($record->comm_date)   ? \Carbon\Carbon::parse($record->comm_date)->format('d-m-Y')   : 'N/A' }}</td>
                                    <td>{{ !empty($record->expiry_date) ? \Carbon\Carbon::parse($record->expiry_date)->format('d-m-Y') : 'N/A' }}</td>
                                    <td>{{ $record->GDH_POSTING_DATE ?? 'N/A' }}</td>

                                    <td title="{{ $record->insured ?? '' }}">
                                        {{ \Illuminate\Support\Str::limit($record->insured ?? 'N/A', 20, '...') }}
                                    </td>
                                    <td>{{ $record->PII_DESC  }}</td>

                                   
                                    <td class="text-end">{{ !empty($record->sum_insured)   ? number_format($record->sum_insured, 2)   : 'N/A' }}</td>
                                    <td class="text-end">{{ !empty($record->gross_premium) ? number_format($record->gross_premium, 2) : 'N/A' }}</td>

                                     <td title="{{ $record->location ?? '' }}">
                                        {{ \Illuminate\Support\Str::limit($record->location ?? 'N/A', 20, '...') }}
                                    </td>
                                   
                                    

                                    
                                     {{-- Risk badge --}}
                                    <td class="text-center">
                                        @if($isDeclined)
                                            <span class="badge bg-danger px-2 py-1">D</span>
                                        @elseif($isN)
                                            <span class="badge bg-secondary px-2 py-1">N</span>
                                        @else
                                            <span class="badge bg-success px-2 py-1">Y</span>
                                        @endif
                                    </td>

                                  
                                    
                                   
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Decline Modal -->
    <div class="modal fade" id="declineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Decline Reinsurance Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="declineForm">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Document Ref</label>
                                <input type="text" id="modalDocRef" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Insured</label>
                                <input type="text" id="modalInsured" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Department</label>
                            <input type="text" id="modalDept" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">From <small class="text-muted">(current user)</small></label>
                            <input type="email" id="modalFrom" class="form-control" readonly
                                   value="{{ $currentUserEmail ?? 'system@yourcompany.com' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">To <small class="text-danger">*</small></label>
                            <input type="text" id="modalTo" class="form-control" placeholder="email@domain.com" required>
                            <div class="invalid-feedback">At least one recipient email is required.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">CC <small class="text-muted">(optional, comma separated)</small></label>
                            <input type="text" id="modalCc" class="form-control" placeholder="cc1@domain.com, cc2@domain.com">
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label fw-bold text-danger">
                                Remarks / Reason for Decline *
                            </label>
                            <textarea class="form-control" id="remarks" rows="5" required
                                      placeholder="Please explain why this request is being declined..."></textarea>
                            <div class="invalid-feedback">Remarks are required.</div>
                        </div>

                        <input type="hidden" id="emailSubject" value="DECLINED - Reinsurance Request">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" id="btnSubmitDecline">
                            <i class="bi bi-envelope-x me-1"></i> Send & Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hidden field for current user email -->
<input type="hidden" id="currentUserEmail" value="{{ $currentUserEmail ?? '' }}">

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
// Risk filter toggle
function setRisk(val) {
    $('#riskFilterInput').val(val);
    const classMap = { Y: 'active-y', D: 'active-d', N: 'active-n', ALL: 'active-all' };
    $('.risk-btn').each(function () {
        const btnVal = $(this).attr('onclick').match(/'(\w+)'/)[1];
        $(this).removeClass('active-y active-d active-n active-all');
        if (btnVal === val) $(this).addClass(classMap[val] || '');
    });
}

$(document).ready(function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $('#new_category').select2({ placeholder: 'Select a department', allowClear: true, width: '100%' });

    $('#reportsTable').DataTable({
        paging: false,
        searching: true,
        ordering: true,
        info: true,
        scrollX: true,
        scrollY: '500px',
        scrollCollapse: false,
        fixedHeader: true,
        autoWidth: true,
        dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Reinsurance Case Report',
                footer: true,
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data, row, column, node) {
                            if (column === 6 || column === 7) return $(node).attr('title') || $(node).text().trim();
                            return $(node).text().trim();
                        }
                    }
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Reinsurance Case Report',
                orientation: 'landscape',
                pageSize: 'A4',
                footer: true,
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data, row, column, node) {
                            if (column === 6 || column === 7) return $(node).attr('title') || $(node).text().trim();
                            return $(node).text().trim();
                        }
                    }
                }
            }
        ]
    });

    let currentUwDoc = null;
    let currentCreatedAt = null;

    $(document).on('click', '.decline-btn', function () {
        const btn = $(this);
        currentUwDoc     = btn.data('uw-doc');
        currentCreatedAt = btn.data('created-at') || 'N/A';

        $('#modalDocRef').val(currentUwDoc);
        $('#modalInsured').val(btn.data('insured'));
        $('#modalDept').val(btn.data('dept'));
        $('#remarks').val('').removeClass('is-invalid');
        $('#modalTo').val(' reho@ail.atlas.pk ').removeClass('is-invalid');  
        $('#modalCc').val('');
        $('#modalFrom').val($('#currentUserEmail').val() || 'system@yourcompany.com');

        $('#btnSubmitDecline').prop('disabled', false)
            .html('<i class="bi bi-envelope-x me-1"></i> Send & Save');
    });
    

    $('#declineForm').on('submit', function (e) {
        e.preventDefault();

        const remarks = $('#remarks').val().trim();
        if (!remarks) {
            $('#remarks').addClass('is-invalid');
            return;
        }

        const to = $('#modalTo').val().trim();
        if (!to) {
            $('#modalTo').addClass('is-invalid');
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const toList = to.split(',').map(e => e.trim()).filter(e => e);
        if (toList.some(email => !emailRegex.test(email))) {
            alert('Please enter valid email addresses in "To" field.');
            return;
        }

        const cc = $('#modalCc').val().trim();
        const dept    = $('#modalDept').val();
        const insured = $('#modalInsured').val();

        const emailBody = `
DECLINE NOTIFICATION

Document Ref : ${currentUwDoc}
Department   : ${dept}
Insured      : ${insured}
Created At   : ${currentCreatedAt}

Reason / Remarks:
─────────────────────────────────────────
${remarks}
─────────────────────────────────────────

This request has been DECLINED.`;

        const $btn = $('#btnSubmitDecline');
        $btn.prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-2"></span> Sending...');

        $.ajax({
            url    : "{{ route('send.email') }}",
            method : 'POST',
            data   : {
                from    : $('#modalFrom').val(),
                to      : to,
                cc      : cc || null,
                subject : $('#emailSubject').val(),
                body    : emailBody,
                record  : { uw_doc: currentUwDoc, remarks, dept, insured }
            },
            success: function (res) {
                if (res.success) {
                    $.ajax({
                        url    : "{{ route('save.remarks') }}",
                        method : 'POST',
                        data   : { uw_doc: currentUwDoc, remarks },
                        success: function (dbRes) {
                            if (dbRes.success) {
                                updateRowInDOM(currentUwDoc);
                                $('#declineModal').modal('hide');
                                showToast('✅ Email sent and record declined successfully!', 'success');
                            } else {
                                $('#declineModal').modal('hide');
                                showToast('⚠️ Email sent but DB error: ' + (dbRes.message || 'Unknown'), 'warning');
                            }
                        },
                        error: function (xhr) {
                            $('#declineModal').modal('hide');
                            showToast('⚠️ Email sent but failed to update DB: ' + (xhr.responseJSON?.message || 'Unknown'), 'warning');
                        }
                    });
                } else {
                    showToast('❌ Email error: ' + (res.message || 'Unknown'), 'danger');
                }
            },
            error: function (xhr) {
                showToast('❌ Failed to send email: ' + (xhr.responseJSON?.message || 'Server error'), 'danger');
            },
            complete: function () {
                $btn.prop('disabled', false)
                    .html('<i class="bi bi-envelope-x me-1"></i> Send & Save');
            }
        });
    });

    function updateRowInDOM(uwDoc) {
        const $row = $('tr[data-uw-doc="' + uwDoc + '"]');
        $row.find('td').eq(0).html(
            '<button class="btn btn-sm btn-secondary" disabled>' +
            '<i class="bi bi-check-circle me-1"></i> Declined</button>'
        );
        $row.find('td').eq(14).html('<span class="badge bg-danger px-2 py-1">D</span>');
        $row.removeClass('highlight-red').addClass('row-declined');
    }

    function showToast(message, type) {
        const bg  = { success: '#d1e7dd', warning: '#fff3cd', danger: '#f8d7da' };
        const col = { success: '#0f5132', warning: '#664d03', danger: '#842029' };
        const $t  = $(`<div style="position:fixed;bottom:24px;right:24px;z-index:9999;
            background:${bg[type]};color:${col[type]};border:1px solid ${col[type]};
            padding:14px 20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.2);
            font-size:14px;max-width:380px;font-weight:500;">${message}</div>`);
        $('body').append($t);
        setTimeout(() => $t.fadeOut(400, () => $t.remove()), 4500);
    }
});
</script>
</body>
</html>